<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Da de alta una empresa (tenant): base de datos propia con sus migraciones, dominio,
 * datos iniciales (config/aprovisionamiento.php) y el usuario administrador.
 * Si cualquier paso falla se elimina la empresa (y su base de datos) para no dejarla a medias.
 */
class AprovisionamientoEmpresaService
{
    public function __construct(private PreparacionEmpresaService $preparacion) {}

    public function crear(array $datos): Tenant
    {
        $id = $datos['subdominio'];

        // Crea el registro y, por el evento TenantCreated, la base de datos y sus migraciones.
        $tenant = Tenant::create([
            'id' => $id,
            'name' => $datos['nombre_empresa'],
            'razon_social' => $datos['razon_social'] ?? null,
            'rfc' => $datos['rfc'] ?? null,
            'plan' => $datos['plan'],
            'admin_email' => $datos['email_admin'],
        ]);

        try {
            $tenant->domains()->create(['domain' => $id . '.' . self::dominioCentral()]);

            $tenant->run(function () use ($datos) {
                $this->preparacion->preparar();
                DB::transaction(fn () => $this->crearAdministrador($datos));
            });
        } catch (\Throwable $e) {
            report($e);
            if (tenancy()->initialized) {
                tenancy()->end(); // run() no lo termina si la función lanzó una excepción
            }
            $tenant->delete(); // también elimina su base de datos (evento TenantDeleted)
            throw $e;
        }

        return $tenant;
    }

    private function crearAdministrador(array $datos): void
    {
        $admin = User::create([
            'email' => $datos['email_admin'],
            'password' => $datos['password_admin'], // el modelo la guarda con hash
            'rol_id' => DB::table('rols')->where('rol', PreparacionEmpresaService::rolAdministrador())->value('id'),
            'planta_id' => DB::table('plantas')->whereNull('deleted_at')->orderBy('id')->value('id'),
            'localidad_id' => DB::table('localidads')->orderBy('id')->value('id'),
        ]);

        $admin->informacion()->create([
            'nombre' => $datos['admin_nombre'],
            'apellidos' => $datos['admin_apellidos'],
            'status' => true,
        ]);
    }

    public static function dominioCentral(): string
    {
        return config('tenancy.central_domains.0', 'quasys.test');
    }

    /** URL de acceso de una empresa, conservando el puerto actual (p. ej. :8000 en desarrollo). */
    public static function urlDe(string $dominio): string
    {
        $request = request();
        $puerto = in_array($request->getPort(), [80, 443], true) ? '' : ':' . $request->getPort();

        return $request->getScheme() . '://' . $dominio . $puerto;
    }
}
