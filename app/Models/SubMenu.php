<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubMenu extends Model
{
    protected $table = 'sub_menus';
    protected $fillable = ['menu_id', 'sub_menu', 'url', 'icono', 'orden', 'activo'];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function accesosAsignados(): HasMany
    {
        return $this->hasMany(AccesoAsignado::class, 'sub_menu_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'acceso_asignados', 'sub_menu_id', 'rol_id')
            ->withTimestamps();
    }
}
