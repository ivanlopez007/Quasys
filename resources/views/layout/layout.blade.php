<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quasys - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-transition {
            transition: width 0.3s ease-in-out;
        }

        .collapsed .menu-text,
        .collapsed .menu-chevron {
            display: none;
        }

        .collapsed {
            width: 4rem;
        }

        .linea-guia {
            border-left: 1px solid #475569;
            margin-left: 0.75rem;
            padding-left: 0.5rem;
        }

        /* Scrollbar personalizada para el sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 10px;
        }
    </style>
</head>

<body class="bg-slate-50 font-sans overflow-hidden">

    <div class="flex h-screen">
        <!-- SIDEBAR -->
        <aside id="sidebar" class="sidebar-transition w-72 bg-slate-900 text-white flex flex-col z-20 shadow-2xl">
            <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-slate-900">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <span class="menu-text font-black text-xl tracking-tighter">QUASYS</span>
                </div>
                <button onclick="toggleSidebar()" class="p-2 hover:bg-slate-800 rounded-xl transition text-slate-400">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="flex-1 mt-4 px-3 space-y-1 overflow-y-auto sidebar-scroll">

                @if ($usuario['rol'] === 'admin' || $usuario['rol'] === 'calidad') 

                <!-- MENU GERENCIA -->
                <div class="pb-2">
                    <p class="menu-text text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-4 mb-2">Operaciones</p>
                    <button onclick="toggleSubmenu('menu-gerencia')" class="w-full flex items-center p-3 rounded-xl hover:bg-slate-800 transition group">
                        <i class="fas fa-chart-pie w-6 text-slate-400 group-hover:text-white"></i>
                        <span class="menu-text ml-3 flex-1 text-left text-sm font-medium">Gerencia</span>
                        <i class="fas fa-chevron-down text-[10px] menu-chevron text-slate-500"></i>
                    </button>
                    <div id="menu-gerencia" class="hidden flex flex-col mt-1 ml-4 space-y-1">
                        <button onclick="toggleSubmenu('ger-control')" class="flex items-center justify-between p-2 text-xs text-slate-400 hover:text-white transition">
                            <span>Control de Documentos</span>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                        </button>
                        <div id="ger-control" class="hidden flex flex-col linea-guia text-[11px] text-slate-500 space-y-1">
                            <a href="#" class="py-1 hover:text-white">Solicitud de cambios</a>
                            <a href="#" class="py-1 hover:text-white">Aprobación de solicitudes</a>
                            <a href="#" class="py-1 hover:text-white">Revisión de solicitudes</a>
                            <a href="#" class="py-1 hover:text-white">Consulta de documentos</a>
                            <a href="#" class="py-1 hover:text-white">Historial de documentos</a>
                            <a href="#" class="py-1 hover:text-white">Matriz de documentos</a>
                            <a href="#" class="py-1 hover:text-white">Revisión de documentos</a>
                            <a href="#" class="py-1 hover:text-white">Hoja de tarea estándar</a>
                        </div>
                        <a href="#" class="p-2 text-xs text-slate-400 hover:text-white">Training Tracker</a>
                    </div>
                </div>

                <!-- MENU CALIDAD -->
                <div class="pb-2">
                    <button onclick="toggleSubmenu('menu-calidad')" class="w-full flex items-center p-3 rounded-xl hover:bg-slate-800 transition group">
                        <i class="fas fa-shield-check w-6 text-slate-400 group-hover:text-white"></i>
                        <span class="menu-text ml-3 flex-1 text-left text-sm font-medium">Calidad</span>
                        <i class="fas fa-chevron-down text-[10px] menu-chevron text-slate-500"></i>
                    </button>
                    <div id="menu-calidad" class="hidden flex flex-col mt-1 ml-4 space-y-1">
                        <button onclick="toggleSubmenu('cal-control')" class="flex items-center justify-between p-2 text-xs text-slate-400 hover:text-white transition">
                            <span>Control de Documentos</span>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                        </button>
                        <div id="cal-control" class="hidden flex flex-col linea-guia text-[11px] text-slate-500 space-y-1">
                            <a href="#" class="py-1 hover:text-white">Solicitud de cambios</a>
                            <a href="#" class="py-1 hover:text-white">Aprobación de solicitudes</a>
                            <a href="#" class="py-1 hover:text-white">Revisión de solicitudes</a>
                            <a href="#" class="py-1 hover:text-white">Consulta de documentos</a>
                            <a href="#" class="py-1 hover:text-white">Historial de documentos</a>
                            <a href="#" class="py-1 hover:text-white">Matriz de documentos</a>
                            <a href="#" class="py-1 hover:text-white">Revisión de documentos</a>
                            <a href="#" class="py-1 hover:text-white">Hoja de tarea estándar</a>
                        </div>
                        <a href="#" class="p-2 text-xs text-slate-400 hover:text-white">Training Tracker</a>
                    </div>
                </div>
                @endif

                <!-- MENU ASOCIADO -->
                <div class="pb-2">
                    <button onclick="toggleSubmenu('menu-asociado')" class="w-full flex items-center p-3 rounded-xl hover:bg-slate-800 transition group">
                        <i class="fas fa-user-tie w-6 text-slate-400 group-hover:text-white"></i>
                        <span class="menu-text ml-3 flex-1 text-left text-sm font-medium">Asociado</span>
                        <i class="fas fa-chevron-down text-[10px] menu-chevron text-slate-500"></i>
                    </button>
                    <div id="menu-asociado" class="hidden flex flex-col mt-1 ml-4 space-y-1">
                        <!-- Sub-Control -->
                        <button onclick="toggleSubmenu('asoc-control')" class="flex items-center justify-between p-2 text-xs text-slate-400 hover:text-white transition">
                            <span>Control de Documentos</span>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                        </button>
                        <div id="asoc-control" class="hidden flex flex-col linea-guia text-[11px] text-slate-500 space-y-1">
                            <a href="#" class="py-1 hover:text-white transition">Solicitud de cambios</a>
                            <a href="#" class="py-1 hover:text-white transition">Aprobación de solicitudes de nivel superior</a>
                            <a href="#" class="py-1 hover:text-white transition">Aprobación de solicitudes THE</a>
                            <a href="#" class="py-1 hover:text-white transition">Estatus de solicitudes</a>
                            <a href="#" class="py-1 hover:text-white transition">Consulta de documentos</a>
                            <a href="#" class="py-1 hover:text-white transition">Historial de documentos</a>
                            <a href="#" class="py-1 hover:text-white transition">Matriz de documentos</a>
                            <a href="#" class="py-1 hover:text-white transition">Revisión de documentos</a>
                        </div>
                        <a href="#" class="p-2 text-xs text-slate-400 hover:text-white transition">Training Tracker</a>
                    </div>
                </div>



            </nav>

            <div class="p-4 border-t border-slate-800">
                <p class="text-center text-slate-500 text-[10px] font-bold uppercase tracking-widest">
                    &copy; 2026 Quasys
                </p>
            </div>
        </aside>

        <!-- CONTENIDO -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 relative z-10">
                <div class="text-slate-400 text-sm font-medium">
                    {{ Route::currentRouteName() }} <span class="text-slate-800 font-bold"></span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-800">{{ $usuario['nombre'] }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $usuario['rol'] }}</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Ivan+Lopez&background=0f172a&color=fff" class="h-10 w-10 rounded-xl shadow-md" alt="Avatar">
                </div>
            </header>

            <main class="flex-1 p-8 overflow-y-auto">
                <div class="h-full bg-white rounded-3xl border border-slate-200 border-dashed flex flex-col items-center justify-center text-slate-300">
                    <i class="fas fa-folder-open text-6xl mb-4"></i>
                    <p class="font-medium tracking-tight">Selecciona una opción del menú para comenzar</p>
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }

        function toggleSubmenu(id) {
            const menu = document.getElementById(id);
            const isCollapsed = document.getElementById('sidebar').classList.contains('collapsed');
            if (!isCollapsed) {
                menu.classList.toggle('hidden');
            }
        }
    </script>
</body>

</html>