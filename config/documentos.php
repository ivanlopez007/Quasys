<?php
 
return [
    // Disco donde se guardan los archivos (debe ser PRIVADO).
    'disk' => env('DOCUMENTOS_DISK', 'r2'),
 
    // Nombres exactos de tu catálogo estado_solicituds.estado_solicitud
    'estados' => [
        'pendiente' => 'Pendiente',
        'aprobado' => 'Aprobado',
        'rechazado' => 'Rechazado',
    ],
 
    // Nombres del catálogo tipo_solicituds.tipo_solicitud. Se comparan sin importar
    // mayúsculas ni acentos ("Revisión" = "revision"), así que el ID puede ser cualquiera.
    'tipos' => [
        'nuevo' => 'Nuevo',
        'revision' => 'Revisión',
        'eliminar' => 'Eliminar',
    ],

    // Los roles son dinámicos, así que el permiso de aprobar se da por acceso:
    // un rol puede aprobar/rechazar cualquier solicitud si tiene asignado el
    // sub menú (sub_menus.url) que coincida EXACTAMENTE con este valor.
    // Además de ese permiso, el jefe inmediato del solicitante siempre puede resolver.
    'submenu_aprobador_url' => 'control-documentos/aprobaciones',
];
 