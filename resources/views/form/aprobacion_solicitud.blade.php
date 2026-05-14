<!-- Google Fonts & Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<script src="https://cdn.tailwindcss.com"></script>

<style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
    }

    .material-symbols-outlined {
        vertical-align: middle;
    }
</style>

<div class="max-w-7xl mx-auto my-10 px-6">

    <!-- ENCABEZADO IGUAL AL ANTERIOR -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tighter flex items-center">
                <span class="material-symbols-outlined mr-2 text-slate-900 text-3xl">task_alt</span>
                Panel de Aprobaciones
            </h1>
            <p class="text-slate-500 text-sm">Gestión y revisión de solicitudes de procedimientos</p>
        </div>

        <!-- Filtro por Área con el mismo estilo del formulario -->
        <div class="flex flex-col min-w-[250px]">
            <label class="text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1 text-right">Filtrar por Área</label>
            <select class="w-full border border-gray-200 bg-white p-2.5 rounded-xl focus:ring-2 focus:ring-slate-900 outline-none text-sm font-semibold text-slate-700 shadow-sm transition">
                <option value="">Todas las Áreas</option>
                <option value="it">Sistemas / IT</option>
                <option value="rh">Recursos Humanos</option>
                <option value="calidad">Control de Calidad</option>
            </select>
        </div>
    </div>

    <!-- BOTONES DE FILTRO TIPO TABS (Seleccionables) -->
    <div class="flex space-x-2 mb-6 bg-white p-1.5 rounded-2xl shadow-sm border border-gray-100 w-fit">
        <!-- Botón Seleccionado -->
        <button class="flex items-center px-6 py-2.5 rounded-xl text-sm font-bold transition-all bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm">
            <span class="material-symbols-outlined mr-2 text-lg">check_circle</span>
            Aprobados
            <span class="ml-2 bg-emerald-700 text-white text-[10px] px-2 py-0.5 rounded-full">12</span>
        </button>
        <!-- Botones No Seleccionados -->
        <button class="flex items-center px-6 py-2.5 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">
            <span class="material-symbols-outlined mr-2 text-lg text-rose-500">cancel</span>
            Rechazados
        </button>
        <button class="flex items-center px-6 py-2.5 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">
            <span class="material-symbols-outlined mr-2 text-lg text-amber-500">do_not_disturb_on</span>
            Canceladas
        </button>
    </div>

    <!-- TABLA CON ENCABEZADO OSCURO (DISEÑO ANTERIOR) -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800 border-b border-slate-700">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-300 uppercase tracking-wider">Solicitud</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-300 uppercase tracking-wider">Área</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-300 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-300 uppercase tracking-wider">Procedimiento</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-300 uppercase tracking-wider">Fecha Solicitud</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-300 uppercase tracking-wider">Solicitante</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-300 uppercase tracking-wider text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-5">
                            <span class="text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">#SOL-2026-04</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-bold text-slate-700">Control de Calidad</div>
                        </td>
                        <td class="px-6 py-5">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg border border-blue-100 bg-blue-50 text-blue-600 uppercase italic">Actualización</span>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-sm font-semibold text-slate-800 truncate max-w-[200px]">Manual de Auditoría ISO</div>
                        </td>
                        <td class="px-6 py-5 text-sm text-slate-600">
                            13 Mayo, 2026
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-slate-800 text-white flex items-center justify-center text-[10px] font-bold shadow-sm mr-2 italic">IL</div>
                                <span class="text-sm font-medium text-slate-700">Ivan Lopez</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <!-- Solo botón de Visualizar -->
                            <button class="text-slate-400 hover:text-slate-900 transition-colors inline-flex p-2 hover:bg-slate-100 rounded-lg">
                                <span class="material-symbols-outlined text-2xl">visibility</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>