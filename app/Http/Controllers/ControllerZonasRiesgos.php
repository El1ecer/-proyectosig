<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasRiesgo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log; // Asegúrate de que esto está importado

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
    // pk.eyJ1IjoidmludGFpbHN6IiwiYSI6ImNtY3MzajdkMTB0MngyanEyc2o5bjAwOHEifQ.FkEeSTHc8LB9ws0_jaQ6FA

   public function exportarPDF()
    {
        $zonas = ZonasRiesgo::all();
        $zonasConMapas = [];

        // Tu token de Mapbox (reconfírmalo de tu cuenta)
        // Este token debe tener acceso a Mapbox Static Images API.
        $mapboxToken = 'pk.eyJ1IjoiZWxpby0xIiwiYSI6ImNtZDgwcnNrbzAxMDMycXB0ZTI3dHNuZzMifQ.2Le6GSlbTKiUPYnvPylIog'; 

        foreach ($zonas as $zona) {
            $validMarkers = []; // Almacena los strings de los marcadores "pin-s+color(long,lat)"
            $validCoordinatesForPoly = []; // Almacena [longitud, latitud] para construir el polígono y centrar el mapa

            // Iterar sobre las 5 posibles coordenadas
            for ($i = 1; $i <= 5; $i++) {
                $lat = $zona->{"latitud" . $i};
                $lng = $zona->{"longitud" . $i};

                // Validar que las coordenadas existan y sean numéricas válidas
                if (is_numeric($lat) && is_numeric($lng) && $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                    // Generar un color diferente para cada marcador si lo deseas, o usa uno fijo
                    $markerColor = match($i) {
                        1 => 'ff0000', // Rojo
                        2 => '00ff00', // Verde
                        3 => '0000ff', // Azul
                        4 => 'ffff00', // Amarillo
                        5 => '00ffff', // Cyan
                        default => 'aaaaaa' // Gris por defecto
                    };
                    $validMarkers[] = "pin-s+{$markerColor}({$lng},{$lat})";
                    $validCoordinatesForPoly[] = [(float)$lng, (float)$lat];
                } else {
                    Log::info("Zona '{$zona->nombre}': Coordenadas lat{$i}/long{$i} inválidas o vacías: Lat={$lat}, Lng={$lng}");
                }
            }

            $polygonPath = null;
            // Solo intentar dibujar un polígono si hay al menos 3 puntos válidos
            if (count($validCoordinatesForPoly) >= 3) {
                // Cerrar el polígono añadiendo el primer punto al final
                $closedPolygonCoords = $validCoordinatesForPoly;
                $closedPolygonCoords[] = $closedPolygonCoords[0]; 
                
                $pathCoordsString = collect($closedPolygonCoords)
                    ->map(fn($c) => implode(',', $c))
                    ->implode(',');

                $fillColor = match($zona->nivelRiesgo) {
                    'Alto' => 'ff000080', // Rojo con 50% de transparencia (hex #RRGGBBAA)
                    'Medio' => 'ffff0080', // Amarillo con 50% de transparencia
                    default => '0476D980' // Azul con 50% de transparencia
                };

                // path-{stroke_width}+{stroke_color}+{fill_color}(polyline_encoded_coordinates)
                // Usamos un grosor de línea de 3, color de borde negro y color de relleno con transparencia
                $polygonPath = "path-3+000000+" . $fillColor . "({$pathCoordsString})";
                Log::info("Zona '{$zona->nombre}': Polígono generado: " . $polygonPath);

            } else {
                Log::info("Zona '{$zona->nombre}': No hay suficientes coordenadas (menos de 3) para dibujar un polígono.");
            }

            // Combinar marcadores y polígono en un solo string para la URL de Mapbox
            $overlays = implode(',', $validMarkers);
            if ($polygonPath) {
                if (!empty($overlays)) {
                    $overlays .= ',' . $polygonPath;
                } else {
                    $overlays = $polygonPath; // El polígono es el único overlay
                }
            }
            
            // Calcular el centro del mapa
            $centerLng = -78.6; // Coordenadas predeterminadas (ej. Ecuador central)
            $centerLat = -0.92;

            if (!empty($validCoordinatesForPoly)) {
                // Calcular el promedio de las coordenadas válidas para centrar el mapa
                // Asegúrate de que los valores sean float antes de promediar
                $centerLng = collect($validCoordinatesForPoly)->pluck(0)->avg();
                $centerLat = collect($validCoordinatesForPoly)->pluck(1)->avg();
                Log::info("Zona '{$zona->nombre}': Centro calculado: {$centerLng},{$centerLat}");
            } else {
                Log::info("Zona '{$zona->nombre}': Usando centro predeterminado.");
            }
            
            $zoom = 14; // Nivel de zoom predeterminado
            $width = 400; // Ancho de la imagen del mapa
            $height = 200; // Alto de la imagen del mapa

            // Construir la URL de Mapbox Static Images
            $mapUrl = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/";
            if (!empty($overlays)) {
                $mapUrl .= "{$overlays}/";
            }
            $mapUrl .= "{$centerLng},{$centerLat},{$zoom}/{$width}x{$height}?access_token={$mapboxToken}";

            // === INICIO DEPURACIÓN Y SOLUCIÓN DE PROBLEMAS DEL MAPA ===
            Log::info("DEBUG: Mapbox URL final para zona '{$zona->nombre}': " . $mapUrl);
            
            $mapaBase64 = null;
            try {
                // Aumentar el tiempo de espera a 30 segundos para redes lentas o respuestas grandes
                $response = Http::timeout(30)->get($mapUrl); 

                Log::info("DEBUG: Respuesta de Mapbox para '{$zona->nombre}' - Status: " . $response->status());
                $contentType = $response->header('Content-Type');
                Log::info("DEBUG: Respuesta de Mapbox para '{$zona->nombre}' - Content-Type: " . $contentType);

                if ($response->successful()) {
                    // Es crucial que Mapbox devuelva una imagen PNG.
                    if (strpos($contentType, 'image/png') !== false) {
                        $mapaBase64 = 'data:image/png;base64,' . base64_encode($response->body());
                        Log::info("DEBUG: Mapa Base64 generado exitosamente para '{$zona->nombre}'.");
                    } else {
                        // Si no es una imagen PNG, algo está mal en la URL o el token/permisos.
                        Log::warning("ADVERTENCIA: Mapbox NO devolvió una imagen PNG para '{$zona->nombre}'. Content-Type: {$contentType}. Body (primeros 500 chars): " . substr($response->body(), 0, 500));
                        $mapaBase64 = null; // No disponible
                    }
                } else {
                    // Si la respuesta no es exitosa (ej. 4xx, 5xx), muestra el mensaje de error de Mapbox.
                    Log::error("ERROR: Falló la carga del mapa para '{$zona->nombre}'. URL: {$mapUrl}. Status: {$response->status()}. Respuesta de error de Mapbox: " . $response->body());
                    $mapaBase64 = null; // No disponible
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                // Error específico de conexión (timeout, no se puede conectar al host, DNS)
                Log::error("ERROR: Error de conexión al generar mapa para '{$zona->nombre}': " . $e->getMessage() . ". URL intentada: " . $mapUrl);
                $mapaBase64 = null;
            } catch (\Exception $e) {
                // Cualquier otro error inesperado en el proceso de solicitud
                Log::error('ERROR: Error general al generar mapa para ' . $zona->nombre . ': ' . $e->getMessage() . '. Archivo: ' . $e->getFile() . ' Línea: ' . $e->getLine());
                $mapaBase64 = null;
            }
            // === FIN DEPURACIÓN Y SOLUCIÓN DE PROBLEMAS DEL MAPA ===

            $zonasConMapas[] = [
                'zona' => $zona,
                'mapa_base64' => $mapaBase64
            ];
        }

        // QR Base64
        $qrContenido = 'https://tusitio.com/zonas-riesgo'; // Asegúrate de que esta URL sea real si el QR va a ser escaneado.
        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->data($qrContenido)
            ->encoding(new Encoding('UTF-8'))
            ->size(250)
            ->margin(15)
            ->build();

        $qrBase64 = 'data:image/png;base64,' . base64_encode($qrCode->getString());

        // Generar PDF
        $pdf = Pdf::loadView('zonasR.reporte', [
            'zonasConMapas' => $zonasConMapas,
            'qrBase64' => $qrBase64
        ]);
        
        // Es crucial para que DomPDF pueda cargar imágenes desde URLs externas o Base64
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        
        return $pdf->download('zonas_riesgo.pdf');
    }
    /**
     * Esta función getBase64FromUrl ya no es necesaria si usas Http::get() de Laravel.
     * La dejo comentada por si tenías alguna dependencia de ella en otro lado,
     * pero para obtener las imágenes de Mapbox, Http::get() es más robusto y Laravel-friendly.
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
    //         $mime = 'image/png';
    //         return 'data:' . $mime . ';base64,' . base64_encode($imageData);
    //     } catch (\Exception $e) {
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