<?php

namespace App\Http\Controllers;

use App\Models\Accion;
use App\Models\EstadoCivil;
use App\Models\MotivoAntidoping;
use App\Models\Planta;
use App\Models\Rol;
use App\Models\Tenant;
use App\Models\TipoContrato;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantController extends Controller
{

    public function formCrearEmpresa()
    {
        return view('central.crear-empresa');
    }


    public function crearEmpresa(Request $request)
    {
        // // 1. Validar la petición recibida desde el formulario Blade
        // $request->validate([
        //     'nombre_empresa' => 'required|string|max:255',
        //     'subdominio'     => 'required|string|max:255|unique:tenants,id',
        //     'email_admin'    => 'required|email|max:255',
        //     'password_admin' => 'required|string|min:8|confirmed',
        //     'plan'           => 'required|string',
        //     'admin_name'     => 'nullable|string|max:255',
        // ], [
        //     'nombre_empresa.required'  => 'El nombre de la empresa es obligatorio.',
        //     'subdominio.required'      => 'El subdominio es obligatorio.',
        //     'subdominio.unique'        => 'Este subdominio ya está registrado.',
        //     'email_admin.required'     => 'El correo electrónico del administrador es obligatorio.',
        //     'email_admin.email'        => 'El correo electrónico debe ser una dirección válida.',
        //     'password_admin.required'  => 'La contraseña del administrador es obligatoria.',
        //     'password_admin.min'       => 'La contraseña debe tener al menos 8 caracteres.',
        //     'password_admin.confirmed' => 'La confirmación de la contraseña no coincide.',
        // ]);

        try {
            $subdomain = $request->input('subdominio');
            // El dominio completo (ej: transportes.gateops.com)
            $fullDomain = $subdomain . '.' . config('tenancy.central_domains.0', 'gateops.com');

            // Crear tenant
            $tenant = Tenant::create([
                'id'   => $subdomain,
                'plan' => $request->input('plan'),
                'name' => $request->input('nombre_empresa'),
            ]);

            $tenant->domains()->create([
                'domain' => $fullDomain,
            ]);

            tenancy()->initialize($tenant);

            try {

                $rolAdmin = Rol::firstOrCreate([
                    'rol' => 'Admin'
                ]);

                $plantaInicial = Planta::firstOrCreate([
                    'planta' => 'Planta Principal',
                    'ubicacion' => 'General'
                ]);

                User::create([
                    'name'      => $request->input('admin_name', 'Administrador'),
                    'email'     => $request->input('email_admin'),
                    'password'  => Hash::make($request->input('password_admin')),
                    'rol_id'    => $rolAdmin->id,
                    'planta_id' => $plantaInicial->id,
                ]);
            } catch (\Throwable $e) {

                logger()->error($e->getMessage());
            }

            tenancy()->end();

            return redirect()->route('central.empresas')->with('success', '¡Empresa, Base de Datos, Catálogos y Administrador creados con éxito!');
        } catch (\Exception $e) {
            // En caso de fallo, asegura limpiar el contexto activo del Tenant
            if (tenancy()->initialized) {
                tenancy()->end();
            }

            return redirect()->back()->withInput()->withErrors(['error' => 'Error en la creación del Tenant: ' . $e->getMessage()]);
        }
    }
}
