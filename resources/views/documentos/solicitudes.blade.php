@extends('layout.layout')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="md:flex md:items-center md:justify-between mb-6">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                {{ $documentoOrigen ? 'Solicitar Cambio / Nueva Versión' : 'Solicitar Alta de Documento' }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $documentoOrigen ? 'Origen: ' . $documentoOrigen->nombre . ' (v' . $documentoOrigen->version . ')' : 'Complete el formulario para proponer un nuevo documento.' }}
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('documentos.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancelar
            </a>
        </div>
    </div>

    @if ($errors->any())
    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded">
        <p class="font-bold">Por favor corrige los siguientes errores:</p>
        <ul class="list-disc ml-5 text-sm">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('documentos.solicitudes.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6 space-y-6">
        @csrf

        @if($documentoOrigen)
        <input type="hidden" name="documento_id" value="{{ $documentoOrigen->id }}">
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nombre del documento -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Nombre del Documento *</label>
                <input type="text" name="nombre_documento" value="{{ old('nombre_documento', $documentoOrigen->nombre ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Versión propuesta -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Versión Propuesta *</label>
                <input type="number" min="1" name="version" value="{{ old('version', $documentoOrigen ? $documentoOrigen->version + 1 : 1) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <!-- Tipo de Solicitud -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Tipo de Solicitud</label>
                <select name="tipo_solicitud_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seleccione tipo...</option>
                    @foreach($tiposSolicitud as $tipo)
                    <option value="{{ $tipo->id }}" {{ old('tipo_solicitud_id') == $tipo->id ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Nivel -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Nivel</label>
                <select name="nivel_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seleccione nivel...</option>
                    @foreach($niveles as $nivel)
                    <option value="{{ $nivel->id }}" {{ old('nivel_id', $documentoOrigen->nivel_id ?? '') == $nivel->id ? 'selected' : '' }}>{{ $nivel->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Subnivel -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Subnivel</label>
                <select name="subnivel_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seleccione subnivel...</option>
                    @foreach($subniveles as $sub)
                    <option value="{{ $sub->id }}" {{ old('subnivel_id', $documentoOrigen->subnivel_id ?? '') == $sub->id ? 'selected' : '' }}>{{ $sub->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Área -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Área</label>
                <select name="area_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seleccione área...</option>
                    @foreach($areas as $area)
                    <option value="{{ $area->id }}" {{ old('area_id', $documentoOrigen->area_id ?? '') == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Localidad -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Localidad</label>
                <select name="localidad_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seleccione localidad...</option>
                    @foreach($localidades as $loc)
                    <option value="{{ $loc->id }}" {{ old('localidad_id', $documentoOrigen->localidad_id ?? '') == $loc->id ? 'selected' : '' }}>{{ $loc->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Retención y Disposición -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Lugar de Retención</label>
                <select name="lugar_retencion_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seleccione...</option>
                    @foreach($lugaresRetencion as $lugar)
                    <option value="{{ $lugar->id }}" {{ old('lugar_retencion_id', $documentoOrigen->lugar_retencion_id ?? '') == $lugar->id ? 'selected' : '' }}>{{ $lugar->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Periodo de Retención</label>
                <select name="periodo_retencion_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seleccione...</option>
                    @foreach($periodosRetencion as $periodo)
                    <option value="{{ $periodo->id }}" {{ old('periodo_retencion_id', $documentoOrigen->periodo_retencion_id ?? '') == $periodo->id ? 'selected' : '' }}>{{ $periodo->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Archivo Adjunto (R2) -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Archivo del Documento (Max 10MB) *</label>
                <input type="file" name="archivo" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            <!-- Justificaciones -->
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Motivo del Cambio</label>
                <textarea name="motivo_cambio" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('motivo_cambio') }}</textarea>
            </div>

            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Descripción de Cambios Realizados</label>
                <textarea name="descripcion_cambios" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('descripcion_cambios') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-medium text-sm rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                Enviar Solicitud
            </button>
        </div>
    </form>
</div>
@endsection