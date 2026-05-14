<!-- Google Fonts: Inter y Material Symbols -->
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
        font-size: 22px;
    }

    input,
    select,
    textarea {
        font-size: 0.9rem !important;
    }
</style>

<div class="max-w-6xl mx-auto my-10 px-4">

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <!-- Encabezado -->
        <div class="bg-slate-800 px-8 py-5 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-white uppercase tracking-widest">Control de Documentación</h2>
                <p class="text-slate-400 text-xs mt-1">Complete los campos para el registro oficial en el sistema</p>
            </div>
            <span class="material-symbols-outlined text-slate-400 font-light text-4xl">edit_note</span>
        </div>

        <form action="#" method="POST" class="p-8 space-y-10">

            <!-- BLOQUE 1: IDENTIFICACIÓN (Separados arriba) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="flex flex-col">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center">
                        <span class="material-symbols-outlined text-sm mr-1">tag</span> No. Solicitud
                    </label>
                    <input type="text" readonly value="SOL-2026-088" class="bg-slate-50 border-b-2 border-gray-200 p-2 outline-none text-slate-700 font-mono focus:border-slate-900 transition">
                </div>
                <div class="flex flex-col">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center">
                        <span class="material-symbols-outlined text-sm mr-1">person</span> Solicitante
                    </label>
                    <input type="text" placeholder="Nombre completo" class="border-b-2 border-gray-200 p-2 outline-none focus:border-slate-900 transition">
                </div>
                <div class="flex flex-col">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 flex items-center">
                        <span class="material-symbols-outlined text-sm mr-1">calendar_today</span> Fecha
                    </label>
                    <input type="date" value="2026-05-13" class="border-b-2 border-gray-200 p-2 outline-none focus:border-slate-900 transition">
                </div>
            </div>

            <!-- BLOQUE 2: CLASIFICACIÓN (Juntos con separación leve) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-5 bg-slate-50 rounded-xl border border-slate-100">
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-slate-400 uppercase mb-1">Tipo de Solicitud</label>
                    <select class="border border-gray-300 p-2 rounded-lg bg-white focus:ring-2 focus:ring-slate-900 outline-none">
                        <option>Actualización de Documento</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-slate-400 uppercase mb-1">Nivel</label>
                    <select class="border border-gray-300 p-2 rounded-lg bg-white focus:ring-2 focus:ring-slate-900 outline-none">
                        <option>Nivel 2: Táctico</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label class="text-[10px] font-bold text-slate-400 uppercase mb-1">Subnivel</label>
                    <select class="border border-gray-300 p-2 rounded-lg bg-white focus:ring-2 focus:ring-slate-900 outline-none">
                        <option>Administrativo</option>
                    </select>
                </div>
            </div>

            <!-- BLOQUE 3: CUERPO DEL FORMULARIO (Todos juntos) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <!-- Procedimiento Grande -->
                <div class="flex flex-col md:col-span-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase mb-2">Procedimiento (Nombre Completo)</label>
                    <select class="border border-gray-300 p-3 rounded-lg bg-white focus:ring-2 focus:ring-slate-900 outline-none text-lg font-semibold text-slate-800 shadow-sm">
                        <option>Procedimiento Operativo Estándar de Almacén</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-[11px] font-bold text-slate-500 uppercase mb-2">Área Responsable</label>
                    <input type="text" class="border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none">
                </div>

                <div class="flex flex-col md:flex-row gap-2">
                    <div class="flex-1">
                        <label class="text-[11px] font-bold text-slate-500 uppercase mb-2 block">No. Proc</label>
                        <input type="text" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none font-mono">
                    </div>
                    <div class="w-20">
                        <label class="text-[11px] font-bold text-slate-500 uppercase mb-2 block">Ver.</label>
                        <input type="text" placeholder="01" class="w-full border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none text-center">
                    </div>
                </div>

                <div class="flex flex-col md:col-span-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase mb-2">Dueño del Procedimiento</label>
                    <input type="text" class="border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none" placeholder="Nombre del responsable">
                </div>

                <div class="flex flex-col">
                    <label class="text-[11px] font-bold text-slate-500 uppercase mb-2">Aprobado Por:</label>
                    <input type="text" class="border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none">
                </div>

                <div class="flex flex-col">
                    <label class="text-[11px] font-bold text-slate-500 uppercase mb-2">Área de Aprobación</label>
                    <input type="text" class="border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none">
                </div>

                <div class="flex flex-col">
                    <label class="text-[11px] font-bold text-slate-500 uppercase mb-2">Lugar de Retención</label>
                    <select class="border border-gray-300 p-3 rounded-lg bg-white focus:ring-2 focus:ring-slate-900 outline-none">
                        <option>Archivo General</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-[11px] font-bold text-slate-500 uppercase mb-2">Periodo Retención</label>
                    <select class="border border-gray-300 p-3 rounded-lg bg-white focus:ring-2 focus:ring-slate-900 outline-none">
                        <option>3 Años</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label class="text-[11px] font-bold text-slate-500 uppercase mb-2">Disposición Final</label>
                    <select class="border border-gray-300 p-3 rounded-lg bg-white focus:ring-2 focus:ring-slate-900 outline-none">
                        <option>Destrucción Documental</option>
                    </select>
                </div>

                <div class="flex flex-col md:col-span-4">
                    <label class="text-[11px] font-bold text-slate-500 uppercase mb-2">Comentarios / Observaciones</label>
                    <textarea rows="2" class="border border-gray-300 p-3 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none resize-none"></textarea>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-10 py-3 bg-slate-900 text-white rounded-xl hover:bg-slate-800 shadow-xl transition-all transform hover:-translate-y-1 font-bold flex items-center uppercase tracking-widest text-xs">
                    <span class="material-symbols-outlined mr-2">send</span> Registrar Documento
                </button>
            </div>
        </form>
    </div>

    <!-- TABLA ABAJO -->
    <div class="mt-12 bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-100 border-b border-gray-200">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-600 uppercase italic">No.</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-600 uppercase italic">Procedimiento</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-600 uppercase italic text-center">Localidad</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-600 uppercase italic">Dueño</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-600 uppercase italic text-center">Vista</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-blue-50/50 transition cursor-default">
                    <td class="px-6 py-4 text-xs font-mono text-slate-500">PR-012</td>
                    <td class="px-6 py-4 text-sm font-bold text-slate-800">Manual de Seguridad e Higiene</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-[10px] font-bold uppercase">Planta Saltillo</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">Ing. Alberto Ramos</td>
                    <td class="px-6 py-4 text-center">
                        <button class="text-slate-400 hover:text-slate-900 transition">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>