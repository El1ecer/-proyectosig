<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControllerZonasRiesgos;
use App\Http\Controllers\ControllerZonasSeguras;
use App\Http\Controllers\ControllerZonasEncuentros;

Route::get('/', function () {
    return view('index');
});
//Ruta para las zonas de riesgo
Route::resource('zonasR', ControllerZonasRiesgos::class);
//Ruta para las zona seguras
Route::resource('zonasS', ControllerZonasSeguras::class);
//Ruta para las zona de encuentro
Route::resource('zonasE', ControllerZonasEncuentros::class);