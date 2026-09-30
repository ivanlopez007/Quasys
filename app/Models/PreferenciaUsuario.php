<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreferenciaUsuario extends Model
{
    protected $table = 'pref_usuarios';
    protected $fillable = ['usuario_id', 'tema'];

    protected $casts = [
        'tema' => 'boolean',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}