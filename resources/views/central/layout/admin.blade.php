<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Administración') · Quasys</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        .fondo-central {
            background-color: #f8fafc;
            background-image: radial-gradient(circle at 1px 1px, rgb(148 163 184 / .18) 1px, transparent 0);
            background-size: 22px 22px;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full fondo-central text-slate-800 antialiased">

    <header class="sticky top-0 z-40 bg-slate-900/95 backdrop-blur border-b border-slate-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('central.empresas.index') }}" class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-shield-halved text-white text-sm"></i>
                </span>
                <span class="leading-tight">
                    <span class="block text-white font-black tracking-tight">Quasys</span>
                    <span class="block text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400">Administración central</span>
                </span>
            </a>

            <nav class="flex items-center gap-1 text-xs font-bold uppercase tracking-wider">
                @php $navActual = fn ($ruta) => request()->routeIs($ruta) ? 'bg-white/10 text-white' : 'text-slate-400 hover:text-white hover:bg-white/5'; @endphp
                <a href="{{ route('central.empresas.index') }}" class="px-3 py-2 rounded-lg transition {{ $navActual('central.empresas.index') }}">
                    <i class="fas fa-building sm:mr-1.5"></i><span class="hidden sm:inline">Empresas</span>
                </a>
                <a href="{{ route('central.empresas') }}" class="px-3 py-2 rounded-lg transition {{ $navActual('central.empresas') }}">
                    <i class="fas fa-plus sm:mr-1.5"></i><span class="hidden sm:inline">Nueva empresa</span>
                </a>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
        @yield('content')
    </main>

    <footer class="max-w-6xl mx-auto px-4 sm:px-6 pb-8 text-[11px] text-slate-400">
        Quasys · Gestión documental ISO 9001
    </footer>

    @stack('scripts')
</body>
</html>
