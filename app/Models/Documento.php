<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Documento extends Model
{
    protected $table = 'documentos';

    protected $fillable = [
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
        'disposicion_final_id',
        'usuario_id',
        'aprobar_id',
        'cambio_documento_id',
        'vigente',
        'fecha_publicacion',
        'fecha_proxima_revision',
        'fecha_baja',
        'motivo_baja',
    ];

    protected $casts = [
        'vigente' => 'boolean',
        'tiempo_retencion' => 'integer',
        'fecha_publicacion' => 'datetime',
        'fecha_proxima_revision' => 'date',
        'fecha_baja' => 'datetime',
    ];

    public function scopeVigentes(Builder $query): Builder
    {
        return $query->where('vigente', true);
    }

    public function scopeObsoletos(Builder $query): Builder
    {
        return $query->where('vigente', false);
    }

    /** Solo los documentos asignados a la planta del usuario (sin planta no ve ninguno). */
    public function scopeVisiblesPara(Builder $query, User $usuario): Builder
    {
        if (!$usuario->planta_id) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('plantas', fn ($q) => $q->whereKey($usuario->planta_id));
    }

    public function esVisiblePara(User $usuario): bool
    {
        return $usuario->planta_id
            && $this->plantas()->whereKey($usuario->planta_id)->exists();
    }

    /**
     * Fin de la retención. Corre desde que la versión deja de ser vigente (fecha_baja):
     * tiempo_retencion es la cantidad copiada del catálogo al solicitar; la unidad sale del periodo.
     * Devuelve null si sigue vigente, si no tiene retención definida o si es permanente.
     */
    protected function fechaFinRetencion(): Attribute
    {
        return Attribute::get(function (): ?Carbon {
            if ($this->vigente || !$this->fecha_baja || !$this->tiempo_retencion || !$this->periodoRetencion) {
                return null;
            }

            $cantidad = (int) $this->tiempo_retencion;
            $inicio = $this->fecha_baja->copy();

            return match (Str::lower(Str::ascii($this->periodoRetencion->periodo_retencion))) {
                'anos', 'ano' => $inicio->addYears($cantidad),
                'meses', 'mes' => $inicio->addMonths($cantidad),
                'semanas', 'semana' => $inicio->addWeeks($cantidad),
                'dias', 'dia' => $inicio->addDays($cantidad),
                'horas', 'hora' => $inicio->addHours($cantidad),
                default => null, // Permanente u otra unidad no reconocida: no vence
            };
        });
    }

    public function retencionVencida(): bool
    {
        return $this->fecha_fin_retencion?->isPast() ?? false;
    }

    // Obtener todas las versiones del mismo documento por su código
    public function historialVersiones(): HasMany
    {
        return $this->hasMany(Documento::class, 'codigo_documento', 'codigo_documento')
            ->orderBy('version', 'desc');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id')->withTrashed();
    }

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobar_id')->withTrashed();
    }

    public function cambioOrigen(): BelongsTo
    {
        return $this->belongsTo(CambioDocumento::class, 'cambio_documento_id');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'nivel_id');
    }

    public function subnivel(): BelongsTo
    {
        return $this->belongsTo(SubNivel::class, 'subnivel_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function localidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class, 'localidad_id');
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

    public function cambios(): HasMany
    {
        return $this->hasMany(CambioDocumento::class, 'documento_id');
    }

    /** Plantas cuyos usuarios pueden ver esta versión. */
    public function plantas(): BelongsToMany
    {
        return $this->belongsToMany(Planta::class, 'documento_planta', 'documento_id', 'planta_id')->withTimestamps();
    }

    public function bitacora(): HasMany
    {
        return $this->hasMany(BitacoraDocumento::class, 'documento_id');
    }
}
