<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CursoAsignado extends Model
{
    protected $table = 'curso_asignados';

    protected $fillable = [
        'usuario_id',
        'curso_id',
        'completado',
        'calificacion',
        'fecha_completado',
    ];

    protected $casts = [
        'completado' => 'boolean',
        'calificacion' => 'decimal:2',
        'fecha_completado' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }
}
