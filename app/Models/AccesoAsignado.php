<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccesoAsignado extends Model
{
    protected $table = 'acceso_asignados';

    protected $fillable = [
        'rol_id',
        'sub_menu_id',
    ];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function subMenu(): BelongsTo
    {
        return $this->belongsTo(SubMenu::class, 'sub_menu_id');
    }
}
