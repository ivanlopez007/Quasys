<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Localidad;
use App\Models\Planta;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with(['informacion', 'rol', 'jefeInmediato.informacion', 'localidad', 'planta', 'area'])
            ->latest()
            ->get();

        $roles = Rol::orderBy('rol', 'asc')->get();
        $jefes = User::with('informacion')->get();
        $localidades = Localidad::all();
        $plantas = Planta::all();
        $areas = Area::all();

        return view('usuarios.index', compact('usuarios', 'roles', 'jefes', 'localidades', 'plantas', 'areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'email'             => 'required|email|max:255|unique:users,email',
            'password'          => 'required|string|min:8',
            'rol_id'            => 'nullable|exists:rols,id',
            'jefe_inmediato_id' => 'nullable|exists:users,id',
            'localidad_id'      => 'nullable|exists:localidads,id',
            'planta_id'         => 'nullable|exists:plantas,id',
            'area_id'           => 'nullable|exists:areas,id',
            'status'            => 'sometimes|boolean',

            'nombre'           => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'rfc'              => 'nullable|string|max:13|unique:informacion_usuarios,rfc',
            'curp'             => 'nullable|string|max:18|unique:informacion_usuarios,curp',
            'fecha_nacimiento' => 'nullable|date',
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
            'jefe_inmediato_id' => ['nullable', 'exists:users,id', Rule::notIn([$usuario->id])],
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

    public function destroy(User $usuario)
    {
        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }
}
