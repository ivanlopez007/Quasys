<?php

namespace App\Http\Controllers;

use App\Models\CambioDocumento;
use App\Models\Documento;
use App\Models\User;
use App\Services\ReasignacionUsuarioService;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Documentos vigentes y solicitudes pendientes cuyo responsable fue dado de baja
 * (soft delete), para asignarles un nuevo responsable.
 */
class DocumentosSinResponsableController extends Controller
{
    public const URL = 'control-documentos/sin-responsable';

    public function __construct(private ReasignacionUsuarioService $reasignacion) {}

    public function index(Request $request)
    {
        $this->autorizar();

        $sinResponsable = fn ($q) => $q->onlyTrashed();
        $anterior = $request->integer('anterior') ?: null;

        $documentos = Documento::vigentes()
            ->whereHas('autor', $sinResponsable)
            ->when($anterior, fn ($q) => $q->where('usuario_id', $anterior))
            ->with(['autor.informacion', 'area', 'plantas'])
            ->orderBy('codigo_documento')
            ->get();

        $solicitudes = CambioDocumento::pendientes()
            ->whereHas('solicitante', $sinResponsable)
            ->when($anterior, fn ($q) => $q->where('solicitante_id', $anterior))
            ->with(['solicitante.informacion', 'tipoSolicitud', 'plantas'])
            ->oldest('fecha_solicitud')
            ->get();

        // Usuarios dados de baja que dejaron algo a su cargo (para filtrar)
        $anteriores = User::onlyTrashed()
            ->with('informacion')
            ->where(fn ($q) => $q
                ->whereHas('documentosElaborados', fn ($d) => $d->where('vigente', true))
                ->orWhereHas('solicitudesDeCambio', fn ($s) => $s->pendientes()))
            ->get();

        $destinos = User::with(['informacion', 'planta'])->get();

        return view('documentos.sin_responsable', compact('documentos', 'solicitudes', 'anteriores', 'anterior', 'destinos'));
    }

    public function reasignar(Request $request)
    {
        $this->autorizar();

        $validated = $request->validate([
            'reasignar_a_id' => ['required', Rule::exists('users', 'id')->whereNull('deleted_at')],
            'documentos' => ['array'],
            'documentos.*' => ['integer'],
            'solicitudes' => ['array'],
            'solicitudes.*' => ['integer'],
        ], [
            'reasignar_a_id.required' => 'Selecciona el nuevo responsable.',
            'reasignar_a_id.exists' => 'El usuario seleccionado no existe o está dado de baja.',
        ]);

        if (empty($validated['documentos']) && empty($validated['solicitudes'])) {
            return back()->with('error', 'Marca al menos un documento o solicitud para reasignar.');
        }

        try {
            $r = $this->reasignacion->reasignarSinResponsable(
                $validated['documentos'] ?? [],
                $validated['solicitudes'] ?? [],
                User::findOrFail($validated['reasignar_a_id']),
                Auth::user()
            );
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        $redirect = back()->with('success', "Se reasignaron {$r['documentos']} documento(s) y {$r['solicitudes']} solicitud(es).");

        if ($r['omitidos']) {
            $redirect->with('warning', 'No se reasignaron: ' . implode(', ', $r['omitidos']) . '. Elige un responsable de una planta asignada al documento.');
        }

        return $redirect;
    }

    private function autorizar(): void
    {
        abort_unless(Auth::user()->tieneAcceso(self::URL), 403, 'No tienes acceso a esta pantalla.');
    }
}
