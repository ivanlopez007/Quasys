<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $table = 'areas';
    protected $fillable = ['area', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function cursos(): HasMany
    {
        return $this->hasMany(Curso::class, 'area_id');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'area_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'area_id');
    }

    public function cambiosDocumento(): HasMany
    {
        return $this->hasMany(CambioDocumento::class, 'area_id');
    }
}
