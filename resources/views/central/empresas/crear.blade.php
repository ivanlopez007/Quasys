@extends('central.layout.admin')

@section('title', 'Nueva empresa')

@php
    $input = 'w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition';
    $inputError = 'border-rose-400 focus:border-rose-500 focus:ring-rose-500/10';
    $label = 'block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5';
    $prefijoBd = config('tenancy.database.prefix');
    $sufijoBd = config('tenancy.database.suffix');
@endphp

@section('content')
<div class="space-y-8">

    <div>
        <a href="{{ route('central.empresas.index') }}" class="text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-slate-700 transition">
            <i class="fas fa-arrow-left mr-1.5"></i> Empresas
        </a>
        <h1 class="text-3xl font-black tracking-tight text-slate-900 mt-2">Nueva empresa</h1>
        <p class="text-sm text-slate-500 mt-1">Se creará un espacio aislado con su propia base de datos, listo para usarse.</p>
    </div>

    @if (session('error'))
        <div class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <i class="fas fa-circle-exclamation mt-0.5"></i>
            <p class="font-semibold">{{ session('error') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
            <p class="font-bold mb-1"><i class="fas fa-circle-exclamation mr-1"></i> Revisa los siguientes campos:</p>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="form-empresa" method="POST" action="{{ route('central.empresas.post') }}" class="grid grid-cols-1 lg:grid-cols-3 gap-6" novalidate>
        @csrf

        <div class="lg:col-span-2 space-y-6">

            {{-- 1. Empresa --}}
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-slate-900 text-white text-xs font-black flex items-center justify-center">1</span>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">Datos de la empresa</h2>
                        <p class="text-[11px] text-slate-400">Cómo se identificará en el sistema</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="nombre_empresa" class="{{ $label }}">Nombre comercial <span class="text-rose-500">*</span></label>
                            <input id="nombre_empresa" name="nombre_empresa" value="{{ old('nombre_empresa') }}" maxlength="150" required autofocus
                                placeholder="Ej. Industrial Hefesto" class="{{ $input }} @error('nombre_empresa') {{ $inputError }} @enderror">
                        </div>
                        <div>
                            <label for="razon_social" class="{{ $label }}">Razón social</label>
                            <input id="razon_social" name="razon_social" value="{{ old('razon_social') }}" maxlength="200"
                                placeholder="Ej. Industrial Hefesto S.A. de C.V." class="{{ $input }}">
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-3 gap-5">
                        <div class="sm:col-span-2">
                            <label for="subdominio" class="{{ $label }}">Subdominio <span class="text-rose-500">*</span></label>
                            <div id="caja-subdominio" class="flex rounded-xl border border-slate-200 bg-white overflow-hidden focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-500/10 transition @error('subdominio') border-rose-400 @enderror">
                                <input id="subdominio" name="subdominio" value="{{ old('subdominio') }}" maxlength="30" required autocomplete="off" spellcheck="false"
                                    placeholder="hefesto" class="flex-1 min-w-0 bg-transparent px-4 py-3 text-sm font-mono text-slate-900 placeholder-slate-400 focus:outline-none">
                                <span class="flex items-center px-4 bg-slate-50 border-l border-slate-200 text-slate-400 text-sm font-mono select-none">.{{ $dominioCentral }}</span>
                            </div>
                            <p id="estado-subdominio" class="text-[11px] mt-1.5 text-slate-400">Minúsculas, números y guiones. Se genera a partir del nombre.</p>
                        </div>
                        <div>
                            <label for="rfc" class="{{ $label }}">RFC</label>
                            <input id="rfc" name="rfc" value="{{ old('rfc') }}" maxlength="13" placeholder="IHE000101XXX" class="{{ $input }} font-mono uppercase">
                        </div>
                    </div>

                    <div>
                        <span class="{{ $label }}">Plan <span class="text-rose-500">*</span></span>
                        <div class="grid sm:grid-cols-3 gap-3">
                            @foreach ($planes as $clave => [$nombre, $detalle, $icono])
                                <label class="relative cursor-pointer rounded-xl border border-slate-200 p-4 hover:border-slate-300 transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 has-[:checked]:ring-4 has-[:checked]:ring-emerald-500/10">
                                    <input type="radio" name="plan" value="{{ $clave }}" class="peer sr-only" @checked(old('plan', 'pro') === $clave)>
                                    <i class="fas {{ $icono }} text-slate-400 peer-checked:text-emerald-600"></i>
                                    <p class="mt-2 text-sm font-black text-slate-900">{{ $nombre }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $detalle }}</p>
                                    <i class="fas fa-circle-check absolute top-3 right-3 text-emerald-500 opacity-0 peer-checked:opacity-100 transition"></i>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- 2. Administrador --}}
            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-slate-900 text-white text-xs font-black flex items-center justify-center">2</span>
                    <div>
                        <h2 class="text-sm font-black text-slate-900">Administrador de la empresa</h2>
                        <p class="text-[11px] text-slate-400">Primer usuario, con acceso a todos los menús</p>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="admin_nombre" class="{{ $label }}">Nombre(s) <span class="text-rose-500">*</span></label>
                            <input id="admin_nombre" name="admin_nombre" value="{{ old('admin_nombre') }}" maxlength="100" required placeholder="Ej. Juan Carlos" class="{{ $input }} @error('admin_nombre') {{ $inputError }} @enderror">
                        </div>
                        <div>
                            <label for="admin_apellidos" class="{{ $label }}">Apellidos <span class="text-rose-500">*</span></label>
                            <input id="admin_apellidos" name="admin_apellidos" value="{{ old('admin_apellidos') }}" maxlength="100" required placeholder="Ej. Pérez Gómez" class="{{ $input }} @error('admin_apellidos') {{ $inputError }} @enderror">
                        </div>
                    </div>
                    <div>
                        <label for="email_admin" class="{{ $label }}">Correo de acceso <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-sm"></i>
                            <input id="email_admin" type="email" name="email_admin" value="{{ old('email_admin') }}" required placeholder="admin@empresa.com" class="{{ $input }} pl-11 @error('email_admin') {{ $inputError }} @enderror">
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label for="password_admin" class="{{ $label }}">Contraseña <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input id="password_admin" type="password" name="password_admin" required minlength="8" autocomplete="new-password" placeholder="Mínimo 8 caracteres" class="{{ $input }} pr-11 @error('password_admin') {{ $inputError }} @enderror">
                                <button type="button" data-ver="password_admin" class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Mostrar"><i class="fas fa-eye text-xs"></i></button>
                            </div>
                            <div class="mt-2 h-1.5 rounded-full bg-slate-100 overflow-hidden"><div id="barra-fuerza" class="h-full w-0 rounded-full transition-all"></div></div>
                            <p id="texto-fuerza" class="text-[11px] text-slate-400 mt-1">&nbsp;</p>
                        </div>
                        <div>
                            <label for="password_admin_confirmation" class="{{ $label }}">Confirmar contraseña <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input id="password_admin_confirmation" type="password" name="password_admin_confirmation" required autocomplete="new-password" placeholder="Repite la contraseña" class="{{ $input }} pr-11">
                                <button type="button" data-ver="password_admin_confirmation" class="absolute right-3 top-1/2 -translate-y-1/2 w-7 h-7 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition" title="Mostrar"><i class="fas fa-eye text-xs"></i></button>
                            </div>
                            <p id="texto-coincide" class="text-[11px] text-slate-400 mt-[1.375rem]">&nbsp;</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        {{-- Resumen --}}
        <aside class="space-y-4 lg:sticky lg:top-24 self-start">
            <div class="rounded-2xl bg-slate-900 text-white shadow-xl overflow-hidden relative">
                <div class="absolute -top-16 -right-16 w-48 h-48 rounded-full bg-emerald-500/20 blur-3xl"></div>
                <div class="relative p-6 space-y-5">
                    <div class="flex items-center gap-3">
                        <div id="avatar" class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-lg font-black uppercase">?</div>
                        <div class="min-w-0">
                            <p id="resumen-nombre" class="font-black truncate">Nueva empresa</p>
                            <p id="resumen-plan" class="text-[11px] text-slate-400">Plan Profesional</p>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="rounded-xl bg-white/5 border border-white/10 p-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">URL de acceso</p>
                            <p id="resumen-url" class="font-mono font-bold text-emerald-300 break-all mt-0.5">—</p>
                        </div>
                        <div class="rounded-xl bg-white/5 border border-white/10 p-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Base de datos</p>
                            <p id="resumen-bd" class="font-mono font-bold text-slate-200 break-all mt-0.5">—</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Se creará automáticamente</p>
                        <ul class="space-y-1.5 text-xs text-slate-300">
                            @php
                                $cfg = config('aprovisionamiento');
                                $numSubMenus = collect($cfg['menus'])->sum(fn ($m) => count($m[2]));
                                $pasos = [
                                    'Base de datos y tablas',
                                    count($cfg['menus']) . ' menús y ' . $numSubMenus . ' pantallas',
                                    'Roles: ' . implode(', ', array_keys($cfg['roles'])),
                                    'Niveles y subniveles ISO, áreas y localidad',
                                    'Retención, disposición final, estados y tipos',
                                    $cfg['planta_inicial'][0] . ' y usuario administrador',
                                ];
                            @endphp
                            @foreach ($pasos as $paso)
                                <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-400 text-[10px]"></i>{{ $paso }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <button id="btn-crear" type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-black uppercase tracking-wider shadow-lg shadow-emerald-600/20 transition disabled:opacity-60 disabled:cursor-wait">
                <i class="fas fa-rocket"></i> <span>Crear empresa</span>
            </button>
            <a href="{{ route('central.empresas.index') }}" class="block w-full text-center px-5 py-3 rounded-xl border border-slate-200 bg-white text-xs font-bold uppercase tracking-wider text-slate-500 hover:bg-slate-50 transition">Cancelar</a>
        </aside>
    </form>
</div>

{{-- Pantalla de espera mientras se crea la base de datos --}}
<div id="creando" class="hidden fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm w-full text-center">
        <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl"><i class="fas fa-circle-notch fa-spin"></i></div>
        <p class="mt-4 font-black text-slate-900">Creando la empresa...</p>
        <p id="creando-paso" class="text-sm text-slate-500 mt-1">Preparando la base de datos</p>
        <p class="text-[11px] text-slate-400 mt-3">Esto puede tardar unos segundos. No cierres esta ventana.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const $ = id => document.getElementById(id);
    const urlEjemplo = @js($urlEjemplo);
    const urlDisponible = @js(route('central.empresas.disponible'));
    const bd = slug => @js($prefijoBd) + slug + @js($sufijoBd);
    const planes = @js(collect($planes)->map(fn ($p) => $p[0]));

    const nombre = $('nombre_empresa'), sub = $('subdominio');
    let subEditado = sub.value !== '';
    let consulta = null, temporizador = null;

    const slug = t => t.toString().toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '')
        .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 30).replace(/-+$/, '');

    function resumen() {
        const s = sub.value;
        $('resumen-nombre').textContent = nombre.value.trim() || 'Nueva empresa';
        $('avatar').textContent = (nombre.value.trim()[0] || '?');
        $('resumen-url').textContent = s ? urlEjemplo.replace('__SUB__', s) : '—';
        $('resumen-bd').textContent = s ? bd(s) : '—';
        const plan = document.querySelector('input[name="plan"]:checked')?.value;
        $('resumen-plan').textContent = 'Plan ' + (planes[plan] || '');
    }

    function estado(texto, tipo) {
        const colores = { ok: 'text-emerald-600', error: 'text-rose-600', info: 'text-slate-400' };
        const iconos = { ok: 'fa-circle-check', error: 'fa-circle-xmark', info: 'fa-circle-notch fa-spin' };
        $('estado-subdominio').className = 'text-[11px] mt-1.5 font-semibold ' + colores[tipo];
        $('estado-subdominio').innerHTML = `<i class="fas ${iconos[tipo]} mr-1"></i>${texto}`;
        $('caja-subdominio').classList.toggle('border-rose-400', tipo === 'error');
    }

    function revisarDisponible() {
        clearTimeout(temporizador);
        if (!sub.value) return;
        estado('Comprobando disponibilidad...', 'info');
        temporizador = setTimeout(async () => {
            const valor = sub.value;
            consulta?.abort();
            consulta = new AbortController();
            try {
                const r = await fetch(urlDisponible + '?subdominio=' + encodeURIComponent(valor), { signal: consulta.signal, headers: { Accept: 'application/json' } });
                const d = await r.json();
                if (valor === sub.value) estado(d.mensaje, d.disponible ? 'ok' : 'error');
            } catch (e) { if (e.name !== 'AbortError') estado('No se pudo comprobar', 'error'); }
        }, 350);
    }

    nombre.addEventListener('input', () => {
        if (!subEditado) { sub.value = slug(nombre.value); revisarDisponible(); }
        resumen();
    });
    sub.addEventListener('input', () => {
        subEditado = sub.value !== '';
        const limpio = slug(sub.value.replace(/-$/, '')) + (sub.value.endsWith('-') ? '-' : '');
        if (sub.value !== limpio) sub.value = limpio;
        resumen(); revisarDisponible();
    });
    document.querySelectorAll('input[name="plan"]').forEach(r => r.addEventListener('change', resumen));

    // Mostrar / ocultar contraseña
    document.querySelectorAll('[data-ver]').forEach(b => b.addEventListener('click', () => {
        const campo = $(b.dataset.ver);
        campo.type = campo.type === 'password' ? 'text' : 'password';
        b.querySelector('i').className = 'fas text-xs ' + (campo.type === 'password' ? 'fa-eye' : 'fa-eye-slash');
    }));

    // Fuerza de la contraseña y coincidencia
    const pass = $('password_admin'), conf = $('password_admin_confirmation');
    function revisarPassword() {
        const v = pass.value;
        const puntos = [v.length >= 8, v.length >= 12, /[A-Z]/.test(v) && /[a-z]/.test(v), /\d/.test(v), /[^A-Za-z0-9]/.test(v)].filter(Boolean).length;
        const niveles = [['', 'w-0', '&nbsp;'], ['bg-rose-500', 'w-1/5', 'Muy débil'], ['bg-orange-500', 'w-2/5', 'Débil'], ['bg-amber-500', 'w-3/5', 'Aceptable'], ['bg-emerald-500', 'w-4/5', 'Fuerte'], ['bg-emerald-600', 'w-full', 'Muy fuerte']];
        const [color, ancho, texto] = v ? niveles[puntos] : niveles[0];
        $('barra-fuerza').className = `h-full rounded-full transition-all ${color} ${ancho}`;
        $('texto-fuerza').innerHTML = v && v.length < 8 ? 'Mínimo 8 caracteres' : texto;

        const t = $('texto-coincide');
        if (!conf.value) { t.innerHTML = '&nbsp;'; return; }
        const iguales = conf.value === v;
        t.className = 'text-[11px] mt-[1.375rem] font-semibold ' + (iguales ? 'text-emerald-600' : 'text-rose-600');
        t.innerHTML = `<i class="fas ${iguales ? 'fa-check' : 'fa-xmark'} mr-1"></i>${iguales ? 'Las contraseñas coinciden' : 'No coinciden'}`;
    }
    pass.addEventListener('input', revisarPassword);
    conf.addEventListener('input', revisarPassword);

    // Envío: pantalla de espera mientras se crea la base de datos
    $('form-empresa').addEventListener('submit', e => {
        if (!e.target.checkValidity()) { e.preventDefault(); e.target.reportValidity(); return; }
        $('btn-crear').disabled = true;
        $('creando').classList.remove('hidden');
        const pasos = ['Preparando la base de datos', 'Creando tablas', 'Cargando catálogos y menús', 'Creando el administrador'];
        let i = 0;
        setInterval(() => { i = Math.min(i + 1, pasos.length - 1); $('creando-paso').textContent = pasos[i]; }, 1800);
    });

    resumen();
    if (sub.value) revisarDisponible();
})();
</script>
@endpush
