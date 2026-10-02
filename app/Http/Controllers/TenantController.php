<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\AprovisionamientoEmpresaService as Aprovisionamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    /** Subdominios que no se pueden usar como empresa. */
    private const RESERVADOS = ['www', 'admin', 'api', 'app', 'mail', 'central', 'soporte', 'static', 'test'];

    private const MENSAJES_SUBDOMINIO = [
        'subdominio.required' => 'Escribe un subdominio.',
        'subdominio.min' => 'El subdominio debe tener al menos 2 caracteres.',
        'subdominio.max' => 'El subdominio puede tener máximo 30 caracteres.',
        'subdominio.regex' => 'Solo letras minúsculas, números y guiones (sin empezar ni terminar en guion).',
        'subdominio.unique' => 'Ese subdominio ya está en uso por otra empresa.',
        'subdominio.not_in' => 'Ese subdominio está reservado por el sistema.',
    ];

    public function __construct(private Aprovisionamiento $aprovisionamiento) {}

    public function index()
    {
        $empresas = Tenant::with('domains')->get()
            ->map(function (Tenant $tenant) {
                $dominio = $tenant->domains->first()?->domain;

                return (object) [
                    'id' => $tenant->id,
                    'nombre' => $tenant->name ?? $tenant->id,
                    'razon_social' => $tenant->razon_social,
                    'plan' => $tenant->plan,
                    'dominio' => $dominio,
                    'url' => $dominio ? Aprovisionamiento::urlDe($dominio) : null,
                    'base_datos' => $tenant->database()->getName(),
                    'creada' => $tenant->created_at,
                    ...$this->estadisticas($tenant),
                ];
            })
            ->sortByDesc('creada')
            ->values();

        return view('central.empresas.index', [
            'empresas' => $empresas,
            'totales' => [
                'empresas' => $empresas->count(),
                'usuarios' => $empresas->sum('usuarios'),
                'documentos' => $empresas->sum('documentos'),
            ],
        ]);
    }

    public function formCrearEmpresa()
    {
        return view('central.empresas.crear', [
            'dominioCentral' => Aprovisionamiento::dominioCentral(),
            'urlEjemplo' => Aprovisionamiento::urlDe('__SUB__.' . Aprovisionamiento::dominioCentral()),
            'planes' => self::planes(),
        ]);
    }

    public function crearEmpresa(Request $request)
    {
        $request->merge(['subdominio' => strtolower(trim((string) $request->input('subdominio')))]);

        $datos = $request->validate([
            'nombre_empresa' => ['required', 'string', 'max:150'],
            'razon_social' => ['nullable', 'string', 'max:200'],
            'rfc' => ['nullable', 'string', 'max:13'],
            'subdominio' => $this->reglasSubdominio(),
            'plan' => ['required', Rule::in(array_keys(self::planes()))],
            'admin_nombre' => ['required', 'string', 'max:100'],
            'admin_apellidos' => ['required', 'string', 'max:100'],
            'email_admin' => ['required', 'email', 'max:255'],
            'password_admin' => ['required', 'string', 'min:8', 'confirmed'],
        ], self::MENSAJES_SUBDOMINIO + [
            'password_admin.confirmed' => 'La confirmación de la contraseña no coincide.',
        ], [
            'nombre_empresa' => 'nombre comercial',
            'admin_nombre' => 'nombre del administrador',
            'admin_apellidos' => 'apellidos del administrador',
            'email_admin' => 'correo del administrador',
            'password_admin' => 'contraseña',
        ]);

        try {
            $tenant = $this->aprovisionamiento->crear($datos);
        } catch (\Throwable $e) {
            return back()->withInput($request->except('password_admin', 'password_admin_confirmation'))
                ->with('error', 'No se pudo crear la empresa: ' . $e->getMessage());
        }

        $dominio = $tenant->domains()->value('domain');

        return redirect()->route('central.empresas.index')
            ->with('creada', [
                'nombre' => $tenant->name,
                'url' => Aprovisionamiento::urlDe($dominio) . '/login',
                'email' => $datos['email_admin'],
            ]);
    }

    /** Comprobación en vivo desde el formulario. */
    public function disponible(Request $request)
    {
        $subdominio = strtolower(trim((string) $request->query('subdominio')));
        $validador = validator(['subdominio' => $subdominio], ['subdominio' => $this->reglasSubdominio()], self::MENSAJES_SUBDOMINIO);

        return response()->json([
            'disponible' => $validador->passes(),
            'mensaje' => $validador->passes() ? 'Disponible' : $validador->errors()->first('subdominio'),
        ]);
    }

    private function reglasSubdominio(): array
    {
        return [
            'required', 'string', 'min:2', 'max:30',
            'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/',
            Rule::notIn(self::RESERVADOS),
            Rule::unique('tenants', 'id'),
            function ($atributo, $valor, $falla) {
                if (DB::table('domains')->where('domain', $valor . '.' . Aprovisionamiento::dominioCentral())->exists()) {
                    $falla('Ese dominio ya está registrado.');
                }
            },
        ];
    }

    /** Usuarios y documentos de cada empresa; si su base no responde, se marca sin romper la lista. */
    private function estadisticas(Tenant $tenant): array
    {
        try {
            return $tenant->run(fn () => [
                'usuarios' => DB::table('users')->whereNull('deleted_at')->count(),
                'documentos' => DB::table('documentos')->where('vigente', true)->count(),
                'ok' => true,
            ]);
        } catch (\Throwable $e) {
            if (tenancy()->initialized) {
                tenancy()->end();
            }

            return ['usuarios' => 0, 'documentos' => 0, 'ok' => false];
        }
    }

    public static function planes(): array
    {
        return [
            'basico' => ['Básico', 'Hasta 25 usuarios', 'fa-seedling'],
            'pro' => ['Profesional', 'Usuarios ilimitados', 'fa-rocket'],
            'empresarial' => ['Empresarial', 'Varias plantas y soporte dedicado', 'fa-building'],
        ];
    }
}
