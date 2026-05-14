<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Multinivel Completo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-transition {
            transition: width 0.3s ease-in-out;
        }

        .menu-text {
            transition: opacity 0.2s;
        }

        .collapsed .menu-text,
        .collapsed .menu-chevron {
            display: none;
        }

        .collapsed {
            width: 4rem;
        }

        /* Línea guía para el tercer nivel */
        .linea-guia {
            border-left: 1px solid #475569;
            margin-left: 0.75rem;
            padding-left: 0.5rem;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans overflow-hidden">

    <div class="flex h-screen">

        <!-- SIDEBAR -->
        <aside id="sidebar" class="sidebar-transition w-64 bg-slate-900 text-white flex flex-col z-20">
            <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800">
                <span class="menu-text font-bold text-xl tracking-wider">SISTEMA</span>
                <span class="avatar hidden sm:block ">
                    <img class="rounded-full size-12" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSudFv7qVDKT7KUcwKSpGcRi8JWvUETCSlCrA&s" alt="">
                </span>
                <button onclick="toggleSidebar()" class="p-2 hover:bg-slate-800 rounded transition">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="flex-1 mt-4 px-3 space-y-2 overflow-y-auto">

                <!-- 1. COLABORADOR -->
                <div>
                    <button onclick="toggleSubmenu('menu-colaborador')" class="w-full flex items-center p-3 rounded-lg hover:bg-slate-800 transition">
                        <i class="fas fa-user-friends w-6"></i>
                        <span class="menu-text ml-3 flex-1 text-left">Colaborador</span>
                        <i class="fas fa-chevron-down text-xs menu-chevron"></i>
                    </button>
                    <div id="menu-colaborador" class="hidden flex flex-col mt-1 ml-4 space-y-1">
                        <!-- Subnivel 2 -->
                        <button onclick="toggleSubmenu('sub-perfil')" class="flex items-center justify-between p-2 text-sm text-slate-400 hover:text-white">
                            <span>Gestión de Perfil</span>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </button>
                        <!-- Subnivel 3 -->
                        <div id="sub-perfil" class="hidden flex flex-col linea-guia text-xs text-slate-500">
                            <a href="#" class="py-1 hover:text-white">Datos Personales</a>
                            <a href="#" class="py-1 hover:text-white">Seguridad</a>
                            <a href="#" class="py-1 hover:text-white">Preferencias</a>
                        </div>
                    </div>
                </div>

                @if ($usuario['rol'] === 'admin' OR $usuario['rol'] === 'calidad')
                <!-- 2. GERENCIA -->
                <div>
                    <button onclick="toggleSubmenu('menu-gerencia')" class="w-full flex items-center p-3 rounded-lg hover:bg-slate-800 transition">
                        <i class="fas fa-chart-line w-6"></i>
                        <span class="menu-text ml-3 flex-1 text-left">Gerencia</span>
                        <i class="fas fa-chevron-down text-xs menu-chevron"></i>
                    </button>
                    <div id="menu-gerencia" class="hidden flex flex-col mt-1 ml-4 space-y-1">
                        <!-- Subnivel 2 -->
                        <button onclick="toggleSubmenu('sub-reportes')" class="flex items-center justify-between p-2 text-sm text-slate-400 hover:text-white">
                            <span>Reportes</span>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </button>
                        <!-- Subnivel 3 -->
                        <div id="sub-reportes" class="hidden flex flex-col linea-guia text-xs text-slate-500">
                            <a href="#" class="py-1 hover:text-white">Ventas</a>
                            <a href="#" class="py-1 hover:text-white">Gastos</a>
                            <a href="#" class="py-1 hover:text-white">Proyecciones</a>
                        </div>
                    </div>
                </div>

                <!-- 3. CALIDAD -->
                <div>
                    <button onclick="toggleSubmenu('menu-calidad')" class="w-full flex items-center p-3 rounded-lg hover:bg-slate-800 transition">
                        <i class="fas fa-check-circle w-6"></i>
                        <span class="menu-text ml-3 flex-1 text-left">Calidad</span>
                        <i class="fas fa-chevron-down text-xs menu-chevron"></i>
                    </button>
                    <div id="menu-calidad" class="hidden flex flex-col mt-1 ml-4 space-y-1">
                        <!-- Subnivel 2 -->
                        @foreach ($menuCalidad as $item)

                        <button onclick="toggleSubmenu('sub-auditoria')" class="flex items-center justify-between p-2 text-sm text-slate-400 hover:text-white">
                            <span>{{ $item['nombre'] }}</span>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </button>

                        @endforeach


                        <!-- Subnivel 3 -->

                        <div id="sub-auditoria" class="hidden flex flex-col linea-guia text-xs text-slate-500">
                            @foreach ($SubmenuReportes as $reporte)
                            <a href="#" class="py-1 hover:text-white">{{ $reporte['nombre'] }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

            </nav>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- TOP BAR -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-end px-8 relative z-10">
                <div class="relative">
                    <button onclick="toggleUserMenu()" class="flex items-center space-x-3 focus:outline-none hover:bg-gray-50 p-2 rounded-lg transition">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-gray-700">{{ $usuario['nombre'] }}</p>
                            <p class="text-xs text-gray-500 italic">{{ $usuario['rol'] }}</p>
                        </div>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($usuario['nombre']) }}&background=0D8ABC&color=fff" class="h-9 w-9 rounded-full" alt="Avatar">
                    </button>

                    <!-- User Dropdown -->
                    <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-gray-200">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-cog mr-2"></i> Settings</a>
                        <hr class="my-1">
                        <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50"><i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesión</a>
                    </div>
                </div>
            </header>

            <!-- ESPACIO VACÍO -->
            <main class="flex-1 p-6 bg-gray-50 overflow-y-auto">
                <div class="h-full border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center text-gray-400">
                    Contenedor para tus pantallas
                </div>
            </main>
        </div>
    </div>

    <script>
        // Función para colapsar/expandir la barra lateral
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }

        // Función para manejar la apertura de submenús, solo si la barra lateral no está colapsada
        function toggleSubmenu(id) {
            const menu = document.getElementById(id);
            const isCollapsed = document.getElementById('sidebar').classList.contains('collapsed');

            // Solo permitimos abrir menús si la barra no está colapsada
            if (!isCollapsed) {
                menu.classList.toggle('hidden');
            }
        }

        // Función para mostrar/ocultar el dropdown de usuario
        function toggleUserMenu() {
            document.getElementById('user-menu').classList.toggle('hidden');
        }

        // Cerrar dropdown de usuario si se hace click fuera
        window.onclick = function(event) {
            if (!event.target.closest('#user-menu') && !event.target.closest('button[onclick="toggleUserMenu()"]')) {
                document.getElementById('user-menu').classList.add('hidden');
            }
        }
    </script>
</body>

</html>