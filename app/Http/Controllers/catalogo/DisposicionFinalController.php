<?php

namespace App\Http\Controllers\catalogo;

use App\Http\Controllers\Controller;
use App\Models\DisposicionFinal;
use Illuminate\Http\Request;

class DisposicionFinalController extends Controller
{
    /**
     * Muestra la lista de disposiciones finales en la vista index.
     */
    public function index()
    {
        $disposicionesFinales = DisposicionFinal::orderBy('id', 'desc')->get();

        return view('catalogo.disposicion_final.index', compact('disposicionesFinales'));
    }

    /**
     * Guarda una nueva disposición final en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'disposicion_final' => 'required|string|max:255',
        ]);

        DisposicionFinal::create([
            'disposicion_final' => $validated['disposicion_final'],
            'activo'            => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.disposicion_final.index')
            ->with('success', 'Disposición final creada correctamente.');
    }

    /**
     * Actualiza la disposición final especificada en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'disposicion_final' => 'required|string|max:255',
        ]);

        $disposicionFinal = DisposicionFinal::findOrFail($id);
        $disposicionFinal->update([
            'disposicion_final' => $validated['disposicion_final'],
            'activo'            => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.disposicion_final.index')
            ->with('success', 'Disposición final actualizada correctamente.');
    }

    /**
     * Elimina la disposición final de la base de datos.
     */
    public function destroy($id)
    {
        $disposicionFinal = DisposicionFinal::findOrFail($id);
        $disposicionFinal->delete();

        return redirect()->route('catalogo.disposicion_final.index')
            ->with('success', 'Disposición final eliminada correctamente.');
    }
}
