<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasRiesgo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;


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

            Log::info('Procesando zona ID: ' . $zona->id . ' Nombre: ' . $zona->nombre);

            for ($i = 1; $i <= 4; $i++) {
                $lat = $zona["latitud$i"];
                $lng = $zona["longitud$i"];
                if (is_numeric($lat) && is_numeric($lng)) {
                    $coordenadas[] = [$lng, $lat]; 
                } else {
                    Log::warning("Coordenada {$i} para zona ID " . $zona->id . " no es numérica: Lat: {$lat}, Lng: {$lng}");
                }
            }

            if (count($coordenadas) >= 3) {
                Log::info('Coordenadas para polígono zona ' . $zona->id . ': ' . json_encode($coordenadas));

                $avgLat = array_sum(array_column($coordenadas, 1)) / count($coordenadas);
                $avgLng = array_sum(array_column($coordenadas, 0)) / count($coordenadas);

                $polygonCoords = implode(",", array_map(function ($coord) {
                    return implode(" ", $coord);
                }, array_merge($coordenadas, [$coordenadas[0]]))); 

                $overlay = "path-5+f44-0.5(" . $polygonCoords . ")";

                $mapboxToken = env('MAPBOX_TOKEN');
                if (empty($mapboxToken)) {
                    Log::error("MAPBOX_TOKEN no está configurado en el archivo .env o está vacío.");
                    $mapaBase64 = null;
                } else {
                    $mapboxUrl = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/{$overlay}/{$avgLng},{$avgLat},15/500x300?access_token={$mapboxToken}";

                    
                    Log::info('URL de Mapbox para zona ' . $zona->id . ': ' . $mapboxUrl);

                    try {
                        $response = Http::timeout(15)->get($mapboxUrl); 

                        if ($response->successful()) { 
                            if ($response->header('Content-Type') === 'image/png') {
                                $mapaBase64 = 'data:image/png;base64,' . base64_encode($response->body());
                                Log::info('Mapa generado exitosamente para zona ' . $zona->id);
                            } else {
                                Log::warning("Mapbox API no devolvió una imagen PNG para zona " . $zona->id . ". Content-Type: " . $response->header('Content-Type') . ". Respuesta: " . $response->body());
                                $mapaBase64 = null;
                            }
                        } else {
                            Log::error("Error en la respuesta de Mapbox API para zona " . $zona->id . ". Status: " . $response->status() . ". Cuerpo: " . $response->body());
                            $mapaBase64 = null;
                        }
                    } catch (\Exception $e) {
                        Log::error("Excepción al obtener mapa de Mapbox para zona " . $zona->id . ": " . $e->getMessage());
                        $mapaBase64 = null;
                    }
                }
            } else {
                Log::warning('Zona ID ' . $zona->id . ' no tiene suficientes coordenadas válidas para formar un polígono (necesita al menos 3). Coordenadas encontradas: ' . count($coordenadas));
                $mapaBase64 = null;
            }

            $zonasConMapas[] = [
                'zona' => $zona,
                'mapa_base64' => $mapaBase64,
            ];
        }

        // ... (resto de tu código para generar QR y PDF) ...
        $qrData = 'https://ejemplo.com/reporte';

        try {
            $qr = Builder::create()
                ->writer(new PngWriter())
                ->data($qrData)
                ->encoding(new Encoding('UTF-8'))
                ->size(100)
                ->margin(10)
                ->build();

            $qrBase64 = 'data:image/png;base64,' . base64_encode($qr->getString());

        } catch (\Exception $e) {
            Log::error("Error al generar el QR para el reporte: " . $e->getMessage());
            $qrBase64 = null;
        }
        // --- FIN: Generación del Código QR ---

        // Cargar la vista
        $pdf = Pdf::loadView('zonasR.reporte', [
            'zonasConMapas' => $zonasConMapas,
            'qrBase64' => $qrBase64,
        ]);

        return $pdf->download('reporte-zonas.pdf');
    }


    // ... el resto de tu controlador sigue igual ...
    // La función getBase64FromUrl sigue comentada, lo cual está bien.

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