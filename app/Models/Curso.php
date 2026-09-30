<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    protected $table = 'cursos';
    protected $fillable = ['titulo', 'descripcion', 'instructor_id', 'status', 'area_id'];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function asignaciones(): HasMany
    {
        return $this->hasMany(CursoAsignado::class, 'curso_id');
    }

    public function usuariosAsignados(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'curso_asignados', 'curso_id', 'usuario_id')
            ->withPivot('completado', 'calificacion', 'fecha_completado')
            ->withTimestamps();
    }
}
