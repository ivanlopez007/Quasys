<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoSolicitud extends Model
{
    protected $table = 'estado_solicituds';
    protected $fillable = ['estado_solicitud', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function cambiosDocumento(): HasMany
    {
        return $this->hasMany(CambioDocumento::class, 'estado_id');
    }
}
