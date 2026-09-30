<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Muestra la lista de áreas en la vista única index.
     */
    public function index()
    {
        $areas = Area::orderBy('id', 'desc')->get();

        return view('catalogo.areas.index', compact('areas'));
    }

    /**
     * Guarda una nueva área en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'area' => 'required|string|max:255',
        ]);

        Area::create([
            'area' => $validated['area'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.areas.index')
            ->with('success', 'Área creada correctamente.');
    }

    /**
     * Actualiza el área especificada en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'area' => 'required|string|max:255',
        ]);

        $area = Area::findOrFail($id);
        $area->update([
            'area' => $validated['area'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.areas.index')
            ->with('success', 'Área actualizada correctamente.');
    }

    /**
     * Elimina el área de la base de datos.
     */
    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        $area->delete();

        return redirect()->route('catalogo.areas.index')
            ->with('success', 'Área eliminada correctamente.');
    }
}