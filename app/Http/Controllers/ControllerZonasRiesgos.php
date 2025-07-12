<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasRiesgo;

class ControllerZonasRiesgos extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Pagina principal
        $zonas=ZonasRiesgo::all();
        return view('zonasR.inicio', compact('zonas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //Vista para crear nuevo punto de riesgo
        return view('zonasR.nuevaZona');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Guardar los datos en la bdd
        $datos = [
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'nivelRiesgo' => $request->nivelRiesgo,
            'latitud1' => $request->latitud1,
            'longitud1' => $request->longitud1,
            'latitud2' => $request->latitud2,
            'longitud2' => $request->longitud2,
            'latitud3' => $request->latitud3,
            'longitud3' => $request->longitud3,
            'latitud4' => $request->latitud4,
            'longitud4' => $request->longitud4,
            'latitud5' => $request->latitud5,
            'longitud5' => $request->longitud5
        ];
        ZonasRiesgo::create($datos);
        return redirect()->route('zonasR.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //Vista para editar
        $zona = ZonasRiesgo::findOrFail($id);
        return view('zonasR.editar', compact('zona'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //Actualizar los datos que mandamos para editar
        $zona = ZonasRiesgo::findOrFail($id);
        $zona->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'nivelRiesgo' => $request->nivelRiesgo,
            'latitud1' => $request->latitud1,
            'longitud1' => $request->longitud1,
            'latitud2' => $request->latitud2,
            'longitud2' => $request->longitud2,
            'latitud3' => $request->latitud3,
            'longitud3' => $request->longitud3,
            'latitud4' => $request->latitud4,
            'longitud4' => $request->longitud4,
            'latitud5' => $request->latitud5,
            'longitud5' => $request->longitud5
        ]);
        return redirect()->route('zonasR.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //Eliminar
        $zona = ZonasRiesgo::findOrFail($id);
        $zona->delete();

        return redirect()->route('zonasR.index');
    }
}
