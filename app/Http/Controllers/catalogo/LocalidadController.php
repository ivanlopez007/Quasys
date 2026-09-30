<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Models\Localidad;
use Illuminate\Http\Request;

class LocalidadController extends Controller
{
    /**
     * Muestra la lista de localidades en la vista única index.
     */
    public function index()
    {
        $localidades = Localidad::orderBy('id', 'desc')->get();

        return view('catalogo.localidades.index', compact('localidades'));
    }

    /**
     * Guarda una nueva localidad en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'localidad' => 'required|string|max:255',
        ]);

        Localidad::create([
            'localidad' => $validated['localidad'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.localidades.index')
            ->with('success', 'Localidad creada correctamente.');
    }

    /**
     * Actualiza la localidad especificada en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'localidad' => 'required|string|max:255',
        ]);

        $localidad = Localidad::findOrFail($id);
        $localidad->update([
            'localidad' => $validated['localidad'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.localidades.index')
            ->with('success', 'Localidad actualizada correctamente.');
    }

    /**
     * Elimina la localidad de la base de datos.
     */
    public function destroy($id)
    {
        $localidad = Localidad::findOrFail($id);
        $localidad->delete();

        return redirect()->route('catalogo.localidades.index')
            ->with('success', 'Localidad eliminada correctamente.');
    }
}
