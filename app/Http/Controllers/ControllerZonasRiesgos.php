<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasRiesgo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log; // ¡IMPORTANTE! Asegúrate de que esto está importado

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
        $zonas = ZonasRiesgo::all();
        return view('zonasR.inicio', compact('zonas'));
    }

    public function mapa()
    {
        //Pagina principal
        $zonas = ZonasRiesgo::all();
        return view('zonasR.mapa', compact('zonas'));
    }

    public function exportarPDF()
    {
        $zonas = ZonasRiesgo::all();

        $zonasConMapas = [];
        foreach ($zonas as $zona) {
            $coordenadas = [];

            for ($i = 1; $i <= 4; $i++) {
                $lat = $zona["latitud$i"];
                $lng = $zona["longitud$i"];
                if (is_numeric($lat) && is_numeric($lng)) {
                    $coordenadas[] = [$lng, $lat]; // lng, lat (¡orden importante para Mapbox!)
                }
            }

            if (count($coordenadas) >= 3) {
                // Calcular centro del mapa
                $avgLat = array_sum(array_column($coordenadas, 1)) / count($coordenadas);
                $avgLng = array_sum(array_column($coordenadas, 0)) / count($coordenadas);

                // Preparar el overlay para el polígono
                $polygonCoords = implode(",", array_map(function ($coord) {
                    return implode(" ", $coord);
                }, array_merge($coordenadas, [$coordenadas[0]]))); // cerrar el polígono

                $overlay = "path-5+f44-0.5(" . $polygonCoords . ")";

                // URL de Mapbox
                $mapboxToken = env('MAPBOX_TOKEN'); // asegúrate de tener esto en .env
                $mapboxUrl = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/{$overlay}/{$avgLng},{$avgLat},15/500x300?access_token={$mapboxToken}";

                try {
                    $response = Http::get($mapboxUrl);
                    if ($response->ok() && $response->header('Content-Type') === 'image/png') {
                        $mapaBase64 = 'data:image/png;base64,' . base64_encode($response->body());
                    } else {
                        $mapaBase64 = null;
                    }
                } catch (\Exception $e) {
                    $mapaBase64 = null;
                }
            } else {
                $mapaBase64 = null;
            }

            $zonasConMapas[] = [
                'zona' => $zona,
                'mapa_base64' => $mapaBase64,
            ];
        }

        // Generar QR para todo el documento (opcional, puede ser URL o mensaje)
        $qr = Builder::create()
            ->data('https://ejemplo.com/reporte')
            ->encoding(new Encoding('UTF-8'))
            ->size(100)
            ->margin(10)
            ->build();

        $qrBase64 = $qr->getDataUri();

        // Cargar la vista
        $pdf = Pdf::loadView('zonasR.reporte', [
            'zonasConMapas' => $zonasConMapas,
            'qrBase64' => $qrBase64,
        ]);

        return $pdf->stream('reporte-zonas.pdf');
    }

    /**
     * Esta función getBase64FromUrl no es necesaria si usas Http::get() de Laravel.
     * La he dejado comentada por si tenías alguna dependencia de ella en otro lado,
     * pero para obtener las imágenes de Mapbox, Http::get() es más robusta y Laravel-friendly.
     */
    // private function getBase64FromUrl($url)
    // {
    //     try {
    //         $contextOptions = [
    //             "ssl" => [
    //                 "verify_peer" => false,
    //                 "verify_peer_name" => false,
    //             ],
    //             "http" => [
    //                 "header" => "User-Agent: Mozilla/5.0\r\n"
    //             ]
    //         ];
    //         $context = stream_context_create($contextOptions);
    //         $imageData = file_get_contents($url, false, $context);
    //         if ($imageData === false) {
    //             throw new \Exception("No se pudo obtener la imagen desde la URL.");
    //         }
    //         $mime = 'image/png'; // Asumimos PNG
    //         return 'data:' . $mime . ';base64,' . base64_encode($imageData);
    //     } catch (\Exception $e) {
    //         Log::error("Error en getBase64FromUrl: " . $e->getMessage() . " URL: " . $url);
    //         return null;
    //     }
    // }

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
        try {
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
        } catch (\Exception $e) {
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
        try {
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
        } catch (\Exception $e) {
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
        try {
            $zona = ZonasRiesgo::findOrFail($id);
            $zona->delete();

            return redirect()->route('zonasR.index')->with('mensaje', 'Zona eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hubo un error al eliminar la zona de riesgo: ' . $e->getMessage());
        }

    }
}