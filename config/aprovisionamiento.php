<?php

/*
|--------------------------------------------------------------------------
| Datos iniciales de cada empresa (tenant)
|--------------------------------------------------------------------------
|
| Se cargan al crear una empresa y con `php artisan empresas:preparar`.
| La carga solo AGREGA lo que falta: nunca modifica ni borra lo que la empresa
| ya tiene. Si agregas una pantalla nueva, súmala en 'menus' y asígnala en 'roles'.
|
*/

return [

    // [menú, icono, [[sub menú, url (sin "/" inicial), icono], ...]]
    'menus' => [
        ['Control de Documentos', 'fas fa-folder-open', [
            ['Lista Maestra', 'control-documentos/', 'fas fa-list-check'],
            ['Solicitudes', 'control-documentos/solicitudes', 'fas fa-file-signature'],
            ['Por aprobar', 'control-documentos/aprobaciones', 'fas fa-check-double'],
            ['Sin responsable', 'control-documentos/sin-responsable', 'fas fa-user-slash'],
        ]],
        ['Usuarios', 'fas fa-user', [
            ['Usuarios', 'usuarios', 'fas fa-user'],
        ]],
        ['Catálogos', 'fas fa-folder', [
            ['Roles', 'catalogo/roles', 'fas fa-user-shield'],
            ['Áreas', 'catalogo/areas', 'fas fa-sitemap'],
            ['Localidades', 'catalogo/localidades', 'fas fa-location-dot'],
            ['Plantas', 'catalogo/planta', 'fas fa-industry'],
            ['Niveles', 'catalogo/niveles', 'fas fa-layer-group'],
            ['Sub Nivel', 'catalogo/sub_nivel', 'fas fa-stream'],
            ['Lugar de Retención', 'catalogo/lugar_retencion', 'fas fa-warehouse'],
            ['Periodo de Retención', 'catalogo/periodo_retencion', 'fas fa-hourglass'],
            ['Disposición Final', 'catalogo/disposicion_final', 'fas fa-recycle'],
            ['Estados de Solicitud', 'catalogo/estado_solicitud', 'fas fa-info-circle'],
            ['Tipo de Solicitud', 'catalogo/tipo_solicitud', 'fas fa-file-alt'],
            ['Menús', 'catalogo/menu', 'fas fa-bars'],
            ['Sub Menús', 'catalogo/sub_menu', 'fas fa-cog'],
            ['Acceso Asignado', 'catalogo/acceso_asignado', 'fas fa-key'],
        ]],
    ],

    // Rol => URLs de sub menú a las que tiene acceso ('*' = todas). El primero es el del administrador.
    'roles' => [
        'Admin' => '*',
        'Revisador de documentos' => [
            'control-documentos/',
            'control-documentos/solicitudes',
            'control-documentos/aprobaciones',
        ],
        'Auditor' => [
            'control-documentos/',
        ],
        'Usuario' => [
            'control-documentos/',
            'control-documentos/solicitudes',
        ],
    ],

    'planta_inicial' => ['Planta Principal', 'General'],

    // Catálogos que se cargan SOLO si la tabla de la empresa está vacía
    // (son propios de cada empresa; si ya capturó los suyos, no se tocan).
    'catalogos' => [
        // nivel => [nombre, [subniveles]]
        'niveles' => [
            1 => ['Gerencia', ['Anexos de la Gerencia']],
            2 => ['Procedimientos y Formatos', ['Manuales', 'Tutoriales', 'Ayudas Visuales']],
            3 => ['Manuales y Tutoriales', ['Procedimientos Locales', 'Formatos Locales', 'Documentos Externos', 'Documentos del Cliente', 'Hoja de Instrucción de Tarea (HIT)', 'Hoja de Tarea Estándar (HTE)']],
            4 => ['Hoja de Trabajo Estandarizado', ['HTE']],
        ],
        'areas' => ['Calidad', 'Producción', 'Sistemas', 'Recursos Humanos', 'Administración'],
        'localidades' => ['Principal'],
        'lugares_retencion' => ['Electrónico', 'Físico'],
        'disposiciones_finales' => ['Archivo Electrónico', 'Archivo Físico', 'Respaldo en Disco Externo', 'Se Elimina'],
        // [unidad, cantidad]
        'periodos_retencion' => [['Años', 1], ['Años', 5], ['Años', 10]],
    ],
];
