<?php

namespace App\Http\Controllers\catalogo;

use App\Http\Controllers\Controller;
use App\Models\TipoSolicitud;
use Illuminate\Http\Request;

class TipoSolicitudController extends Controller
{
    /**
     * Muestra la lista de tipos de solicitud en la vista index.
     */
    public function index()
    {
        $tiposSolicitud = TipoSolicitud::orderBy('id', 'desc')->get();

        return view('catalogo.tipo_solicitud.index', compact('tiposSolicitud'));
    }

    /**
     * Guarda un nuevo tipo de solicitud en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_solicitud' => 'required|string|max:255',
        ]);

        TipoSolicitud::create([
            'tipo_solicitud' => $validated['tipo_solicitud'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.tipo_solicitud.index')
            ->with('success', 'Tipo de solicitud creado correctamente.');
    }

    /**
     * Actualiza el tipo de solicitud especificado en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'tipo_solicitud' => 'required|string|max:255',
        ]);

        $tipoSolicitud = TipoSolicitud::findOrFail($id);
        $tipoSolicitud->update([
            'tipo_solicitud' => $validated['tipo_solicitud'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.tipo_solicitud.index')
            ->with('success', 'Tipo de solicitud actualizado correctamente.');
    }

    /**
     * Elimina el tipo de solicitud de la base de datos.
     */
    public function destroy($id)
    {
        $tipoSolicitud = TipoSolicitud::findOrFail($id);
        $tipoSolicitud->delete();

        return redirect()->route('catalogo.tipo_solicitud.index')
            ->with('success', 'Tipo de solicitud eliminado correctamente.');
    }
}
