<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControllerZonasRiesgos;
use App\Http\Controllers\ControllerZonasSeguras;
use App\Http\Controllers\ControllerZonasEncuentros;
use App\Http\Controllers\ControllerUsuarios;

Route::get('/', function () {
    return view('index');
});

// NOTA SIEMPRE PONER LAS RUTAS DIRECTAS AL INICIO

//Definimos una ruta directa para el listado de usuarios
Route::get('/inicioS/lista', [ControllerUsuarios::class, 'lista'])->name('inicioS.lista');
//Definir la ruta para login
Route::post('/inicioS/login', [ControllerUsuarios::class, 'login'])->name('inicioS.login');
// Definir ruta para el logout
Route::get('/inicioS/logout', [ControllerUsuarios::class, 'logout'])->name('inicioS.logout');

// Definir ruta para el mapa
Route::get('/zonasR/mapa', [ControllerZonasRiesgos::class, 'mapa'])->name('zonasR.mapa');
// Definir ruta para el mapa
Route::get('/zonasS/mapa', [ControllerZonasSeguras::class, 'mapa'])->name('zonasS.mapa');
// Definir ruta para el mapa
Route::get('/zonasE/mapa', [ControllerZonasEncuentros::class, 'mapa'])->name('zonasE.mapa');
// Definir ruta para exportar pdf
Route::get('/zonasR/reporte', [ControllerZonasRiesgos::class, 'exportarPDF'])->name('zonasR.reporte');
// Definir ruta para exportar pdf
Route::get('/zonasS/reporte', [ControllerZonasSeguras::class, 'exportarPDF'])->name('zonasS.reporte');
// Definir ruta para exportar pdf
Route::get('/zonasE/reporte', [ControllerZonasEncuentros::class, 'exportarPDF'])->name('zonasE.reporte');


//Ruta para las zonas de riesgo
Route::resource('zonasR', ControllerZonasRiesgos::class);
//Ruta para las zona seguras
Route::resource('zonasS', ControllerZonasSeguras::class);
//Ruta para las zona de encuentro
Route::resource('zonasE', ControllerZonasEncuentros::class);
//Ruta para las zona de encuentro
Route::resource('inicioS', ControllerUsuarios::class);


// Route::get('/inicioS/lista', [ClienteController::class, 'lista']);