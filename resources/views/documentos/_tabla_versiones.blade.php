{{-- Tabla de versiones de un documento. Uso: @include('documentos._tabla_versiones', ['versiones' => $versiones]) --}}
<div class="overflow-x-auto">
    <table class="w-full text-left">
        <thead class="bg-slate-50/50 border-b border-slate-200">
            <tr class="text-[10px] font-bold uppercase tracking-wider text-slate-500">
                <th class="px-5 py-3">Versión</th>
                <th class="px-5 py-3">Nivel / Subnivel</th>
                <th class="px-5 py-3">Estado</th>
                <th class="px-5 py-3">Publicación</th>
                <th class="px-5 py-3">Autor / Aprobó</th>
                <th class="px-5 py-3">Cambios</th>
                <th class="px-5 py-3">Retención</th>
                <th class="px-5 py-3 text-right">Archivo</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($versiones as $v)
                <tr class="hover:bg-slate-50/80 transition align-top {{ $v->vigente ? '' : 'opacity-90' }}">
                    <td class="px-5 py-4">
                        <span class="inline-flex rounded-full bg-slate-100 text-slate-800 px-2.5 py-1 text-xs font-mono font-black">v{{ $v->version }}</span>
                    </td>
                    <td class="px-5 py-4 text-xs">@include('documentos._nivel', ['item' => $v])</td>
                    <td class="px-5 py-4">
                        @if ($v->vigente)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-800 px-2.5 py-1 text-[11px] font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Vigente
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 text-slate-600 px-2.5 py-1 text-[11px] font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Obsoleto
                            </span>
                            @if ($v->fecha_baja)
                                <p class="text-[11px] text-slate-400 mt-1">Baja: {{ $v->fecha_baja->format('d/m/Y') }}</p>
                            @endif
                        @endif
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-600 whitespace-nowrap">{{ $v->fecha_publicacion?->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-4 text-xs text-slate-600">
                        <p><span class="text-slate-400">Autor:</span> <span class="font-semibold">@include('documentos._persona', ['u' => $v->autor])</span></p>
                        <p class="mt-0.5"><span class="text-slate-400">Aprobó:</span> <span class="font-semibold">@include('documentos._persona', ['u' => $v->aprobador])</span></p>
                    </td>
                    <td class="px-5 py-4 text-xs text-slate-600 max-w-xs">
                        @if ($v->cambioOrigen?->descripcion_cambios || $v->cambioOrigen?->motivo_cambio)
                            @if ($v->cambioOrigen->descripcion_cambios)
                                <p class="font-semibold text-slate-700">{{ $v->cambioOrigen->descripcion_cambios }}</p>
                            @endif
                            @if ($v->cambioOrigen->motivo_cambio)
                                <p class="text-[11px] text-slate-400 mt-1">Motivo: {{ $v->cambioOrigen->motivo_cambio }}</p>
                            @endif
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                        @if ($v->motivo_baja && ! $v->vigente)
                            <p class="text-[11px] text-slate-400 mt-1">{{ $v->motivo_baja }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-xs whitespace-nowrap">
                        @if ($v->periodoRetencion)
                            <p class="font-semibold text-slate-700">{{ \App\Models\PeriodoRetencion::describir($v->tiempo_retencion, $v->periodoRetencion->periodo_retencion) }}</p>
                        @endif
                        @if ($v->fecha_fin_retencion)
                            <p class="text-[11px] {{ $v->retencionVencida() ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                {{ $v->retencionVencida() ? 'Venció' : 'Vence' }}: {{ $v->fecha_fin_retencion->format('d/m/Y') }}
                            </p>
                        @elseif ($v->vigente)
                            <p class="text-[11px] text-slate-400">Corre al quedar obsoleto</p>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <a href="{{ route('documentos.archivo.ver', ['path' => $v->url_documento]) }}" target="_blank" title="Ver archivo"
                            class="inline-flex w-8 h-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition"><i class="fas fa-eye text-xs"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
