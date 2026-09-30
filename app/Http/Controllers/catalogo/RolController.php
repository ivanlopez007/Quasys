<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use Illuminate\Http\Request;

class RolController extends Controller
{
    /**
     * Muestra la lista de roles en la vista única index.
     */
    public function index()
    {
        $roles = Rol::orderBy('id', 'desc')->get();

        return view('catalogo.roles.index', compact('roles'));
    }

    /**
     * Guarda un nuevo rol en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rol' => 'required|string|max:255',
        ]);

        Rol::create([
            'rol' => $validated['rol'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    /**
     * Actualiza el rol especificado en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'rol' => 'required|string|max:255',
        ]);

        $rol = Rol::findOrFail($id);
        $rol->update([
            'rol' => $validated['rol'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Elimina el rol de la base de datos.
     */
    public function destroy($id)
    {
        $rol = Rol::findOrFail($id);
        $rol->delete();

        return redirect()->route('catalogo.roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }
}
