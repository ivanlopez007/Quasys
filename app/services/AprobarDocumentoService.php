<?php

namespace App\Services;

use App\Models\CambioDocumento;
use App\Models\Documento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Auth;

class AprobarDocumentoService
{
    /**
     * Aprueba la solicitud, copia el archivo en R2 y publica la nueva versión.
     */
    public function ejecutar(CambioDocumento $solicitud, int $aprobadorId, int $estadoAprobadoId): Documento
    {
        return DB::transaction(function () use ($solicitud, $aprobadorId, $estadoAprobadoId) {
            // 1. Obtener identificador del Tenant actual
            $tenantId = tenant('id') ?? Auth::user()->tenant_id;

            // 2. Determinar código y versión del documento
            $codigoDocumento = $solicitud->documento 
                ? $solicitud->documento->codigo_documento 
                : $this->generarCodigoDocumento($solicitud);

            $nuevaVersion = $solicitud->version;

            // 3. Gestionar archivos en Cloudflare R2
            $extension = pathinfo($solicitud->url_documento, PATHINFO_EXTENSION);
            $nombreArchivoOficial = "{$codigoDocumento}_v{$nuevaVersion}.{$extension}";
            
            // Ruta destino inmutable por tenant
            $nuevaRutaR2 = "tenants/{$tenantId}/documentos/{$codigoDocumento}/v{$nuevaVersion}/{$nombreArchivoOficial}";

            // Verificar existencia en R2 y copiar a la ubicación final
            if (Storage::disk('r2')->exists($solicitud->url_documento)) {
                Storage::disk('r2')->copy($solicitud->url_documento, $nuevaRutaR2);
            } else {
                throw new Exception("El archivo fuente no se encuentra en Cloudflare R2: {$solicitud->url_documento}");
            }

            // 4. Desactivar versión anterior si existe
            if ($solicitud->documento_id) {
                Documento::where('codigo_documento', $codigoDocumento)
                    ->where('vigente', true)
                    ->update(['vigente' => false]);
            }

            // 5. Crear el nuevo registro en la tabla 'documentos'
            $nuevoDocumento = Documento::create([
                'codigo_documento'     => $codigoDocumento,
                'nombre_documento'     => $solicitud->nombre_documento,
                'version'              => $nuevaVersion,
                'url_documento'        => $nuevaRutaR2,
                'nivel_id'             => $solicitud->nivel_id,
                'subnivel_id'          => $solicitud->subnivel_id,
                'localidad_id'         => $solicitud->localidad_id,
                'area_id'              => $solicitud->area_id,
                'lugar_retencion_id'   => $solicitud->lugar_retencion_id,
                'periodo_retencion_id' => $solicitud->periodo_retencion_id,
                'disposicion_final_id' => $solicitud->disposicion_final_id,
                'usuario_id'           => $solicitud->solicitante_id,
                'aprobar_id'           => $aprobadorId,
                'cambio_documento_id'  => $solicitud->id,
                'vigente'              => true,
                'fecha_publicacion'    => now(),
            ]);

            // 6. Actualizar la solicitud de cambio
            $solicitud->update([
                'estado_id'        => $estadoAprobadoId,
                'aprobar_id'       => $aprobadorId,
                'fecha_aprobacion' => now(),
            ]);

            return $nuevoDocumento;
        });
    }

    private function generarCodigoDocumento(CambioDocumento $solicitud): string
    {
        // Lógica de generación de correlativo (ejemplo: DOC-00012)
        $consecutivo = Documento::max('id') + 1;
        return 'DOC-' . str_pad($consecutivo, 5, '0', STR_PAD_LEFT);
    }
}