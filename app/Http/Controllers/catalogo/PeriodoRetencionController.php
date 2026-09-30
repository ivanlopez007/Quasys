<?php

namespace App\Http\Controllers\catalogo;

use App\Http\Controllers\Controller;
use App\Models\PeriodoRetencion;
use Illuminate\Http\Request;

class PeriodoRetencionController extends Controller
{
    /**
     * Muestra la lista de periodos de retención en la vista index.
     */
    public function index()
    {
        $periodosRetencion = PeriodoRetencion::orderBy('id', 'desc')->get();

        return view('catalogo.periodo_retencion.index', compact('periodosRetencion'));
    }

    /**
     * Guarda un nuevo periodo de retención en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'periodo_retencion' => 'required|string|max:255',
            'tiempo'            => 'required|integer|min:0',
        ]);

        PeriodoRetencion::create([
            'periodo_retencion' => $validated['periodo_retencion'],
            'tiempo'            => $validated['tiempo'],
            'activo'            => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.periodo_retencion.index')
            ->with('success', 'Periodo de retención creado correctamente.');
    }

    /**
     * Actualiza el periodo de retención especificado en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'periodo_retencion' => 'required|string|max:255',
            'tiempo'            => 'required|integer|min:0',
        ]);

        $periodoRetencion = PeriodoRetencion::findOrFail($id);
        $periodoRetencion->update([
            'periodo_retencion' => $validated['periodo_retencion'],
            'tiempo'            => $validated['tiempo'],
            'activo'            => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.periodo_retencion.index')
            ->with('success', 'Periodo de retención actualizado correctamente.');
    }

    /**
     * Elimina el periodo de retención de la base de datos.
     */
    public function destroy($id)
    {
        $periodoRetencion = PeriodoRetencion::findOrFail($id);
        $periodoRetencion->delete();

        return redirect()->route('catalogo.periodo_retencion.index')
            ->with('success', 'Periodo de retención eliminado correctamente.');
    }
}
