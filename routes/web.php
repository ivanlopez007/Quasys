<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;




foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {

    Route::get('/', function () {
        return '<h1>Welcome to the central domain</h1>';
    });
    
    });


    Route::get('/test-email', function () {
    try {
        $correoDestino = 'ivanlopez@industrialhefesto.com';

        Mail::raw('¡Hola Ivan! Esta es una prueba exitosa de envío de correo desde tu aplicación Laravel.', function ($message) use ($correoDestino) {
            $message->to($correoDestino)
                    ->subject('Prueba de Correo - Industrial Hefesto');
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Correo enviado correctamente a ' . $correoDestino,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Falló el envío de correo.',
            'error' => $e->getMessage(),
        ], 500);
    }
});
}

$tipoSolicitus = [
    ['id' => 1, 'nombre' => 'Eliminar'],
    ['id' => 2, 'nombre' => 'Nuevo'],
    ['id' => 3, 'nombre' => 'Revision']
];

$nivel = [
    ['id' => 1, 'nombre' => 'Nivel 1'],
    ['id' => 2, 'nombre' => 'Nivel 2'],
    ['id' => 3, 'nombre' => 'Nivel 3']
];

//el subnivel debe de estar vinculado con el nivel
$subnivel = [
    ['id' => 1, 'nombre' => 'Subnivel 1', 'nivel_id' => 1],
    ['id' => 2, 'nombre' => 'Subnivel 2', 'nivel_id' => 1],
    ['id' => 3, 'nombre' => 'Subnivel 3', 'nivel_id' => 2],
    ['id' => 4, 'nombre' => 'Subnivel 4', 'nivel_id' => 2],
    ['id' => 5, 'nombre' => 'Subnivel 5', 'nivel_id' => 3],
    ['id' => 6, 'nombre' => 'Subnivel 6', 'nivel_id' => 3]
];


Route::get('/', function () {
    return view('welcome');
});



Route::get('/auth/login', function () {
    return view('auth.login');
})->name('login');


Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/calidad/dashboard', function () {
    return view('calidad.dashboard');
})->name('calidad.dashboard');

Route::get('/user/dashboard', function () {
    return view('user.dashboard');
})->name('user.dashboard');

Route::get('/layout', function () {
    $usuarios = [
        [
            'id' => 1,
            'nombre' => 'Ivan Lopez',
            'email' => 'ivan.lopez@example.com',
            'rol' => 'admin',
            'activo' => true
        ],
        [
            'id' => 2,
            'nombre' => 'María López',
            'email' => 'maria.lopez@example.com',
            'rol' => 'calidad',
            'activo' => true
        ],
        [
            'id' => 3,
            'nombre' => 'Carlos García',
            'email' => 'carlos.garcia@example.com',
            'rol' => 'user',
            'activo' => false
        ]
    ];
    
    $menuCalidad = [
        [
            'id' => 1,
            'nombre' => 'Dashboard',
            'ruta' => '/calidad/dashboard',
            'icono' => 'fas fa-tachometer-alt'
        ],
        [
            'id' => 2,
            'nombre' => 'Reportes',
            'ruta' => '/calidad/reportes',
            'icono' => 'fas fa-chart-bar'
        ],
        [
            'id' => 3,
            'nombre' => 'Configuración',
            'ruta' => '/calidad/configuracion',
            'icono' => 'fas fa-cogs'
        ]
    ];

    $SubmenuReportes = [
        [
            'id' => 1,
            'nombre' => 'Reporte de Calidad',
            'ruta' => '/calidad/reportes/calidad',
            'icono' => 'fas fa-file-alt'
        ],
        [
            'id' => 2,
            'nombre' => 'Reporte de Incidentes',
            'ruta' => '/calidad/reportes/incidentes',
            'icono' => 'fas fa-exclamation-triangle'
        ]
    ];


    $usuario = $usuarios[2]; // Usuario ejemplo

    return view('layout.layout', compact('usuario', 'menuCalidad', 'SubmenuReportes'));
})->name('layout.layout');


Route::get('/crear-usuario', function () {
    return view('form.crear_usuario');
})->name('form.crear_usuario');

Route::get('/solicitar-cambio', function () use ($tipoSolicitus, $nivel, $subnivel) {
    return view('form.solicitar_cambio', compact('tipoSolicitus', 'nivel', 'subnivel'));
})->name('form.solicitar_cambio');   

Route::get('/aprobacion-solicitud', function () {
    return view('form.aprobacion_solicitud');
})->name('form.aprobacion_solicitud');

Route::get('/historial-procedimiento', function () {
    return view('form.historial_procedimiento');
})->name('form.historial_procedimiento');

Route::get('/formato', function () {
    return view('form.formato');
})->name('form.formato');   