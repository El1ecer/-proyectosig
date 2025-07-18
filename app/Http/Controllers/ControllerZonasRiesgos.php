<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasRiesgo; // Asegúrate de que el modelo ZonasRiesgo exista y sea correcto.
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http; // Para hacer la solicitud HTTP a Mapbox.
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log; // Para depuración, es útil mantenerlo.
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter; // Si no usas etiquetas, puedes quitarlo.
use Endroid\QrCode\Label\Font\NotoSans; // Si no usas fuentes personalizadas para QR, puedes quitarlo.
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin; // Si no usas este modo de QR, puedes quitarlo.


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
        $zonas = ZonasRiesgo::all();
        return view('zonasR.inicio', compact('zonas'));
    }

    public function mapa()
    {
        $zonas = ZonasRiesgo::all();
        return view('zonasR.mapa', compact('zonas'));
    }

    public function exportarPDF()
    {
        $zonas = ZonasRiesgo::all();
        $zonasConMapas = [];

        // Este es el token que has proporcionado y que funciona en otro lado
        $mapboxToken = 'pk.eyJ1IjoiZWxpby0xIiwiYSI6ImNtZDgwcnNrbzAxMDMycXB0ZTI3dHNuZzMifQ.2Le6GSlbTKiUPYnvPylIog';

        // --- PRUEBA DE MAPA FORZADO (TEMPORAL) ---
        // Vamos a intentar generar un mapa para una ubicación conocida y funcional.
        // Latitud y Longitud de un punto en Saquisilí, Cotopaxi, Ecuador
        $testLat = -0.8359;
        $testLng = -78.6738;
        $testZonaId = 9999; // ID ficticio para la prueba

        Log::info("--- INICIANDO PRUEBA DE MAPA FORZADO ---");
        Log::info("  Coordenadas de prueba: Lat={$testLat}, Lng={$testLng}");

        $mapaBase64Test = null;
        $mapboxUrlTest = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/{$testLng},{$testLat},14,0,0/300x300?access_token={$mapboxToken}";
        Log::info('  URL de Mapbox de prueba: ' . $mapboxUrlTest);

        try {
            $responseTest = Http::timeout(20)->get($mapboxUrlTest); // Aumentamos el timeout por seguridad

            if ($responseTest->successful()) {
                if ($responseTest->header('Content-Type') === 'image/png') {
                    $mapaBase64Test = 'data:image/png;base64,' . base64_encode($responseTest->body());
                    Log::info('  Mapa de prueba OBTENIDO EXITOSAMENTE y convertido a Base64.');
                } else {
                    Log::warning("  Mapbox API de prueba NO devolvió una imagen PNG. Content-Type: " . $responseTest->header('Content-Type') . ". Cuerpo (primeros 200 chars): " . substr($responseTest->body(), 0, 200));
                }
            } else {
                Log::error("  ERROR en la respuesta de Mapbox de prueba. Status: " . $responseTest->status() . ". Cuerpo: " . $responseTest->body());
            }
        } catch (\Exception $e) {
            Log::error("  EXCEPCIÓN al conectar con Mapbox para mapa de prueba: " . $e->getMessage());
        }
        Log::info("--- FIN PRUEBA DE MAPA FORZADO ---");

        // Añadimos esta zona de prueba al principio de la colección
        // Esto te permitirá ver si al menos el mapa de prueba aparece.
        $zonaTest = new \App\Models\ZonasRiesgo();
        $zonaTest->id = $testZonaId;
        $zonaTest->nombre = "Zona de Prueba (Saquisilí)";
        $zonaTest->descripcion = "Este es un mapa de prueba para depuración.";
        $zonaTest->nivelRiesgo = "Alto";
        // Las demás propiedades no son necesarias para esta prueba
        $zonasConMapas[] = [
            'zona' => $zonaTest,
            'mapa_base64' => $mapaBase64Test,
        ];
        // --- FIN PRUEBA DE MAPA FORZADO ---


        foreach ($zonas as $zona) {
            $mapaBase64 = null;
            $lat = $zona->latitud1; // Tomamos la primera latitud
            $lng = $zona->longitud1; // Tomamos la primera longitud

            Log::info('--- Procesando Zona de Riesgo REAL ID: ' . $zona->id . ' - Nombre: ' . $zona->nombre . ' ---');
            Log::info("  Valores leídos de DB: latitud1='{$lat}', longitud1='{$lng}'");


            if (is_numeric($lat) && is_numeric($lng) && $lat !== '' && $lng !== '') {
                $zoom = 14;
                $size = "300x300";

                $mapboxUrl = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/{$lng},{$lat},{$zoom},0,0/{$size}?access_token={$mapboxToken}";
                Log::info('  URL de Mapbox para zona REAL ' . $zona->id . ': ' . $mapboxUrl);

                try {
                    $response = Http::timeout(15)->get($mapboxUrl);

                    if ($response->successful()) {
                        if ($response->header('Content-Type') === 'image/png') {
                            $mapaBase64 = 'data:image/png;base64,' . base64_encode($response->body());
                            Log::info('  Mapa de Mapbox REAL obtenido y convertido a Base64 para Zona ID ' . $zona->id);
                        } else {
                            Log::warning("  Mapbox API REAL NO devolvió una imagen PNG para Zona ID " . $zona->id . ". Content-Type: " . $response->header('Content-Type') . ". Cuerpo (primeros 200 chars): " . substr($response->body(), 0, 200));
                        }
                    } else {
                        Log::error("  ERROR en la respuesta de Mapbox REAL para Zona ID " . $zona->id . ". Status: " . $response->status() . ". Cuerpo: " . $response->body());
                    }
                } catch (\Exception $e) {
                    Log::error("  EXCEPCIÓN al conectar con Mapbox para Zona REAL ID " . $zona->id . ": " . $e->getMessage());
                }
            } else {
                Log::warning('  Primera coordenada (latitud1, longitud1) NO VÁLIDA o vacía para Zona REAL ID ' . $zona->id . '. No se generará mapa para esta zona.');
            }

            $zonasConMapas[] = [
                'zona' => $zona,
                'mapa_base64' => $mapaBase64,
            ];
        }

        return $this->generarPdfConQr($zonasConMapas);
    }

    /**
     * Helper privado para generar el PDF y el QR, manteniendo el código DRY.
     */
    private function generarPdfConQr($zonasConMapas)
    {
        $qrDataGeneral = url('/zonas-riesgo'); // Asegúrate de que esta ruta existe

        $qrBase64General = null;
        try {
            $qrBuilder = Builder::create()
                ->writer(new PngWriter())
                ->data($qrDataGeneral)
                ->encoding(new Encoding('UTF-8'))
                ->size(150)
                ->margin(10)
                ->build();

            $qrBase64General = 'data:image/png;base64,' . base64_encode($qrBuilder->getString());
            Log::info('QR General del Reporte generado exitosamente con la URL: ' . $qrDataGeneral);
        } catch (\Exception $e) {
            Log::error('ERROR al generar el QR General del Reporte: ' . $e->getMessage());
        }

        $pdf = Pdf::loadView('zonasR.reporte', [
            'zonasConMapas' => $zonasConMapas,
            'qrBase64General' => $qrBase64General, // Pasar como qrBase64General
        ]);

        return $pdf->download('reporte-zonas.pdf');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
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
        try {
            $zona = ZonasRiesgo::findOrFail($id);
            $zona->delete();
            return redirect()->route('zonasR.index')->with('mensaje', 'Zona eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hubo un error al eliminar la zona de riesgo: ' . $e->getMessage());
        }

    }
}