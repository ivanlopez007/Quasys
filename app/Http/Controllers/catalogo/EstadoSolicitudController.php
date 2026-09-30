<?php

namespace App\Http\Controllers\catalogo;

use App\Http\Controllers\Controller;
use App\Models\EstadoSolicitud;
use Illuminate\Http\Request;

class EstadoSolicitudController extends Controller
{
    /**
     * Muestra la lista de estados de solicitud en la vista index.
     */
    public function index()
    {
        $estadosSolicitud = EstadoSolicitud::orderBy('id', 'desc')->get();

        return view('catalogo.estado_solicitud.index', compact('estadosSolicitud'));
    }

    /**
     * Guarda un nuevo estado de solicitud en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'estado_solicitud' => 'required|string|max:255',
        ]);

        EstadoSolicitud::create([
            'estado_solicitud' => $validated['estado_solicitud'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.estado_solicitud.index')
            ->with('success', 'Estado de solicitud creado correctamente.');
    }

    /**
     * Actualiza el estado de solicitud especificado en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'estado_solicitud' => 'required|string|max:255',
        ]);

        $estadoSolicitud = EstadoSolicitud::findOrFail($id);
        $estadoSolicitud->update([
            'estado_solicitud' => $validated['estado_solicitud'],
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('catalogo.estado_solicitud.index')
            ->with('success', 'Estado de solicitud actualizado correctamente.');
    }

    /**
     * Elimina el estado de solicitud de la base de datos.
     */
    public function destroy($id)
    {
        $estadoSolicitud = EstadoSolicitud::findOrFail($id);
        $estadoSolicitud->delete();

        return redirect()->route('catalogo.estado_solicitud.index')
            ->with('success', 'Estado de solicitud eliminado correctamente.');
    }
}
