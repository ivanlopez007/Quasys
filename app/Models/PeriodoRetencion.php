<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PeriodoRetencion extends Model
{
    protected $table = 'periodo_retencions';
    protected $fillable = ['periodo_retencion', 'tiempo', 'activo'];

    protected $casts = [
        'tiempo' => 'integer',
        'activo' => 'boolean',
    ];

    /** Texto completo del periodo: "5 Años", "1 Año", "Permanente". */
    public static function describir(?int $tiempo, ?string $unidad): string
    {
        $unidad = trim((string) $unidad);

        if (!$tiempo || Str::lower(Str::ascii($unidad)) === 'permanente') {
            return $unidad ?: '—';
        }

        $singular = ['Años' => 'Año', 'Meses' => 'Mes', 'Semanas' => 'Semana', 'Días' => 'Día', 'Horas' => 'Hora'];

        return $tiempo . ' ' . ($tiempo === 1 ? ($singular[$unidad] ?? $unidad) : $unidad);
    }

    protected function etiqueta(): Attribute
    {
        return Attribute::get(fn () => self::describir($this->tiempo, $this->periodo_retencion));
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'periodo_retencion_id');
    }

    public function cambiosDocumento(): HasMany
    {
        return $this->hasMany(CambioDocumento::class, 'periodo_retencion_id');
    }
}
