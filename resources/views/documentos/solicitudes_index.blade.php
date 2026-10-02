@extends('layout.layout')

@php $modoAprobacion = $modoAprobacion ?? false; @endphp

@section('title', $modoAprobacion ? 'Por aprobar' : 'Solicitudes de documentos')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">{{ $modoAprobacion ? 'Solicitudes por aprobar' : 'Solicitudes de documentos' }}</h1>
            <p class="text-xs text-slate-500 mt-1">
                {{ $modoAprobacion ? 'Pendientes que tú puedes aprobar o rechazar' : 'Control de cambios: documentos nuevos, revisiones y bajas' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('documentos.index') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition shadow-sm">
                <i class="fas fa-list mr-1.5"></i> Lista maestra
            </a>
            <a href="{{ route('documentos.solicitudes.create') }}"
                class="px-4 py-2.5 rounded-xl bg-slate-900 text-xs font-bold uppercase tracking-wider text-white hover:bg-slate-700 transition shadow-sm">
                <i class="fas fa-plus mr-1.5"></i> Nueva solicitud
            </a>
        </div>
    </div>

    <div class="space-y-3">@include('documentos._alertas')</div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        @unless ($modoAprobacion)
            <form method="GET" class="p-4 bg-slate-50/50 border-b border-slate-200 flex flex-col sm:flex-row gap-3">
                <select name="estado_id" class="sm:w-64 rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900">
                    <option value="">Todos los estados</option>
                    @foreach ($estados as $estado)
                        <option value="{{ $estado->id }}" @selected(request('estado_id') == $estado->id)>{{ $estado->estado_solicitud }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-slate-800 text-white text-xs font-bold uppercase tracking-wider px-5 py-2.5 hover:bg-slate-700 transition">Filtrar</button>
                @if (request()->filled('estado_id'))
                    <a href="{{ route('documentos.solicitudes.index') }}" class="rounded-xl border border-gray-300 bg-white text-slate-500 px-4 py-2.5 text-xs font-bold uppercase tracking-wider hover:bg-slate-50 transition text-center">Limpiar</a>
                @endif
            </form>
        @endunless

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 border-b border-slate-200">
                    <tr class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-5 py-3">Tipo</th>
                        <th class="px-5 py-3">Documento</th>
                        <th class="px-5 py-3">Nivel / Subnivel</th>
                        <th class="px-5 py-3">Versión</th>
                        <th class="px-5 py-3">Solicitante</th>
                        <th class="px-5 py-3">Motivo</th>
                        <th class="px-5 py-3">Estado</th>
                        <th class="px-5 py-3">Resolución</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($solicitudes as $s)
                        @php
                            $estadoNombre = $s->estado?->estado_solicitud;
                            $estadoClases = match ($estadoNombre) {
                                config('documentos.estados.aprobado') => ['bg-emerald-50 text-emerald-800', 'bg-emerald-500'],
                                config('documentos.estados.rechazado') => ['bg-rose-50 text-rose-800', 'bg-rose-500'],
                                default => ['bg-amber-50 text-amber-800', 'bg-amber-500'],
                            };
                            [$tipoIcono, $tipoClase] = match ($s->tipoClave()) {
                                \App\Models\CambioDocumento::TIPO_NUEVO => ['fa-file-circle-plus', 'text-slate-700'],
                                \App\Models\CambioDocumento::TIPO_REVISION => ['fa-pen-to-square', 'text-slate-700'],
                                \App\Models\CambioDocumento::TIPO_ELIMINAR => ['fa-trash-can', 'text-rose-600'],
                                default => ['fa-file', 'text-slate-500'],
                            };
                            $puedeResolver = $s->esPendiente() && $s->puedeSerResueltaPor(auth()->user(), $esAprobador ?? null);
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition align-top">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-2 text-xs font-bold {{ $tipoClase }}">
                                    <i class="fas {{ $tipoIcono }}"></i> {{ $s->tipoSolicitud?->tipo_solicitud ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-mono text-xs font-bold text-slate-800">{{ $s->codigo_documento ?? '—' }}</span>
                                <p class="text-sm text-slate-700 mt-0.5">{{ $s->nombre_documento }}</p>
                            </td>
                            <td class="px-5 py-4 text-xs">@include('documentos._nivel', ['item' => $s])</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full bg-slate-100 text-slate-700 px-2.5 py-1 text-[11px] font-mono font-bold">v{{ $s->version }}</span>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-600">
                                <p class="font-semibold">@include('documentos._persona', ['u' => $s->solicitante])</p>
                                <p class="text-[11px] text-slate-400">{{ $s->fecha_solicitud?->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-600 max-w-[220px]">
                                <p class="line-clamp-2" title="{{ $s->motivo_cambio }}">{{ $s->motivo_cambio ?: '—' }}</p>
                                @if ($s->descripcion_cambios)
                                    <p class="line-clamp-2 text-[11px] text-slate-400 mt-1" title="{{ $s->descripcion_cambios }}">{{ $s->descripcion_cambios }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $estadoClases[0] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $estadoClases[1] }}"></span> {{ $estadoNombre ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-600 max-w-[200px]">
                                @if ($s->aprobar_id)
                                    <p class="font-semibold">@include('documentos._persona', ['u' => $s->aprobador])</p>
                                    <p class="text-[11px] text-slate-400">{{ $s->fecha_aprobacion?->format('d/m/Y H:i') }}</p>
                                    @if ($s->comentario_aprobador)
                                        <p class="text-[11px] text-slate-500 mt-1 line-clamp-2" title="{{ $s->comentario_aprobador }}">“{{ $s->comentario_aprobador }}”</p>
                                    @endif
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    @if ($s->url_documento)
                                        <a href="{{ route('documentos.archivo.ver', ['path' => $s->url_documento]) }}" target="_blank" title="Ver archivo"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition"><i class="fas fa-eye text-xs"></i></a>
                                    @endif
                                    @if ($s->codigo_documento && $s->documento_id)
                                        <a href="{{ route('documentos.historial', $s->codigo_documento) }}" title="Historial"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition"><i class="fas fa-clock-rotate-left text-xs"></i></a>
                                    @endif
                                    @if ($puedeResolver)
                                        <button type="button" onclick="abrirResolucion('aprobar', {{ $s->id }}, @js($s->codigo_documento), @js($s->nombre_documento))"
                                            class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 text-[11px] font-bold uppercase tracking-wider hover:bg-emerald-100 transition">
                                            <i class="fas fa-check mr-1"></i> Aprobar
                                        </button>
                                        <button type="button" onclick="abrirResolucion('rechazar', {{ $s->id }}, @js($s->codigo_documento), @js($s->nombre_documento))"
                                            class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 text-[11px] font-bold uppercase tracking-wider hover:bg-rose-100 transition">
                                            <i class="fas fa-xmark mr-1"></i> Rechazar
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-16 text-center">
                                <i class="fas fa-inbox text-3xl text-slate-300"></i>
                                <p class="mt-3 text-sm font-bold text-slate-500">{{ $modoAprobacion ? 'No tienes solicitudes pendientes por aprobar' : 'No hay solicitudes registradas' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($solicitudes->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 bg-slate-50/50">{{ $solicitudes->links() }}</div>
        @endif
    </div>
</div>

{{-- MODAL: aprobar / rechazar --}}
<div id="modal-resolucion" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="bg-slate-800 px-6 py-4 flex items-center justify-between">
            <h3 id="modal-titulo" class="text-white text-sm font-bold uppercase tracking-wider"></h3>
            <button type="button" onclick="cerrarResolucion()" class="text-slate-400 hover:text-white transition"><i class="fas fa-xmark"></i></button>
        </div>
        <form id="form-resolucion" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3">
                <p id="modal-codigo" class="font-mono text-xs font-bold text-slate-800"></p>
                <p id="modal-nombre" class="text-sm text-slate-600"></p>
            </div>
            <div>
                <label for="comentario_aprobador" id="modal-label" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5"></label>
                <textarea id="comentario_aprobador" name="comentario_aprobador" rows="4" maxlength="1000"
                    class="w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="cerrarResolucion()" class="px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-bold uppercase tracking-wider text-slate-600 hover:bg-slate-50 transition">Cancelar</button>
                <button id="modal-confirmar" type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider text-white transition"></button>
            </div>
        </form>
    </div>
</div>

<script>
    const rutasResolucion = {
        aprobar: @js(route('documentos.solicitudes.aprobar', ['id' => '__ID__'])),
        rechazar: @js(route('documentos.solicitudes.rechazar', ['id' => '__ID__'])),
    };

    function abrirResolucion(accion, id, codigo, nombre) {
        const esAprobar = accion === 'aprobar';
        const comentario = document.getElementById('comentario_aprobador');
        const boton = document.getElementById('modal-confirmar');

        document.getElementById('form-resolucion').action = rutasResolucion[accion].replace('__ID__', id);
        document.getElementById('modal-titulo').textContent = esAprobar ? 'Aprobar solicitud' : 'Rechazar solicitud';
        document.getElementById('modal-codigo').textContent = codigo || 'Sin código';
        document.getElementById('modal-nombre').textContent = nombre || '';
        document.getElementById('modal-label').textContent = esAprobar ? 'Comentario (opcional)' : 'Motivo del rechazo (obligatorio)';
        comentario.required = !esAprobar;
        comentario.value = '';
        boton.textContent = esAprobar ? 'Confirmar aprobación' : 'Confirmar rechazo';
        boton.className = 'px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider text-white transition ' +
            (esAprobar ? 'bg-emerald-600 hover:bg-emerald-500' : 'bg-rose-600 hover:bg-rose-500');

        document.getElementById('modal-resolucion').classList.remove('hidden');
        comentario.focus();
    }

    function cerrarResolucion() {
        document.getElementById('modal-resolucion').classList.add('hidden');
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarResolucion(); });
</script>
@endsection
