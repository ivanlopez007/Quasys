@extends('layout.layout')

@section('title', 'Lista Maestra')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Lista Maestra de Documentos</h1>
            <p class="text-xs text-slate-500 mt-1">Información documentada vigente del sistema de gestión de calidad</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('documentos.solicitudes.index') }}"
                class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase tracking-wider text-slate-700 hover:bg-slate-50 transition shadow-sm">
                <i class="fas fa-inbox mr-1.5"></i> Solicitudes
            </a>
            <a href="{{ route('documentos.solicitudes.create') }}"
                class="px-4 py-2.5 rounded-xl bg-slate-900 text-xs font-bold uppercase tracking-wider text-white hover:bg-slate-700 transition shadow-sm">
                <i class="fas fa-plus mr-1.5"></i> Nuevo documento
            </a>
        </div>
    </div>

    @include('documentos._alertas')

    @unless (auth()->user()->planta_id)
        <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 shadow-sm">
            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
            <p class="font-semibold">No tienes una planta asignada, por eso no ves documentos. Pide al administrador que te asigne una en el catálogo de Usuarios.</p>
        </div>
    @endunless

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Filtros --}}
        <form method="GET" class="p-4 bg-slate-50/50 border-b border-slate-200 grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-5 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por código o nombre..."
                    class="w-full rounded-xl border border-gray-300 pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900">
            </div>
            <select name="area_id" class="md:col-span-3 rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900">
                <option value="">Todas las áreas</option>
                @foreach ($areas as $area)
                    <option value="{{ $area->id }}" @selected(request('area_id') == $area->id)>{{ $area->area }}</option>
                @endforeach
            </select>
            <select name="nivel_id" class="md:col-span-2 rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900">
                <option value="">Todos los niveles</option>
                @foreach ($niveles as $nivel)
                    <option value="{{ $nivel->id }}" @selected(request('nivel_id') == $nivel->id)>{{ $nivel->nivel }} · {{ $nivel->nombre }}</option>
                @endforeach
            </select>
            <div class="md:col-span-2 flex gap-2">
                <button class="flex-1 rounded-xl bg-slate-800 text-white text-xs font-bold uppercase tracking-wider px-3 py-2.5 hover:bg-slate-700 transition">Filtrar</button>
                @if (request()->hasAny(['q', 'area_id', 'nivel_id']))
                    <a href="{{ route('documentos.index') }}" class="rounded-xl border border-gray-300 bg-white text-slate-500 px-3 py-2.5 hover:bg-slate-50 transition" title="Limpiar">
                        <i class="fas fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- Tabla --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 border-b border-slate-200">
                    <tr class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-5 py-3">Código</th>
                        <th class="px-5 py-3">Documento</th>
                        <th class="px-5 py-3">Versión</th>
                        <th class="px-5 py-3">Clasificación</th>
                        <th class="px-5 py-3">Área / Localidad</th>
                        <th class="px-5 py-3">Publicado</th>
                        <th class="px-5 py-3">Próx. revisión</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($documentos as $doc)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-5 py-4">
                                <span class="font-mono text-xs font-bold text-slate-800">{{ $doc->codigo_documento }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-slate-800">{{ $doc->nombre_documento }}</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Autor: @include('documentos._persona', ['u' => $doc->autor])</p>
                                @if ($doc->plantas->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 mt-1.5">
                                        @foreach ($doc->plantas as $planta)
                                            <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 border border-slate-200 px-1.5 py-0.5 text-[10px] font-bold text-slate-600">
                                                <i class="fas fa-industry text-[9px] text-slate-400"></i> {{ $planta->planta }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-800 px-2.5 py-1 text-[11px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> v{{ $doc->version }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-600">
                                <p class="font-semibold">{{ $doc->nivel?->nombre ?? '—' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $doc->subnivel?->nombre }}</p>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-600">
                                <p class="font-semibold">{{ $doc->area?->area ?? '—' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $doc->localidad?->localidad }}</p>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-600 whitespace-nowrap">{{ $doc->fecha_publicacion?->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 text-xs whitespace-nowrap">
                                @if ($doc->fecha_proxima_revision)
                                    <span class="{{ $doc->fecha_proxima_revision->isPast() ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                                        {{ $doc->fecha_proxima_revision->format('d/m/Y') }}
                                        @if ($doc->fecha_proxima_revision->isPast()) <i class="fas fa-triangle-exclamation ml-1"></i> @endif
                                    </span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('documentos.archivo.ver', ['path' => $doc->url_documento]) }}" target="_blank" title="Ver archivo"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition"><i class="fas fa-eye text-xs"></i></a>
                                    <a href="{{ route('documentos.historial', $doc->codigo_documento) }}" title="Historial de versiones"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition"><i class="fas fa-clock-rotate-left text-xs"></i></a>
                                    <a href="{{ route('documentos.solicitudes.create', ['documento_id' => $doc->id, 'tipo' => \App\Models\CambioDocumento::tipoId(\App\Models\CambioDocumento::TIPO_REVISION)]) }}" title="Solicitar revisión"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition"><i class="fas fa-pen-to-square text-xs"></i></a>
                                    <a href="{{ route('documentos.solicitudes.create', ['documento_id' => $doc->id, 'tipo' => \App\Models\CambioDocumento::tipoId(\App\Models\CambioDocumento::TIPO_ELIMINAR)]) }}" title="Solicitar baja"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-rose-500 hover:bg-rose-50 hover:text-rose-600 transition"><i class="fas fa-trash-can text-xs"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <i class="fas fa-folder-open text-3xl text-slate-300"></i>
                                <p class="mt-3 text-sm font-bold text-slate-500">No hay documentos vigentes</p>
                                <p class="text-xs text-slate-400">Ajusta los filtros o crea una solicitud de documento nuevo.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($documentos->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 bg-slate-50/50">{{ $documentos->links() }}</div>
        @endif
    </div>
</div>
@endsection
