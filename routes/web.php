<?php

use App\Http\Controllers\AutenticacionTiController;
use App\Http\Controllers\PanelServicioController;
use App\Http\Controllers\PortalClienteController;
use App\Http\Controllers\ServicioController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/ti/ingreso');

Route::get('/ti/ingreso', [AutenticacionTiController::class, 'mostrarIngreso'])
    ->middleware('guest')
    ->name('ti.ingreso');
Route::redirect('/login', '/ti/ingreso')->name('login');
Route::post('/ti/ingreso', [AutenticacionTiController::class, 'ingresar'])
    ->middleware(['guest', 'throttle:6,1'])
    ->name('ti.ingresar');
Route::post('/ti/salir', [AutenticacionTiController::class, 'salir'])
    ->middleware('auth')
    ->name('ti.salir');

Route::get('/seguimiento/{token}', [PortalClienteController::class, 'mostrar'])
    ->middleware('throttle:60,1')
    ->name('portal.servicio');
Route::post('/seguimiento/{token}/servicios/{servicio}/mensajes', [PortalClienteController::class, 'enviarMensaje'])
    ->middleware('throttle:5,1')
    ->name('portal.mensajes');
Route::post('/seguimiento/{token}/servicios/{servicio}/autorizacion', [
    PortalClienteController::class,
    'responderAutorizacion',
])->middleware('throttle:10,1')->name('portal.autorizacion');

Route::redirect('/dashboard', '/ti');

Route::middleware('auth')->prefix('ti')->name('ti.')->group(function () {
    Route::get('/', [PanelServicioController::class, 'index'])->name('servicios.index');
    Route::get('/servicios/nuevo', [ServicioController::class, 'crear'])->name('servicios.crear');
    Route::post('/servicios', [ServicioController::class, 'guardar'])->name('servicios.guardar');
    Route::get('/servicios/{servicio}', [PanelServicioController::class, 'mostrar'])->name('servicios.mostrar');
    Route::get('/servicios/{servicio}/imprimir', [ServicioController::class, 'imprimir'])->name('servicios.imprimir');
    Route::patch('/servicios/{servicio}', [PanelServicioController::class, 'actualizar'])->name('servicios.actualizar');
    Route::post('/servicios/{servicio}/mensajes', [PanelServicioController::class, 'responder'])->name('servicios.responder');
});
