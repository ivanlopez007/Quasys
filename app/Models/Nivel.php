<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nivel extends Model
{
    protected $table = 'nivels';
    protected $fillable = ['nivel', 'nombre', 'activo'];

    protected $casts = [
        'nivel' => 'integer',
        'activo' => 'boolean',
    ];

    public function subNiveles(): HasMany
    {
        return $this->hasMany(SubNivel::class, 'nivel_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'nivel_id');
    }

    public function cambiosDocumento(): HasMany
    {
        return $this->hasMany(CambioDocumento::class, 'nivel_id');
    }
}
