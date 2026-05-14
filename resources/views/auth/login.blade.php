<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quasys - Acceso</title>
    <!-- Google Fonts & Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Un toque sutil de suavidad para el fondo */
        .bg-custom {
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="bg-custom flex items-center justify-center min-h-screen p-4">

    <div class="max-w-sm w-full">
        <!-- Logo y Título unificado -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-slate-900 text-white rounded-2xl shadow-lg mb-4">
                <span class="material-symbols-outlined text-3xl">task_alt</span>
            </div>
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">
                Quasys
            </h1>
            <p class="text-slate-400 text-xs font-medium uppercase tracking-widest mt-1">Sistema Central</p>
        </div>

        <!-- Tarjeta de Login Refinada -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            
            <form action="#" method="POST" class="p-8 space-y-6">
                <!-- Campo Email -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-1">Correo Electrónico</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">mail</span>
                        <input type="email" name="email" required
                            class="w-full bg-slate-50 border border-slate-200 pl-11 pr-4 py-3 rounded-xl focus:ring-2 focus:ring-slate-900 focus:bg-white outline-none transition-all text-slate-700 text-sm" 
                            placeholder="usuario@empresa.com">
                    </div>
                </div>

                <!-- Campo Contraseña -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider ml-1">Contraseña</label>
                        <a href="#" class="text-[10px] font-bold text-slate-400 hover:text-slate-900 transition uppercase underline underline-offset-2">¿Olvidaste?</a>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xl">lock</span>
                        <input type="password" name="password" required
                            class="w-full bg-slate-50 border border-slate-200 pl-11 pr-4 py-3 rounded-xl focus:ring-2 focus:ring-slate-900 focus:bg-white outline-none transition-all text-slate-700 text-sm" 
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Recordar sesión -->
                <div class="flex items-center px-1">
                    <input type="checkbox" id="remember" class="w-4 h-4 text-slate-900 border-slate-300 rounded focus:ring-slate-900">
                    <label for="remember" class="ml-2 text-xs text-slate-500 font-semibold uppercase tracking-wide">Recordar sesión</label>
                </div>

                <!-- Botón de Entrada -->
                <button type="submit" 
                    class="w-full bg-slate-900 text-white py-4 rounded-xl font-bold uppercase tracking-[0.15em] hover:bg-slate-800 shadow-md transition-all active:scale-[0.98] flex items-center justify-center text-xs">
                    <span>Acceder</span>
                    <span class="material-symbols-outlined ml-2 text-lg">login</span>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center mt-10 text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em]">
            &copy; 2026 Quasys APP
        </p>
    </div>

</body>
</html>