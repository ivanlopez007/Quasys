@php
// Control de tema (Simulado desde el backend, 'light' o 'dark')
$temaDelBackend = 'light';
@endphp

<!DOCTYPE html>
<html lang="es" class="h-full {{ $temaDelBackend === 'dark' ? 'dark' : '' }}" id="html-tag">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - GateOps</title>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>

    <style>
        .material-icons-round {
            font-size: 20px !important;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>

<body class="h-full text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-950 font-sans antialiased flex flex-col justify-between transition-colors duration-200">

    <div class="absolute top-4 right-4 z-50">
        <button id="btn-toggle-theme" class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/80 transition shadow-sm flex items-center justify-center">
            <div class="hidden dark:block">
                <span class="material-icons-round">light_mode</span>
            </div>
            <div class="block dark:hidden">
                <span class="material-icons-round">dark_mode</span>
            </div>
        </button>
    </div>

    <div class="flex-1 flex flex-col items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md space-y-8">
            
            <div class="flex flex-col items-center text-center">
                <div class="p-3 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-2xl border border-emerald-500/20 flex items-center justify-center shadow-sm shadow-emerald-500/5 mb-4">
                    <span class="material-icons-round text-3xl">shield</span>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">GateOps</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Sistema Inteligente de Control de Accesos y Casetas
                </p>
            </div>

            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-3xl p-8 shadow-xl shadow-slate-100/40 dark:shadow-none">
                <form class="space-y-6" action="{{ route('central.login.post') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label for="email" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Correo Electrónico</label>
                        <div class="relative">
                            <span class="material-icons-round absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">mail_outline</span>
                            <input id="email" name="email" type="email" autocomplete="email" required placeholder="correo@empresa.com" 
                                class="w-full pl-11 pr-4 py-3 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-500 transition-all font-medium">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Contraseña</label>
                            <a href="#" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:text-emerald-500 transition">¿La olvidaste?</a>
                        </div>
                        <div class="relative">
                            <span class="material-icons-round absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">lock_open</span>
                            <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="••••••••" 
                                class="w-full pl-11 pr-12 py-3 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 dark:focus:border-emerald-500 transition-all font-medium">
                            
                            <button type="button" id="btn-toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition flex items-center justify-center">
                                <span class="material-icons-round text-lg" id="password-icon">visibility</span>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" 
                            class="h-4 w-4 rounded border-slate-300 dark:border-slate-700 text-emerald-600 focus:ring-emerald-500/20 dark:bg-slate-800">
                        <label for="remember-me" class="ml-2 block text-sm font-medium text-slate-600 dark:text-slate-400 select-none">
                            Mantener sesión iniciada
                        </label>
                    </div>

                    <button type="submit" 
                        class="w-full flex justify-center items-center gap-2 px-4 py-3 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-500 active:bg-emerald-700 rounded-xl shadow-md shadow-emerald-500/10 hover:shadow-emerald-500/20 transition-all duration-150">
                        <span class="material-icons-round text-lg">vpn_key</span>
                        Ingresar al Sistema
                    </button>
                </form>
            </div>
            
        </div>
    </div>

    <footer class="py-6 text-center text-xs text-slate-400 dark:text-slate-600 border-t border-slate-200/40 dark:border-slate-900/40 bg-white/40 dark:bg-slate-950/20 backdrop-blur-sm">
        <p>&copy; 2026 GateOps. Todos los derechos reservados.</p>
        <p class="font-semibold text-emerald-600 dark:text-emerald-500/80 mt-1">by Grupo RIYLO</p>
    </footer>

    <script>
        // 1. CONTROL DE MODO OSCURO / MODO CLARO
        const btnToggleTheme = document.getElementById('btn-toggle-theme');
        const htmlTag = document.getElementById('html-tag');

        btnToggleTheme.addEventListener('click', () => {
            htmlTag.classList.toggle('dark');
        });

        // 2. MOSTRAR / OCULTAR CONTRASEÑA
        const btnTogglePassword = document.getElementById('btn-toggle-password');
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('password-icon');

        btnTogglePassword.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                passwordIcon.textContent = 'visibility';
            }
        });
    </script>
</body>

</html>