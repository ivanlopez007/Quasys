<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $table = 'menus';
    protected $fillable = ['menu', 'icono', 'orden', 'activo'];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    public function subMenus(): HasMany
    {
        return $this->hasMany(SubMenu::class, 'menu_id')->orderBy('orden');
    }
}
