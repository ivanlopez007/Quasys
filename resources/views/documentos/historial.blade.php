@extends('layout.layout')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Historial de Revisiones</h1>
            <p class="text-sm text-gray-600">Documento: <strong class="text-gray-800">{{ $documento->nombre }}</strong> ({{ $documento->codigo ?? 'ID: ' . $documento->id }})</p>
        </div>
        <a href="{{ route('documentos.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 text-sm font-medium">
            Volver a Documentos
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Versión</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Solicitante</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Aprobador</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Motivo / Descripción</th>
                    <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-right font-medium text-gray-500 uppercase">Archivo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($historial as $cambio)
                <tr>
                    <td class="px-6 py-4 font-bold text-gray-900">
                        v{{ $cambio->version }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $cambio->solicitante->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $cambio->aprobar->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ ($cambio->estado->nombre ?? '') === 'Aprobado' ? 'bg-green-100 text-green-800' : '' }}
                                {{ ($cambio->estado->nombre ?? '') === 'Pendiente' ? 'bg-amber-100 text-amber-800' : '' }}
                                {{ ($cambio->estado->nombre ?? '') === 'Rechazado' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ $cambio->estado->nombre ?? 'Pendiente' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-500 max-w-xs truncate">
                        <div class="font-medium text-gray-700">{{ $cambio->motivo_cambio ?? 'Sin motivo especificado' }}</div>
                        <div class="text-xs text-gray-400">{{ $cambio->descripcion_cambios }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-500 text-xs">
                        {{ $cambio->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('documentos.archivo.ver', ['path' => $cambio->url_documento]) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 font-medium">
                            Descargar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay registros en el historial de este documento.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection