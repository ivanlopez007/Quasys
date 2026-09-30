<!DOCTYPE html>
<html lang="es" class="h-full dark" id="html-tag">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GateOps | Software de Administración Logística y Seguridad C-TPAT/OEA</title>

    <!-- Material Icons Round -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>

    <style>
        .material-icons-round {
            font-size: 24px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            vertical-align: middle;
        }
    </style>
</head>

<body class="min-h-full text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 font-sans antialiased transition-colors duration-200">

    <!-- Navegación -->
    <header class="sticky top-0 z-50 backdrop-blur-md bg-white/80 dark:bg-slate-950/85 border-b border-slate-200 dark:border-slate-800/80 transition-colors">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <!-- Logotipo unificado con tu panel -->
                <div class="p-1.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg border border-emerald-500/20 flex items-center justify-center">
                    <span class="material-icons-round text-xl">shield</span>
                </div>
                <div>
                    <span class="text-sm font-bold tracking-tight text-slate-900 dark:text-white">GATE<span class="text-emerald-500">OPS</span></span>
                    <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Plataforma Logística</p>
                </div>
            </div>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600 dark:text-slate-300">
                <a href="#modulos" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Módulos</a>
                <a href="#cumplimiento" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">C-TPAT & OEA</a>
                <a href="#contacto" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Contacto</a>
            </nav>

            <div class="flex items-center gap-4">
                <!-- Toggle de Tema (Claro/Oscuro) idéntico a tu panel -->
                <button id="btn-toggle-theme" class="rounded-xl border border-slate-200 dark:border-slate-800 p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900 transition flex items-center justify-center">
                    <div class="hidden dark:block">
                        <span class="material-icons-round text-lg">light_mode</span>
                    </div>
                    <div class="block dark:hidden">
                        <span class="material-icons-round text-lg">dark_mode</span>
                    </div>
                </button>

                <a href="#contacto" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition-all shadow-sm shadow-emerald-500/20">
                    Agendar Demo
                </a>
            </div>
        </div>
    </header>

    <!-- Sección Hero -->
    <section class="relative overflow-hidden pt-20 pb-16 lg:pt-28 lg:pb-24 border-b border-slate-200 dark:border-slate-900">
        <!-- Efecto de iluminación sutil de fondo -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-emerald-500/10 via-transparent to-transparent pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 mb-6">
                    <span class="material-icons-round text-sm">share</span> Multi-Cliente & Multi-Tenant
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-none mb-6">
                    Centraliza, protege y optimiza la operación de tu flota en un solo lugar
                </h1>
                <p class="text-base sm:text-lg text-slate-600 dark:text-slate-400 font-normal leading-relaxed mb-10 max-w-2xl mx-auto">
                    El software de administración para empresas transportistas que integra control de personal, seguridad C-TPAT/OEA, pre-nómina e intercambios de trailers en tiempo real.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="#contacto" class="w-full sm:w-auto px-8 py-3.5 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white text-center transition-all shadow-md shadow-emerald-500/15">
                        Agendar Demo Sin Costo
                    </a>
                    <a href="#modulos" class="w-full sm:w-auto px-8 py-3.5 rounded-xl text-sm font-semibold bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 text-center border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all">
                        Explorar Módulos
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección El Problema -->
    <section class="py-16 bg-white dark:bg-slate-900/30 border-b border-slate-200 dark:border-slate-900">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-6">¿Sigue tu operación dispersa en decenas de archivos de Excel?</h2>
            <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-base sm:text-lg">
                El control manual en el sector logístico cuesta tiempo, dinero y pone en riesgo tus certificaciones. Si un operador no cuenta con su documentación al día o si pierdes la trazabilidad de un intercambio en caseta, tu negocio se expone. <span class="text-emerald-600 dark:text-emerald-400 font-semibold">GateOps digitaliza y automatiza el control de tus patios para que operes con total tranquilidad.</span>
            </p>
        </div>
    </section>

    <!-- Sección Módulos (Usando tus iconos de Google) -->
    <section id="modulos" class="py-20 max-w-7xl mx-auto px-6">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight mb-4">Módulos del Sistema</h2>
            <p class="text-slate-500 dark:text-slate-400">Todo lo necesario para el transporte, control de caseta y cumplimiento internacional.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Módulo 1 -->
            <div class="bg-white dark:bg-slate-900/40 p-8 rounded-2xl border border-slate-200 dark:border-slate-800/80 hover:border-emerald-500/30 dark:hover:border-emerald-500/30 transition-all shadow-sm">
                <div class="w-12 h-12 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center mb-6 border border-emerald-500/10">
                    <span class="material-icons-round">badge</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Altas, Bajas y Nómina</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Digitaliza los expedientes completos de operadores y personal administrativo. Controla incidencias, cálculos de viajes para pre-nómina y recibe alertas automáticas antes del vencimiento de licencias federales o exámenes médicos.
                </p>
            </div>

            <!-- Módulo 2 -->
            <div class="bg-white dark:bg-slate-900/40 p-8 rounded-2xl border border-slate-200 dark:border-slate-800/80 hover:border-emerald-500/30 dark:hover:border-emerald-500/30 transition-all shadow-sm">
                <div class="w-12 h-12 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center mb-6 border border-emerald-500/10">
                    <span class="material-icons-round">fact_check</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Seguridad y Antidopings</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Agiliza y registra las pruebas de confianza y controles de antidoping aleatorios de tus choferes. Mantén un historial auditable que respalde la confiabilidad de tu equipo ante autoridades y clientes de exportación.
                </p>
            </div>

            <!-- Módulo 3 -->
            <div class="bg-white dark:bg-slate-900/40 p-8 rounded-2xl border border-slate-200 dark:border-slate-800/80 hover:border-emerald-500/30 dark:hover:border-emerald-500/30 transition-all shadow-sm">
                <div class="w-12 h-12 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center mb-6 border border-emerald-500/10">
                    <span class="material-icons-round">local_shipping</span>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-3">Intercambios de Trailers</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Facilita el control de entradas y salidas en caseta. Realiza inspecciones de seguridad digitalizadas y auditorías rápidas de sellos para asegurar que cada contenedor salga en óptimas condiciones.
                </p>
            </div>
        </div>
    </section>

    <!-- Sección Cumplimiento C-TPAT y OEA -->
    <section id="cumplimiento" class="py-20 bg-white dark:bg-slate-900/20 border-y border-slate-200 dark:border-slate-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="text-emerald-600 dark:text-emerald-400 text-xs font-bold tracking-widest uppercase mb-3 block">Estándares Internacionales</span>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight mb-6">Listo para Auditorías C-TPAT y OEA</h2>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-8">
                        Las empresas transportistas de exportación deben cumplir rigurosos lineamientos de seguridad en la cadena de suministro. Con GateOps, dejas atrás el papeleo físico en caseta y organizas las evidencias bajo estándares internacionales.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <span class="material-icons-round text-emerald-600 dark:text-emerald-400 mt-0.5">verified_user</span>
                            <div>
                                <h4 class="text-slate-900 dark:text-white font-semibold text-sm">Inspecciones de Seguridad Digitales</h4>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">Registro de sellos de seguridad, firmas electrónicas y listas de verificación al alcance de un clic.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-icons-round text-emerald-600 dark:text-emerald-400 mt-0.5">cloud_done</span>
                            <div>
                                <h4 class="text-slate-900 dark:text-white font-semibold text-sm">Información Centralizada</h4>
                                <p class="text-slate-500 dark:text-slate-400 text-sm">Cada uno de tus clientes o sucursales cuenta con su propio entorno seguro (<code class="text-emerald-600 dark:text-emerald-400 bg-slate-100 dark:bg-slate-800 px-1.5 py-0.5 rounded text-xs">empresa.gateops.com</code>).</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-100 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 p-8 rounded-2xl relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-emerald-500/5 blur-3xl rounded-full"></div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Beneficios Clave</h3>
                    <div class="space-y-4">
                        <div class="p-4 bg-white dark:bg-slate-950/80 rounded-xl border border-slate-200 dark:border-slate-800/60 shadow-sm">
                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm block mb-1">Operación Multi-Tenant</span>
                            Bases de datos aisladas para resguardar con máxima privacidad la información sensible de tus operaciones y clientes.
                        </div>
                        <div class="p-4 bg-white dark:bg-slate-950/80 rounded-xl border border-slate-200 dark:border-slate-800/60 shadow-sm">
                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm block mb-1">Respuestas al Instante</span>
                            Monitorea el estatus de las inspecciones en tus patios y las incidencias de los choferes desde cualquier dispositivo móvil en tiempo real.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sección Contacto / Cero Precios, Solo Formulario -->
    <section id="contacto" class="py-20 max-w-7xl mx-auto px-6">
        <div class="bg-white dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 sm:p-12 lg:p-16 relative overflow-hidden shadow-md">
            <div class="grid lg:grid-cols-12 gap-12 relative z-10">
                <div class="lg:col-span-5 flex flex-col justify-between">
                    <div>
                        <span class="text-emerald-600 dark:text-emerald-400 text-xs font-bold tracking-widest uppercase mb-3 block">Agendar Demostración</span>
                        <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-4">¿Listo para modernizar tu logística?</h2>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-8 text-sm sm:text-base">
                            Descubre cómo GateOps puede adaptarse a las necesidades específicas de tu empresa transportista o de patio. Platícanos tu caso y un especialista se pondrá en contacto contigo.
                        </p>
                    </div>
                    <div class="space-y-3 text-xs sm:text-sm text-slate-500 dark:text-slate-400">
                        <p class="flex items-center gap-2">
                            <span class="material-icons-round text-emerald-600 dark:text-emerald-400 text-lg">check_circle</span> Demo personalizada sin costo ni compromiso.
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="material-icons-round text-emerald-600 dark:text-emerald-400 text-lg">lock</span> Tu información de contacto se maneja de forma confidencial.
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <form class="space-y-5 bg-slate-50 dark:bg-slate-950/60 p-6 sm:p-8 rounded-2xl border border-slate-200 dark:border-slate-800">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nombre" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nombre Completo</label>
                                <input type="text" id="nombre" name="nombre" placeholder="Ej. Iván López" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all" required>
                            </div>
                            <div>
                                <label for="empresa" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Empresa</label>
                                <input type="text" id="empresa" name="empresa" placeholder="Ej. Transportes Hefesto" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all" required>
                            </div>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label for="correo" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Correo Corporativo</label>
                                <input type="email" id="correo" name="correo" placeholder="ejemplo@tuempresa.com" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all" required>
                            </div>
                            <div>
                                <label for="telefono" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">WhatsApp / Teléfono</label>
                                <input type="tel" id="telefono" name="telefono" placeholder="Ej. +52 844 123 4567" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all" required>
                            </div>
                        </div>
                        <div>
                            <label for="mensaje" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">¿Cuál es tu principal necesidad operativa?</label>
                            <textarea id="mensaje" name="mensaje" rows="3" placeholder="Ej. Digitalizar las firmas en caseta y registrar las pruebas de confianza de los operadores..." class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-3 text-slate-900 dark:text-white text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm transition-all shadow-md shadow-emerald-500/20">
                            Enviar Datos y Agendar Demo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-900 py-12 bg-white dark:bg-slate-950 transition-colors">
        <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-6 text-sm text-slate-500 dark:text-slate-400">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold tracking-tight text-slate-900 dark:text-white">GATE<span class="text-emerald-500">OPS</span></span>
            </div>
            <p class="text-xs">© 2026 Todos los derechos reservados.</p>
            <div class="flex gap-6 text-xs">
                <a href="#" class="hover:text-slate-700 dark:hover:text-slate-200 transition-colors">Aviso de Privacidad</a>
                <a href="#" class="hover:text-slate-700 dark:hover:text-slate-200 transition-colors">Términos de Servicio</a>
            </div>
        </div>
    </footer>

    <!-- JavaScript para control de Tema -->
    <script>
        const btnToggleTheme = document.getElementById('btn-toggle-theme');
        const htmlTag = document.getElementById('html-tag');

        btnToggleTheme.addEventListener('click', () => {
            htmlTag.classList.toggle('dark');
        });
    </script>
</body>

</html>