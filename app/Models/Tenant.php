<?php
namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

// Fíjate en el "implements TenantWithDatabase" (esto resuelve el error)
class Tenant extends BaseTenant implements TenantWithDatabase
{
    // Usamos los traits necesarios para base de datos y dominios
    use HasDatabase, HasDomains;

    // Tu configuración anterior para no autoincrementar el ID
    protected $keyType = 'string';
    public $incrementing = false;

    // Permitir asignación masiva para las pruebas en Tinker
    protected $guarded = [];
}