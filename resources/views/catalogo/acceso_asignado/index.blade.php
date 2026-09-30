@extends('layout.layout')

@section('content')
<div class="space-y-6">

    <!-- ALERTAS -->
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

    <!-- ENCABEZADO -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Asignación de Accesos a Roles</h1>
            <p class="text-xs font-semibold text-slate-400 mt-0.5">Asigna qué submenús y opciones del sistema puede visualizar cada rol</p>
        </div>
    </div>

    <!-- SELECCIÓN DE ROL Y FORMULARIO DE PERMISOS -->
    <form action="{{ route('catalogo.acceso_asignado.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- SELECTOR DE ROL -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="w-full sm:w-1/2 space-y-1">
                <label for="rol_select" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Seleccionar Rol de Usuario <span class="text-rose-500">*</span>
                </label>
                <select id="rol_select" name="rol_id" onchange="changeRol(this.value)" class="w-full px-4 py-2.5 text-xs font-bold border border-slate-200 rounded-xl focus:outline-none focus:border-slate-800 text-slate-800 bg-slate-50/50 transition">
                    <option value="">-- Selecciona un Rol --</option>
                    @foreach($roles as $rol)
                    <option value="{{ $rol->id }}" {{ $selectedRolId == $rol->id ? 'selected' : '' }}>
                        {{ $rol->nombre ?? $rol->rol }}
                    </option>
                    @endforeach
                </select>
            </div>

            @if($selectedRolId)
            <div class="flex items-center gap-3 w-full sm:w-auto justify-end pt-2 sm:pt-0">
                <button type="button" onclick="toggleAllCheckboxes(true)" class="px-3 py-2 text-[11px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    <i class="fas fa-check-double mr-1"></i> Marcar Todo
                </button>
                <button type="button" onclick="toggleAllCheckboxes(false)" class="px-3 py-2 text-[11px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    <i class="fas fa-square mr-1"></i> Desmarcar Todo
                </button>
                <button type="submit" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md transition">
                    <i class="fas fa-floppy-disk"></i>
                    <span>Guardar Cambios</span>
                </button>
            </div>
            @endif
        </div>

        <!-- GRID DE MENÚS Y SUBMENÚS -->
        @if($selectedRolId)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @forelse($menus as $menu)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    <!-- Header del Menú Padre -->
                    <div class="bg-slate-900 text-white px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center gap-2 font-bold text-xs">
                            <i class="{{ $menu->icono ?: 'fas fa-folder' }} text-slate-400"></i>
                            <span>{{ $menu->menu ?? $menu->nombre }}</span>
                        </div>
                        <button type="button" onclick="toggleGroup('menu_group_{{ $menu->id }}')" class="text-[10px] bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold px-2 py-1 rounded-lg transition">
                            Seleccionar Grupo
                        </button>
                    </div>

                    <!-- Lista de Submenús -->
                    <div class="p-4 space-y-3 menu_group_{{ $menu->id }}">
                        @forelse($menu->subMenus as $subMenu)
                        @php
                        $isChecked = in_array($subMenu->id, $assignedSubMenuIds);
                        @endphp
                        <label class="flex items-start gap-3 p-2.5 rounded-xl border border-slate-100 hover:bg-slate-50 transition cursor-pointer group">
                            <input type="checkbox" name="sub_menus[]" value="{{ $subMenu->id }}" {{ $isChecked ? 'checked' : '' }} class="sub-menu-checkbox mt-0.5 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <i class="{{ $subMenu->icono ?: 'fas fa-link' }} text-slate-400 text-xs group-hover:text-slate-700 transition"></i>
                                    <span class="text-xs font-bold text-slate-800 block truncate">{{ $subMenu->sub_menu }}</span>
                                </div>
                                @if($subMenu->url)
                                <span class="text-[10px] font-mono text-slate-400 block truncate">{{ $subMenu->url }}</span>
                                @endif
                            </div>
                        </label>
                        @empty
                        <p class="text-xs text-slate-400 font-medium italic text-center py-4">Sin submenús en este menú</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
                <i class="fas fa-folder-open text-4xl mb-3 block text-slate-300"></i>
                No hay menús registrados o activos en el sistema.
            </div>
            @endforelse
        </div>

        <!-- BOTÓN FLOTANTE GUARDAR ABAJO -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-6 py-3 rounded-xl shadow-lg transition">
                <i class="fas fa-floppy-disk"></i>
                <span>Guardar Cambios de Accesos</span>
            </button>
        </div>
        @else
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400">
            <i class="fas fa-user-shield text-4xl mb-3 block text-slate-300"></i>
            <p class="text-xs font-bold text-slate-600">Por favor, selecciona un rol de la lista para gestionar sus accesos.</p>
        </div>
        @endif
    </form>
</div>

<!-- JAVASCRIPT -->
<script>
    // Cambiar de rol en la URL al seleccionar el option
    function changeRol(rolId) {
        if (rolId) {
            window.location.href = "{{ route('catalogo.acceso_asignado.index') }}?rol_id=" + rolId;
        }
    }

    // Marcar / Desmarcar todos los checkboxes de la pantalla
    function toggleAllCheckboxes(status) {
        const checkboxes = document.querySelectorAll('.sub-menu-checkbox');
        checkboxes.forEach(cb => cb.checked = status);
    }

    // Marcar / Desmarcar por grupo (Menú Padre)
    function toggleGroup(className) {
        const checkboxes = document.querySelectorAll('.' + className + ' .sub-menu-checkbox');
        if (checkboxes.length === 0) return;

        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    }
</script>
@endsection