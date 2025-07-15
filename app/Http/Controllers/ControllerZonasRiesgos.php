<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasRiesgo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ControllerZonasRiesgos extends Controller
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
        $zonas=ZonasRiesgo::all();
        return view('zonasR.inicio', compact('zonas'));
    }

    public function mapa(){
        //Pagina principal
        $zonas=ZonasRiesgo::all();
        return view('zonasR.mapa', compact('zonas'));
    }
    // pk.eyJ1IjoidmludGFpbHN6IiwiYSI6ImNtY3MzajdkMTB0MngyanEyc2o5bjAwOHEifQ.FkEeSTHc8LB9ws0_jaQ6FA

    public function exportarPDF()
    {
        $zonas = ZonasRiesgo::all();

        foreach ($zonas as $zona) {
            $markers = [];
            $coordenadas = [];

            if ($zona->latitud1 && $zona->longitud1) {
                $markers[] = "pin-s+ff0000({$zona->longitud1},{$zona->latitud1})";
                $coordenadas[] = [$zona->longitud1, $zona->latitud1];
            }
            if ($zona->latitud2 && $zona->longitud2) {
                $markers[] = "pin-s+00ff00({$zona->longitud2},{$zona->latitud2})";
                $coordenadas[] = [$zona->longitud2, $zona->latitud2];
            }
            if ($zona->latitud3 && $zona->longitud3) {
                $markers[] = "pin-s+0000ff({$zona->longitud3},{$zona->latitud3})";
                $coordenadas[] = [$zona->longitud3, $zona->latitud3];
            }
            if ($zona->latitud4 && $zona->longitud4) {
                $markers[] = "pin-s+ffff00({$zona->longitud4},{$zona->latitud4})";
                $coordenadas[] = [$zona->longitud4, $zona->latitud4];
            }
            if ($zona->latitud5 && $zona->longitud5) {
                $markers[] = "pin-s+00ffff({$zona->longitud5},{$zona->latitud5})";
                $coordenadas[] = [$zona->longitud5, $zona->latitud5];
            }

            // Si hay más de un punto, cerrar el polígono para la línea
            if(count($coordenadas) > 1){
                $coordenadas[] = $coordenadas[0]; // opcional para cerrar la línea en forma de polígono
            }

            // Construir path para la línea: "lng lat;lng lat;..."
            $pathCoords = collect($coordenadas)
                ->map(fn($c) => $c[0] . ' ' . $c[1])
                ->implode(';');

            // Definir path con ancho 3px, borde negro, sin relleno transparente
            $path = "path-3+000000-00000000($pathCoords)";

            // Unir marcadores y path separados por coma
            $overlays = implode(',', $markers);
            if ($pathCoords) {
                $overlays .= ',' . $path;
            }

            // Calcular centro
            $centerLng = collect($coordenadas)->pluck(0)->avg() ?? -78.6;
            $centerLat = collect($coordenadas)->pluck(1)->avg() ?? -0.92;

            $zoom = 13;
            $width = 400;
            $height = 200;
            $token = 'pk.eyJ1IjoidmludGFpbHN6IiwiYSI6ImNtY3MzajdkMTB0MngyanEyc2o5bjAwOHEifQ.FkEeSTHc8LB9ws0_jaQ6FA';

            $mapUrl = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/$overlays/$centerLng,$centerLat,$zoom/{$width}x{$height}?access_token=$token";

            try {
                $response = Http::timeout(10)->get($mapUrl);

                if ($response->successful()) {
                    $base64 = 'data:image/png;base64,' . base64_encode($response->body());
                    $zona->mapa_base64 = $base64; // temporal para la vista PDF
                } else {
                    $zona->mapa_base64 = null;
                }
            } catch (\Exception $e) {
                $zona->mapa_base64 = null;
            }
        }

        $pdf = Pdf::loadView('zonasR.reporte', compact('zonas'));
        return $pdf->download('zonas_riesgo.pdf');
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
        //Vista para crear nuevo punto de riesgo
        return view('zonasR.nuevaZona');
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
        try{
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
            return redirect()->route('zonasR.index')->with('mensaje', 'Zona creada correctamente.');
        }
        catch (\Exception $e){
            return redirect()->back()->with('error', 'Hubo un error al guardar la zona de riesgo: ' . $e->getMessage());
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
        $zona = ZonasRiesgo::findOrFail($id);
        return view('zonasR.editar', compact('zona'));
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
            return redirect()->route('zonasR.index')->with('mensaje', 'Zona actualizada correctamente.');
        }
        catch (\Exception $e){
            return redirect()->back()->with('error', 'Hubo un error al actualizar la zona de riesgo: ' . $e->getMessage());
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
            $zona = ZonasRiesgo::findOrFail($id);
            $zona->delete();

            return redirect()->route('zonasR.index')->with('mensaje', 'Zona eliminada correctamente.');
        }
        catch (\Exception $e){
            return redirect()->back()->with('error', 'Hubo un error al eliminar la zona de riesgo: ' . $e->getMessage());
        }
        
    }
}
