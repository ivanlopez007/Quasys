<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rol extends Model
{
    protected $table = 'rols';
    protected $fillable = ['rol', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'rol_id');
    }

    public function accesosAsignados(): HasMany
    {
        return $this->hasMany(AccesoAsignado::class, 'rol_id');
    }

    public function subMenus(): BelongsToMany
    {
        return $this->belongsToMany(SubMenu::class, 'acceso_asignados', 'rol_id', 'sub_menu_id')
            ->withTimestamps();
    }
}
