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
                        <th class="py-3.5 px-6">Estatus</th>
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
                            {{ $user->jefeInmediato?->informacion ? $user->jefeInmediato->informacion->nombre . ' ' . $user->jefeInmediato->informacion->apellidos : '—' }}
                        </td>
                        <td class="py-3.5 px-6">
                            @if($user->informacion?->status)
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
                            <button onclick="openEditModal(@js($user))" class="p-2 text-slate-400 hover:text-slate-800 rounded-lg hover:bg-slate-100 transition" title="Editar">
                                <i class="fas fa-pen-to-square"></i>
                            </button>
                            <button onclick="openDeleteModal({{ $user->id }}, @js(trim(($user->informacion?->nombre ?? '') . ' ' . ($user->informacion?->apellidos ?? ''))))" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition" title="Eliminar">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 font-medium">
                            <i class="fas fa-users-slash text-4xl mb-3 block text-slate-300"></i>
                            No hay usuarios registrados en el sistema.
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

<!-- MODAL ELIMINAR -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl border border-slate-100 overflow-hidden text-center p-6 space-y-4">
        <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mx-auto text-xl">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
        <div>
            <h3 class="font-bold text-slate-800 text-sm">¿Eliminar Usuario?</h3>
            <p class="text-xs text-slate-500 mt-1">Estás a punto de eliminar a <strong id="deleteName" class="text-slate-800"></strong>.</p>
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

    function openDeleteModal(id, nombre) {
        document.getElementById('deleteName').innerText = nombre;
        document.getElementById('deleteForm').action = baseUrl + '/' + id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
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