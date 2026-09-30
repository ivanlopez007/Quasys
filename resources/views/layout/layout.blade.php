<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quasys - Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
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
            width: 4.5rem;
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

    <div class="flex h-screen w-screen overflow-hidden">

        <!-- SIDEBAR -->
        <aside id="sidebar" class="sidebar-transition w-72 bg-slate-900 text-white flex flex-col shrink-0 z-20 shadow-2xl">
            <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-slate-900 shrink-0">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <span class="menu-text font-black text-xl tracking-tighter whitespace-nowrap">QUASYS</span>
                </div>
                <button onclick="toggleSidebar()" class="p-2 hover:bg-slate-800 rounded-xl transition text-slate-400">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="flex-1 mt-4 px-3 space-y-1 overflow-y-auto sidebar-scroll">

                @if(isset($menusLayout) && $menusLayout->isNotEmpty())
                @foreach($menusLayout as $menu)
                <!-- MENU DINÁMICO -->
                <div class="pb-2">
                    <button onclick="toggleSubmenu('menu-{{ $menu->id }}')" class="w-full flex items-center p-3 rounded-xl hover:bg-slate-800 transition group">
                        <i class="{{ $menu->icono ?? 'fas fa-folder' }} w-6 text-slate-400 group-hover:text-white"></i>
                        <span class="menu-text ml-3 flex-1 text-left text-sm font-medium">{{ $menu->nombre ?? $menu->menu }}</span>
                        <i class="fas fa-chevron-down text-[10px] menu-chevron text-slate-500"></i>
                    </button>

                    <div id="menu-{{ $menu->id }}" class="hidden flex flex-col mt-1 ml-4 space-y-1">
                        @foreach($menu->subMenus as $subMenu)
                        {{-- Si el submenú tiene hijos (sub-submenús / agrupador) --}}
                        @if(isset($subMenu->hijos) && count($subMenu->hijos) > 0)
                        <button onclick="toggleSubmenu('sub-{{ $subMenu->id }}')" class="flex items-center justify-between p-2 text-xs text-slate-400 hover:text-white transition w-full text-left">
                            <span>{{ $subMenu->nombre ?? $subMenu->sub_menu }}</span>
                            <i class="fas fa-chevron-right text-[8px]"></i>
                        </button>
                        <div id="sub-{{ $subMenu->id }}" class="hidden flex flex-col linea-guia text-[11px] text-slate-500 space-y-1">
                            @foreach($subMenu->hijos as $hijo)
                            <a href="{{ $hijo->ruta ? (Route::has($hijo->ruta) ? route($hijo->ruta) : url($hijo->ruta)) : '#' }}"
                                class="py-1 hover:text-white transition">
                                {{ $hijo->nombre ?? $hijo->sub_menu }}
                            </a>
                            @endforeach
                        </div>
                        @else
                        {{-- Enlace directo de submenú --}}
                        <a href="{{ $subMenu->ruta ? (Route::has($subMenu->ruta) ? route($subMenu->ruta) : url($subMenu->ruta)) : '#' }}"
                            class="p-2 text-xs text-slate-400 hover:text-white transition flex items-center gap-2">
                            @if(!empty($subMenu->icono))
                            <i class="{{ $subMenu->icono }} text-[10px]"></i>
                            @endif
                            <span>{{ $subMenu->nombre ?? $subMenu->sub_menu }}</span>
                        </a>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
                @else
                <div class="p-4 text-center text-slate-500 text-xs font-semibold">
                    Sin permisos asignados
                </div>
                @endif

            </nav>

            <div class="p-4 border-t border-slate-800">
                <p class="text-center text-slate-500 text-[10px] font-bold uppercase tracking-widest">
                    &copy; 2026 Quasys
                </p>
            </div>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 h-screen overflow-hidden">

            <!-- HEADER SUPERIOR -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 shrink-0 relative z-10">
                <div class="text-slate-400 text-xs sm:text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-home text-slate-300"></i>
                    <span>/</span>
                    <span class="text-slate-700 font-semibold">@yield('title', 'Dashboard')</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-bold text-slate-800">{{ Auth::user()->informacion->nombre ?? Auth::user()->name }}</p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">{{ Auth::user()->rol->rol ?? 'Sin Rol' }}</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->informacion->nombre ?? Auth::user()->name) }}&background=0f172a&color=fff" class="h-9 w-9 rounded-xl shadow-sm border border-slate-100" alt="Avatar">
                </div>
            </header>

            <!-- ÁREA DINÁMICA CON SCROLL -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- SCRIPTS CORE DEL LAYOUT -->
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }

        function toggleSubmenu(id) {
            const sidebar = document.getElementById('sidebar');
            if (sidebar.classList.contains('collapsed')) {
                sidebar.classList.remove('collapsed');
            }
            const menu = document.getElementById(id);
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }
    </script>
</body>

</html>