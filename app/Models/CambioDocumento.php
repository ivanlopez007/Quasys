<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class CambioDocumento extends Model
{
    // Claves de config('documentos.tipos'). El ID real se busca por nombre en tipo_solicituds.
    public const TIPO_NUEVO = 'nuevo';
    public const TIPO_REVISION = 'revision';
    public const TIPO_ELIMINAR = 'eliminar';

    /** @var array<string, array<string, ?int>> clave => id, por tenant */
    private static array $tiposCache = [];

    /** Mapa clave => id del catálogo, resuelto por nombre (sin importar mayúsculas ni acentos). */
    public static function tiposIds(): array
    {
        $tenant = (string) (tenant('id') ?? 'central');

        return self::$tiposCache[$tenant] ??= (function () {
            $normalizar = fn ($texto) => Str::lower(Str::ascii(trim((string) $texto)));

            $idsPorNombre = TipoSolicitud::pluck('id', 'tipo_solicitud')
                ->mapWithKeys(fn ($id, $nombre) => [$normalizar($nombre) => (int) $id]);

            return collect(config('documentos.tipos'))
                ->map(fn ($nombre) => $idsPorNombre[$normalizar($nombre)] ?? null)
                ->all();
        })();
    }

    public static function tipoId(string $clave): ?int
    {
        return self::tiposIds()[$clave] ?? null;
    }

    /** Clave (nuevo / revision / eliminar) de un ID del catálogo, o null si no corresponde a ninguno. */
    public static function claveDeTipo(?int $id): ?string
    {
        $clave = $id ? array_search($id, self::tiposIds(), true) : false;

        return $clave === false ? null : $clave;
    }

    public function tipoClave(): ?string
    {
        return self::claveDeTipo((int) $this->tipo_solicitud_id);
    }

    protected $table = 'cambio_documentos';

    protected $fillable = [
        'documento_id',
        'codigo_documento',
        'nombre_documento',
        'version',
        'url_documento',
        'nivel_id',
        'subnivel_id',
        'localidad_id',
        'area_id',
        'lugar_retencion_id',
        'periodo_retencion_id',
        'tiempo_retencion',
        'fecha_proxima_revision',
        'disposicion_final_id',
        'tipo_solicitud_id',
        'solicitante_id',
        'aprobar_id',
        'estado_id',
        'motivo_cambio',
        'descripcion_cambios',
        'comentario_aprobador',
        'fecha_solicitud',
        'fecha_aprobacion',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'fecha_proxima_revision' => 'date',
    ];

    public function scopePendientes(Builder $query): Builder
    {
        return $query->whereHas('estado', fn ($q) => $q->where('estado_solicitud', config('documentos.estados.pendiente')));
    }

    public function esPendiente(): bool
    {
        return $this->estado?->estado_solicitud === config('documentos.estados.pendiente');
    }

    public function esNuevo(): bool
    {
        return $this->tipoClave() === self::TIPO_NUEVO;
    }

    public function esRevision(): bool
    {
        return $this->tipoClave() === self::TIPO_REVISION;
    }

    public function esEliminacion(): bool
    {
        return $this->tipoClave() === self::TIPO_ELIMINAR;
    }

    /**
     * Separación de funciones (ISO 9001): nadie resuelve su propia solicitud.
     * Puede resolverla el jefe inmediato del solicitante o un rol aprobador.
     */
    public function puedeSerResueltaPor(User $usuario): bool
    {
        if ((int) $this->solicitante_id === (int) $usuario->id) {
            return false;
        }

        if ((int) $this->solicitante?->jefe_inmediato_id === (int) $usuario->id) {
            return true;
        }

        return self::usuarioEsAprobador($usuario);
    }

    /**
     * Los roles son dinámicos: un usuario es aprobador si su rol tiene asignado
     * el sub menú definido en config('documentos.submenu_aprobador_url').
     */
    public static function usuarioEsAprobador(User $usuario): bool
    {
        return $usuario->tieneAcceso(config('documentos.submenu_aprobador_url'));
    }

    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitante_id')->withTrashed();
    }

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobar_id')->withTrashed();
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoSolicitud::class, 'estado_id');
    }

    public function tipoSolicitud(): BelongsTo
    {
        return $this->belongsTo(TipoSolicitud::class, 'tipo_solicitud_id');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'nivel_id');
    }

    public function subnivel(): BelongsTo
    {
        return $this->belongsTo(SubNivel::class, 'subnivel_id');
    }

    public function localidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class, 'localidad_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function lugarRetencion(): BelongsTo
    {
        return $this->belongsTo(LugarRetencion::class, 'lugar_retencion_id');
    }

    public function periodoRetencion(): BelongsTo
    {
        return $this->belongsTo(PeriodoRetencion::class, 'periodo_retencion_id');
    }

    public function disposicionFinal(): BelongsTo
    {
        return $this->belongsTo(DisposicionFinal::class, 'disposicion_final_id');
    }

    /** Plantas que podrán ver el documento cuando se apruebe la solicitud. */
    public function plantas(): BelongsToMany
    {
        return $this->belongsToMany(Planta::class, 'cambio_documento_planta', 'cambio_documento_id', 'planta_id')->withTimestamps();
    }

    public function documentoResultante(): HasOne
    {
        return $this->hasOne(Documento::class, 'cambio_documento_id');
    }
}
