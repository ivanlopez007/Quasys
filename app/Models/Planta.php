<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Planta extends Model
{
    use SoftDeletes;

    protected $table = 'plantas';
    protected $fillable = ['localidad_id', 'planta', 'ubicacion'];

    public function localidad(): BelongsTo
    {
        return $this->belongsTo(Localidad::class, 'localidad_id');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'planta_id');
    }

    public function documentos(): BelongsToMany
    {
        return $this->belongsToMany(Documento::class, 'documento_planta', 'planta_id', 'documento_id')->withTimestamps();
    }
}
