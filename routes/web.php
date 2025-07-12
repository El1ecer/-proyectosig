<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControllerZonasRiesgos;
use App\Http\Controllers\ControllerZonasSeguras;

Route::get('/', function () {
    return view('index');
});
//Ruta para las zonas de riesgo
Route::resource('zonasR', ControllerZonasRiesgos::class);
//Ruta para las zona seguras
Route::resource('zonasS', ControllerZonasSeguras::class);