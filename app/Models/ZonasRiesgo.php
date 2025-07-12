<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ZonasRiesgo extends Model
{
    use HasFactory;
    protected $fillable=[
        'nombre',
        'descripcion',
        'nivelRiesgo',
        'latitud1',
        'longitud1',
        'latitud2',
        'longitud2',
        'latitud3',
        'longitud3',
        'latitud4',
        'longitud4',
        'latitud5',
        'longitud5'
    ];
}
