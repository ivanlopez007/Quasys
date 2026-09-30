<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodoRetencion extends Model
{
    protected $table = 'periodo_retencions';
    protected $fillable = ['periodo_retencion', 'tiempo', 'activo'];

    protected $casts = [
        'tiempo' => 'integer',
        'activo' => 'boolean',
    ];

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'periodo_retencion_id');
    }

    public function cambiosDocumento(): HasMany
    {
        return $this->hasMany(CambioDocumento::class, 'periodo_retencion_id');
    }
}
