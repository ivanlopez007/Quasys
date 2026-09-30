<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoSolicitud extends Model
{
    protected $table = 'tipo_solicituds';
    protected $fillable = ['tipo_solicitud', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function cambiosDocumento(): HasMany
    {
        return $this->hasMany(CambioDocumento::class, 'tipo_solicitud_id');
    }
}