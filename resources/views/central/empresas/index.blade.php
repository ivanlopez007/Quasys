@extends('central.layout.admin')

@section('title', 'Empresas')

@php
    $planes = \App\Http\Controllers\TenantController::planes();
    $colorPlan = ['basico' => 'bg-slate-100 text-slate-700 border-slate-200', 'pro' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'empresarial' => 'bg-indigo-50 text-indigo-700 border-indigo-200'];
@endphp

@section('content')
<div class="space-y-8">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-emerald-600">Clientes</p>
            <h1 class="text-3xl font-black tracking-tight text-slate-900 mt-1">Empresas</h1>
            <p class="text-sm text-slate-500 mt-1">Cada empresa tiene su propia base de datos, usuarios y documentos.</p>
        </div>
        <a href="{{ route('central.empresas') }}" class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-slate-900/10 transition">
            <i class="fas fa-plus"></i> Nueva empresa
        </a>
    </div>

    {{-- Empresa recién creada --}}
    @if ($creada = session('creada'))
        <div class="relative overflow-hidden rounded-2xl border border-emerald-200 bg-white shadow-sm">
            <div class="absolute inset-y-0 left-0 w-1.5 bg-emerald-500"></div>
            <div class="p-6 pl-8 flex flex-col md:flex-row md:items-center gap-5">
                <div class="w-12 h-12 shrink-0 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fas fa-circle-check"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-black text-slate-900">¡{{ $creada['nombre'] }} está lista!</p>
                    <p class="text-sm text-slate-500 mt-0.5">Se creó su base de datos, los catálogos iniciales, los menús y el administrador.</p>
                    <div class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-xs">
                        <span class="text-slate-500">Acceso: <a href="{{ $creada['url'] }}" target="_blank" class="font-mono font-bold text-emerald-700 hover:underline">{{ $creada['url'] }}</a></span>
                        <span class="text-slate-500">Usuario: <span class="font-mono font-bold text-slate-800">{{ $creada['email'] }}</span></span>
                    </div>
                </div>
                <a href="{{ $creada['url'] }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold uppercase tracking-wider transition shrink-0">
                    Abrir <i class="fas fa-arrow-up-right-from-square"></i>
                </a>
            </div>
        </div>
    @endif

    {{-- Totales --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach ([
            ['Empresas', $totales['empresas'], 'fa-building', 'from-slate-800 to-slate-900 text-white', 'text-slate-400'],
            ['Usuarios activos', $totales['usuarios'], 'fa-users', 'from-white to-white text-slate-900', 'text-slate-400'],
            ['Documentos vigentes', $totales['documentos'], 'fa-file-lines', 'from-white to-white text-slate-900', 'text-slate-400'],
        ] as [$etiqueta, $valor, $icono, $fondo, $tenue])
            <div class="rounded-2xl border border-slate-200 bg-gradient-to-br {{ $fondo }} p-5 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider {{ $tenue }}">{{ $etiqueta }}</p>
                    <p class="text-3xl font-black mt-1">{{ number_format($valor) }}</p>
                </div>
                <i class="fas {{ $icono }} text-2xl {{ $tenue }}"></i>
            </div>
        @endforeach
    </div>

    {{-- Lista --}}
    @if ($empresas->isEmpty())
        <div class="rounded-2xl border-2 border-dashed border-slate-300 bg-white/60 px-6 py-16 text-center">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl"><i class="fas fa-building"></i></div>
            <p class="mt-4 font-bold text-slate-700">Aún no hay empresas</p>
            <p class="text-sm text-slate-500">Crea la primera para empezar.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($empresas as $e)
                <div class="group rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md hover:border-slate-300 transition overflow-hidden">
                    <div class="p-5 flex items-start gap-4">
                        <div class="w-12 h-12 shrink-0 rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 text-white flex items-center justify-center text-lg font-black uppercase">
                            {{ mb_substr($e->nombre, 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="font-black text-slate-900 truncate">{{ $e->nombre }}</p>
                                @if ($e->plan)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full border text-[10px] font-bold {{ $colorPlan[$e->plan] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                        <i class="fas {{ $planes[$e->plan][2] ?? 'fa-tag' }} text-[9px]"></i> {{ $planes[$e->plan][0] ?? ucfirst($e->plan) }}
                                    </span>
                                @endif
                            </div>
                            @if ($e->razon_social)
                                <p class="text-xs text-slate-500 truncate">{{ $e->razon_social }}</p>
                            @endif
                            @if ($e->url)
                                <a href="{{ $e->url }}/login" target="_blank" class="inline-flex items-center gap-1.5 mt-1 text-xs font-mono font-bold text-emerald-700 hover:underline">
                                    {{ $e->dominio }} <i class="fas fa-arrow-up-right-from-square text-[9px]"></i>
                                </a>
                            @else
                                <p class="mt-1 text-xs text-rose-600 font-bold"><i class="fas fa-triangle-exclamation mr-1"></i>Sin dominio</p>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-3 border-t border-slate-100 text-center divide-x divide-slate-100 bg-slate-50/60">
                        <div class="py-3">
                            <p class="text-lg font-black text-slate-900">{{ $e->ok ? $e->usuarios : '—' }}</p>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Usuarios</p>
                        </div>
                        <div class="py-3">
                            <p class="text-lg font-black text-slate-900">{{ $e->ok ? $e->documentos : '—' }}</p>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Documentos</p>
                        </div>
                        <div class="py-3">
                            <p class="text-xs font-bold text-slate-700 mt-1">{{ $e->creada?->format('d/m/Y') }}</p>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mt-1">Alta</p>
                        </div>
                    </div>
                    <div class="px-5 py-2.5 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                        <span><i class="fas fa-database mr-1"></i><span class="font-mono">{{ $e->base_datos }}</span></span>
                        @unless ($e->ok)
                            <span class="font-bold text-rose-600"><i class="fas fa-plug-circle-xmark mr-1"></i>Base de datos sin respuesta</span>
                        @endunless
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
