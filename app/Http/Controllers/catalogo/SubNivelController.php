<?php

namespace App\Http\Controllers\catalogo;

use App\Http\Controllers\Controller;
use App\Models\Nivel;
use App\Models\SubNivel;
use Illuminate\Http\Request;

class SubNivelController extends Controller
{
    /**
     * Muestra la lista de subniveles y niveles en la vista index.
     */
    public function index()
    {
        $subNiveles = SubNivel::with('nivel')->orderBy('id', 'desc')->get();
        $niveles = Nivel::where('activo', true)->orderBy('nombre', 'asc')->get();

        return view('catalogo.sub_nivel.index', compact('subNiveles', 'niveles'));
    }

    /**
     * Guarda un nuevo subnivel en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'   => 'required|string|max:255',
            'nivel_id' => 'required|exists:nivels,id',
        ]);

        SubNivel::create([
            'nombre'   => $validated['nombre'],
            'nivel_id' => $validated['nivel_id'],
            'activo'   => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.sub_nivel.index')
            ->with('success', 'Subnivel registrado correctamente.');
    }

    /**
     * Actualiza el subnivel especificado en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre'   => 'required|string|max:255',
            'nivel_id' => 'required|exists:nivels,id',
        ]);

        $subNivel = SubNivel::findOrFail($id);
        $subNivel->update([
            'nombre'   => $validated['nombre'],
            'nivel_id' => $validated['nivel_id'],
            'activo'   => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.sub_nivel.index')
            ->with('success', 'Subnivel actualizado correctamente.');
    }

    /**
     * Elimina el subnivel de la base de datos.
     */
    public function destroy($id)
    {
        $subNivel = SubNivel::findOrFail($id);
        $subNivel->delete();

        return redirect()->route('catalogo.sub_nivel.index')
            ->with('success', 'Subnivel eliminado correctamente.');
    }
}
