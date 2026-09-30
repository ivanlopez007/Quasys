@extends('layout.layout')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Documentos Vigentes</h1>
            <p class="text-sm text-gray-600">Catálogo de documentos autorizados y actualizados.</p>
        </div>
        <a href="{{ route('documentos.solicitudes.create') }}"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
            + Nueva Solicitud
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <bg-gray-50 class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código / Nombre</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Versión</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área / Localidad</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel / Subnivel</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </bg-gray-50>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($documentos as $doc)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $doc->nombre }}</div>
                        <div class="text-xs text-gray-500">{{ $doc->codigo ?? 'ID: ' . $doc->id }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            v{{ $doc->version }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ $doc->area->nombre ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-400">{{ $doc->localidad->nombre ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div>{{ $doc->nivel->nombre ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-400">{{ $doc->subnivel->nombre ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <!-- Ver Archivo desde Cloudflare R2 -->
                        <a href="{{ route('documentos.archivo.ver', ['path' => $doc->url_documento]) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                            Ver Archivo
                        </a>

                        <!-- Ver Historial -->
                        <a href="{{ route('documentos.historial', $doc->id) }}" class="text-gray-600 hover:text-gray-900">
                            Historial
                        </a>

                        <!-- Solicitar Cambio -->
                        <a href="{{ route('documentos.solicitudes.create', ['documento_id' => $doc->id]) }}" class="text-amber-600 hover:text-amber-900">
                            Solicitar Cambio
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                        No hay documentos vigentes publicados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
            {{ $documentos->links() }}
        </div>
    </div>
</div>
@endsection