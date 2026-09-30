<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisposicionFinal extends Model
{
    protected $table = 'disposicion_finals';
    protected $fillable = ['disposicion_final', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'disposicion_final_id');
    }

    public function cambiosDocumento(): HasMany
    {
        return $this->hasMany(CambioDocumento::class, 'disposicion_final_id');
    }
}
