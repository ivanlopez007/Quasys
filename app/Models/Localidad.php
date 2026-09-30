<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Localidad extends Model
{
    protected $table = 'localidads';
    protected $fillable = ['localidad', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function plantas(): HasMany
    {
        return $this->hasMany(Planta::class, 'localidad_id');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'localidad_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'localidad_id');
    }

    public function cambiosDocumento(): HasMany
    {
        return $this->hasMany(CambioDocumento::class, 'localidad_id');
    }
}