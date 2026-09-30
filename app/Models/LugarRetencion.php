<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LugarRetencion extends Model
{
    protected $table = 'lugar_retencions';
    protected $fillable = ['lugar_retencion', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'lugar_retencion_id');
    }

    public function cambiosDocumento(): HasMany
    {
        return $this->hasMany(CambioDocumento::class, 'lugar_retencion_id');
    }
}