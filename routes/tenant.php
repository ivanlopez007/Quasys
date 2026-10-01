<?php

declare(strict_types=1);

use App\Http\Controllers\catalogo\EstadoSolicitudController;
use App\Http\Controllers\catalogo\AreaController;
use App\Http\Controllers\catalogo\RolController;
use App\Http\Controllers\catalogo\LocalidadController;
use App\Http\Controllers\catalogo\NivelController;
use App\Http\Controllers\catalogo\MenuController;
use App\Http\Controllers\catalogo\TipoSolicitudController;
use App\Http\Controllers\catalogo\LugarRetencionController;
use App\Http\Controllers\catalogo\PeriodoRetencionController;
use App\Http\Controllers\catalogo\DisposicionFinalController;
use App\Http\Controllers\catalogo\PlantaController;
use App\Http\Controllers\catalogo\SubNivelController;
use App\Http\Controllers\catalogo\SubMenuController;
use App\Http\Controllers\catalogo\AccesoAsignadoController;
use App\Http\Controllers\UserController;


use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DocumentosSinResponsableController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    
    Route::get('/', function () {
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });


    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');




    // Catálogos y usuarios: solo usuarios autenticados
    Route::middleware(['auth'])->group(function () {
        Route::prefix('catalogo')->name('catalogo.')->group(function () {
            Route::resource('roles', RolController::class)->except(['create', 'edit', 'show']);
            Route::resource('areas', AreaController::class)->except(['create', 'edit', 'show']);
            Route::resource('localidades', LocalidadController::class)->except(['create', 'edit', 'show']);
            Route::resource('niveles', NivelController::class)->except(['create', 'edit', 'show']);
            Route::resource('menu', MenuController::class)->except(['create', 'edit', 'show']);
            Route::resource('estado_solicitud', EstadoSolicitudController::class)->except(['create', 'edit', 'show']);
            Route::resource('tipo_solicitud', TipoSolicitudController::class)->except(['create', 'edit', 'show']);
            Route::resource('lugar_retencion', LugarRetencionController::class)->except(['create', 'edit', 'show']);
            Route::resource('periodo_retencion', PeriodoRetencionController::class)->except(['create', 'edit', 'show']);
            Route::resource('disposicion_final', DisposicionFinalController::class)->except(['create', 'edit', 'show']);
            Route::resource('planta', PlantaController::class)->except(['create', 'edit', 'show']);
            Route::resource('sub_nivel', SubNivelController::class)->except(['create', 'edit', 'show']);
            Route::resource('sub_menu', SubMenuController::class)->except(['create', 'edit', 'show']);
            Route::get('acceso_asignado', [AccesoAsignadoController::class, 'index'])->name('acceso_asignado.index');
            Route::post('acceso_asignado', [AccesoAsignadoController::class, 'store'])->name('acceso_asignado.store');
        });

        Route::resource('usuarios', UserController::class)->except(['create', 'edit', 'show']);
        Route::post('usuarios/{id}/restaurar', [UserController::class, 'restore'])->whereNumber('id')->name('usuarios.restore');
        Route::post('usuarios/{id}/reasignar', [UserController::class, 'reasignar'])->whereNumber('id')->name('usuarios.reasignar');
    });



    Route::middleware(['auth'])->prefix('control-documentos')->name('documentos.')->group(function () {
        // Documentos vigentes
        Route::get('/', [DocumentoController::class, 'index'])->name('index');
        Route::get('/historial/{codigo}', [DocumentoController::class, 'historial'])->name('historial');
        Route::get('/versiones/{codigo}', [DocumentoController::class, 'versiones'])->name('versiones');
        Route::get('/archivo/ver', [DocumentoController::class, 'verArchivo'])->name('archivo.ver');
        Route::get('/archivo/contenido', [DocumentoController::class, 'contenidoArchivo'])->name('archivo.contenido');

        // Solicitudes
        Route::get('/solicitudes', [DocumentoController::class, 'solicitudesIndex'])->name('solicitudes.index');
        Route::get('/solicitudes/crear', [DocumentoController::class, 'createSolicitud'])->name('solicitudes.create');
        Route::post('/solicitudes', [DocumentoController::class, 'storeSolicitud'])->name('solicitudes.store');

        // Documentos y solicitudes cuyo responsable fue dado de baja
        Route::get('/sin-responsable', [DocumentosSinResponsableController::class, 'index'])->name('sin_responsable.index');
        Route::post('/sin-responsable/reasignar', [DocumentosSinResponsableController::class, 'reasignar'])->name('sin_responsable.reasignar');

        // Bandeja "Por aprobar" (el sub menú con esta URL da permiso de aprobador)
        Route::get('/aprobaciones', [DocumentoController::class, 'aprobaciones'])->name('aprobaciones');

        // Aprobación y Rechazo
        Route::post('/solicitudes/{id}/aprobar', [DocumentoController::class, 'aprobar'])->name('solicitudes.aprobar');
        Route::post('/solicitudes/{id}/rechazar', [DocumentoController::class, 'rechazar'])->name('solicitudes.rechazar');
    });
});
