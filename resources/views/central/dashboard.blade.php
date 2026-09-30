@extends('central.layout.layout')

@section('title', 'Panel de Control - GateOps')
@section('header_title', 'Indicadores Operativos')

@section('content')
<!-- Fila de Bienvenida -->
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Panel Principal</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Monitoreo de inspecciones, cumplimiento C-TPAT/OEA y estatus de flota en tiempo real.</p>
    </div>

    <!-- Rango de fecha o acción rápida -->
    <div class="inline-flex items-center gap-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 px-4 py-2.5 rounded-xl shadow-sm">
        <span class="material-icons-round text-slate-400 text-sm">calendar_today</span>
        <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">Hoy: {{ now()->format('d M, Y') }}</span>
    </div>
</div>

<!-- 1. Tarjetas de Métricas Clave -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Tarjeta: Operadores -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 p-6 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Operadores Activos</span>
            <span class="p-1.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg material-icons-round">badge</span>
        </div>
        <div class="text-3xl font-extrabold text-slate-900 dark:text-white">124</div>
        <div class="text-xs text-emerald-500 font-semibold flex items-center gap-1 mt-2">
            <span class="material-icons-round text-xs">trending_up</span> +4% esta semana
        </div>
    </div>

    <!-- Tarjeta: Inspecciones C-TPAT / OEA -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 p-6 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Inspecciones Hoy</span>
            <span class="p-1.5 bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg material-icons-round">verified_user</span>
        </div>
        <div class="text-3xl font-extrabold text-slate-900 dark:text-white">42</div>
        <div class="text-xs text-slate-400 dark:text-slate-500 font-medium flex items-center gap-1 mt-2">
            <span class="material-icons-round text-xs">done_all</span> 100% de cumplimiento
        </div>
    </div>

    <!-- Tarjeta: Unidades en Patio -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 p-6 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Unidades en Patio</span>
            <span class="p-1.5 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-lg material-icons-round">local_shipping</span>
        </div>
        <div class="text-3xl font-extrabold text-slate-900 dark:text-white">18</div>
        <div class="text-xs text-amber-500 font-semibold flex items-center gap-1 mt-2">
            <span class="material-icons-round text-xs">hourglass_empty</span> Promedio de estadía: 2.4 hrs
        </div>
    </div>

    <!-- Tarjeta: Alertas / Incidencias -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 p-6 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Sellos Rechazados</span>
            <span class="p-1.5 bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-lg material-icons-round">gpp_maybe</span>
        </div>
        <div class="text-3xl font-extrabold text-slate-900 dark:text-white">1</div>
        <div class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-2">
            <span class="material-icons-round text-xs">report_problem</span> Inspección detenida
        </div>
    </div>
</div>

<!-- 2. Sección de Gráficas (En medio del dashboard) -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

    <!-- Gráfica Principal: Flujo de Inspecciones (Barra / Línea combinada) -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 p-6 rounded-2xl shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white">Flujo de Inspecciones de Seguridad</h3>
                <p class="text-xs text-slate-400 dark:text-slate-500">Comparativa semanal de ingresos vs salidas inspeccionadas</p>
            </div>
            <span class="text-xs font-bold text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded-lg">C-TPAT / OEA</span>
        </div>
        <div class="h-80 relative">
            <canvas id="chartInspecciones"></canvas>
        </div>
    </div>

    <!-- Gráfica Secundaria: Distribución de Flota por Estatus (Donut) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 p-6 rounded-2xl shadow-sm flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white">Estatus de la Flota</h3>
                    <p class="text-xs text-slate-400 dark:text-slate-500">Distribución actual de tractores</p>
                </div>
            </div>
            <div class="h-56 relative flex items-center justify-center">
                <canvas id="chartFlota"></canvas>
            </div>
        </div>

        <!-- Leyendas Personalizadas de la Gráfica de Donut -->
        <div class="grid grid-cols-3 gap-2 border-t border-slate-100 dark:border-slate-800/80 pt-4 mt-4">
            <div class="text-center">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 mb-1"></span>
                <span class="block text-[10px] font-bold text-slate-400 uppercase">Activos</span>
                <span class="text-xs font-bold text-slate-800 dark:text-white">70%</span>
            </div>
            <div class="text-center">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-500 mb-1"></span>
                <span class="block text-[10px] font-bold text-slate-400 uppercase">Taller</span>
                <span class="text-xs font-bold text-slate-800 dark:text-white">20%</span>
            </div>
            <div class="text-center">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-slate-700 mb-1"></span>
                <span class="block text-[10px] font-bold text-slate-400 uppercase">Inactivo</span>
                <span class="text-xs font-bold text-slate-800 dark:text-white">10%</span>
            </div>
        </div>
    </div>

</div>

<!-- 3. Tabla de Actividad Reciente -->
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-200 dark:border-slate-800/80 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-slate-900 dark:text-white">Registro de Accesos y Altas de Hoy</h3>
            <p class="text-xs text-slate-400 dark:text-slate-500">Últimos movimientos validados en caseta</p>
        </div>
        <button class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm shadow-emerald-500/10">
            <span class="material-icons-round text-sm">add</span> Nuevo Acceso
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-100 dark:border-slate-800/50 bg-slate-50/50 dark:bg-slate-950/20">
                    <th class="p-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Operador</th>
                    <th class="p-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Vehículo / Placas</th>
                    <th class="p-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Inspección C-TPAT</th>
                    <th class="p-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Hora Entrada</th>
                    <th class="p-4 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Estatus</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50 text-sm">
                <tr>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-xs text-slate-600 dark:text-slate-300">
                                JR
                            </div>
                            <div>
                                <span class="block font-bold text-slate-900 dark:text-white">Jorge Ramírez</span>
                                <span class="block text-[10px] text-slate-400 dark:text-slate-500">Licencia Federal: Activa</span>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-slate-600 dark:text-slate-300">
                        <span class="font-semibold block">Kenworth T680</span>
                        <span class="text-xs text-slate-400 dark:text-slate-500">Placas: 45-AA-7B</span>
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2 py-1 rounded-lg border border-emerald-500/10">
                            <span class="material-icons-round text-sm">verified</span> Aprobado (17 Puntos)
                        </span>
                    </td>
                    <td class="p-4 text-slate-500 dark:text-slate-400">11:15 AM</td>
                    <td class="p-4">
                        <span class="inline-block px-2.5 py-1 text-xs font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-lg">En Patio</span>
                    </td>
                </tr>
                <tr>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-bold text-xs text-slate-600 dark:text-slate-300">
                                AM
                            </div>
                            <div>
                                <span class="block font-bold text-slate-900 dark:text-white">Arturo Mendoza</span>
                                <span class="block text-[10px] text-slate-400 dark:text-slate-500">Licencia Federal: Activa</span>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-slate-600 dark:text-slate-300">
                        <span class="font-semibold block">International ProStar</span>
                        <span class="text-xs text-slate-400 dark:text-slate-500">Placas: 12-BB-8C</span>
                    </td>
                    <td class="p-4">
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 dark:text-rose-400 bg-rose-500/10 px-2 py-1 rounded-lg border border-rose-500/10">
                            <span class="material-icons-round text-sm">gpp_bad</span> Sello no coincide
                        </span>
                    </td>
                    <td class="p-4 text-slate-500 dark:text-slate-400">10:42 AM</td>
                    <td class="p-4">
                        <span class="inline-block px-2.5 py-1 text-xs font-bold bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-lg">Retenido</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<!-- Inyección de Chart.js desde su CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Paletas de color dinámicas basadas en modo claro u oscuro
    const isDark = document.getElementById('html-tag').classList.contains('dark');
    const textMutedColor = isDark ? '#64748b' : '#94a3b8';
    const gridLineColor = isDark ? 'rgba(51, 65, 85, 0.4)' : 'rgba(226, 232, 240, 0.8)';

    // ==========================================
    // 1. Configuración Gráfico de Inspecciones
    // ==========================================
    const ctxInspecciones = document.getElementById('chartInspecciones').getContext('2d');
    new Chart(ctxInspecciones, {
        type: 'bar',
        data: {
            labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            datasets: [{
                    label: 'Entradas Inspeccionadas',
                    data: [35, 45, 42, 50, 48, 25],
                    backgroundColor: 'rgba(16, 185, 129, 0.85)', // Emerald
                    borderRadius: 8,
                    borderSkipped: false,
                },
                {
                    label: 'Salidas Inspeccionadas',
                    data: [30, 40, 38, 45, 42, 20],
                    backgroundColor: 'rgba(59, 130, 246, 0.85)', // Blue
                    borderRadius: 8,
                    borderSkipped: false,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        boxHeight: 12,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            size: 12,
                            weight: 'semibold',
                            family: 'sans-serif'
                        },
                        color: isDark ? '#f1f5f9' : '#334155'
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: textMutedColor,
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    grid: {
                        color: gridLineColor
                    },
                    ticks: {
                        color: textMutedColor,
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // ==========================================
    // 2. Configuración Gráfico de Flota (Dona)
    // ==========================================
    const ctxFlota = document.getElementById('chartFlota').getContext('2d');
    new Chart(ctxFlota, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [70, 20, 10],
                backgroundColor: [
                    '#10b981', // Emerald (Activos)
                    '#f59e0b', // Amber (Taller)
                    isDark ? '#334155' : '#cbd5e1' // Slate (Inactivos)
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: {
                legend: {
                    display: false
                } // Usamos leyendas personalizadas en HTML arriba
            }
        }
    });
</script>
@endpush