@extends('layout.layout')

@section('title', 'Visor de documento')

@section('content')
<div class="max-w-6xl mx-auto space-y-4">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <span class="font-mono text-xs font-bold text-slate-500">{{ $origen->codigo_documento }}</span>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> v{{ $origen->version }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                    <i class="fas fa-file-word mr-1"></i> {{ $extension }}
                </span>
            </div>
            <h1 class="text-xl font-black tracking-tight text-slate-900 truncate mt-1">{{ $origen->nombre_documento }}</h1>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <button type="button" onclick="window.close()" class="px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-xs font-bold uppercase tracking-wider text-slate-600 hover:bg-slate-50 transition">
                Cerrar
            </button>
            <a href="{{ $urlDescarga }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider shadow-sm transition">
                <i class="fas fa-download"></i> Descargar
            </a>
        </div>
    </div>

    @if ($urlContenido)
        <div class="bg-slate-200/70 rounded-2xl border border-slate-200 shadow-sm overflow-auto" style="max-height: calc(100vh - 220px)">
            <div id="visor-estado" class="flex flex-col items-center justify-center gap-3 py-24 text-slate-500">
                <i class="fas fa-circle-notch fa-spin text-2xl"></i>
                <p class="text-xs font-bold uppercase tracking-wider">Cargando documento...</p>
            </div>
            <div id="visor-docx" class="py-6"></div>
        </div>
        <p class="text-[10px] text-slate-400">
            <i class="fas fa-circle-info mr-1"></i>Vista previa generada en el navegador. Algunos elementos avanzados de Word (macros, campos, ciertos gráficos) pueden verse distintos; para la versión exacta descarga el archivo.
        </p>
    @else
        {{-- .doc (Word 97-2003): formato binario que el navegador no puede interpretar --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-10 text-center space-y-4">
            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mx-auto text-2xl">
                <i class="fas fa-file-word"></i>
            </div>
            <div>
                <h2 class="font-bold text-slate-800">Vista previa no disponible</h2>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    Este archivo está en formato Word 97-2003 (.doc), que no se puede previsualizar en el navegador.
                    Descárgalo para abrirlo. Para futuras versiones, se recomienda guardarlo como .docx o PDF.
                </p>
            </div>
            <a href="{{ $urlDescarga }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold uppercase tracking-wider shadow-sm transition">
                <i class="fas fa-download"></i> Descargar archivo
            </a>
        </div>
    @endif
</div>

@if ($urlContenido)
    <style>
        /* Páginas de Word con sombra, centradas sobre el fondo gris */
        #visor-docx .docx-wrapper { background: transparent; padding: 0; }
        #visor-docx .docx-wrapper > section.docx { box-shadow: 0 1px 3px rgb(15 23 42 / .15); margin: 0 auto 1.5rem; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.3.7/dist/docx-preview.min.js"></script>
    <script>
        (async function () {
            const estado = document.getElementById('visor-estado');
            try {
                const respuesta = await fetch(@js($urlContenido), { credentials: 'same-origin' });
                if (!respuesta.ok) throw new Error('HTTP ' + respuesta.status);

                await docx.renderAsync(await respuesta.blob(), document.getElementById('visor-docx'), null, {
                    className: 'docx',
                    inWrapper: true,
                    ignoreLastRenderedPageBreak: true,
                    breakPages: true,
                    renderHeaders: true,
                    renderFooters: true,
                    renderFootnotes: true,
                });
                estado.remove();
            } catch (e) {
                console.error(e);
                estado.innerHTML = `
                    <i class="fas fa-triangle-exclamation text-2xl text-rose-500"></i>
                    <p class="text-xs font-bold text-slate-700">No se pudo mostrar la vista previa.</p>
                    <a href="{{ $urlDescarga }}" class="text-xs font-bold text-slate-900 underline">Descargar el archivo</a>`;
            }
        })();
    </script>
@endif
@endsection
