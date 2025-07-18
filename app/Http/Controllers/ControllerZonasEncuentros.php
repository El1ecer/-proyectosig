<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasEncuentro; 

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Log; 

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh; 

class ControllerZonasEncuentros extends Controller
{
    public function index()
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }

        $zonas = ZonasEncuentro::all();
        return view('zonasE.inicio', compact('zonas'));
    }

    public function mapa()
    {
        $zonas = ZonasEncuentro::all();
        return view('zonasE.mapa', compact('zonas'));
    }

    public function exportarPDF()
    {
        $zonas = ZonasEncuentro::all(); 
        $zonasConMapas = [];

        foreach ($zonas as $zona) {
            $lat = $zona->latitud;
            $lng = $zona->longitud;

            Log::info('Procesando Zona de Encuentro ID: ' . $zona->id . ' - Nombre: ' . $zona->nombre);

            $mapaBase64 = null; 

            if (is_numeric($lat) && is_numeric($lng)) {
                $color = '2ECC71'; 
                if ($zona->capacidad > 100 && $zona->capacidad <= 500) {
                    $color = 'F1C40F'; 
                } elseif ($zona->capacidad > 500 && $zona->capacidad <= 1000) {
                    $color = 'E74C3C'; 
                }

                $marker = "pin-s+$color($lng,$lat)";
                $zoom = 15;
                $size = "400x200"; 

                $mapboxToken = env('MAPBOX_TOKEN');

                if (empty($mapboxToken)) {
                    Log::error("ERROR: MAPBOX_TOKEN no está configurado o está vacío en el archivo .env para Zona de Encuentro ID: " . $zona->id);
                } else {
                    $mapUrl = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/$marker/$lng,$lat,$zoom/$size?access_token=$mapboxToken";
                    Log::info('URL de Mapbox generada para Zona ID ' . $zona->id . ': ' . $mapUrl);

                    try {
                        $response = Http::timeout(15)->get($mapUrl); 

                        if ($response->successful()) {
                            if ($response->header('Content-Type') === 'image/png') {
                                $mapaBase64 = 'data:image/png;base64,' . base64_encode($response->body());
                                Log::info('Mapa de Mapbox obtenido y convertido a Base64 para Zona ID ' . $zona->id);
                            } else {
                                Log::warning('Mapbox API no devolvió una imagen PNG para Zona ID ' . $zona->id . '. Content-Type: ' . $response->header('Content-Type') . '. Cuerpo: ' . $response->body());
                            }
                        } else {
                            Log::error('Error en la respuesta de Mapbox para Zona ID ' . $zona->id . '. Status: ' . $response->status() . '. Cuerpo: ' . $response->body());
                        }
                    } catch (\Exception $e) {
                        Log::error('Excepción al conectar con Mapbox para Zona ID ' . $zona->id . ': ' . $e->getMessage());
                    }
                }
            } else {
                Log::warning('Coordenadas no válidas (Latitud: ' . $lat . ', Longitud: ' . $lng . ') para Zona ID ' . $zona->id . '. Mapa no disponible.');
            }

            $zonasConMapas[] = [
                'zona' => $zona,
                'mapa_base64' => $mapaBase64,
            ];
        }

        $qrBase64General = null; 
        $qrDataGeneral = route('zonasE.index'); 

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

        $pdf = Pdf::loadView('zonasE.reporte', [
            'zonasConMapas' => $zonasConMapas,      
            'qrBase64General' => $qrBase64General,  
        ]);

        return $pdf->download('puntos_encuentro.pdf');
    }

    public function create()
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }

        return view('zonasE.nuevaZona');
    }

    public function store(Request $request)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }

        try {
            ZonasEncuentro::create($request->only(['nombre', 'capacidad', 'responsable', 'latitud', 'longitud']));
            return redirect()->route('zonasE.index')->with('mensaje', 'Punto de encuentro registrado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al guardar el punto de encuentro: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un error al guardar el punto de encuentro: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }

        $zona = ZonasEncuentro::findOrFail($id);
        return view('zonasE.show', compact('zona'));
    }

    public function edit(string $id)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }

        $zona = ZonasEncuentro::findOrFail($id);
        return view('zonasE.editar', compact('zona'));
    }

    public function update(Request $request, string $id)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }

        try {
            $zona = ZonasEncuentro::findOrFail($id);
            $zona->update($request->only(['nombre', 'capacidad', 'responsable', 'latitud', 'longitud']));
            return redirect()->route('zonasE.index')->with('mensaje', 'Punto de encuentro actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar el punto de encuentro: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un error al actualizar el punto de encuentro: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }

        try {
            $zona = ZonasEncuentro::findOrFail($id);
            $zona->delete();
            return redirect()->route('zonasE.index')->with('mensaje', 'Punto de encuentro eliminado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar el punto de encuentro: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un error al eliminar el punto de encuentro: ' . $e->getMessage());
        }
    }
}
