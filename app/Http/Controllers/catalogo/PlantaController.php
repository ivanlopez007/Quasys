<?php

namespace App\Http\Controllers\catalogo;

use App\Http\Controllers\Controller;
use App\Models\Localidad;
use App\Models\Planta;
use Illuminate\Http\Request;

class PlantaController extends Controller
{
    /**
     * Muestra la lista de plantas y localidades en la vista index.
     */
    public function index()
    {
        $plantas = Planta::with('localidad')->orderBy('id', 'desc')->get();
        $localidades = Localidad::orderBy('localidad', 'asc')->get();

        return view('catalogo.planta.index', compact('plantas', 'localidades'));
    }

    /**
     * Guarda una nueva planta en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'planta'       => 'required|string|max:255',
            'localidad_id' => 'nullable|exists:localidads,id',
            'ubicacion'    => 'nullable|string|max:255',
        ]);

        Planta::create([
            'planta'       => $validated['planta'],
            'localidad_id' => $validated['localidad_id'] ?? null,
            'ubicacion'    => $validated['ubicacion'] ?? null,
        ]);

        return redirect()->route('catalogo.planta.index')
            ->with('success', 'Planta registrada correctamente.');
    }

    /**
     * Actualiza la planta especificada en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'planta'       => 'required|string|max:255',
            'localidad_id' => 'nullable|exists:localidads,id',
            'ubicacion'    => 'nullable|string|max:255',
        ]);

        $planta = Planta::findOrFail($id);
        $planta->update([
            'planta'       => $validated['planta'],
            'localidad_id' => $validated['localidad_id'] ?? null,
            'ubicacion'    => $validated['ubicacion'] ?? null,
        ]);

        return redirect()->route('catalogo.planta.index')
            ->with('success', 'Planta actualizada correctamente.');
    }

    /**
     * Elimina (soft delete) la planta de la base de datos.
     */
    public function destroy($id)
    {
        $planta = Planta::findOrFail($id);
        $planta->delete();

        return redirect()->route('catalogo.planta.index')
            ->with('success', 'Planta eliminada correctamente.');
    }
}
