<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasEncuentro;

class ControllerZonasEncuentros extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Pagina principal
        $zonas=ZonasEncuentro::all();
        return view('zonasE.inicio', compact('zonas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //Vista para guardar datos
        return view('zonasE.nuevaZona');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Guardar los datos en la bdd
        try {
            $datos = [
                'nombre' => $request->nombre,
                'capacidad' => $request->capacidad,
                'responsable' => $request->responsable,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud
            ];
            ZonasEncuentro::create($datos);
            return redirect()->route('zonasE.index')->with('mensaje', 'Punto de encuentro registrado correctamente.');

        } catch (\Exception $e) { //Se captura la excepcion y la manda en el mensaje de error
            //El back es para redirigir de vuelta al formulario
            return redirect()->back()->with('error', 'Hubo un error al guardar el punto de encuentro: ' . $e->getMessage());
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
        //Vista para editar
        $zona = ZonasEncuentro::findOrFail($id);
        return view('zonasE.editar', compact('zona'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //Actualizar los datos que mandamos para editar
        try{
            $zona = ZonasEncuentro::findOrFail($id);
            $zona->update([
                'nombre' => $request->nombre,
                'capacidad' => $request->capacidad,
                'responsable' => $request->responsable,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud
            ]);
            return redirect()->route('zonasE.index')->with('mensaje', 'Punto de encuentro actualizado correctamente.');
        }
        catch (\Exception $e){
            return redirect()->back()->with('error', 'Hubo un error al actualizar el punto de encuentro: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //Eliminar
        try{
            $zona = ZonasEncuentro::findOrFail($id);
            $zona->delete();

            return redirect()->route('zonasE.index')->with('mensaje', 'Punto de encuentro eliminado correctamente.');
        }
        catch (\Exception $e){
            return redirect()->back()->with('error', 'Hubo un error al eliminar el punto de encuentro: ' . $e->getMessage());
        }
    }
}
