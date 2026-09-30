<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubNivel extends Model
{
    protected $table = 'sub_nivels';
    protected $fillable = ['nombre', 'nivel_id', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'nivel_id');
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'subnivel_id');
    }

    public function cambiosDocumento(): HasMany
    {
        return $this->hasMany(CambioDocumento::class, 'subnivel_id');
    }
}