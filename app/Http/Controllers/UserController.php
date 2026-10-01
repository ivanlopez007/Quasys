<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Localidad;
use App\Models\Planta;
use App\Models\Rol;
use App\Models\User;
use App\Services\ReasignacionUsuarioService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct(private ReasignacionUsuarioService $reasignacion) {}

    /** ?ver=eliminados muestra los usuarios dados de baja (soft delete). */
    public function index(Request $request)
    {
        $verEliminados = $request->query('ver') === 'eliminados';

        $usuarios = User::with(['informacion', 'rol', 'jefeInmediato.informacion', 'localidad', 'planta', 'area'])
            ->withCount([
                'documentosElaborados as documentos_vigentes_count' => fn($q) => $q->where('vigente', true),
                'solicitudesDeCambio as solicitudes_pendientes_count' => fn($q) => $q->pendientes(),
                'subordinados',
            ])
            ->when($verEliminados, fn($q) => $q->onlyTrashed()->latest('deleted_at'), fn($q) => $q->latest())
            ->get();

        $totalActivos = User::count();
        $totalEliminados = User::onlyTrashed()->count();

        $roles = Rol::orderBy('rol', 'asc')->get();
        $jefes = User::with('informacion')->get(); // solo activos: también son los posibles destinos de reasignación
        $localidades = Localidad::all();
        $plantas = Planta::all();
        $areas = Area::all();

        return view('usuarios.index', compact(
            'usuarios', 'roles', 'jefes', 'localidades', 'plantas', 'areas',
            'verEliminados', 'totalActivos', 'totalEliminados'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email'             => 'required|email|max:255|unique:users,email',
            'password'          => 'required|string|min:8',
            'rol_id'            => 'nullable|exists:rols,id',
            'jefe_inmediato_id' => ['nullable', Rule::exists('users', 'id')->whereNull('deleted_at')],
            'localidad_id'      => 'nullable|exists:localidads,id',
            'planta_id'         => 'nullable|exists:plantas,id',
            'area_id'           => 'nullable|exists:areas,id',
            'status'            => 'sometimes|boolean',

            'nombre'           => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'rfc'              => 'nullable|string|max:13|unique:informacion_usuarios,rfc',
            'curp'             => 'nullable|string|max:18|unique:informacion_usuarios,curp',
            'fecha_nacimiento' => 'nullable|date',
        ], [
            'email.unique' => 'Este correo ya está registrado. Si pertenece a un usuario eliminado, restáuralo desde la pestaña Eliminados.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $user = User::create([
                'email'             => $validated['email'],
                'password'          => $validated['password'],
                'rol_id'            => $validated['rol_id'] ?? null,
                'jefe_inmediato_id' => $validated['jefe_inmediato_id'] ?? null,
                'localidad_id'      => $validated['localidad_id'] ?? null,
                'planta_id'         => $validated['planta_id'] ?? null,
                'area_id'           => $validated['area_id'] ?? null,
            ]);

            $user->informacion()->create([
                'nombre'           => $validated['nombre'],
                'apellidos'        => $validated['apellidos'],
                'rfc'              => $validated['rfc'] ?? null,
                'curp'             => $validated['curp'] ?? null,
                'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
                'status'           => $request->boolean('status'),
            ]);
        });

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario registrado exitosamente.');
    }

    public function update(Request $request, User $usuario)
    {
        $info = $usuario->informacion;

        $validated = $request->validate([
            'email'             => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password'          => 'nullable|string|min:8',
            'rol_id'            => 'nullable|exists:rols,id',
            'jefe_inmediato_id' => ['nullable', Rule::exists('users', 'id')->whereNull('deleted_at'), Rule::notIn([$usuario->id])],
            'localidad_id'      => 'nullable|exists:localidads,id',
            'planta_id'         => 'nullable|exists:plantas,id',
            'area_id'           => 'nullable|exists:areas,id',
            'status'            => 'sometimes|boolean',

            'nombre'           => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'rfc'              => ['nullable', 'string', 'max:13', Rule::unique('informacion_usuarios', 'rfc')->ignore($info?->id)],
            'curp'             => ['nullable', 'string', 'max:18', Rule::unique('informacion_usuarios', 'curp')->ignore($info?->id)],
            'fecha_nacimiento' => 'nullable|date',
        ]);

        DB::transaction(function () use ($usuario, $validated, $request) {
            $userData = [
                'email'             => $validated['email'],
                'rol_id'            => $validated['rol_id'] ?? null,
                'jefe_inmediato_id' => $validated['jefe_inmediato_id'] ?? null,
                'localidad_id'      => $validated['localidad_id'] ?? null,
                'planta_id'         => $validated['planta_id'] ?? null,
                'area_id'           => $validated['area_id'] ?? null,
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = $validated['password'];
            }

            $usuario->update($userData);

            $usuario->informacion()->updateOrCreate(
                ['usuario_id' => $usuario->id],
                [
                    'nombre'           => $validated['nombre'],
                    'apellidos'        => $validated['apellidos'],
                    'rfc'              => $validated['rfc'] ?? null,
                    'curp'             => $validated['curp'] ?? null,
                    'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
                    'status'           => $request->boolean('status'),
                ]
            );
        });

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Baja lógica (soft delete): el usuario se puede restaurar después.
     * Opcionalmente transfiere en el mismo paso lo que tenía a su cargo.
     */
    public function destroy(Request $request, User $usuario)
    {
        if ((int) $usuario->id === (int) Auth::id()) {
            return back()->withErrors(['usuario' => 'No puedes eliminar tu propia cuenta.']);
        }

        $validated = $request->validate([
            'reasignar_a_id' => ['nullable', Rule::exists('users', 'id')->whereNull('deleted_at'), Rule::notIn([$usuario->id])],
        ], [
            'reasignar_a_id.exists' => 'El usuario destino no existe o está eliminado.',
            'reasignar_a_id.not_in' => 'No puedes reasignar al mismo usuario que eliminas.',
        ]);

        $resultado = DB::transaction(function () use ($usuario, $validated) {
            $resultado = !empty($validated['reasignar_a_id'])
                ? $this->reasignacion->reasignar($usuario, User::findOrFail($validated['reasignar_a_id']), Auth::user())
                : null;

            $usuario->delete();

            return $resultado;
        });

        $redirect = redirect()->route('usuarios.index')->with('success',
            'Usuario dado de baja. Puedes restaurarlo desde la pestaña Eliminados.' . ($resultado ? ' ' . $this->resumen($resultado) : ''));

        $restante = $this->reasignacion->pendientesDe($usuario);
        if (array_sum($restante) > 0) {
            $redirect->with('warning', "Quedaron sin reasignar: {$restante['documentos']} documento(s) vigente(s), "
                . "{$restante['solicitudes']} solicitud(es) pendiente(s) y {$restante['subordinados']} subordinado(s). "
                . 'Puedes reasignarlos desde la pestaña Eliminados.');
        }

        return $redirect;
    }

    public function restore(int $id)
    {
        $usuario = User::onlyTrashed()->findOrFail($id);
        $usuario->restore();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario restaurado: ' . ($usuario->informacion?->nombre ?? $usuario->email) . ' puede volver a iniciar sesión.');
    }

    /** Transfiere documentos, solicitudes y subordinados de un usuario (activo o eliminado) a otro activo. */
    public function reasignar(Request $request, int $id)
    {
        $origen = User::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'reasignar_a_id' => ['required', Rule::exists('users', 'id')->whereNull('deleted_at'), Rule::notIn([$origen->id])],
        ], [
            'reasignar_a_id.required' => 'Selecciona el usuario que recibirá los documentos.',
            'reasignar_a_id.exists' => 'El usuario destino no existe o está eliminado.',
            'reasignar_a_id.not_in' => 'El usuario destino debe ser distinto.',
        ]);

        try {
            $resultado = $this->reasignacion->reasignar($origen, User::findOrFail($validated['reasignar_a_id']), Auth::user());
        } catch (DomainException $e) {
            return back()->withErrors(['reasignar_a_id' => $e->getMessage()]);
        }

        return back()->with('success', 'Reasignación completada. ' . $this->resumen($resultado));
    }

    private function resumen(array $r): string
    {
        return "Se reasignaron {$r['documentos']} documento(s), {$r['solicitudes']} solicitud(es) pendiente(s) y {$r['subordinados']} subordinado(s).";
    }
}
