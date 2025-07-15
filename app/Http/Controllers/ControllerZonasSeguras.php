<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasSegura;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

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
// pk.eyJ1IjoidmludGFpbHN6IiwiYSI6ImNtY3MzajdkMTB0MngyanEyc2o5bjAwOHEifQ.FkEeSTHc8LB9ws0_jaQ6FA
    public function exportarPDF()
    {
        $zonas = ZonasSegura::all();

        foreach ($zonas as $zona) {
            $centerLat = $zona->latitud;
            $centerLng = $zona->longitud;

            if (!$centerLat || !$centerLng) {
                $zona->mapa_base64 = null;
                continue;
            }

            // Color del pin según el tipo de seguridad
            $color = '0476D9'; // Azul
            if ($zona->tipoSeguridad === 'Bajo') {
                $color = 'FF0000';
            } elseif ($zona->tipoSeguridad === 'Medio') {
                $color = 'FFFF00';
            }

            // Pin en coordenadas
            $markerOverlay = "pin-s+$color($centerLng,$centerLat)";

            // Zoom bajo para mostrar un área más amplia
            $zoom = 15;
            $width = 400;
            $height = 200;

            $token = 'pk.eyJ1IjoidmludGFpbHN6IiwiYSI6ImNtY3MzajdkMTB0MngyanEyc2o5bjAwOHEifQ.FkEeSTHc8LB9ws0_jaQ6FA';

            $mapUrl = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/$markerOverlay/$centerLng,$centerLat,$zoom/{$width}x{$height}?access_token=$token";

            try {
                $response = Http::timeout(10)->get($mapUrl);

                if ($response->successful()) {
                    $base64 = 'data:image/png;base64,' . base64_encode($response->body());
                    $zona->mapa_base64 = $base64;
                } else {
                    $zona->mapa_base64 = null;
                }
            } catch (\Exception $e) {
                $zona->mapa_base64 = null;
            }
        }

        $pdf = Pdf::loadView('zonasS.reporte', compact('zonas'));
        return $pdf->download('zonas_seguras.pdf');
    }



    private function getBase64FromUrl($url)
    {
        try {
            // Configurar contexto para evitar errores SSL y permitir acceso remoto
            $contextOptions = [
                "ssl" => [
                    "verify_peer" => false,
                    "verify_peer_name" => false,
                ],
                "http" => [
                    "header" => "User-Agent: Mozilla/5.0\r\n"
                ]
            ];

            $context = stream_context_create($contextOptions);
            $imageData = file_get_contents($url, false, $context);

            if ($imageData === false) {
                throw new \Exception("No se pudo obtener la imagen desde la URL.");
            }

            $mime = 'image/png'; // Asumimos que todas las imágenes de Mapbox son PNG
            return 'data:' . $mime . ';base64,' . base64_encode($imageData);
        } catch (\Exception $e) {
            return null; // Puedes también loguear el error si lo necesitas
        }
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
