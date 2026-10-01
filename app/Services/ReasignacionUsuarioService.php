<?php

namespace App\Services;

use App\Models\BitacoraDocumento;
use App\Models\CambioDocumento;
use App\Models\Documento;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Transfiere a otro usuario lo que queda "a cargo" de un usuario que se da de baja.
 *
 * - Documentos VIGENTES: cambian de responsable (usuario_id). Las versiones obsoletas
 *   conservan a su autor original para no alterar la trazabilidad histórica.
 * - Solicitudes PENDIENTES: pasan al nuevo usuario para que les dé seguimiento.
 * - Subordinados: su jefe inmediato pasa a ser el nuevo usuario.
 *
 * Cada documento y solicitud reasignados quedan registrados en la bitácora.
 */
class ReasignacionUsuarioService
{
    /** Lo que el usuario tiene a su cargo actualmente. */
    public function pendientesDe(User $usuario): array
    {
        return [
            'documentos' => Documento::vigentes()->where('usuario_id', $usuario->id)->count(),
            'solicitudes' => CambioDocumento::pendientes()->where('solicitante_id', $usuario->id)->count(),
            'subordinados' => User::where('jefe_inmediato_id', $usuario->id)->count(),
        ];
    }

    /**
     * Reasigna documentos vigentes y solicitudes pendientes puntuales que quedaron sin
     * responsable (su usuario está dado de baja). Lo que ya tiene un responsable activo,
     * o un documento que el destino no podría ver por su planta, se omite y se informa.
     *
     * @return array{documentos: int, solicitudes: int, omitidos: array<int, string>}
     */
    public function reasignarSinResponsable(array $documentoIds, array $solicitudIds, User $destino, User $actor): array
    {
        if ($destino->trashed()) {
            throw new DomainException('No se puede reasignar a un usuario eliminado.');
        }

        return DB::transaction(function () use ($documentoIds, $solicitudIds, $destino, $actor) {
            $omitidos = [];
            $sinResponsable = fn ($q) => $q->onlyTrashed();

            $documentos = Documento::vigentes()
                ->whereIn('id', $documentoIds)
                ->whereHas('autor', $sinResponsable)
                ->with('plantas:id')
                ->lockForUpdate()
                ->get();

            $asignados = 0;
            foreach ($documentos as $documento) {
                if (!$destino->planta_id || !$documento->plantas->contains('id', $destino->planta_id)) {
                    $omitidos[] = "{$documento->codigo_documento} (no está asignado a la planta del nuevo responsable)";
                    continue;
                }

                BitacoraDocumento::registrar('documento_reasignado', $actor->id, $documento->id, null, [
                    'de_usuario_id' => $documento->usuario_id,
                    'a_usuario_id' => $destino->id,
                    'codigo' => $documento->codigo_documento,
                    'version' => $documento->version,
                ]);
                $documento->update(['usuario_id' => $destino->id]);
                $asignados++;
            }

            $solicitudes = CambioDocumento::pendientes()
                ->whereIn('id', $solicitudIds)
                ->whereHas('solicitante', $sinResponsable)
                ->lockForUpdate()
                ->get();

            foreach ($solicitudes as $solicitud) {
                BitacoraDocumento::registrar('solicitud_reasignada', $actor->id, $solicitud->documento_id, $solicitud->id, [
                    'de_usuario_id' => $solicitud->solicitante_id,
                    'a_usuario_id' => $destino->id,
                    'codigo' => $solicitud->codigo_documento,
                ]);
                $solicitud->update(['solicitante_id' => $destino->id]);
            }

            return ['documentos' => $asignados, 'solicitudes' => $solicitudes->count(), 'omitidos' => $omitidos];
        });
    }

    public function reasignar(User $origen, User $destino, User $actor): array
    {
        if ((int) $origen->id === (int) $destino->id) {
            throw new DomainException('El usuario destino debe ser distinto al usuario origen.');
        }
        if ($destino->trashed()) {
            throw new DomainException('No se puede reasignar a un usuario eliminado.');
        }

        return DB::transaction(function () use ($origen, $destino, $actor) {
            $detalle = ['de_usuario_id' => $origen->id, 'a_usuario_id' => $destino->id];

            // Documentos vigentes
            $documentos = Documento::vigentes()
                ->where('usuario_id', $origen->id)
                ->lockForUpdate()
                ->get(['id', 'codigo_documento', 'version']);

            Documento::whereIn('id', $documentos->pluck('id'))->update(['usuario_id' => $destino->id]);

            foreach ($documentos as $documento) {
                BitacoraDocumento::registrar('documento_reasignado', $actor->id, $documento->id, null, $detalle + [
                    'codigo' => $documento->codigo_documento,
                    'version' => $documento->version,
                ]);
            }

            // Solicitudes pendientes
            $solicitudes = CambioDocumento::pendientes()
                ->where('solicitante_id', $origen->id)
                ->lockForUpdate()
                ->get(['id', 'documento_id', 'codigo_documento']);

            CambioDocumento::whereIn('id', $solicitudes->pluck('id'))->update(['solicitante_id' => $destino->id]);

            foreach ($solicitudes as $solicitud) {
                BitacoraDocumento::registrar('solicitud_reasignada', $actor->id, $solicitud->documento_id, $solicitud->id, $detalle + [
                    'codigo' => $solicitud->codigo_documento,
                ]);
            }

            // Subordinados. Si el destino era subordinado del origen, hereda el jefe del origen
            // (no puede ser su propio jefe).
            $subordinados = User::where('jefe_inmediato_id', $origen->id)->where('id', '!=', $destino->id)
                ->update(['jefe_inmediato_id' => $destino->id]);

            User::whereKey($destino->id)->where('jefe_inmediato_id', $origen->id)
                ->update(['jefe_inmediato_id' => $origen->jefe_inmediato_id]);

            return [
                'documentos' => $documentos->count(),
                'solicitudes' => $solicitudes->count(),
                'subordinados' => $subordinados,
            ];
        });
    }
}
