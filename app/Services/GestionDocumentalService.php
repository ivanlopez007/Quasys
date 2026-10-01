<?php

namespace App\Services;

use App\Mail\SolicitudResueltaMail;
use App\Models\BitacoraDocumento;
use App\Models\CambioDocumento;
use App\Models\Documento;
use App\Models\EstadoSolicitud;
use App\Models\PeriodoRetencion;
use App\Models\User;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GestionDocumentalService
{
    /** Campos de clasificación que una Revisión hereda del documento origen si no se envían. */
    private const CAMPOS_HEREDABLES = [
        'nivel_id',
        'subnivel_id',
        'localidad_id',
        'area_id',
        'lugar_retencion_id',
        'periodo_retencion_id',
        'disposicion_final_id',
        'fecha_proxima_revision',
    ];

    private function disco(): Filesystem
    {
        return Storage::disk(config('documentos.disk'));
    }

    private function estadoId(string $clave): int
    {
        $nombre = config("documentos.estados.$clave");
        $id = EstadoSolicitud::where('estado_solicitud', $nombre)->value('id');

        if (!$id) {
            throw new DomainException("No existe el estado «{$nombre}» en el catálogo de estados de solicitud.");
        }

        return (int) $id;
    }

    private function pendientes(): Builder
    {
        return CambioDocumento::whereHas(
            'estado',
            fn ($q) => $q->where('estado_solicitud', config('documentos.estados.pendiente'))
        );
    }

    // ------------------------------------------------------------------
    // CREAR SOLICITUD (Nuevo / Revisión / Eliminar)
    // ------------------------------------------------------------------
    public function crearSolicitud(User $usuario, array $datos, ?UploadedFile $archivo): CambioDocumento
    {
        $tipo = (int) $datos['tipo_solicitud_id'];
        $clave = CambioDocumento::claveDeTipo($tipo);
        $origen = null;

        if (!$clave) {
            throw new DomainException('Tipo de solicitud no válido. Revisa que el catálogo tenga los tipos Nuevo, Revisión y Eliminar.');
        }

        if ($clave === CambioDocumento::TIPO_NUEVO) {
            $codigo = trim($datos['codigo_documento']);

            if (Documento::where('codigo_documento', $codigo)->exists()) {
                throw new DomainException("Ya existe un documento con el código {$codigo}. Para modificarlo solicita una Revisión.");
            }
            if ($this->pendientes()->where('codigo_documento', $codigo)->exists()) {
                throw new DomainException("Ya hay una solicitud pendiente con el código {$codigo}.");
            }

            $version = 1;
        } else {
            $origen = Documento::where('vigente', true)->find($datos['documento_id']);

            if (!$origen || !$origen->esVisiblePara($usuario)) {
                throw new DomainException('El documento seleccionado no existe, ya no está vigente o no está asignado a tu planta.');
            }
            if ($this->pendientes()->where('codigo_documento', $origen->codigo_documento)->exists()) {
                throw new DomainException('Este documento ya tiene una solicitud pendiente de resolver.');
            }

            $codigo = $origen->codigo_documento;
            $version = $clave === CambioDocumento::TIPO_REVISION
                ? Documento::where('codigo_documento', $codigo)->max('version') + 1
                : $origen->version;
        }

        $clasificacion = [];
        foreach (self::CAMPOS_HEREDABLES as $campo) {
            $clasificacion[$campo] = $datos[$campo] ?? $origen?->{$campo};
        }

        // La cantidad se copia del catálogo en este momento: si después se edita el
        // catálogo, esta versión conserva la retención con la que fue aprobada.
        $clasificacion['tiempo_retencion'] = $clasificacion['periodo_retencion_id']
            ? PeriodoRetencion::whereKey($clasificacion['periodo_retencion_id'])->value('tiempo')
            : null;

        // Plantas que podrán verlo; una Revisión hereda las del documento origen si no se envían.
        $plantas = collect($datos['plantas'] ?? $origen?->plantas()->pluck('plantas.id') ?? [])
            ->map(fn ($id) => (int) $id)->unique()->values()->all();

        if ($clave !== CambioDocumento::TIPO_ELIMINAR && empty($plantas)) {
            throw new DomainException('Selecciona al menos una planta que pueda ver el documento.');
        }

        $ruta = null;
        if ($clave !== CambioDocumento::TIPO_ELIMINAR) {
            try {
                $ruta = $archivo?->store('solicitudes_temp', config('documentos.disk'));
            } catch (\Throwable $e) {
                report($e);
                $ruta = null;
            }

            if (!$ruta) {
                throw new DomainException('No se pudo subir el archivo al almacenamiento. Intenta de nuevo o avisa al administrador.');
            }
        }

        try {
            return DB::transaction(function () use ($usuario, $datos, $tipo, $origen, $codigo, $version, $clasificacion, $ruta, $plantas) {
                $solicitud = CambioDocumento::create($clasificacion + [
                    'documento_id' => $origen?->id,
                    'codigo_documento' => $codigo,
                    'nombre_documento' => $datos['nombre_documento'] ?? $origen->nombre_documento,
                    'version' => $version,
                    'url_documento' => $ruta,
                    'tipo_solicitud_id' => $tipo,
                    'solicitante_id' => $usuario->id,
                    'estado_id' => $this->estadoId('pendiente'),
                    'motivo_cambio' => $datos['motivo_cambio'] ?? null,
                    'descripcion_cambios' => $datos['descripcion_cambios'] ?? null,
                    'fecha_solicitud' => now(),
                ]);

                $solicitud->plantas()->sync($plantas);

                BitacoraDocumento::registrar(
                    'solicitud_creada',
                    $usuario->id,
                    $origen?->id,
                    $solicitud->id,
                    ['tipo' => $tipo, 'codigo' => $codigo, 'version' => $version, 'plantas' => $plantas]
                );

                return $solicitud;
            });
        } catch (\Throwable $e) {
            if ($ruta) {
                $this->disco()->delete($ruta);
            }
            throw $e;
        }
    }

    // ------------------------------------------------------------------
    // APROBAR
    // ------------------------------------------------------------------
    public function aprobar(int $id, User $aprobador, ?string $comentario): CambioDocumento
    {
        $movimiento = null; // para deshacer el movimiento de archivo si falla la BD

        try {
            $solicitud = DB::transaction(function () use ($id, $aprobador, $comentario, &$movimiento) {
                $solicitud = CambioDocumento::with(['estado', 'solicitante'])->lockForUpdate()->findOrFail($id);

                $this->validarResolucion($solicitud, $aprobador);

                $esEliminacion = $solicitud->esEliminacion();
                $resultante = $esEliminacion
                    ? $this->aplicarEliminacion($solicitud)
                    : $this->publicar($solicitud, $aprobador, $movimiento);

                $solicitud->update([
                    'version' => $resultante->version,
                    'url_documento' => $esEliminacion ? null : $resultante->url_documento,
                    'aprobar_id' => $aprobador->id,
                    'estado_id' => $this->estadoId('aprobado'),
                    'comentario_aprobador' => $comentario,
                    'fecha_aprobacion' => now(),
                ]);

                BitacoraDocumento::registrar(
                    'solicitud_aprobada',
                    $aprobador->id,
                    $resultante->id,
                    $solicitud->id,
                    ['tipo' => $solicitud->tipo_solicitud_id, 'codigo' => $resultante->codigo_documento, 'version' => $resultante->version]
                );

                return $solicitud;
            });
        } catch (\Throwable $e) {
            if ($movimiento) {
                try {
                    $this->disco()->move($movimiento['a'], $movimiento['de']);
                } catch (\Throwable $revertir) {
                    report($revertir);
                }
            }
            throw $e;
        }

        $this->notificarResolucion($solicitud, true);

        return $solicitud;
    }

    /** Eliminar = dar de baja (obsoleto). El archivo NO se borra: la retención lo protege. */
    private function aplicarEliminacion(CambioDocumento $solicitud): Documento
    {
        $documento = Documento::lockForUpdate()->find($solicitud->documento_id);

        if (!$documento || !$documento->vigente) {
            throw new DomainException('El documento ya no está vigente; no se puede dar de baja.');
        }

        $documento->update([
            'vigente' => false,
            'fecha_baja' => now(),
            'motivo_baja' => $solicitud->motivo_cambio,
        ]);

        return $documento;
    }

    /** Nuevo / Revisión: publica una versión nueva y deja obsoleta la anterior. */
    private function publicar(CambioDocumento $solicitud, User $aprobador, ?array &$movimiento): Documento
    {
        $codigo = $solicitud->codigo_documento;
        $versiones = Documento::where('codigo_documento', $codigo)->lockForUpdate()->get();

        if ($solicitud->esRevision()) {
            $vigente = $versiones->firstWhere('vigente', true);

            if (!$vigente || (int) $vigente->id !== (int) $solicitud->documento_id) {
                throw new DomainException('La versión que se quería revisar ya no es la vigente. Rechaza esta solicitud y crea una nueva.');
            }
        } elseif ($versiones->isNotEmpty()) {
            throw new DomainException("Ya existe un documento con el código {$codigo}.");
        }

        $version = ((int) $versiones->max('version')) + 1;

        // Exactamente este código, solo las vigentes (corrige el orWhere sin agrupar del original).
        Documento::where('codigo_documento', $codigo)
            ->where('vigente', true)
            ->update([
                'vigente' => false,
                'fecha_baja' => now(),
                'motivo_baja' => "Sustituido por la versión {$version}",
            ]);

        // Mover el archivo de temporal a su ruta definitiva
        $origen = $solicitud->url_documento;
        $disco = $this->disco();

        if (!$origen || !$disco->exists($origen)) {
            throw new DomainException('El archivo de la solicitud ya no existe en el almacenamiento.');
        }

        $extension = pathinfo($origen, PATHINFO_EXTENSION);
        $destino = "documentos/{$codigo}/v{$version}_" . Str::uuid() . ($extension ? ".{$extension}" : '');

        $disco->move($origen, $destino);
        $movimiento = ['de' => $origen, 'a' => $destino];

        $documento = Documento::create([
            'codigo_documento' => $codigo,
            'nombre_documento' => $solicitud->nombre_documento,
            'version' => $version,
            'url_documento' => $destino,
            'nivel_id' => $solicitud->nivel_id,
            'subnivel_id' => $solicitud->subnivel_id,
            'localidad_id' => $solicitud->localidad_id,
            'area_id' => $solicitud->area_id,
            'lugar_retencion_id' => $solicitud->lugar_retencion_id,
            'periodo_retencion_id' => $solicitud->periodo_retencion_id,
            'tiempo_retencion' => $solicitud->tiempo_retencion,
            'disposicion_final_id' => $solicitud->disposicion_final_id,
            'usuario_id' => $solicitud->solicitante_id,
            'aprobar_id' => $aprobador->id,
            'cambio_documento_id' => $solicitud->id,
            'vigente' => true,
            'fecha_publicacion' => now(),
            'fecha_proxima_revision' => $solicitud->fecha_proxima_revision,
        ]);

        $documento->plantas()->sync($solicitud->plantas()->pluck('plantas.id'));

        return $documento;
    }

    // ------------------------------------------------------------------
    // RECHAZAR
    // ------------------------------------------------------------------
    public function rechazar(int $id, User $aprobador, string $comentario): CambioDocumento
    {
        $rutaTemporal = null;

        $solicitud = DB::transaction(function () use ($id, $aprobador, $comentario, &$rutaTemporal) {
            $solicitud = CambioDocumento::with(['estado', 'solicitante'])->lockForUpdate()->findOrFail($id);

            $this->validarResolucion($solicitud, $aprobador);

            $rutaTemporal = $solicitud->url_documento;

            $solicitud->update([
                'aprobar_id' => $aprobador->id,
                'estado_id' => $this->estadoId('rechazado'),
                'comentario_aprobador' => $comentario,
                'fecha_aprobacion' => now(),
                'url_documento' => null,
            ]);

            BitacoraDocumento::registrar(
                'solicitud_rechazada',
                $aprobador->id,
                $solicitud->documento_id,
                $solicitud->id,
                ['codigo' => $solicitud->codigo_documento, 'motivo' => $comentario]
            );

            return $solicitud;
        });

        // El archivo se borra solo después de confirmar la transacción.
        if ($rutaTemporal) {
            try {
                $this->disco()->delete($rutaTemporal);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        $this->notificarResolucion($solicitud, false);

        return $solicitud;
    }

    /**
     * Avisa por correo al solicitante, con copia a su jefe inmediato, cómo se resolvió su
     * solicitud. Se envía después de confirmar la transacción; si el correo falla, la
     * resolución NO se revierte (solo se registra el error en el log).
     */
    private function notificarResolucion(CambioDocumento $solicitud, bool $aprobada): void
    {
        try {
            $solicitud->load(['solicitante.informacion', 'solicitante.jefeInmediato', 'aprobador.informacion', 'tipoSolicitud']);

            $activo = fn (?User $u) => $u && !$u->trashed() && filled($u->email);
            $solicitante = $solicitud->solicitante;
            $jefe = $solicitante?->jefeInmediato;

            $para = $activo($solicitante) ? $solicitante->email : null;
            $copia = $activo($jefe) && $jefe->email !== $para ? $jefe->email : null;

            if (!$para && !$copia) {
                return;
            }

            $correo = Mail::to($para ?? $copia);
            if ($para && $copia) {
                $correo->cc($copia);
            }

            $correo->send(new SolicitudResueltaMail($solicitud, $aprobada));
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function validarResolucion(CambioDocumento $solicitud, User $usuario): void
    {
        if (!$solicitud->esPendiente()) {
            throw new DomainException('Esta solicitud ya fue procesada anteriormente.');
        }

        if (!$solicitud->puedeSerResueltaPor($usuario)) {
            throw new AuthorizationException(
                'No puedes resolver esta solicitud: no puedes aprobar las tuyas y debe resolverla el jefe inmediato del solicitante o un rol aprobador.'
            );
        }
    }
}
