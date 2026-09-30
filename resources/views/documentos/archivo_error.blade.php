@extends('layout.layout')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h2 class="mt-6 text-2xl font-extrabold text-gray-900">Archivo no disponible</h2>
        <p class="mt-2 text-sm text-gray-600">
            {{ $mensaje ?? 'No se pudo encontrar el archivo solicitado en el almacenamiento Cloudflare R2 o el enlace firmado ha expirado.' }}
        </p>
        <div class="mt-6">
            <a href="{{ route('documentos.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Volver a Documentos
            </a>
        </div>
    </div>
</div>
@endsection