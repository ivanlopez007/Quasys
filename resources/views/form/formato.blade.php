<!-- Google Fonts & Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
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

    .hidden-modal {
        display: none !important;
    }
</style>

<div class="max-w-7xl mx-auto my-10 px-6">

    <!-- ENCABEZADO -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 uppercase tracking-tighter flex items-center">
                <span class="material-symbols-outlined mr-2 text-slate-900 text-3xl">description</span>
                Información General del Formato
            </h1>
            <p class="text-slate-500 text-sm italic font-medium text-slate-400">Control unificado de parámetros y disposición final</p>
        </div>
        <div class="flex gap-3">
            <button onclick="toggleModal()" class="bg-amber-400 text-slate-900 px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-amber-500 transition shadow-lg flex items-center">
                <span class="material-symbols-outlined mr-2">visibility</span> Ver Documento
            </button>
            <button class="bg-slate-900 text-white px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition shadow-lg flex items-center">
                <span class="material-symbols-outlined mr-2">download</span> Descargar PDF
            </button>
        </div>
    </div>

    <!-- TABLA UNIFICADA (DISEÑO ORIGINAL) -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-800 border-b border-slate-700">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-300 uppercase tracking-widest">Nombre y No. Uso</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-300 uppercase tracking-widest text-center">Versión / Nivel</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-300 uppercase tracking-widest">Área y Dueño</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-300 uppercase tracking-widest">Retención y Lugar</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-300 uppercase tracking-widest">Disposición Final</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 italic">
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-6">
                            <div class="text-sm font-black text-slate-800 uppercase leading-tight mb-1">Checklist de Embarques Críticos</div>
                            <span class="text-[10px] font-mono font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">FOR-LOG-022</span>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <div class="text-sm font-black text-slate-700">v4.0</div>
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Nivel 3 - Operativo</div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="text-xs font-bold text-slate-700 uppercase">Logística</div>
                            <div class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Ivan Lopez</div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="text-xs font-bold text-slate-700 uppercase flex items-center">
                                <span class="material-symbols-outlined text-sm mr-1 text-amber-500">schedule</span> 5 Años
                            </div>
                            <div class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Archivo Muerto Central</div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="inline-flex items-center text-[10px] font-black text-rose-600 bg-rose-50 px-3 py-1 rounded-full border border-rose-100 uppercase">
                                <span class="material-symbols-outlined text-sm mr-1">auto_delete</span> Trituración
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- POP UP (MODAL) PARA VER EL DOCUMENTO -->
    <div id="modalDocumento" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center z-50 px-4 hidden-modal">
        <div class="bg-slate-100 w-full max-w-3xl rounded-3xl shadow-2xl overflow-hidden scale-95 transition-all duration-300 border border-white/20" id="modalContent">

            <!-- Header Modal -->
            <div class="bg-slate-800 p-5 flex justify-between items-center border-b border-slate-700">
                <div class="flex items-center space-x-3 text-white">
                    <span class="material-symbols-outlined text-amber-400">visibility</span>
                    <span class="text-xs font-black uppercase tracking-widest">Previsualización de Formato</span>
                </div>
                <button onclick="toggleModal()" class="text-slate-400 hover:text-white transition">
                    <span class="material-symbols-outlined text-3xl">close</span>
                </button>
            </div>

            <!-- Contenido del Documento -->
            <div class="p-10">
                <div class="bg-white p-12 shadow-sm rounded-sm relative border border-gray-200">
                    <!-- Marca de Agua -->
                    <div class="absolute inset-0 flex items-center justify-center opacity-[0.03] pointer-events-none">
                        <span class="text-6xl font-black rotate-12 text-slate-900 uppercase">INDUSTRIAL HEFESTO</span>
                    </div>

                    <div class="relative z-10">
                        <div class="border-b-2 border-slate-900 pb-4 mb-8 flex justify-between items-center">
                            <div class="h-8 w-20 bg-slate-900 flex items-center justify-center rounded">
                                <span class="text-[7px] text-white font-black italic tracking-tighter">HEFESTO</span>
                            </div>
                            <div class="text-right">
                                <h4 class="text-xs font-black text-slate-800">FOR-LOG-022</h4>
                                <p class="text-[8px] text-slate-400 font-bold">VERSIÓN 4.0</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="h-4 bg-slate-50 border-b border-gray-100"></div>
                            <div class="h-4 bg-slate-50 border-b border-gray-100"></div>
                            <div class="grid grid-cols-2 gap-8 mt-10">
                                <div class="border-t border-slate-300 pt-1 text-center">
                                    <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest italic">Firma de Elaboración</span>
                                </div>
                                <div class="border-t border-slate-300 pt-1 text-center">
                                    <span class="text-[7px] font-black text-slate-400 uppercase tracking-widest italic">Firma de Autorización</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-center">
                    <button onclick="toggleModal()" class="bg-slate-900 text-white px-10 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg hover:bg-slate-800 transition">
                        Cerrar Vista Previa
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- SCRIPT PARA CONTROLAR EL MODAL -->
<script>
    function toggleModal() {
        const modal = document.getElementById('modalDocumento');
        const modalContent = document.getElementById('modalContent');

        if (modal.classList.contains('hidden-modal')) {
            modal.classList.remove('hidden-modal');
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        } else {
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden-modal');
            }, 200);
        }
    }

    // Cerrar al hacer clic fuera del modal
    window.onclick = function(event) {
        const modal = document.getElementById('modalDocumento');
        if (event.target == modal) {
            toggleModal();
        }
    }
</script>