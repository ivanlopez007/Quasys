<?php

namespace App\Http\Controllers;

use App\Models\CambioDocumento;
use App\Models\Documento;
use App\Models\Nivel;
use App\Models\SubNivel;
use App\Models\Localidad;
use App\Models\Area;
use App\Models\LugarRetencion;
use App\Models\PeriodoRetencion;
use App\Models\DisposicionFinal;
use App\Models\TipoSolicitud;
use App\Models\EstadoSolicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DocumentoController extends Controller
{
    /**
     * Listado de documentos vigentes.
     */
    public function index()
    {
        $documentos = Documento::with(['nivel', 'subnivel', 'area', 'localidad'])
            ->where('activo', true)
            ->latest()
            ->paginate(15);

        return view('documentos.index', compact('documentos'));
    }

    /**
     * Listado de solicitudes pendientes / historial de solicitudes.
     */
    public function solicitudesIndex()
    {
        $solicitudes = CambioDocumento::with(['solicitante', 'aprobar', 'estado', 'tipoSolicitud'])
            ->latest()
            ->paginate(20);

        return view('documentos.solicitudes_index', compact('solicitudes'));
    }

    /**
     * Formulario para crear una nueva solicitud de documento o actualización.
     */
    public function createSolicitud(Request $request)
    {
        $documentoOrigen = null;
        if ($request->has('documento_id')) {
            $documentoOrigen = Documento::find($request->documento_id);
        }

        $niveles = Nivel::all();
        $subniveles = SubNivel::all();
        $localidades = Localidad::all();
        $areas = Area::all();
        $lugaresRetencion = LugarRetencion::all();
        $periodosRetencion = PeriodoRetencion::all();
        $disposicionesFinales = DisposicionFinal::all();
        $tiposSolicitud = TipoSolicitud::all();

        return view('documentos.solicitudes_create', compact(
            'documentoOrigen',
            'niveles',
            'subniveles',
            'localidades',
            'areas',
            'lugaresRetencion',
            'periodosRetencion',
            'disposicionesFinales',
            'tiposSolicitud'
        ));
    }

    /**
     * Guardar la solicitud y subir el archivo preliminar a Cloudflare R2.
     */
    public function storeSolicitud(Request $request)
    {
        $request->validate([
            'documento_id'           => 'nullable|exists:documentos,id',
            'nombre_documento'       => 'required|string|max:255',
            'version'                => 'required|integer|min:1',
            'archivo'                => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg|max:10240',
            'nivel_id'               => 'nullable|exists:nivels,id',
            'subnivel_id'            => 'nullable|exists:sub_nivels,id',
            'localidad_id'           => 'nullable|exists:localidads,id',
            'area_id'                => 'nullable|exists:areas,id',
            'lugar_retencion_id'     => 'nullable|exists:lugar_retencions,id',
            'periodo_retencion_id'   => 'nullable|exists:periodo_retencions,id',
            'disposicion_final_id'   => 'nullable|exists:disposicion_finals,id',
            'tipo_solicitud_id'      => 'nullable|exists:tipo_solicituds,id',
            'motivo_cambio'          => 'nullable|string',
            'descripcion_cambios'    => 'nullable|string',
        ]);

        // Cargar el archivo al storage R2 (disco 'r2')
        $path = $request->file('archivo')->store('solicitudes_temp', 'r2');

        $estadoPendiente = EstadoSolicitud::where('nombre', 'Pendiente')->first()->id ?? 1;

        CambioDocumento::create([
            'documento_id'         => $request->documento_id,
            'nombre_documento'     => $request->nombre_documento,
            'version'              => $request->version,
            'url_documento'        => $path,
            'nivel_id'             => $request->nivel_id,
            'subnivel_id'          => $request->subnivel_id,
            'localidad_id'         => $request->localidad_id,
            'area_id'              => $request->area_id,
            'lugar_retencion_id'   => $request->lugar_retencion_id,
            'periodo_retencion_id' => $request->periodo_retencion_id,
            'disposicion_final_id' => $request->disposicion_final_id,
            'tipo_solicitud_id'    => $request->tipo_solicitud_id,
            'solicitante_id'       => Auth::id(),
            'estado_id'            => $estadoPendiente,
            'motivo_cambio'        => $request->motivo_cambio,
            'descripcion_cambios'  => $request->descripcion_cambios,
            'fecha_solicitud'      => now(),
        ]);

        return redirect()->route('documentos.solicitudes.index')->with('success', 'Solicitud registrada correctamente.');
    }

    /**
     * Aprobar la solicitud y publicar/actualizar el documento oficial.
     */
    public function aprobar(Request $request, $id)
    {
        $request->validate([
            'comentario_aprobador' => 'nullable|string',
        ]);

        $solicitud = CambioDocumento::findOrFail($id);
        $estadoAprobado = EstadoSolicitud::where('nombre', 'Aprobado')->first()->id ?? 2;

        DB::transaction(function () use ($solicitud, $request, $estadoAprobado) {
            // 1. Copiar el archivo de 'solicitudes_temp' a 'documentos' en R2
            $nuevaRuta = 'documentos/' . basename($solicitud->url_documento);
            if (Storage::disk('r2')->exists($solicitud->url_documento)) {
                Storage::disk('r2')->copy($solicitud->url_documento, $nuevaRuta);
            }

            // 2. Si ya existía un documento base, desactivar la versión previa
            if ($solicitud->documento_id) {
                Documento::where('id', $solicitud->documento_id)->update(['activo' => false]);
            }

            // 3. Crear/Actualizar la versión activa del documento
            $documento = Documento::updateOrCreate(
                ['id' => $solicitud->documento_id],
                [
                    'nombre'               => $solicitud->nombre_documento,
                    'version'              => $solicitud->version,
                    'url_documento'        => $nuevaRuta,
                    'nivel_id'             => $solicitud->nivel_id,
                    'subnivel_id'          => $solicitud->subnivel_id,
                    'localidad_id'         => $solicitud->localidad_id,
                    'area_id'              => $solicitud->area_id,
                    'lugar_retencion_id'   => $solicitud->lugar_retencion_id,
                    'periodo_retencion_id' => $solicitud->periodo_retencion_id,
                    'disposicion_final_id' => $solicitud->disposicion_final_id,
                    'cambio_documento_id'  => $solicitud->id,
                    'activo'               => true,
                ]
            );

            // 4. Actualizar estado de la solicitud
            $solicitud->update([
                'documento_id'         => $documento->id,
                'aprobar_id'           => Auth::id(),
                'estado_id'            => $estadoAprobado,
                'comentario_aprobador' => $request->comentario_aprobador,
                'fecha_aprobacion'     => now(),
            ]);
        });

        return redirect()->back()->with('success', 'La solicitud fue aprobada y el documento se publicó oficialmente.');
    }

    /**
     * Rechazar la solicitud.
     */
    public function rechazar(Request $request, $id)
    {
        $request->validate([
            'comentario_aprobador' => 'required|string',
        ]);

        $solicitud = CambioDocumento::findOrFail($id);
        $estadoRechazado = EstadoSolicitud::where('nombre', 'Rechazado')->first()->id ?? 3;

        $solicitud->update([
            'aprobar_id'           => Auth::id(),
            'estado_id'            => $estadoRechazado,
            'comentario_aprobador' => $request->comentario_aprobador,
            'fecha_aprobacion'     => now(),
        ]);

        return redirect()->back()->with('warning', 'La solicitud ha sido rechazada.');
    }

    /**
     * Historial de versiones del documento por su código/ID.
     */
    public function historial($codigo)
    {
        $documento = Documento::where('id', $codigo)->orWhere('codigo', $codigo)->firstOrFail();
        $historial = CambioDocumento::where('documento_id', $documento->id)
            ->with(['solicitante', 'aprobar', 'estado'])
            ->orderBy('version', 'desc')
            ->get();

        return view('documentos.historial', compact('documento', 'historial'));
    }

    /**
     * Generar URL privada/firmada de Cloudflare R2 para ver o descargar el archivo.
     */
    public function verArchivo(Request $request)
    {
        $request->validate(['path' => 'required|string']);

        $path = $request->query('path');

        if (!Storage::disk('r2')->exists($path)) {
            return view('documentos.archivo_error', [
                'mensaje' => 'El archivo no existe en el almacenamiento o fue removido.'
            ]);
        }

        $url = Storage::disk('r2')->temporaryUrl(
            $path,
            now()->addMinutes(30)
        );

        return redirect()->away($url);
    }
}
