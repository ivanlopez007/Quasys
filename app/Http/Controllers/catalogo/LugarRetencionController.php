<?php

namespace App\Http\Controllers\catalogo;

use App\Http\Controllers\Controller;
use App\Models\LugarRetencion;
use Illuminate\Http\Request;

class LugarRetencionController extends Controller
{
    /**
     * Muestra la lista de lugares de retención en la vista index.
     */
    public function index()
    {
        $lugaresRetencion = LugarRetencion::orderBy('id', 'desc')->get();

        return view('catalogo.lugar_retencion.index', compact('lugaresRetencion'));
    }

    /**
     * Guarda un nuevo lugar de retención en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lugar_retencion' => 'required|string|max:255',
        ]);

        LugarRetencion::create([
            'lugar_retencion' => $validated['lugar_retencion'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.lugar_retencion.index')
            ->with('success', 'Lugar de retención creado correctamente.');
    }

    /**
     * Actualiza el lugar de retención especificado en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'lugar_retencion' => 'required|string|max:255',
        ]);

        $lugarRetencion = LugarRetencion::findOrFail($id);
        $lugarRetencion->update([
            'lugar_retencion' => $validated['lugar_retencion'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.lugar_retencion.index')
            ->with('success', 'Lugar de retención actualizado correctamente.');
    }

    /**
     * Elimina el lugar de retención de la base de datos.
     */
    public function destroy($id)
    {
        $lugarRetencion = LugarRetencion::findOrFail($id);
        $lugarRetencion->delete();

        return redirect()->route('catalogo.lugar_retencion.index')
            ->with('success', 'Lugar de retención eliminado correctamente.');
    }
}
