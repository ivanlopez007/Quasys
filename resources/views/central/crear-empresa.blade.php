@extends('central.layout.layout')

@section('title', 'Panel de Control - GateOps')
@section('header_title', 'REGISTRO DE NUEVA EMPRESA (TENANT)')

@section('content')

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col gap-1">
        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Administración Central</span>
        <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Dar de alta nueva empresa (Tenant)</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Crea un entorno de base de datos aislado y genera las credenciales de administrador para el nuevo cliente.
        </p>
    </div>

    @if ($errors->any())
    <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-xs font-semibold space-y-1">
        <div class="flex items-center gap-2 mb-1">
            <span class="material-icons-round text-base">error</span>
            <span>Por favor corrige los siguientes errores:</span>
        </div>
        <ul class="list-disc pl-5 space-y-0.5 font-normal">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('central.empresas.post') }}" method="POST" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm space-y-5">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                        <span class="material-icons-round text-emerald-600 dark:text-emerald-400">business</span>
                        Datos de la Empresa
                    </h3>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nombre_empresa" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nombre Comercial</label>
                            <input type="text" id="nombre_empresa" name="nombre_empresa" value="{{ old('nombre_empresa') }}" placeholder="Ej. Transportes del Norte"
                                class="w-full bg-slate-50 dark:bg-slate-950 border @error('nombre_empresa') border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror rounded-xl px-4 py-3 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all" required>
                        </div>

                        <div>
                            <label for="tax_name" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Razón Social</label>
                            <input type="text" id="tax_name" name="tax_name" value="{{ old('tax_name') }}" placeholder="Ej. Transportistas S.A. de C.V."
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label for="subdominio" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Subdominio / Identificador único</label>
                        <div class="flex rounded-xl border @error('subdominio') border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror bg-slate-50 dark:bg-slate-950 focus-within:border-emerald-500 focus-within:ring-1 focus-within:ring-emerald-500 transition-all overflow-hidden">
                            <input type="text" id="subdominio" name="subdominio" value="{{ old('subdominio') }}" placeholder="transportes-del-norte"
                                class="flex-1 bg-transparent px-4 py-3 text-slate-900 dark:text-white text-sm focus:outline-none" required>
                            <span class="flex items-center px-4 bg-slate-100 dark:bg-slate-800 border-l border-slate-200 dark:border-slate-800 text-slate-500 dark:text-slate-400 text-sm font-semibold select-none">
                                .gateops.com
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2">
                            Se usará para la ruta del inquilino y para aislar la base de datos (Ej: <span id="db_preview" class="font-mono text-emerald-600 dark:text-emerald-400">tenant_transportes_del_norte</span>).
                        </p>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label for="plan" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Plan de Suscripción</label>
                            <select id="plan" name="plan" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                                <option value="basic" {{ old('plan') == 'basic' ? 'selected' : '' }}>Básico (Hasta 50 unidades)</option>
                                <option value="pro" {{ old('plan') == 'pro' || !old('plan') ? 'selected' : '' }}>Profesional (Unidades ilimitadas)</option>
                                <option value="enterprise" {{ old('plan') == 'enterprise' ? 'selected' : '' }}>Enterprise (Soporte C-TPAT dedicado)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm space-y-5">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                        <span class="material-icons-round text-emerald-600 dark:text-emerald-400">admin_panel_settings</span>
                        Usuario Administrador Primario
                    </h3>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="admin_name" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nombre del Administrador</label>
                            <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name', 'Administrador') }}" placeholder="Ej. Juan Pérez"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all" required>
                        </div>

                        <div>
                            <label for="email_admin" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Correo de Acceso</label>
                            <input type="email" id="email_admin" name="email_admin" value="{{ old('email_admin') }}" placeholder="admin@empresa.com"
                                class="w-full bg-slate-50 dark:bg-slate-950 border @error('email_admin') border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror rounded-xl px-4 py-3 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all" required>
                        </div>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password_admin" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Contraseña Temporal</label>
                            <input type="password" id="password_admin" name="password_admin" placeholder="••••••••"
                                class="w-full bg-slate-50 dark:bg-slate-950 border @error('password_admin') border-rose-500 @else border-slate-200 dark:border-slate-800 @enderror rounded-xl px-4 py-3 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all" required>
                        </div>

                        <div>
                            <label for="password_admin_confirmation" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Confirmar Contraseña</label>
                            <input type="password" id="password_admin_confirmation" name="password_admin_confirmation" placeholder="••••••••"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all" required>
                        </div>
                    </div>

                    <div class="flex items-end pt-2">
                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <input type="checkbox" name="force_password_change" value="1" {{ old('force_password_change') || is_null(old('force_password_change')) ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500 h-4 w-4 bg-slate-50 dark:bg-slate-950">
                            <span class="text-xs font-semibold text-slate-600 dark:text-slate-400">Forzar cambio de contraseña en el primer inicio</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500/5 blur-3xl rounded-full"></div>

                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3 mb-4">
                        Resumen de Creación
                    </h3>

                    <div class="space-y-4">
                        <div class="p-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800/80">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">URL de Acceso Directo</span>
                            <span id="url_preview" class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 tracking-tight">https://[subdominio].gateops.com</span>
                        </div>

                        <div class="space-y-2.5">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Procesos de Aprovisionamiento</span>
                            <div class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400">
                                <span class="material-icons-round text-emerald-500 text-base">check_circle</span>
                                Creación de Base de Datos aislada
                            </div>
                            <div class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400">
                                <span class="material-icons-round text-emerald-500 text-base">check_circle</span>
                                Ejecución automática de Migraciones
                            </div>
                            <div class="flex items-center gap-2.5 text-xs text-slate-600 dark:text-slate-400">
                                <span class="material-icons-round text-emerald-500 text-base">check_circle</span>
                                Carga de Catálogos (Seeding Base)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3.5 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition-all shadow-md shadow-emerald-500/15">
                        <span class="material-icons-round text-lg">add_business</span>
                        Crear e Inicializar Tenant
                    </button>
                    <a href="#" class="w-full inline-flex items-center justify-center px-4 py-3.5 rounded-xl text-sm font-semibold bg-transparent hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 transition-all text-center">
                        Cancelar
                    </a>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const companyNameInput = document.getElementById('nombre_empresa'); // ID Actualizado
        const subdomainInput = document.getElementById('subdominio'); // ID Actualizado
        const urlPreview = document.getElementById('url_preview');
        const dbPreview = document.getElementById('db_preview');

        // Función para limpiar caracteres especiales de un string y hacerlo slug
        function generateSlug(text) {
            return text.toString().toLowerCase()
                .trim()
                .normalize('NFD') // Quita acentos de letras en español
                .replace(/[\u0300-\u036f]/g, "")
                .replace(/\s+/g, '-') // Reemplaza espacios por -
                .replace(/[^\w\-]+/g, '') // Remueve caracteres no alfanuméricos
                .replace(/\-\-+/g, '-') // Evita guiones dobles --
                .replace(/^-+/, '') // Quita guión inicial
                .replace(/-+$/, ''); // Quita guión final
        }

        // Si ya hay un valor previo debido a redirect withInput()
        if (subdomainInput.value) {
            updatePreviews(subdomainInput.value);
        }

        companyNameInput.addEventListener('input', (e) => {
            const slug = generateSlug(e.target.value);
            subdomainInput.value = slug;
            updatePreviews(slug);
        });

        subdomainInput.addEventListener('input', (e) => {
            const slug = generateSlug(e.target.value);
            subdomainInput.value = slug;
            updatePreviews(slug);
        });

        function updatePreviews(slug) {
            if (slug) {
                urlPreview.textContent = `https://${slug}.gateops.com`;
                dbPreview.textContent = `tenant_${slug.replace(/-/g, '_')}`;
            } else {
                urlPreview.textContent = 'https://[subdominio].gateops.com';
                dbPreview.textContent = 'tenant_database';
            }
        }
    });
</script>
@endsection