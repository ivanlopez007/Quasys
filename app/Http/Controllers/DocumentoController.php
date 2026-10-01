<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSolicitudRequest;
use App\Models\Area;
use App\Models\BitacoraDocumento;
use App\Models\CambioDocumento;
use App\Models\DisposicionFinal;
use App\Models\Documento;
use App\Models\EstadoSolicitud;
use App\Models\Localidad;
use App\Models\LugarRetencion;
use App\Models\Nivel;
use App\Models\PeriodoRetencion;
use App\Models\Planta;
use App\Models\SubNivel;
use App\Models\TipoSolicitud;
use App\Services\GestionDocumentalService;
use DomainException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentoController extends Controller
{
    public function __construct(private GestionDocumentalService $servicio) {}

    /**
     * Lista Maestra: documentos vigentes, con búsqueda y filtros.
     */
    public function index(Request $request)
    {
        $documentos = Documento::with(['nivel', 'subnivel', 'area', 'localidad', 'autor.informacion', 'plantas'])
            ->vigentes()
            ->visiblesPara(Auth::user())
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q')->trim();
                $query->where(fn($w) => $w
                    ->where('codigo_documento', 'like', "%{$q}%")
                    ->orWhere('nombre_documento', 'like', "%{$q}%"));
            })
            ->when($request->filled('area_id'), fn($q) => $q->where('area_id', $request->area_id))
            ->when($request->filled('nivel_id'), fn($q) => $q->where('nivel_id', $request->nivel_id))
            ->latest('fecha_publicacion')
            ->paginate(15)
            ->withQueryString();

        $areas = Area::where('activo', true)->get();
        $niveles = Nivel::where('activo', true)->get();

        return view('documentos.index', compact('documentos', 'areas', 'niveles'));
    }

    /**
     * Solicitudes: cada usuario ve las suyas y las de sus subordinados;
     * los roles aprobadores ven todas.
     */
    public function solicitudesIndex(Request $request)
    {
        $usuario = Auth::user();
        $esAprobador = CambioDocumento::usuarioEsAprobador($usuario);

        $solicitudes = CambioDocumento::with(['solicitante.informacion', 'aprobador.informacion', 'estado', 'tipoSolicitud', 'documento'])
            ->when(!$esAprobador, fn($q) => $q->where(fn($w) => $w
                ->where('solicitante_id', $usuario->id)
                ->orWhereHas('solicitante', fn($s) => $s->where('jefe_inmediato_id', $usuario->id))))
            ->when($request->filled('estado_id'), fn($q) => $q->where('estado_id', $request->estado_id))
            ->latest('fecha_solicitud')
            ->paginate(20)
            ->withQueryString();

        $estados = EstadoSolicitud::where('activo', true)->get();

        return view('documentos.solicitudes_index', compact('solicitudes', 'estados', 'esAprobador'));
    }

    /**
     * "Por aprobar": pendientes que el usuario puede resolver.
     * Aprobadores por acceso ven todas; el jefe inmediato ve las de sus subordinados.
     * Nadie ve aquí sus propias solicitudes.
     */
    public function aprobaciones()
    {
        $usuario = Auth::user();
        $esAprobador = CambioDocumento::usuarioEsAprobador($usuario);

        $solicitudes = CambioDocumento::with(['solicitante.informacion', 'aprobador.informacion', 'estado', 'tipoSolicitud', 'documento'])
            ->whereHas('estado', fn($q) => $q->where('estado_solicitud', config('documentos.estados.pendiente')))
            ->where('solicitante_id', '!=', $usuario->id)
            ->when(!$esAprobador, fn($q) => $q->whereHas('solicitante', fn($s) => $s->where('jefe_inmediato_id', $usuario->id)))
            ->oldest('fecha_solicitud')
            ->paginate(20);

        $estados = collect();
        $modoAprobacion = true;

        return view('documentos.solicitudes_index', compact('solicitudes', 'estados', 'modoAprobacion', 'esAprobador'));
    }

    /**
     * Formulario de solicitud (Nuevo / Revisión / Eliminar).
     * Con ?documento_id=X se precarga el documento vigente a revisar o eliminar.
     */
    public function createSolicitud(Request $request)
    {
        $documentoOrigen = $request->filled('documento_id')
            ? Documento::vigentes()->visiblesPara(Auth::user())->with('plantas')->find($request->documento_id)
            : null;

        // Sin documento precargado, Revisión/Eliminar eligen uno de los vigentes de su planta.
        $documentosVigentes = $documentoOrigen ? collect() : Documento::vigentes()
            ->visiblesPara(Auth::user())
            ->with('plantas:id')
            ->orderBy('codigo_documento')
            ->get();

        return view('documentos.solicitudes_create', [
            'documentoOrigen' => $documentoOrigen,
            'documentosVigentes' => $documentosVigentes,
            'codigosPendientes' => CambioDocumento::pendientes()->pluck('codigo_documento')->filter()->flip(),
            'niveles' => Nivel::where('activo', true)->get(),
            'subniveles' => SubNivel::where('activo', true)->get(),
            'localidades' => Localidad::where('activo', true)->get(),
            'areas' => Area::where('activo', true)->get(),
            'lugaresRetencion' => LugarRetencion::where('activo', true)->get(),
            // Unidades de retención: Años, Días, Semanas. La cantidad se captura en el formulario.
            'periodosRetencion' => PeriodoRetencion::where('activo', true)->get(),
            'disposicionesFinales' => DisposicionFinal::where('activo', true)->get(),
            'tiposSolicitud' => TipoSolicitud::where('activo', true)->get(),
            'plantas' => Planta::orderBy('planta')->get(),
        ]);
    }

    public function storeSolicitud(StoreSolicitudRequest $request)
    {
        try {
            $this->servicio->crearSolicitud(
                Auth::user(),
                $request->validated(),
                $request->file('archivo')
            );
        } catch (DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('documentos.solicitudes.index')
            ->with('success', 'La solicitud ha sido registrada y está pendiente de aprobación.');
    }

    public function aprobar(Request $request, $id)
    {
        $request->validate(['comentario_aprobador' => 'nullable|string|max:1000']);

        try {
            $this->servicio->aprobar((int) $id, Auth::user(), $request->comentario_aprobador);
        } catch (AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Solicitud aprobada con éxito. La Lista Maestra fue actualizada.');
    }

    public function rechazar(Request $request, $id)
    {
        $request->validate(['comentario_aprobador' => 'required|string|max:1000']);

        try {
            $this->servicio->rechazar((int) $id, Auth::user(), $request->comentario_aprobador);
        } catch (AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('warning', 'La solicitud ha sido rechazada formalmente.');
    }

    /**
     * Historial de versiones de un documento. Se busca SOLO por código
     * (antes también por id, lo que podía confundir un id con un código numérico).
     * Solo se muestran las versiones asignadas a la planta del usuario.
     */
    public function historial($codigo)
    {
        $versiones = Documento::where('codigo_documento', $codigo)
            ->visiblesPara(Auth::user())
            ->with(['autor.informacion', 'aprobador.informacion', 'cambioOrigen', 'periodoRetencion', 'plantas'])
            ->orderBy('version', 'desc')
            ->get();

        abort_if($versiones->isEmpty(), 404);

        $documento = $versiones->first(); // la versión más reciente

        return view('documentos.historial', compact('documento', 'versiones'));
    }

    /**
     * Abre un archivo: PDF e imágenes van directo a una URL firmada temporal; Word se
     * muestra en el visor propio. Con ?descargar=1 fuerza la descarga.
     */
    public function verArchivo(Request $request)
    {
        [$path, $documento, $solicitud] = $this->resolverArchivo($request);

        $disco = Storage::disk(config('documentos.disk'));
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $origen = $documento ?? $solicitud;
        $nombreDescarga = Str::slug($origen->codigo_documento . ' v' . $origen->version . ' ' . $origen->nombre_documento) . ($extension ? ".{$extension}" : '');

        BitacoraDocumento::registrar(
            $request->boolean('descargar') ? 'archivo_descargado' : 'archivo_visto',
            Auth::id(),
            $documento?->id,
            $solicitud?->id,
            ['path' => $path]
        );

        $urlDescarga = $disco->temporaryUrl($path, now()->addMinutes(30), [
            'ResponseContentDisposition' => 'attachment; filename="' . $nombreDescarga . '"',
        ]);

        if ($request->boolean('descargar')) {
            return redirect()->away($urlDescarga);
        }

        // El navegador no sabe mostrar Word: se renderiza en una página propia (docx)
        // o se ofrece la descarga (doc, formato binario antiguo).
        if (in_array($extension, ['docx', 'doc'], true)) {
            return view('documentos.visor', [
                'origen' => $origen,
                'extension' => $extension,
                'urlContenido' => $extension === 'docx' ? route('documentos.archivo.contenido', ['path' => $path]) : null,
                'urlDescarga' => $urlDescarga,
            ]);
        }

        return redirect()->away($disco->temporaryUrl($path, now()->addMinutes(30)));
    }

    /**
     * Contenido del archivo servido por la app (mismo origen), para que el visor de Word
     * lo pueda leer sin exponer el bucket ni configurar CORS en R2.
     */
    public function contenidoArchivo(Request $request)
    {
        [$path] = $this->resolverArchivo($request);

        return Storage::disk(config('documentos.disk'))->response($path, null, [
            'Content-Disposition' => 'inline',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    /**
     * Solo sirve rutas que existen en la BD (evita leer cualquier objeto del bucket) y los
     * archivos de solicitudes pendientes solo los ve quien participa en ellas.
     *
     * @return array{0: string, 1: ?Documento, 2: ?CambioDocumento}
     */
    private function resolverArchivo(Request $request): array
    {
        $request->validate(['path' => 'required|string|max:500']);

        $path = $request->query('path');
        $usuario = Auth::user();

        $documento = Documento::where('url_documento', $path)->first();
        $solicitud = null;

        if ($documento) {
            abort_if(!$documento->esVisiblePara($usuario), 403, 'Este documento no está asignado a tu planta.');
        } else {
            $solicitud = CambioDocumento::with('solicitante')->where('url_documento', $path)->first();

            abort_if(!$solicitud, 404, 'El archivo no está registrado.');

            $autorizado = (int) $solicitud->solicitante_id === (int) $usuario->id
                || $solicitud->puedeSerResueltaPor($usuario);

            abort_if(!$autorizado, 403, 'No tienes permiso para ver este archivo.');
        }

        abort_if(!Storage::disk(config('documentos.disk'))->exists($path), 404, 'El archivo no existe en el almacenamiento o fue removido.');

        return [$path, $documento, $solicitud];
    }
}
