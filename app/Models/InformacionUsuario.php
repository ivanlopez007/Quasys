<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InformacionUsuario extends Model
{
    protected $table = 'informacion_usuarios';
    protected $fillable = ['usuario_id', 'nombre', 'apellidos', 'rfc', 'curp', 'fecha_nacimiento', 'status'];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'status' => 'boolean',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}