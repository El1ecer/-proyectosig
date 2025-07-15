<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasEncuentro;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ControllerZonasEncuentros extends Controller
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
        $zonas=ZonasEncuentro::all();
        return view('zonasE.inicio', compact('zonas'));
    }

    public function mapa(){
        //Pagina principal
        $zonas=ZonasEncuentro::all();
        return view('zonasE.mapa', compact('zonas'));
    }

    public function exportarPDF()
    {
        $zonas = ZonasEncuentro::all();

        foreach ($zonas as $zona) {
            $lat = $zona->latitud;
            $lng = $zona->longitud;

            if (!$lat || !$lng) {
                $zona->mapa_base64 = null;
                continue;
            }

            // Elegir color de marcador según capacidad
            $color = 'E74C3C'; // Verde por defecto (1-100)
            if ($zona->capacidad == 500) {
                $color = 'F1C40F'; // Amarillo (101-500)
            } elseif ($zona->capacidad == 1000) {
                $color = '2ECC71'; // Rojo (501-1000)
            }

            // Crear el marcador
            $marker = "pin-s+$color($lng,$lat)";

            // Construir URL Mapbox
            $zoom = 15;
            $size = "400x200";
            $token = 'pk.eyJ1IjoidmludGFpbHN6IiwiYSI6ImNtY3MzajdkMTB0MngyanEyc2o5bjAwOHEifQ.FkEeSTHc8LB9ws0_jaQ6FA';

            $mapUrl = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/$marker/$lng,$lat,$zoom/$size?access_token=$token";

            try {
                $response = Http::timeout(10)->get($mapUrl);

                if ($response->successful()) {
                    $zona->mapa_base64 = 'data:image/png;base64,' . base64_encode($response->body());
                } else {
                    $zona->mapa_base64 = null;
                }
            } catch (\Exception $e) {
                $zona->mapa_base64 = null;
            }
        }

        $pdf = Pdf::loadView('zonasE.reporte', compact('zonas'));
        return $pdf->download('puntos_encuentro.pdf');
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
        return view('zonasE.nuevaZona');
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
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        //Vista para editar
        $zona = ZonasEncuentro::findOrFail($id);
        return view('zonasE.editar', compact('zona'));
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
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
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
