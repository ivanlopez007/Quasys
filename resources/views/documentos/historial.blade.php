@extends('layout.layout')

@section('title', 'Historial de versiones')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="font-mono text-xs font-bold text-slate-500">{{ $documento->codigo_documento }}</p>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">{{ $documento->nombre_documento }}</h1>
            <p class="text-xs text-slate-500 mt-1">Historial de versiones · {{ $versiones->count() }} {{ $versiones->count() === 1 ? 'versión' : 'versiones' }}</p>
        </div>
        <a href="{{ route('documentos.index') }}" class="text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900 transition">
            <i class="fas fa-arrow-left mr-1.5"></i> Volver a la lista maestra
        </a>
    </div>

    <div class="space-y-3">@include('documentos._alertas')</div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @include('documentos._tabla_versiones', ['versiones' => $versiones])
    </div>
</div>
@endsection
