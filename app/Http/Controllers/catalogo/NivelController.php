<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Models\Nivel;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    /**
     * Muestra la lista de niveles en la vista única index.
     */
    public function index()
    {
        $niveles = Nivel::orderBy('id', 'desc')->get();

        return view('catalogo.niveles.index', compact('niveles'));
    }

    /**
     * Guarda un nuevo nivel en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nivel' => 'required|integer|min:0',
            'nombre' => 'required|string|max:255',
        ]);

        Nivel::create([
            'nivel' => $validated['nivel'],
            'nombre' => $validated['nombre'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.niveles.index')
            ->with('success', 'Nivel creado correctamente.');
    }

    /**
     * Actualiza el nivel especificado en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nivel' => 'required|integer|min:0',
            'nombre' => 'required|string|max:255',
        ]);

        $nivel = Nivel::findOrFail($id);
        $nivel->update([
            'nivel' => $validated['nivel'],
            'nombre' => $validated['nombre'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.niveles.index')
            ->with('success', 'Nivel actualizado correctamente.');
    }

    /**
     * Elimina el nivel de la base de datos.
     */
    public function destroy($id)
    {
        $nivel = Nivel::findOrFail($id);
        $nivel->delete();

        return redirect()->route('catalogo.niveles.index')
            ->with('success', 'Nivel eliminado correctamente.');
    }
}
