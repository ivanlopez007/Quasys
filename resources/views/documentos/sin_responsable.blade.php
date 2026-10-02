@extends('layout.layout')

@section('title', 'Sin responsable')

@php
    $th = 'px-5 py-3.5 text-left';
    $check = 'rounded border-gray-300 text-slate-900 focus:ring-slate-900';
@endphp

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Documentos sin responsable</h1>
            <p class="text-xs text-slate-500 mt-1">Documentos vigentes y solicitudes pendientes cuyo responsable fue dado de baja</p>
        </div>
        <a href="{{ route('usuarios.index', ['ver' => 'eliminados']) }}" class="text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900 transition">
            <i class="fas fa-user-slash mr-1.5"></i> Ver usuarios eliminados
        </a>
    </div>

    <div class="space-y-3">@include('documentos._alertas')</div>

    {{-- Resumen --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Documentos vigentes</p>
            <p class="text-3xl font-black mt-1 {{ $documentos->isNotEmpty() ? 'text-rose-600' : 'text-slate-900' }}">{{ $documentos->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Solicitudes pendientes</p>
            <p class="text-3xl font-black mt-1 {{ $solicitudes->isNotEmpty() ? 'text-amber-600' : 'text-slate-900' }}">{{ $solicitudes->count() }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Usuarios dados de baja con pendientes</p>
            <p class="text-3xl font-black mt-1 text-slate-900">{{ $anteriores->count() }}</p>
        </div>
    </div>

    @if ($documentos->isEmpty() && $solicitudes->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-5 py-16 text-center">
            <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto text-2xl">
                <i class="fas fa-circle-check"></i>
            </div>
            <p class="mt-4 text-sm font-bold text-slate-700">
                {{ $anterior ? 'Este usuario ya no tiene nada pendiente de reasignar.' : 'Todos los documentos vigentes tienen un responsable activo.' }}
            </p>
            @if ($anterior)
                <a href="{{ route('documentos.sin_responsable.index') }}" class="inline-block mt-2 text-xs font-bold text-slate-500 hover:text-slate-900 underline">Ver todos</a>
            @endif
        </div>
    @else
        {{-- Filtro por responsable anterior --}}
        @if ($anteriores->count() > 1 || $anterior)
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <label for="anterior" class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Responsable anterior</label>
                <select id="anterior" name="anterior" onchange="this.form.submit()" class="rounded-xl border border-gray-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-slate-900">
                    <option value="">Todos</option>
                    @foreach ($anteriores as $u)
                        <option value="{{ $u->id }}" @selected($anterior == $u->id)>@include('documentos._persona', ['u' => $u])</option>
                    @endforeach
                </select>
            </form>
        @endif

        <form method="POST" action="{{ route('documentos.sin_responsable.reasignar') }}" id="form-reasignar" class="space-y-6">
            @csrf

            {{-- Documentos vigentes --}}
            @if ($documentos->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="bg-slate-800 px-5 py-3.5 flex items-center justify-between">
                        <h2 class="text-white text-sm font-bold uppercase tracking-wider"><i class="fas fa-file-lines text-slate-400 mr-2"></i>Documentos vigentes</h2>
                        <span class="text-[11px] font-bold text-slate-400">{{ $documentos->count() }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-slate-50 text-[11px] uppercase tracking-wider font-bold text-slate-500 border-b border-slate-200">
                                    <th class="{{ $th }} w-10"><input type="checkbox" data-todos="documentos" class="{{ $check }}" title="Marcar todos"></th>
                                    <th class="{{ $th }}">Código</th>
                                    <th class="{{ $th }}">Documento</th>
                                    <th class="{{ $th }}">Nivel / Subnivel</th>
                                    <th class="{{ $th }}">Versión</th>
                                    <th class="{{ $th }}">Plantas</th>
                                    <th class="{{ $th }}">Responsable anterior</th>
                                    <th class="{{ $th }} text-right">Ver</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($documentos as $doc)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-5 py-4">
                                            <input type="checkbox" name="documentos[]" value="{{ $doc->id }}" data-grupo="documentos"
                                                data-plantas="{{ $doc->plantas->pluck('id')->implode(',') }}" class="{{ $check }} fila-check">
                                        </td>
                                        <td class="px-5 py-4"><span class="font-mono text-xs font-bold text-slate-800">{{ $doc->codigo_documento }}</span></td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm font-bold text-slate-800">{{ $doc->nombre_documento }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $doc->area?->area }}</p>
                                        </td>
                                        <td class="px-5 py-4 text-xs">@include('documentos._nivel', ['item' => $doc])</td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-800 px-2.5 py-1 text-[11px] font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> v{{ $doc->version }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse ($doc->plantas as $planta)
                                                    <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 border border-slate-200 px-1.5 py-0.5 text-[10px] font-bold text-slate-600">{{ $planta->planta }}</span>
                                                @empty
                                                    <span class="text-slate-300 text-xs">—</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-xs text-slate-600">
                                            <p class="font-semibold">@include('documentos._persona', ['u' => $doc->autor])</p>
                                            <span class="inline-flex items-center px-1.5 py-0.5 mt-1 rounded-md text-[9px] font-bold uppercase bg-rose-50 text-rose-600 border border-rose-200">
                                                Baja {{ $doc->autor?->deleted_at?->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex items-center justify-end gap-1">
                                                <a href="{{ route('documentos.archivo.ver', ['path' => $doc->url_documento]) }}" target="_blank" title="Ver archivo"
                                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition"><i class="fas fa-eye text-xs"></i></a>
                                                <a href="{{ route('documentos.historial', $doc->codigo_documento) }}" title="Historial"
                                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition"><i class="fas fa-clock-rotate-left text-xs"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Solicitudes pendientes --}}
            @if ($solicitudes->isNotEmpty())
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="bg-slate-800 px-5 py-3.5 flex items-center justify-between">
                        <h2 class="text-white text-sm font-bold uppercase tracking-wider"><i class="fas fa-hourglass-half text-slate-400 mr-2"></i>Solicitudes pendientes</h2>
                        <span class="text-[11px] font-bold text-slate-400">{{ $solicitudes->count() }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-slate-50 text-[11px] uppercase tracking-wider font-bold text-slate-500 border-b border-slate-200">
                                    <th class="{{ $th }} w-10"><input type="checkbox" data-todos="solicitudes" class="{{ $check }}" title="Marcar todas"></th>
                                    <th class="{{ $th }}">Tipo</th>
                                    <th class="{{ $th }}">Código</th>
                                    <th class="{{ $th }}">Documento</th>
                                    <th class="{{ $th }}">Nivel / Subnivel</th>
                                    <th class="{{ $th }}">Solicitada</th>
                                    <th class="{{ $th }}">Solicitante (dado de baja)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($solicitudes as $s)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-5 py-4">
                                            <input type="checkbox" name="solicitudes[]" value="{{ $s->id }}" data-grupo="solicitudes" class="{{ $check }} fila-check">
                                        </td>
                                        <td class="px-5 py-4 text-xs font-bold text-slate-700 whitespace-nowrap">{{ $s->tipoSolicitud?->tipo_solicitud ?? '—' }}</td>
                                        <td class="px-5 py-4"><span class="font-mono text-xs font-bold text-slate-800">{{ $s->codigo_documento }}</span></td>
                                        <td class="px-5 py-4">
                                            <p class="text-sm text-slate-700">{{ $s->nombre_documento }}</p>
                                            <p class="text-[11px] text-slate-400">v{{ $s->version }}</p>
                                        </td>
                                        <td class="px-5 py-4 text-xs">@include('documentos._nivel', ['item' => $s])</td>
                                        <td class="px-5 py-4 text-xs text-slate-600 whitespace-nowrap">{{ $s->fecha_solicitud?->format('d/m/Y H:i') }}</td>
                                        <td class="px-5 py-4 text-xs text-slate-600">
                                            <p class="font-semibold">@include('documentos._persona', ['u' => $s->solicitante])</p>
                                            <span class="inline-flex items-center px-1.5 py-0.5 mt-1 rounded-md text-[9px] font-bold uppercase bg-rose-50 text-rose-600 border border-rose-200">
                                                Baja {{ $s->solicitante?->deleted_at?->format('d/m/Y') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="px-5 py-3 text-[10px] text-slate-400 border-t border-slate-100">
                        <i class="fas fa-circle-info mr-1"></i>El nuevo responsable quedará como solicitante y recibirá el correo cuando se apruebe o rechace.
                    </p>
                </div>
            @endif

            {{-- Barra de reasignación --}}
            <div class="sticky bottom-4 bg-white rounded-2xl border border-slate-200 shadow-lg p-4 flex flex-col md:flex-row md:items-center gap-3">
                <p class="text-xs font-bold text-slate-600 md:mr-auto">
                    <span id="contador">0</span> seleccionado(s)
                </p>
                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                    <label for="reasignar_a_id" class="text-[11px] font-bold uppercase tracking-wider text-slate-500 whitespace-nowrap">Nuevo responsable</label>
                    <select id="reasignar_a_id" name="reasignar_a_id" required class="rounded-xl border border-gray-300 px-3 py-2.5 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 sm:w-72">
                        <option value="">Selecciona un usuario...</option>
                        @foreach ($destinos as $u)
                            <option value="{{ $u->id }}" data-planta="{{ $u->planta_id }}" @selected(old('reasignar_a_id') == $u->id)>
                                {{ $u->informacion ? trim($u->informacion->nombre . ' ' . $u->informacion->apellidos) : $u->email }}{{ $u->planta ? ' · ' . $u->planta->planta : ' · sin planta' }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" id="btn-reasignar" disabled
                        class="px-5 py-2.5 rounded-xl bg-slate-900 text-xs font-bold uppercase tracking-wider text-white hover:bg-slate-700 transition shadow-sm disabled:opacity-40 disabled:cursor-not-allowed whitespace-nowrap">
                        <i class="fas fa-right-left mr-1.5"></i> Reasignar
                    </button>
                </div>
            </div>
            <p id="aviso-planta" class="hidden -mt-3 text-xs font-semibold text-amber-700">
                <i class="fas fa-triangle-exclamation mr-1"></i><span></span>
            </p>
        </form>
    @endif
</div>

<script>
    (function () {
        const form = document.getElementById('form-reasignar');
        if (!form) return;

        const checks = () => Array.from(form.querySelectorAll('.fila-check'));
        const destino = document.getElementById('reasignar_a_id');
        const boton = document.getElementById('btn-reasignar');
        const aviso = document.getElementById('aviso-planta');

        function actualizar() {
            const marcados = checks().filter(c => c.checked);
            document.getElementById('contador').textContent = marcados.length;
            boton.disabled = marcados.length === 0 || !destino.value;

            // Avisa qué documentos no podrá recibir el destino por no pertenecer a su planta
            const planta = destino.selectedOptions[0]?.dataset.planta || '';
            const fuera = marcados.filter(c => c.dataset.plantas !== undefined && !c.dataset.plantas.split(',').includes(planta));
            aviso.classList.toggle('hidden', !destino.value || fuera.length === 0);
            aviso.querySelector('span').textContent = fuera.length
                ? `${fuera.length} documento(s) no están asignados a la planta de este usuario y se omitirán.`
                : '';
        }

        form.querySelectorAll('[data-todos]').forEach(todos => {
            todos.addEventListener('change', () => {
                form.querySelectorAll(`.fila-check[data-grupo="${todos.dataset.todos}"]`).forEach(c => c.checked = todos.checked);
                actualizar();
            });
        });
        checks().forEach(c => c.addEventListener('change', actualizar));
        destino.addEventListener('change', actualizar);
        actualizar();
    })();
</script>
@endsection
