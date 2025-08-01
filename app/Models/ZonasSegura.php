<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ZonasSegura extends Model
{
    use HasFactory;
    protected $fillable=[
        'nombre',
        'radio',
        'tipoSeguridad',
        'latitud',
        'longitud'
    ];
}
