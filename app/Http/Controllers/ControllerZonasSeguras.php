<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasSegura;

class ControllerZonasSeguras extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        //Pagina principal
        $zonas=ZonasSegura::all();
        return view('zonasS.inicio', compact('zonas'));
    }

    public function mapa(){
        //Pagina principal
        $zonas=ZonasSegura::all();
        return view('zonasS.mapa', compact('zonas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        //Vista para guardar datos
        return view('zonasS.nuevaZona');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        //Guardar los datos en la bdd
        try {
            $datos = [
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'radio' => $request->radio,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'tipoSeguridad' => $request->tipoSeguridad
            ];
            ZonasSegura::create($datos);
            return redirect()->route('zonasS.index')->with('mensaje', 'Zona segura registrada correctamente.');

        } catch (\Exception $e) { //Se captura la excepcion y la manda en el mensaje de error
            //El back es para redirigir de vuelta al formulario
            return redirect()->back()->with('error', 'Hubo un error al guardar la zona segura: ' . $e->getMessage());
        }
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
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        //Vista para editar
        $zona = ZonasSegura::findOrFail($id);
        return view('zonasS.editar', compact('zona'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        //Actualizar los datos que mandamos para editar
        try{
            $zona = ZonasSegura::findOrFail($id);
            $zona->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'radio' => $request->radio,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'tipoSeguridad' => $request->tipoSeguridad
            ]);
            return redirect()->route('zonasS.index')->with('mensaje', 'Zona actualizada correctamente.');
        }
        catch (\Exception $e){
            return redirect()->back()->with('error', 'Hubo un error al actualizar la zona segura: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        //Eliminar
        try{
            $zona = ZonasSegura::findOrFail($id);
            $zona->delete();

            return redirect()->route('zonasS.index')->with('mensaje', 'Zona eliminada correctamente.');
        }
        catch (\Exception $e){
            return redirect()->back()->with('error', 'Hubo un error al eliminar la zona segura: ' . $e->getMessage());
        }
    }
}
