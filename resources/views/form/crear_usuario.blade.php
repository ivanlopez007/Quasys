<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Formulario dentro del contenedor principal -->

<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
    <div class="bg-slate-800 px-6 py-4">
        <h2 class="text-xl font-bold text-white uppercase tracking-wider">Registro de Colaborador</h2>
    </div>

    <form action="#" method="POST" class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Nombre -->
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Nombre(s)</label>
                <input type="text" name="nombre" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition" placeholder="Ej. Juan">
            </div>

            <!-- Apellido Paterno -->
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Apellido Paterno</label>
                <input type="text" name="apellido_paterno" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition" placeholder="Ej. Pérez">
            </div>

            <!-- Apellido Materno -->
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Apellido Materno</label>
                <input type="text" name="apellido_materno" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition" placeholder="Ej. García">
            </div>

            <!-- Área -->
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Area</label>
                <select name="area" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none bg-white transition cursor-pointer">
                    <option value="" disabled selected>Seleccione área</option>
                    <option value="operaciones">RH</option>
                    <option value="ventas">Calidad</option>
                    <option value="marketing">Marketing</option>
                    <option value="asistencia">Sistemas</option>
                </select>
            </div>

            <!-- Posición -->
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Posición</label>
                <input type="text" name="posicion" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition" placeholder="Ej. Analista Jr">
            </div>

            <!-- RFC -->
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 uppercase mb-1">RFC</label>
                <input type="text" name="rfc" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition" placeholder="ABCD123456XYZ">
            </div>

            <!-- Teléfono -->
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Teléfono</label>
                <input type="tel" name="telefono" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition" placeholder="5512345678">
            </div>

            <!-- Correo -->
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Correo Electrónico</label>
                <input type="email" name="correo" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition" placeholder="usuario@empresa.com">
            </div>

            <!-- Supervisor -->
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Supervisor Directo</label>
                <input type="text" name="supervisor" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition" placeholder="Nombre del jefe">
            </div>

            <!-- Fecha Ingreso -->
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Fecha de Ingreso</label>
                <input type="date" disabled value="<?= date('Y-m-d') ?>" name="fecha_ingreso" class="disabled border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition">
            </div>

            <!-- Fecha Nacimiento -->
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none transition">
            </div>

            <!-- Aplicación (Select) -->
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Aplicación</label>
                <select name="aplicacion" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none bg-white transition cursor-pointer">
                    <option value="" disabled selected>Seleccione aplicación</option>
                    <option value="sap">SAP</option>
                    <option value="crm">CRM</option>
                    <option value="erp">ERP Global</option>
                    <option value="asistencia">Sistema Asistencia</option>
                </select>
            </div>

            <!-- Acción (Select) -->
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 uppercase mb-1">Acción Requerida</label>
                <select name="accion" class="border border-gray-300 p-2 rounded-lg focus:ring-2 focus:ring-slate-900 outline-none bg-white transition cursor-pointer">
                    <option value="" disabled selected>Seleccione acción</option>
                    <option value="alta">Alta de Usuario</option>
                    <option value="baja">Baja de Usuario</option>
                    <option value="modificacion">Modificación de Permisos</option>
                    <option value="reset">Reset de Contraseña</option>
                </select>
            </div>

        </div>

        <!-- Botones de Acción -->
        <div class="mt-10 flex justify-end space-x-4 border-t pt-6">
            <button type="reset" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-100 transition font-medium">
                Limpiar
            </button>
            <button type="submit" class="px-6 py-2 bg-slate-900 text-white rounded-lg hover:bg-slate-800 shadow-lg transition font-bold">
                Guardar Información
            </button>
        </div>
    </form>
</div>