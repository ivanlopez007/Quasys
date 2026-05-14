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

    /* Clase para ocultar el modal */
    .hidden-modal {
        display: none !important;
    }
</style>

<div class="max-w-7xl mx-auto my-10 px-6">

    <!-- ENCABEZADO Y FILTROS -->
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-6">
        <div>
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tighter flex items-center">
                <span class="material-symbols-outlined mr-2 text-slate-900 text-3xl">history</span>
                Historial de Procedimientos
            </h1>
            <p class="text-slate-500 text-sm">Consulta de versiones y trazabilidad de documentos</p>
        </div>

        <div class="flex gap-4">
            <div class="flex flex-col min-w-[200px]">
                <label class="text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Área</label>
                <select class="border border-gray-200 bg-white p-2.5 rounded-xl focus:ring-2 focus:ring-slate-900 outline-none text-sm font-semibold text-slate-700 shadow-sm">
                    <option value="">Todas las Áreas</option>
                    <option value="it">Sistemas</option>
                </select>
            </div>
            <div class="flex flex-col min-w-[200px]">
                <label class="text-[10px] font-bold text-slate-400 uppercase mb-1 ml-1">Nivel</label>
                <select class="border border-gray-200 bg-white p-2.5 rounded-xl focus:ring-2 focus:ring-slate-900 outline-none text-sm font-semibold text-slate-700 shadow-sm">
                    <option value="">Todos los Niveles</option>
                    <option value="1">Nivel 1</option>
                </select>
            </div>
        </div>
    </div>

    <!-- TABLA PRINCIPAL -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800 border-b border-slate-700">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-300 uppercase tracking-wider">Procedimiento</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-300 uppercase tracking-wider">No. Proc</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-300 uppercase tracking-wider text-center">Versión Actual</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-300 uppercase tracking-wider text-center">Fecha</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-300 uppercase tracking-wider">Área</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-300 uppercase tracking-wider text-center italic">Historial</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-5 font-bold text-slate-800 text-sm">Manual de Seguridad e Higiene</td>
                        <td class="px-6 py-5 text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded">MSH-001</td>
                        <td class="px-6 py-5 text-center text-sm font-black text-slate-700">v3.0</td>
                        <td class="px-6 py-5 text-center text-xs text-slate-500 font-bold">13/05/2026</td>
                        <td class="px-6 py-5 text-[10px] font-bold text-slate-600 uppercase">Recursos Humanos</td>
                        <td class="px-6 py-5 text-center">
                            <!-- Botón para abrir Modal -->
                            <button onclick="toggleModal()" class="text-slate-900 bg-amber-400 hover:bg-amber-500 transition-all p-2 rounded-lg shadow-sm flex items-center justify-center mx-auto group">
                                <span class="material-symbols-outlined text-xl group-hover:rotate-180 transition-transform duration-500">history_toggle_off</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- POP UP (MODAL) - Oculto por defecto -->
    <div id="modalHistorial" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-50 px-4 hidden-modal">
        <div class="bg-white w-full max-w-4xl rounded-3xl shadow-2xl overflow-hidden border border-white/20 scale-95 transition-all duration-300" id="modalContent">

            <!-- Encabezado del Pop-up -->
            <div class="bg-slate-800 p-6 flex justify-between items-center border-b border-slate-700">
                <div class="flex items-center space-x-3">
                    <div class="bg-amber-400 p-2 rounded-xl text-slate-900">
                        <span class="material-symbols-outlined font-bold">manage_search</span>
                    </div>
                    <div>
                        <h3 class="text-white font-black uppercase text-sm tracking-wider">Historial de Versiones</h3>
                        <p class="text-slate-400 text-[11px] uppercase tracking-widest">Manual de Seguridad e Higiene</p>
                    </div>
                </div>
                <button onclick="toggleModal()" class="text-slate-400 hover:text-white transition">
                    <span class="material-symbols-outlined text-3xl">close</span>
                </button>
            </div>

            <!-- Tabla dentro del Pop-up -->
            <div class="p-8">
                <div class="overflow-hidden border border-gray-100 rounded-2xl shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Procedimiento</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Formato</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Versión</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest text-center">Fecha</th>
                                <th class="px-6 py-4 text-[10px] font-black text-slate-500 uppercase tracking-widest">Área</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <!-- Versión Actual -->
                            <tr class="bg-emerald-50/40">
                                <td class="px-6 py-4 text-xs font-bold text-slate-800">Manual de Seguridad e Higiene</td>
                                <td class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase italic">PDF / Digital</td>
                                <td class="px-6 py-4 text-center text-xs font-black text-emerald-700">v3.0</td>
                                <td class="px-6 py-4 text-center text-[10px] font-bold text-slate-500 italic">13/05/2026</td>
                                <td class="px-6 py-4 text-[10px] font-bold text-slate-600 uppercase">RRHH</td>
                            </tr>
                            <!-- Versión Anterior -->
                            <tr class="opacity-60">
                                <td class="px-6 py-4 text-xs font-medium text-slate-600">Manual de Seguridad e Higiene</td>
                                <td class="px-6 py-4 text-[10px] font-medium text-slate-400 uppercase">Físico / Archivo</td>
                                <td class="px-6 py-4 text-center text-xs font-bold text-slate-500">v2.0</td>
                                <td class="px-6 py-4 text-center text-[10px] font-medium text-slate-400">10/01/2025</td>
                                <td class="px-6 py-4 text-[10px] font-medium text-slate-500 uppercase">RRHH</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <button onclick="toggleModal()" class="bg-slate-900 text-white px-8 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest shadow-lg hover:bg-slate-800 transition">
                        Cerrar Historial
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- SCRIPT PARA CONTROLAR EL MODAL -->
<script>
    function toggleModal() {
        const modal = document.getElementById('modalHistorial');
        const modalContent = document.getElementById('modalContent');

        if (modal.classList.contains('hidden-modal')) {
            // Abrir
            modal.classList.remove('hidden-modal');
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        } else {
            // Cerrar
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden-modal');
            }, 200);
        }
    }

    // Cerrar si hacen clic fuera del cuadro blanco
    window.onclick = function(event) {
        const modal = document.getElementById('modalHistorial');
        if (event.target == modal) {
            toggleModal();
        }
    }
</script>