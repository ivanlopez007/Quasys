@extends('layout.layout')

@section('title', 'Nueva solicitud')

@php
    use App\Models\CambioDocumento;

    // Sin documento origen solo se puede pedir un documento NUEVO.
    // Con documento origen (vigente) solo Revisión o Eliminar.
    // Los tipos se reconocen por nombre (config documentos.tipos), no por ID.
    // Con un documento precargado solo tiene sentido Revisión o Eliminar.
    $tipos = $tiposSolicitud->filter(fn ($t) => in_array(
        CambioDocumento::claveDeTipo($t->id),
        $documentoOrigen
            ? [CambioDocumento::TIPO_REVISION, CambioDocumento::TIPO_ELIMINAR]
            : [CambioDocumento::TIPO_NUEVO, CambioDocumento::TIPO_REVISION, CambioDocumento::TIPO_ELIMINAR],
        true
    ));

    // Plantas: lo enviado, las del documento origen o, en un documento nuevo, la planta del usuario.
    $plantasMarcadas = array_map('intval', old('plantas', $documentoOrigen
        ? $documentoOrigen->plantas->pluck('id')->all()
        : array_filter([auth()->user()->planta_id])));

    $tipoActual = old('tipo_solicitud_id', request('tipo', $tipos->count() === 1 ? $tipos->first()?->id : null));

    $input = 'w-full rounded-xl border border-gray-300 px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900';
    $label = 'block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5';

    // [campo, etiqueta, colección, texto de la opción, atributos extra de la opción]
    $selects = [
        ['nivel_id', 'Nivel', $niveles, fn ($n) => $n->nivel . ' · ' . $n->nombre, fn ($n) => ''],
        ['subnivel_id', 'Subnivel', $subniveles, fn ($n) => $n->nombre, fn ($n) => 'data-nivel=' . $n->nivel_id],
        ['localidad_id', 'Localidad', $localidades, fn ($n) => $n->localidad, fn ($n) => ''],
        ['area_id', 'Área', $areas, fn ($n) => $n->area, fn ($n) => ''],
        ['lugar_retencion_id', 'Lugar de retención', $lugaresRetencion, fn ($n) => $n->lugar_retencion, fn ($n) => ''],
        ['disposicion_final_id', 'Disposición final', $disposicionesFinales, fn ($n) => $n->disposicion_final, fn ($n) => ''],
    ];
@endphp

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900">Nueva solicitud</h1>
            <p class="text-xs text-slate-500 mt-1">La solicitud quedará pendiente hasta que sea aprobada</p>
        </div>
        <a href="{{ route('documentos.index') }}" class="text-xs font-bold uppercase tracking-wider text-slate-500 hover:text-slate-900 transition">
            <i class="fas fa-arrow-left mr-1.5"></i> Volver a la lista maestra
        </a>
    </div>

    <div class="space-y-3">@include('documentos._alertas')</div>

    <form method="POST" action="{{ route('documentos.solicitudes.store') }}" enctype="multipart/form-data"
        class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf

        <div class="bg-slate-800 px-6 py-4">
            <h2 class="text-white text-sm font-bold uppercase tracking-wider">Datos de la solicitud</h2>
        </div>

        <div class="p-6 space-y-6">

            @if ($documentoOrigen)
                <input type="hidden" name="documento_id" value="{{ $documentoOrigen->id }}">
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Documento vigente</p>
                        <p class="font-mono text-xs font-bold text-slate-800 mt-0.5">{{ $documentoOrigen->codigo_documento }}</p>
                        <p class="text-sm text-slate-700">{{ $documentoOrigen->nombre_documento }}</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 self-start rounded-full bg-emerald-50 text-emerald-800 px-2.5 py-1 text-[11px] font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> v{{ $documentoOrigen->version }} vigente
                    </span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="tipo_solicitud_id" class="{{ $label }}">Tipo de solicitud</label>
                    <select id="tipo_solicitud_id" name="tipo_solicitud_id" onchange="aplicarTipo()" class="{{ $input }}">
                        @if ($tipos->count() !== 1) <option value="">Selecciona...</option> @endif
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id }}" data-clave="{{ CambioDocumento::claveDeTipo($tipo->id) }}" @selected($tipoActual == $tipo->id)>{{ $tipo->tipo_solicitud }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- REVISIÓN / ELIMINAR sin documento precargado: elegir el documento vigente --}}
            @unless ($documentoOrigen)
                <div data-tipos="{{ CambioDocumento::TIPO_REVISION }},{{ CambioDocumento::TIPO_ELIMINAR }}" class="hidden">
                    <label for="documento_id" class="{{ $label }}">Documento vigente</label>
                    @if ($documentosVigentes->isEmpty())
                        <p class="text-xs text-slate-500 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <i class="fas fa-circle-info mr-1"></i>No hay documentos vigentes asignados a tu planta.
                        </p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                <input type="text" id="buscar-documento" placeholder="Buscar por código o nombre..." autocomplete="off" class="{{ $input }} pl-9">
                            </div>
                            <select id="documento_id" name="documento_id" onchange="precargarDocumento()" class="{{ $input }} md:col-span-2 font-mono">
                                <option value="">Selecciona el documento...</option>
                                @foreach ($documentosVigentes as $doc)
                                    @php
                                        $pendiente = isset($codigosPendientes[$doc->codigo_documento]);
                                        // Datos que se precargan en el formulario al elegir este documento
                                        $datosDoc = $doc->only(['nombre_documento', 'nivel_id', 'subnivel_id', 'localidad_id', 'area_id', 'lugar_retencion_id', 'disposicion_final_id', 'periodo_retencion_id']) + [
                                            'fecha_proxima_revision' => $doc->fecha_proxima_revision?->toDateString(),
                                            'plantas' => $doc->plantas->pluck('id'),
                                        ];
                                    @endphp
                                    <option value="{{ $doc->id }}" @selected(old('documento_id') == $doc->id) @disabled($pendiente) data-codigo="{{ $doc->codigo_documento }}"
                                        data-busqueda="{{ Str::lower($doc->codigo_documento . ' ' . $doc->nombre_documento) }}"
                                        data-datos='@json($datosDoc)'>
                                        {{ $doc->codigo_documento }} · v{{ $doc->version }} · {{ $doc->nombre_documento }}{{ $pendiente ? ' (con solicitud pendiente)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1">Solo aparecen los documentos vigentes de tu planta. Los que ya tienen una solicitud pendiente no se pueden elegir.</p>
                    @endif
                </div>
            @endunless

            {{-- Aviso Eliminar --}}
            <div data-tipos="{{ CambioDocumento::TIPO_ELIMINAR }}" class="hidden rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-xs text-rose-800">
                <i class="fas fa-triangle-exclamation mr-1.5"></i>
                Al aprobarse, el documento pasará a <strong>obsoleto</strong>. El archivo no se borra: se conserva durante su periodo de retención.
            </div>

            {{-- Solo NUEVO: código --}}
            <div data-tipos="{{ CambioDocumento::TIPO_NUEVO }}" class="hidden grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="codigo_documento" class="{{ $label }}">Código del documento</label>
                    <input id="codigo_documento" type="text" name="codigo_documento" value="{{ old('codigo_documento') }}" maxlength="100"
                        placeholder="Ej. PR-CAL-001" class="{{ $input }} font-mono uppercase">
                    <p class="text-[10px] text-slate-400 mt-1">Letras, números, punto, guion y guion bajo. Sin espacios.</p>
                </div>
            </div>

            {{-- NUEVO y REVISIÓN: nombre y archivo --}}
            <div data-tipos="{{ CambioDocumento::TIPO_NUEVO }},{{ CambioDocumento::TIPO_REVISION }}" class="hidden grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label for="nombre_documento" class="{{ $label }}">Nombre del documento</label>
                    <input id="nombre_documento" type="text" name="nombre_documento" maxlength="255"
                        value="{{ old('nombre_documento', $documentoOrigen?->nombre_documento) }}" class="{{ $input }}">
                </div>
                <div>
                    <label for="archivo" class="{{ $label }}">Archivo</label>
                    <input id="archivo" type="file" name="archivo" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg"
                        class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-900 file:px-3 file:py-2.5 file:text-[11px] file:font-bold file:uppercase file:tracking-wider file:text-white hover:file:bg-slate-700 rounded-xl border border-gray-300">
                    <p class="text-[10px] text-slate-400 mt-1">PDF, Word, Excel, PNG o JPG. Máx. 10 MB.</p>
                </div>
            </div>

            {{-- NUEVO y REVISIÓN: clasificación y retención --}}
            <div data-tipos="{{ CambioDocumento::TIPO_NUEVO }},{{ CambioDocumento::TIPO_REVISION }}" class="hidden">
                <p class="text-[11px] font-black uppercase tracking-wider text-slate-400 mb-3 pb-2 border-b border-slate-100">Clasificación y retención</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($selects as [$campo, $etiqueta, $coleccion, $texto, $extra])
                        <div>
                            <label for="{{ $campo }}" class="{{ $label }}">{{ $etiqueta }}</label>
                            <select id="{{ $campo }}" name="{{ $campo }}" class="{{ $input }}">
                                <option value="">Selecciona...</option>
                                @foreach ($coleccion as $item)
                                    <option value="{{ $item->id }}" {!! $extra($item) !!} @selected(old($campo, $documentoOrigen?->{$campo}) == $item->id)>{{ $texto($item) }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach

                    <div>
                        <label for="periodo_retencion_id" class="{{ $label }}">Periodo de retención</label>
                        <select id="periodo_retencion_id" name="periodo_retencion_id" class="{{ $input }}">
                            <option value="">Selecciona...</option>
                            @foreach ($periodosRetencion as $p)
                                <option value="{{ $p->id }}" @selected(old('periodo_retencion_id', $documentoOrigen?->periodo_retencion_id) == $p->id)>{{ $p->etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="fecha_proxima_revision" class="{{ $label }}">Próxima revisión (opcional)</label>
                        <input id="fecha_proxima_revision" type="date" name="fecha_proxima_revision" min="{{ now()->addDay()->toDateString() }}"
                            value="{{ old('fecha_proxima_revision', $documentoOrigen?->fecha_proxima_revision?->toDateString()) }}" class="{{ $input }}">
                    </div>
                </div>
                <p class="text-[10px] text-slate-400 mt-3"><i class="fas fa-circle-info mr-1"></i>La retención se cuenta desde que esta versión deja de estar vigente.</p>
            </div>

            {{-- NUEVO y REVISIÓN: plantas que pueden verlo --}}
            <div data-tipos="{{ CambioDocumento::TIPO_NUEVO }},{{ CambioDocumento::TIPO_REVISION }}" class="hidden">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                    <p class="text-[11px] font-black uppercase tracking-wider text-slate-400">Plantas que pueden verlo</p>
                    <label class="inline-flex items-center gap-2 text-[11px] font-bold text-slate-500 cursor-pointer select-none">
                        <input type="checkbox" id="plantas-todas" class="rounded border-gray-300 text-slate-900 focus:ring-slate-900"> Seleccionar todas
                    </label>
                </div>
                @if ($plantas->isEmpty())
                    <p class="text-xs text-rose-600"><i class="fas fa-triangle-exclamation mr-1"></i>No hay plantas registradas. Da de alta al menos una en el catálogo de Plantas.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach ($plantas as $planta)
                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-2.5 cursor-pointer hover:bg-slate-50 transition has-[:checked]:border-slate-900 has-[:checked]:bg-slate-50">
                                <input type="checkbox" name="plantas[]" value="{{ $planta->id }}" class="planta-check rounded border-gray-300 text-slate-900 focus:ring-slate-900"
                                    @checked(in_array($planta->id, $plantasMarcadas, true))>
                                <span class="min-w-0">
                                    <span class="block text-sm font-semibold text-slate-800 truncate">{{ $planta->planta }}</span>
                                    @if ($planta->ubicacion)
                                        <span class="block text-[10px] text-slate-400 truncate">{{ $planta->ubicacion }}</span>
                                    @endif
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif
                <p class="text-[10px] text-slate-400 mt-3"><i class="fas fa-circle-info mr-1"></i>Solo los usuarios de las plantas marcadas verán este documento en la Lista Maestra.</p>
            </div>

            {{-- Motivo (todos los tipos) --}}
            <div data-tipos="{{ CambioDocumento::TIPO_ELIMINAR }},{{ CambioDocumento::TIPO_NUEVO }},{{ CambioDocumento::TIPO_REVISION }}" class="hidden">
                <label for="motivo_cambio" class="{{ $label }}">
                    <span data-tipos-label="{{ CambioDocumento::TIPO_NUEVO }}">Motivo (opcional)</span>
                    <span data-tipos-label="{{ CambioDocumento::TIPO_REVISION }},{{ CambioDocumento::TIPO_ELIMINAR }}" class="hidden">Motivo (obligatorio)</span>
                </label>
                <textarea id="motivo_cambio" name="motivo_cambio" rows="3" maxlength="2000" class="{{ $input }}">{{ old('motivo_cambio') }}</textarea>
            </div>

            {{-- Solo REVISIÓN: qué cambió --}}
            <div data-tipos="{{ CambioDocumento::TIPO_REVISION }}" class="hidden">
                <label for="descripcion_cambios" class="{{ $label }}">Descripción de los cambios</label>
                <textarea id="descripcion_cambios" name="descripcion_cambios" rows="4" maxlength="5000"
                    placeholder="Qué se modificó respecto a la versión vigente..." class="{{ $input }}">{{ old('descripcion_cambios') }}</textarea>
            </div>

            {{-- REVISIÓN / ELIMINAR: todas las versiones del documento elegido --}}
            <div data-tipos="{{ CambioDocumento::TIPO_REVISION }},{{ CambioDocumento::TIPO_ELIMINAR }}" class="hidden">
                <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                    <p class="text-[11px] font-black uppercase tracking-wider text-slate-400">Versiones del documento</p>
                    <a id="link-historial" href="{{ $documentoOrigen ? route('documentos.historial', $documentoOrigen->codigo_documento) : '#' }}" target="_blank"
                        class="{{ $documentoOrigen ? '' : 'hidden' }} text-[11px] font-bold text-slate-500 hover:text-slate-900 transition">
                        <i class="fas fa-up-right-from-square mr-1"></i> Abrir historial
                    </a>
                </div>
                <div id="tabla-versiones" class="rounded-xl border border-slate-200 overflow-hidden">
                    @if ($documentoOrigen)
                        @include('documentos._tabla_versiones', ['versiones' => $versionesOrigen])
                    @else
                        <p class="px-4 py-8 text-center text-xs text-slate-400">
                            <i class="fas fa-clock-rotate-left mr-1"></i> Elige un documento vigente para ver sus versiones.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-200 flex justify-end gap-2">
            <a href="{{ route('documentos.index') }}" class="px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-xs font-bold uppercase tracking-wider text-slate-600 hover:bg-slate-50 transition">Cancelar</a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 text-xs font-bold uppercase tracking-wider text-white hover:bg-slate-700 transition shadow-sm">
                <i class="fas fa-paper-plane mr-1.5"></i> Enviar solicitud
            </button>
        </div>
    </form>
</div>

<script>
    // Muestra solo las secciones del tipo elegido y deshabilita los campos ocultos
    // para que no se envíen (por ejemplo, el archivo en una solicitud de baja).
    function aplicarTipo() {
        // Clave del tipo (nuevo / revision / eliminar), independiente del ID del catálogo
        const tipo = document.getElementById('tipo_solicitud_id').selectedOptions[0]?.dataset.clave || '';

        document.querySelectorAll('[data-tipos]').forEach(sec => {
            const visible = tipo !== '' && sec.dataset.tipos.split(',').includes(tipo);
            sec.classList.toggle('hidden', !visible);
            sec.querySelectorAll('input, select, textarea').forEach(el => el.disabled = !visible);
        });

        document.querySelectorAll('[data-tipos-label]').forEach(el => {
            el.classList.toggle('hidden', !el.dataset.tiposLabel.split(',').includes(tipo));
        });

        filtrarSubniveles();
    }

    // El subnivel depende del nivel elegido
    function filtrarSubniveles() {
        const nivel = document.getElementById('nivel_id').value;
        const sub = document.getElementById('subnivel_id');

        Array.from(sub.options).forEach(op => {
            if (!op.dataset.nivel) return;
            const ok = nivel !== '' && op.dataset.nivel === nivel;
            op.hidden = !ok;
            op.disabled = !ok;
        });

        if (sub.selectedOptions[0] && sub.selectedOptions[0].disabled) sub.value = '';
    }

    // Al elegir un documento vigente, precarga sus datos (nombre, clasificación, retención y plantas)
    function precargarDocumento() {
        const opcion = document.getElementById('documento_id')?.selectedOptions[0];
        cargarVersiones(opcion?.dataset.codigo);
        if (!opcion || !opcion.dataset.datos) return;

        const datos = JSON.parse(opcion.dataset.datos);
        Object.entries(datos).forEach(([campo, valor]) => {
            if (campo === 'plantas') return;
            const el = document.getElementById(campo);
            if (el) el.value = valor ?? '';
        });

        filtrarSubniveles();
        const subnivel = document.getElementById('subnivel_id');
        if (subnivel) subnivel.value = datos.subnivel_id ?? '';

        const plantas = (datos.plantas || []).map(String);
        document.querySelectorAll('.planta-check').forEach(c => c.checked = plantas.includes(c.value));
        if (typeof sincronizarTodas === 'function') sincronizarTodas();
    }

    // Trae la tabla de versiones del documento elegido
    const urlVersiones = @js(route('documentos.versiones', ['codigo' => '__CODIGO__']));
    const urlHistorial = @js(route('documentos.historial', ['codigo' => '__CODIGO__']));
    let versionesPeticion = 0;

    async function cargarVersiones(codigo) {
        const contenedor = document.getElementById('tabla-versiones');
        const link = document.getElementById('link-historial');
        if (!contenedor || !document.getElementById('documento_id')) return;

        const mensaje = (icono, texto) =>
            `<p class="px-4 py-8 text-center text-xs text-slate-400"><i class="fas ${icono} mr-1"></i> ${texto}</p>`;

        if (!codigo) {
            contenedor.innerHTML = mensaje('fa-clock-rotate-left', 'Elige un documento vigente para ver sus versiones.');
            link.classList.add('hidden');
            return;
        }

        const peticion = ++versionesPeticion;
        contenedor.innerHTML = mensaje('fa-circle-notch fa-spin', 'Cargando versiones...');
        link.href = urlHistorial.replace('__CODIGO__', encodeURIComponent(codigo));
        link.classList.remove('hidden');

        try {
            const respuesta = await fetch(urlVersiones.replace('__CODIGO__', encodeURIComponent(codigo)), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!respuesta.ok) throw new Error('HTTP ' + respuesta.status);
            const html = await respuesta.text();
            if (peticion === versionesPeticion) contenedor.innerHTML = html; // ignora respuestas viejas
        } catch (e) {
            if (peticion === versionesPeticion) contenedor.innerHTML = mensaje('fa-triangle-exclamation', 'No se pudieron cargar las versiones.');
        }
    }

    // Si se regresó al formulario con un documento ya elegido (error de validación), muestra sus versiones
    if (document.getElementById('documento_id')?.value) cargarVersiones(document.getElementById('documento_id').selectedOptions[0]?.dataset.codigo);

    // Filtra la lista de documentos vigentes mientras se escribe
    document.getElementById('buscar-documento')?.addEventListener('input', e => {
        const texto = e.target.value.trim().toLowerCase();
        document.querySelectorAll('#documento_id option[data-busqueda]').forEach(op => {
            op.hidden = texto !== '' && !op.dataset.busqueda.includes(texto);
        });
    });

    // "Seleccionar todas" las plantas
    const plantasTodas = document.getElementById('plantas-todas');
    const plantasChecks = () => Array.from(document.querySelectorAll('.planta-check'));
    const sincronizarTodas = () => {
        const checks = plantasChecks();
        plantasTodas.checked = checks.length > 0 && checks.every(c => c.checked);
    };
    plantasTodas?.addEventListener('change', () => plantasChecks().forEach(c => c.checked = plantasTodas.checked));
    plantasChecks().forEach(c => c.addEventListener('change', sincronizarTodas));
    if (plantasTodas) sincronizarTodas();

    document.getElementById('nivel_id').addEventListener('change', filtrarSubniveles);
    aplicarTipo();
</script>
@endsection
