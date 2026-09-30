@extends('layout.layout')

@section('content')
<div class="space-y-6">

    <!-- ALERTAS DE ÉXITO Y ERRORES DE VALIDACIÓN -->
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-bold flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-circle-check text-emerald-500 text-sm"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs font-bold space-y-1">
        <div class="flex items-center gap-2 mb-1">
            <i class="fas fa-circle-exclamation text-rose-500 text-sm"></i>
            <span>Ocurrieron errores al procesar la solicitud:</span>
        </div>
        <ul class="list-disc list-inside text-[11px] font-medium text-rose-700">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- ENCABEZADO Y ACCIONES PRINCIPALES -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Catálogo de Menús</h1>
            <p class="text-xs font-semibold text-slate-400 mt-0.5">Gestión de la navegación principal del sistema</p>
        </div>
        <div>
            <button onclick="openCreateModal()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg transition">
                <i class="fas fa-plus"></i>
                <span>Nuevo Menú</span>
            </button>
        </div>
    </div>

    <!-- TARJETA CONTENEDORA / TABLA -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <!-- BARRA DE BÚSQUEDA Y FILTROS -->
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-3 justify-between items-center">
            <div class="relative w-full sm:w-80">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Buscar menú..." class="w-full pl-9 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-slate-400 text-slate-700 font-medium transition">
            </div>
            <div class="text-xs text-slate-400 font-semibold w-full sm:w-auto text-right">
                Total: <span class="text-slate-800 font-bold">{{ count($menus ?? []) }}</span> registros
            </div>
        </div>

        <!-- TABLA -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="menusTable">
                <thead>
                    <tr class="bg-slate-900 text-white text-[11px] uppercase tracking-wider font-bold">
                        <th class="py-3.5 px-6">ID</th>
                        <th class="py-3.5 px-6">Orden</th>
                        <th class="py-3.5 px-6">Icono</th>
                        <th class="py-3.5 px-6">Menú</th>
                        <th class="py-3.5 px-6">Estatus</th>
                        <th class="py-3.5 px-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($menus ?? [] as $menuItem)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-6 font-bold text-slate-400">#{{ $menuItem->id }}</td>
                        <td class="py-3.5 px-6 font-bold text-slate-800">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 text-slate-700 font-black border border-slate-200">
                                {{ $menuItem->orden }}
                            </span>
                        </td>
                        <td class="py-3.5 px-6">
                            @if($menuItem->icono)
                            <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 text-sm">
                                <i class="{{ $menuItem->icono }}"></i>
                            </div>
                            @else
                            <span class="text-slate-300 italic text-[11px]">Sin icono</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 font-bold text-slate-800">{{ $menuItem->menu }}</td>
                        <td class="py-3.5 px-6">
                            @if($menuItem->activo)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactivo
                            </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 text-right space-x-1">
                            <button onclick="openEditModal({{ $menuItem->id }}, '{{ addslashes($menuItem->menu) }}', '{{ addslashes($menuItem->icono ?? '') }}', {{ $menuItem->orden }}, {{ $menuItem->activo ? 'true' : 'false' }})" class="p-2 text-slate-400 hover:text-slate-800 rounded-lg hover:bg-slate-100 transition" title="Editar">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <button onclick="openDeleteModal({{ $menuItem->id }}, '{{ addslashes($menuItem->menu) }}')" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Eliminar">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 font-medium">
                            <i class="fas fa-folder-open text-4xl mb-3 block text-slate-300"></i>
                            No hay menús registrados en el catálogo.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: CREAR / EDITAR                      -->
<!-- ========================================== -->
<div id="menuModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
        <!-- Header del Modal -->
        <div class="px-6 py-4 bg-slate-900 text-white flex items-center justify-between">
            <h3 id="modalTitle" class="font-bold text-sm tracking-tight flex items-center gap-2">
                <i class="fas fa-bars text-slate-400"></i>
                <span>Nuevo Menú</span>
            </h3>
            <button onclick="closeMenuModal()" class="text-slate-400 hover:text-white transition text-xs p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Formulario del Modal -->
        <form id="menuForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">

            <!-- Campo Menú -->
            <div>
                <label for="menuInput" class="block text-xs font-bold text-slate-700 mb-1.5">Nombre del Menú <span class="text-rose-500">*</span></label>
                <input type="text" id="menuInput" name="menu" required placeholder="Ej. Configuración, Catálogos, Reportes..." class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-slate-800 text-slate-800 font-medium transition">
            </div>

            <!-- Campo Icono FontAwesome -->
            <div>
                <label for="iconoInput" class="block text-xs font-bold text-slate-700 mb-1.5">Icono (FontAwesome)</label>
                <div class="relative">
                    <input type="text" id="iconoInput" name="icono" placeholder="Ej. fas fa-cog, fas fa-folder, fas fa-users" class="w-full pl-3.5 pr-10 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-slate-800 text-slate-800 font-medium transition" oninput="updateIconPreview(this.value)">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 text-xs">
                        <i id="iconPreview" class="fas fa-icons"></i>
                    </div>
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Ingresa clases de FontAwesome para previsualizar.</p>
            </div>

            <!-- Campo Orden -->
            <div>
                <label for="ordenInput" class="block text-xs font-bold text-slate-700 mb-1.5">Orden de Aparición <span class="text-rose-500">*</span></label>
                <input type="number" id="ordenInput" name="orden" required min="0" value="0" placeholder="0" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl focus:outline-none focus:border-slate-800 text-slate-800 font-medium transition">
            </div>

            <!-- Campo Activo -->
            <div class="pt-1">
                <label class="relative inline-flex items-center cursor-pointer gap-3">
                    <input type="checkbox" id="activoInput" name="activo" value="1" class="sr-only peer" checked>
                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-slate-900"></div>
                    <span class="text-xs font-bold text-slate-700">Estatus Activo</span>
                </label>
            </div>

            <!-- Footer del Modal -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeMenuModal()" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white rounded-xl shadow-md transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: CONFIRMAR ELIMINACIÓN               -->
<!-- ========================================== -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl border border-slate-100 overflow-hidden text-center p-6 space-y-4">
        <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mx-auto text-xl">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
        <div>
            <h3 class="font-bold text-slate-800 text-sm">¿Eliminar Menú?</h3>
            <p class="text-xs text-slate-500 mt-1">Estás a punto de eliminar el menú <strong id="deleteMenuName" class="text-slate-800"></strong>. Esta acción no se puede deshacer.</p>
        </div>
        <form id="deleteForm" method="POST" action="" class="flex items-center justify-center gap-2 pt-2">
            @csrf
            <input type="hidden" name="_method" value="DELETE">
            <button type="button" onclick="closeDeleteModal()" class="w-full py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">
                Cancelar
            </button>
            <button type="submit" class="w-full py-2 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-md transition">
                Sí, Eliminar
            </button>
        </form>
    </div>
</div>

<!-- JAVASCRIPT DE FUNCIONALIDAD -->
<script>
    const baseUrl = "{{ route('catalogo.menu.index') }}";

    function updateIconPreview(val) {
        const preview = document.getElementById('iconPreview');
        preview.className = val.trim() !== '' ? val : 'fas fa-icons';
    }

    // Modal Crear / Editar
    function openCreateModal() {
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-bars text-slate-400"></i><span>Nuevo Menú</span>';
        document.getElementById('menuForm').action = baseUrl;
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('menuInput').value = '';
        document.getElementById('iconoInput').value = '';
        document.getElementById('ordenInput').value = 0;
        document.getElementById('activoInput').checked = true;
        updateIconPreview('');
        document.getElementById('menuModal').classList.remove('hidden');
    }

    function openEditModal(id, menu, icono, orden, activo) {
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-pen-to-square text-slate-400"></i><span>Editar Menú</span>';
        document.getElementById('menuForm').action = baseUrl + '/' + id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('menuInput').value = menu;
        document.getElementById('iconoInput').value = icono;
        document.getElementById('ordenInput').value = orden;
        document.getElementById('activoInput').checked = (activo === true || activo === 1 || activo === '1');
        updateIconPreview(icono);
        document.getElementById('menuModal').classList.remove('hidden');
    }

    function closeMenuModal() {
        document.getElementById('menuModal').classList.add('hidden');
    }

    // Modal Eliminar
    function openDeleteModal(id, nombre) {
        document.getElementById('deleteMenuName').innerText = nombre;
        document.getElementById('deleteForm').action = baseUrl + '/' + id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Búsqueda simple en vivo dentro de la tabla
    function filterTable() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#menusTable tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(input) ? '' : 'none';
        });
    }
</script>
@endsection