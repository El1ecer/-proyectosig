<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControllerZonasRiesgos;

Route::get('/', function () {
    return view('index');
});

Route::resource('zonasR', ControllerZonasRiesgos::class);