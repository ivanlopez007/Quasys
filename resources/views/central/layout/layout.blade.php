<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full dark" id="html-tag">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard - Quasys')</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>

    <style>
        .material-icons-round {
            font-size: 22px;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            vertical-align: middle;
        }

        /* Scrollbars elegantes */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #334155;
        }
    </style>
    @stack('styles')
</head>

<body class="h-full text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 font-sans antialiased transition-colors duration-200 overflow-hidden">

    @php
    // Definición dinámica del menú (Si no viene desde un ViewComposer o Controller, usa esta configuración por defecto)
    $sidebarMenu = $sidebarMenu ?? [
    [
    'section' => 'Administración',
    'items' => [
    [
    'title' => 'Inicio / Panel',
    'icon' => 'dashboard',
    'route' => 'dashboard', // Nombre de la ruta en web.php
    'url' => '#', // Caída si la ruta no existe
    ],
    [
    'title' => 'Altas y Bajas (Personal)',
    'icon' => 'badge',
    'route' => 'personal.index',
    'url' => '#',
    ],
    ]
    ],
    [
    'section' => 'Operaciones',
    'items' => [
    [
    'title' => 'Intercambio de Trailers',
    'icon' => 'local_shipping',
    'route' => 'trailers.index',
    'url' => '#',
    ],
    [
    'title' => 'Seguridad & Antidopings',
    'icon' => 'fact_check',
    'route' => 'seguridad.index',
    'url' => '#',
    ],
    ]
    ]
    ];
    @endphp

    <div class="flex h-full w-full overflow-hidden relative">

        <!-- SIDEBAR -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-64 translate-x-0 flex flex-col justify-between border-r border-slate-200 dark:border-slate-800/80 bg-white dark:bg-slate-900 transition-transform duration-300 ease-in-out shrink-0">

            <div>
                <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-200 dark:border-slate-800/80">
                    <div class="p-1.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg border border-emerald-500/20 flex items-center justify-center">
                        <span class="material-icons-round text-xl">shield</span>
                    </div>
                    <div>
                        <span class="text-sm font-bold tracking-tight text-slate-900 dark:text-white">QUA<span class="text-emerald-500">SYS</span></span>
                        <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">BY QUASYS</p>
                    </div>
                </div>

                <!-- RENDER DINÁMICO DEL MENÚ -->
                <nav class="p-4 space-y-4 overflow-y-auto max-h-[calc(100vh-8rem)]">
                    @foreach($sidebarMenu as $group)
                    <div>
                        @if(isset($group['section']) && $group['section'])
                        <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider px-3 mb-2">
                            {{ $group['section'] }}
                        </span>
                        @endif

                        <div class="space-y-1.5">
                            @foreach($group['items'] as $item)
                            @php
                            // Generar URL válida por ruta o fallback a URL manual
                            $targetUrl = isset($item['route']) && Route::has($item['route'])
                            ? route($item['route'])
                            : ($item['url'] ?? '#');

                            // Determinar si la opción actual está activa
                            $isActive = isset($item['route']) && Route::has($item['route'])
                            ? request()->routeIs($item['route'] . '*')
                            : (request()->url() === $targetUrl);
                            @endphp

                            <a href="{{ $targetUrl }}"
                                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ $isActive ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border border-emerald-500/15 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/50' }}">
                                <span class="material-icons-round">{{ $item['icon'] ?? 'link' }}</span>
                                {{ $item['title'] }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </nav>
            </div>

            <div class="p-4 border-t border-slate-200 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-950/20 text-center">
                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold tracking-wider uppercase">Quasys v1.0</span>
            </div>
        </aside>

        <!-- WRAPPER PRINCIPAL -->
        <div id="main-content-wrapper" class="flex-1 flex flex-col h-full overflow-hidden pl-64 transition-all duration-300">

            <header class="h-16 border-b border-slate-200 dark:border-slate-800/80 bg-white/80 dark:bg-slate-900/50 backdrop-blur-md flex items-center justify-between px-6 shrink-0">

                <button id="btn-toggle-sidebar" class="p-2 -ml-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center transition">
                    <span class="material-icons-round">menu</span>
                </button>

                <h2 class="text-sm font-bold text-slate-900 dark:text-white hidden sm:block ml-4">
                    @yield('header_title', 'Panel de Administración')
                </h2>

                <div class="flex items-center gap-3 ml-auto">
                    <button id="btn-toggle-theme" class="rounded-xl border border-slate-200 dark:border-slate-800 p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900 transition flex items-center justify-center">
                        <div class="hidden dark:block">
                            <span class="material-icons-round text-lg">light_mode</span>
                        </div>
                        <div class="block dark:hidden">
                            <span class="material-icons-round text-lg">dark_mode</span>
                        </div>
                    </button>

                    <div class="w-px h-6 bg-slate-200 dark:bg-slate-800"></div>

                    <div class="relative">
                        <button id="btn-user-menu" class="flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 p-1.5 rounded-xl transition focus:outline-none select-none">
                            <div class="hidden md:block text-right">
                                <span class="block text-xs font-bold text-slate-900 dark:text-white leading-tight">{{ Auth::user()->name ?? 'Iván López' }}</span>
                                <span class="block text-[10px] font-bold text-emerald-600 dark:text-emerald-500 uppercase tracking-wider leading-none mt-0.5">Administrador</span>
                            </div>
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold flex items-center justify-center text-sm">
                                {{ substr(Auth::user()->name ?? 'Iván', 0, 1) }}
                            </div>
                            <span class="material-icons-round text-slate-400 text-sm hidden md:block">expand_more</span>
                        </button>

                        <div id="user-dropdown" class="absolute right-0 mt-2 w-52 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl py-2 hidden z-50 transition-all">
                            <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-800/50 mb-1">
                                <span class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Opciones de Cuenta</span>
                            </div>

                            <a href="#" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white transition">
                                <span class="material-icons-round text-slate-400">person</span>
                                Mi Perfil
                            </a>

                            <div class="h-px bg-slate-100 dark:bg-slate-800/50 my-1"></div>

                            <form action="#" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-500/10 transition">
                                    <span class="material-icons-round">logout</span>
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                @yield('content')
            </main>

        </div>
    </div>

    <div id="sidebar-overlay" class="fixed inset-0 z-30 bg-slate-900/40 backdrop-blur-sm hidden"></div>

    <script>
        // 1. Tema Oscuro / Claro
        const btnToggleTheme = document.getElementById('btn-toggle-theme');
        const htmlTag = document.getElementById('html-tag');

        btnToggleTheme.addEventListener('click', () => {
            htmlTag.classList.toggle('dark');
        });

        // 2. Control de Apertura / Cierre de Sidebar
        const btnToggleSidebar = document.getElementById('btn-toggle-sidebar');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const mainContent = document.getElementById('main-content-wrapper');

        function handleResize() {
            const isMobile = window.innerWidth < 1024;
            if (isMobile) {
                sidebar.classList.add('-translate-x-full');
                mainContent.classList.remove('pl-64');
                sidebarOverlay.classList.add('hidden');
            } else {
                sidebar.classList.remove('-translate-x-full');
                mainContent.classList.add('pl-64');
                sidebarOverlay.classList.add('hidden');
            }
        }

        handleResize();
        window.addEventListener('resize', handleResize);

        btnToggleSidebar.addEventListener('click', (e) => {
            e.stopPropagation();
            const isMobile = window.innerWidth < 1024;

            if (isMobile) {
                sidebar.classList.toggle('-translate-x-full');
                sidebarOverlay.classList.toggle('hidden');
            } else {
                if (sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                    mainContent.classList.add('pl-64');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    mainContent.classList.remove('pl-64');
                }
            }
        });

        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        });

        // 3. Menú Desplegable del Perfil
        const btnUserMenu = document.getElementById('btn-user-menu');
        const userDropdown = document.getElementById('user-dropdown');

        btnUserMenu.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!userDropdown.classList.contains('hidden') && !btnUserMenu.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    </script>
    @stack('scripts')
</body>

</html>