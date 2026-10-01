@extends('layout.layout')

@section('content')
<div class="space-y-6">

    <!-- ALERTAS DE ÉXITO O ERROR -->
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

    @if(session('warning'))
    <div class="bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl text-xs font-bold flex items-center justify-between">
        <div class="flex items-center gap-2">
            <i class="fas fa-triangle-exclamation text-amber-500 text-sm"></i>
            <span>{{ session('warning') }}</span>
        </div>
        <button onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-800">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs font-bold space-y-1">
        <div class="flex items-center gap-2 mb-1">
            <i class="fas fa-circle-exclamation text-rose-500 text-sm"></i>
            <span>Errores al procesar la solicitud:</span>
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
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Catálogo de Usuarios</h1>
            <p class="text-xs font-semibold text-slate-400 mt-0.5">Administración de credenciales, datos personales y asignaciones organizacionales</p>
        </div>
        <div>
            <button onclick="openCreateModal()" class="inline-flex items-center gap-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-lg transition">
                <i class="fas fa-user-plus"></i>
                <span>Nuevo Usuario</span>
            </button>
        </div>
    </div>

    <!-- PESTAÑAS: ACTIVOS / ELIMINADOS -->
    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl w-fit">
        <a href="{{ route('usuarios.index') }}"
           class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $verEliminados ? 'text-slate-500 hover:text-slate-800' : 'bg-white text-slate-900 shadow-sm' }}">
            <i class="fas fa-user-check mr-1.5"></i> Activos
            <span class="ml-1 px-1.5 py-0.5 rounded-md text-[10px] {{ $verEliminados ? 'bg-slate-200 text-slate-500' : 'bg-slate-900 text-white' }}">{{ $totalActivos }}</span>
        </a>
        <a href="{{ route('usuarios.index', ['ver' => 'eliminados']) }}"
           class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $verEliminados ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
            <i class="fas fa-user-slash mr-1.5"></i> Eliminados
            <span class="ml-1 px-1.5 py-0.5 rounded-md text-[10px] {{ $verEliminados ? 'bg-rose-600 text-white' : 'bg-slate-200 text-slate-500' }}">{{ $totalEliminados }}</span>
        </a>
    </div>

    <!-- TABLA DE USUARIOS -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row gap-3 justify-between items-center">
            <div class="relative w-full sm:w-80">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Buscar por nombre, correo, RFC o área..." class="w-full pl-9 pr-4 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 text-slate-700 font-medium transition">
            </div>
            <div class="text-xs text-slate-400 font-semibold w-full sm:w-auto text-right">
                Total: <span class="text-slate-800 font-bold">{{ count($usuarios ?? []) }}</span> usuarios
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="userTable">
                <thead>
                    <tr class="bg-slate-900 text-white text-[11px] uppercase tracking-wider font-bold">
                        <th class="py-3.5 px-6">Usuario / Correo</th>
                        <th class="py-3.5 px-6">Rol</th>
                        <th class="py-3.5 px-6">Ubicación / Área</th>
                        <th class="py-3.5 px-6">Jefe Inmediato</th>
                        <th class="py-3.5 px-6">A su cargo</th>
                        <th class="py-3.5 px-6">{{ $verEliminados ? 'Eliminado el' : 'Estatus' }}</th>
                        <th class="py-3.5 px-6 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($usuarios ?? [] as $user)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3.5 px-6">
                            <div class="font-bold text-slate-800">
                                {{ $user->informacion ? $user->informacion->nombre . ' ' . $user->informacion->apellidos : 'Sin Nombre' }}
                            </div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $user->email }}</div>
                        </td>
                        <td class="py-3.5 px-6">
                            @if($user->rol)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $user->rol->rol }}
                            </span>
                            @else
                            <span class="text-slate-400 italic">Sin Rol</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6 space-y-0.5">
                            <div class="text-slate-800 font-bold">{{ $user->area->area ?? 'Sin Área' }}</div>
                            <div class="text-[10px] text-slate-400">
                                {{ $user->planta->planta ?? '—' }} | {{ $user->localidad->localidad ?? '—' }}
                            </div>
                        </td>
                        <td class="py-3.5 px-6 font-semibold text-slate-600">
                            {{ $user->jefeInmediato?->informacion ? $user->jefeInmediato->informacion->nombre . ' ' . $user->jefeInmediato->informacion->apellidos : ($user->jefeInmediato?->email ?? '—') }}
                            @if($user->jefeInmediato?->trashed())
                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-md text-[9px] font-bold uppercase bg-rose-50 text-rose-600 border border-rose-200" title="Su jefe inmediato está eliminado: asígnale otro">Eliminado</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6">
                            @php($aCargo = $user->documentos_vigentes_count + $user->solicitudes_pendientes_count + $user->subordinados_count)
                            @if($aCargo > 0)
                            <div class="flex flex-wrap gap-1">
                                @if($user->documentos_vigentes_count)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200" title="Documentos vigentes">
                                    <i class="fas fa-file-lines text-slate-400"></i> {{ $user->documentos_vigentes_count }}
                                </span>
                                @endif
                                @if($user->solicitudes_pendientes_count)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200" title="Solicitudes pendientes">
                                    <i class="fas fa-hourglass-half text-amber-400"></i> {{ $user->solicitudes_pendientes_count }}
                                </span>
                                @endif
                                @if($user->subordinados_count)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200" title="Subordinados">
                                    <i class="fas fa-users text-slate-400"></i> {{ $user->subordinados_count }}
                                </span>
                                @endif
                            </div>
                            @else
                            <span class="text-slate-300">—</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-6">
                            @if($verEliminados)
                            <div class="font-semibold text-slate-600">{{ $user->deleted_at->format('d/m/Y') }}</div>
                            <div class="text-[10px] text-slate-400">{{ $user->deleted_at->format('H:i') }}</div>
                            @elseif($user->informacion?->status)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactivo
                            </span>
                            @endif
                        </td>
                        @php($datosCargo = ['id' => $user->id, 'nombre' => trim(($user->informacion?->nombre ?? '') . ' ' . ($user->informacion?->apellidos ?? '')) ?: $user->email, 'documentos' => $user->documentos_vigentes_count, 'solicitudes' => $user->solicitudes_pendientes_count, 'subordinados' => $user->subordinados_count])
                        <td class="py-3.5 px-6 text-right space-x-1 whitespace-nowrap">
                            @if($verEliminados)
                            <form method="POST" action="{{ route('usuarios.restore', $user->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition" title="Restaurar">
                                    <i class="fas fa-rotate-left"></i> Restaurar
                                </button>
                            </form>
                            @if($aCargo > 0)
                            <button onclick="openReasignarModal(@js($datosCargo))" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-lg transition" title="Reasignar documentos">
                                <i class="fas fa-right-left"></i> Reasignar
                            </button>
                            @endif
                            @else
                            <button onclick="openEditModal(@js($user))" class="p-2 text-slate-400 hover:text-slate-800 rounded-lg hover:bg-slate-100 transition" title="Editar">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            @if($aCargo > 0)
                            <button onclick="openReasignarModal(@js($datosCargo))" class="p-2 text-slate-400 hover:text-slate-800 rounded-lg hover:bg-slate-100 transition" title="Reasignar documentos">
                                <i class="fas fa-right-left"></i>
                            </button>
                            @endif
                            @if($user->id !== auth()->id())
                            <button onclick="openDeleteModal(@js($datosCargo))" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Eliminar">
                                <i class="fas fa-trash-can"></i>
                            </button>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400 font-medium">
                            <i class="fas fa-users-slash text-4xl mb-3 block text-slate-300"></i>
                            {{ $verEliminados ? 'No hay usuarios eliminados.' : 'No hay usuarios registrados en el sistema.' }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL UNIFICADO CON DISEÑO DEL FORMULARIO  -->
<!-- ========================================== -->
<div id="userModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="max-w-4xl w-full bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200 my-8 transform transition-all">

        <!-- Header del Formulario -->
        <div class="bg-slate-800 px-6 py-4 flex items-center justify-between">
            <h2 id="modalTitle" class="text-xl font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-user-plus text-slate-400"></i>
                <span>Nuevo Usuario</span>
            </h2>
            <button onclick="closeModal()" class="text-slate-400 hover:text-white transition text-sm p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Formulario estilo Registro de Colaborador -->
        <form id="userForm" method="POST" action="" class="p-8">
            @csrf
            <input type="hidden" id="formMethod" name="_method" value="POST">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Nombre(s) -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Nombre(s) <span class="text-rose-500">*</span></label>
                    <input type="text" id="nombreInput" name="nombre" required placeholder="Ej. Juan Carlos" class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition">
                </div>

                <!-- Apellidos -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Apellidos <span class="text-rose-500">*</span></label>
                    <input type="text" id="apellidosInput" name="apellidos" required placeholder="Ej. Pérez Gómez" class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition">
                </div>

                <!-- RFC -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1">RFC</label>
                    <input type="text" id="rfcInput" name="rfc" maxlength="13" placeholder="PEGC900101XXX" class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition uppercase font-mono">
                </div>

                <!-- CURP -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1">CURP</label>
                    <input type="text" id="curpInput" name="curp" maxlength="18" placeholder="PEGC900101HXXRMR01" class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition uppercase font-mono">
                </div>

                <!-- Fecha de Nacimiento -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Fecha de Nacimiento</label>
                    <input type="date" id="fechaNacimientoInput" name="fecha_nacimiento" class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition">
                </div>

                <!-- Correo Electrónico -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Correo Electrónico <span class="text-rose-500">*</span></label>
                    <input type="email" id="emailInput" name="email" required placeholder="usuario@empresa.com" class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition">
                </div>

                <!-- Contraseña -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Contraseña <span id="pwdRequiredHint" class="text-rose-500">*</span></label>
                    <input type="password" id="passwordInput" name="password" placeholder="••••••••" class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition">
                    <span id="pwdHelp" class="text-[10px] text-gray-400 hidden mt-1">Dejar en blanco para conservar actual.</span>
                </div>

                <!-- Rol -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Rol del Sistema</label>
                    <select id="rolInput" name="rol_id" class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none bg-white transition cursor-pointer">
                        <option value="">-- Seleccionar Rol --</option>
                        @foreach($roles as $rol)
                        <option value="{{ $rol->id }}">{{ $rol->rol }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Jefe Inmediato -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Jefe Inmediato</label>
                    <select id="jefeInput" name="jefe_inmediato_id" class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none bg-white transition cursor-pointer">
                        <option value="">-- Sin Jefe --</option>
                        @foreach($jefes as $jefe)
                        <option value="{{ $jefe->id }}">{{ $jefe->informacion ? $jefe->informacion->nombre . ' ' . $jefe->informacion->apellidos : $jefe->email }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Localidad -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Localidad</label>
                    <select id="localidadInput" name="localidad_id" class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none bg-white transition cursor-pointer">
                        <option value="">-- Seleccionar Localidad --</option>
                        @foreach($localidades as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->localidad }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Planta -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Planta</label>
                    <select id="plantaInput" name="planta_id" class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none bg-white transition cursor-pointer">
                        <option value="">-- Seleccionar Planta --</option>
                        @foreach($plantas as $planta)
                        <option value="{{ $planta->id }}">{{ $planta->planta }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Área -->
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Área</label>
                    <select id="areaInput" name="area_id" class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none bg-white transition cursor-pointer">
                        <option value="">-- Seleccionar Área --</option>
                        @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->area }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Estatus -->
            <div class="mt-6 flex items-center">
                <label class="relative inline-flex items-center cursor-pointer gap-3">
                    <input type="checkbox" id="statusInput" name="status" value="1" class="sr-only peer" checked>
                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-slate-900"></div>
                    <span class="text-xs font-semibold text-gray-500 uppercase">Usuario Activo</span>
                </label>
            </div>

            <!-- Botones de Acción -->
            <div class="mt-8 flex justify-end space-x-4 border-t border-gray-200 pt-6">
                <button type="button" onclick="closeModal()" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100 transition font-medium text-sm">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 shadow-lg transition font-bold text-sm">
                    Guardar Información
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de usuarios activos que pueden recibir documentos (se clona en ambos modales) -->
<template id="destinoOptions">
    <option value="">-- Seleccionar usuario --</option>
    @foreach($jefes as $destino)
    <option value="{{ $destino->id }}">{{ $destino->informacion ? $destino->informacion->nombre . ' ' . $destino->informacion->apellidos : $destino->email }}</option>
    @endforeach
</template>

<!-- MODAL ELIMINAR -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-100 overflow-hidden p-6 space-y-4">
        <div class="text-center space-y-3">
            <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mx-auto text-xl">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 text-sm">¿Dar de baja al usuario?</h3>
                <p class="text-xs text-slate-500 mt-1">Estás a punto de eliminar a <strong id="deleteName" class="text-slate-800"></strong>. Ya no podrá iniciar sesión, pero podrás restaurarlo desde la pestaña Eliminados.</p>
            </div>
        </div>
        <form id="deleteForm" method="POST" action="" class="space-y-4">
            @csrf
            <input type="hidden" name="_method" value="DELETE">

            <div id="deleteCargo" class="hidden rounded-xl border border-amber-200 bg-amber-50 p-4 space-y-3 text-left">
                <p class="text-[11px] font-bold text-amber-800">
                    <i class="fas fa-circle-info mr-1"></i> Este usuario tiene a su cargo: <span id="deleteCargoTexto"></span>
                </p>
                <div class="flex flex-col">
                    <label for="deleteDestino" class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Reasignar a (opcional)</label>
                    <select id="deleteDestino" name="reasignar_a_id" class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none bg-white transition cursor-pointer"></select>
                    <span class="text-[10px] text-slate-500 mt-1">Si no eliges a nadie, podrás reasignarlo después desde la pestaña Eliminados.</span>
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 pt-2">
                <button type="button" onclick="closeDeleteModal()" class="w-full py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">
                    Cancelar
                </button>
                <button type="submit" class="w-full py-2 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-md transition">
                    Sí, dar de baja
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL REASIGNAR -->
<div id="reasignarModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">
        <div class="bg-slate-800 px-6 py-4 flex items-center justify-between">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-right-left text-slate-400"></i>
                <span>Reasignar documentos</span>
            </h2>
            <button onclick="closeReasignarModal()" class="text-slate-400 hover:text-white transition text-sm p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="reasignarForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <p class="text-xs text-slate-600">
                Todo lo que <strong id="reasignarName" class="text-slate-800"></strong> tiene a su cargo pasará al usuario que elijas:
            </p>
            <ul id="reasignarLista" class="text-xs text-slate-700 space-y-1 pl-1"></ul>
            <div class="flex flex-col">
                <label for="reasignarDestino" class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Nuevo responsable <span class="text-rose-500">*</span></label>
                <select id="reasignarDestino" name="reasignar_a_id" required class="border border-gray-300 p-2 text-sm rounded-lg focus:ring-2 focus:ring-slate-900 outline-none bg-white transition cursor-pointer"></select>
            </div>
            <p class="text-[10px] text-slate-400">
                Las versiones obsoletas conservan a su autor original. Cada reasignación queda registrada en la bitácora.
            </p>
            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                <button type="button" onclick="closeReasignarModal()" class="px-5 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-bold bg-slate-900 hover:bg-slate-800 text-white rounded-xl shadow-md transition">
                    Reasignar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JAVASCRIPT -->
<script>
    const baseUrl = "{{ route('usuarios.index') }}";

    function openCreateModal() {
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-plus text-slate-400"></i><span>Nuevo Usuario</span>';
        document.getElementById('userForm').action = baseUrl;
        document.getElementById('formMethod').value = 'POST';

        document.getElementById('nombreInput').value = '';
        document.getElementById('apellidosInput').value = '';
        document.getElementById('rfcInput').value = '';
        document.getElementById('curpInput').value = '';
        document.getElementById('fechaNacimientoInput').value = '';

        document.getElementById('emailInput').value = '';
        document.getElementById('passwordInput').value = '';
        document.getElementById('passwordInput').required = true;
        document.getElementById('pwdRequiredHint').classList.remove('hidden');
        document.getElementById('pwdHelp').classList.add('hidden');

        document.getElementById('rolInput').value = '';
        document.getElementById('jefeInput').value = '';
        document.getElementById('localidadInput').value = '';
        document.getElementById('plantaInput').value = '';
        document.getElementById('areaInput').value = '';
        document.getElementById('statusInput').checked = true;

        document.getElementById('userModal').classList.remove('hidden');
    }

    function openEditModal(user) {
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-pen-to-square text-slate-400"></i><span>Editar Usuario</span>';
        document.getElementById('userForm').action = baseUrl + '/' + user.id;
        document.getElementById('formMethod').value = 'PUT';

        const info = user.informacion || {};
        document.getElementById('nombreInput').value = info.nombre || '';
        document.getElementById('apellidosInput').value = info.apellidos || '';
        document.getElementById('rfcInput').value = info.rfc || '';
        document.getElementById('curpInput').value = info.curp || '';
        document.getElementById('fechaNacimientoInput').value = info.fecha_nacimiento ? info.fecha_nacimiento.split('T')[0] : '';

        document.getElementById('emailInput').value = user.email || '';
        document.getElementById('passwordInput').value = '';
        document.getElementById('passwordInput').required = false;
        document.getElementById('pwdRequiredHint').classList.add('hidden');
        document.getElementById('pwdHelp').classList.remove('hidden');

        document.getElementById('rolInput').value = user.rol_id || '';
        document.getElementById('jefeInput').value = user.jefe_inmediato_id || '';
        document.getElementById('localidadInput').value = user.localidad_id || '';
        document.getElementById('plantaInput').value = user.planta_id || '';
        document.getElementById('areaInput').value = user.area_id || '';
        document.getElementById('statusInput').checked = info.status === 1 || info.status === true;

        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    // Llena un <select> con los usuarios activos, sin el usuario origen
    function llenarDestinos(select, excluirId) {
        select.innerHTML = document.getElementById('destinoOptions').innerHTML;
        const propia = select.querySelector(`option[value="${excluirId}"]`);
        if (propia) propia.remove();
    }

    function describirCargo(u) {
        return [
            u.documentos ? `${u.documentos} documento(s) vigente(s)` : null,
            u.solicitudes ? `${u.solicitudes} solicitud(es) pendiente(s)` : null,
            u.subordinados ? `${u.subordinados} subordinado(s)` : null,
        ].filter(Boolean);
    }

    function openDeleteModal(u) {
        document.getElementById('deleteName').innerText = u.nombre;
        document.getElementById('deleteForm').action = baseUrl + '/' + u.id;

        const cargo = describirCargo(u);
        const select = document.getElementById('deleteDestino');
        llenarDestinos(select, u.id);
        select.disabled = cargo.length === 0; // no enviar el campo si no hay nada que reasignar
        document.getElementById('deleteCargo').classList.toggle('hidden', cargo.length === 0);
        document.getElementById('deleteCargoTexto').innerText = cargo.join(', ') + '.';

        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function openReasignarModal(u) {
        document.getElementById('reasignarName').innerText = u.nombre;
        document.getElementById('reasignarForm').action = baseUrl + '/' + u.id + '/reasignar';
        document.getElementById('reasignarLista').innerHTML = describirCargo(u)
            .map(t => `<li><i class="fas fa-check text-emerald-500 mr-1.5"></i>${t}</li>`).join('');
        llenarDestinos(document.getElementById('reasignarDestino'), u.id);
        document.getElementById('reasignarModal').classList.remove('hidden');
    }

    function closeReasignarModal() {
        document.getElementById('reasignarModal').classList.add('hidden');
    }

    function filterTable() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#userTable tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(input) ? '' : 'none';
        });
    }
</script>
@endsection