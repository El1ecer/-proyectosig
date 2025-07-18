<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasEncuentro; // Asegúrate de que el nombre del modelo sea correcto y coincida con tu base de datos

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage; // Puede que no sea estrictamente necesario si solo manejas base64
use Illuminate\Support\Facades\Http; // Para hacer peticiones HTTP a Mapbox
use Illuminate\Support\Facades\Log; // Para registrar eventos y errores

// --- CLASES DE ENDROID/QRCODE ---
// ¡Importante: Asegúrate de que estas líneas no estén comentadas!
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh; // Clase para el nivel de corrección de errores
// Las siguientes son opcionales y no se usan en el código actual, puedes quitarlas si quieres
// use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
// use Endroid\QrCode\Label\Font\NotoSans;
// use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
// --- FIN CLASES DE ENDROID/QRCODE ---

class ControllerZonasEncuentros extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Redirige si el usuario no es Administrador
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        // Pagina principal: obtiene todas las zonas de encuentro
        $zonas = ZonasEncuentro::all();
        return view('zonasE.inicio', compact('zonas'));
    }

    /**
     * Muestra la vista del mapa.
     */
    public function mapa()
    {
        // Pagina principal del mapa: obtiene todas las zonas para mostrarlas
        $zonas = ZonasEncuentro::all();
        return view('zonasE.mapa', compact('zonas'));
    }

    /**
     * Exporta un PDF con la información de las zonas de encuentro.
     * Incluye un mapa de Mapbox para cada zona y un QR general al final del reporte.
     */
    public function exportarPDF()
    {
        $zonas = ZonasEncuentro::all(); // Obtiene todas las zonas de encuentro de la base de datos

        // Array para almacenar la información de cada zona junto con su mapa en base64
        $zonasConMapas = [];

        // --- Bucle para procesar cada zona individualmente ---
        foreach ($zonas as $zona) {
            $lat = $zona->latitud;
            $lng = $zona->longitud;

            Log::info('Procesando Zona de Encuentro ID: ' . $zona->id . ' - Nombre: ' . $zona->nombre);

            $mapaBase64 = null; // Inicializamos la variable del mapa para cada zona

            // Verificamos que las coordenadas sean válidas antes de intentar generar el mapa
            if (is_numeric($lat) && is_numeric($lng)) {
                // Lógica para asignar un color de marcador de Mapbox basado en la capacidad
                $color = '2ECC71'; // Verde por defecto (Capacidad 1-100)
                if ($zona->capacidad > 100 && $zona->capacidad <= 500) {
                    $color = 'F1C40F'; // Amarillo (Capacidad 101-500)
                } elseif ($zona->capacidad > 500 && $zona->capacidad <= 1000) {
                    $color = 'E74C3C'; // Rojo (Capacidad 501-1000)
                }

                $marker = "pin-s+$color($lng,$lat)"; // Formato del marcador para la URL de Mapbox
                $zoom = 15; // Nivel de zoom
                $size = "400x200"; // Tamaño de la imagen del mapa (ancho x alto)

                // Obtener el token de Mapbox desde las variables de entorno
                $mapboxToken = env('MAPBOX_TOKEN');

                if (empty($mapboxToken)) {
                    Log::error("ERROR: MAPBOX_TOKEN no está configurado o está vacío en el archivo .env para Zona de Encuentro ID: " . $zona->id);
                } else {
                    // Construir la URL completa para la API de Mapbox Static Images
                    $mapUrl = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/$marker/$lng,$lat,$zoom/$size?access_token=$mapboxToken";

                    Log::info('URL de Mapbox generada para Zona ID ' . $zona->id . ': ' . $mapUrl);

                    try {
                        // Realizar la petición HTTP a Mapbox
                        $response = Http::timeout(15)->get($mapUrl); // Tiempo de espera de 15 segundos

                        // Verificar si la petición fue exitosa (código de estado 2xx)
                        if ($response->successful()) {
                            // Verificar que la respuesta sea de tipo imagen/png
                            if ($response->header('Content-Type') === 'image/png') {
                                $mapaBase64 = 'data:image/png;base64,' . base64_encode($response->body());
                                Log::info('Mapa de Mapbox obtenido y convertido a Base64 para Zona ID ' . $zona->id);
                            } else {
                                // Si Mapbox no devuelve PNG, es probable que sea un error de la API
                                Log::warning('Mapbox API no devolvió una imagen PNG para Zona ID ' . $zona->id . '. Content-Type: ' . $response->header('Content-Type') . '. Cuerpo: ' . $response->body());
                            }
                        } else {
                            // Log si la respuesta de Mapbox no fue exitosa (ej. 401, 404, etc.)
                            Log::error('Error en la respuesta de Mapbox para Zona ID ' . $zona->id . '. Status: ' . $response->status() . '. Cuerpo: ' . $response->body());
                        }
                    } catch (\Exception $e) {
                        // Captura cualquier excepción durante la petición HTTP (ej. problemas de red)
                        Log::error('Excepción al conectar con Mapbox para Zona ID ' . $zona->id . ': ' . $e->getMessage());
                    }
                }
            } else {
                Log::warning('Coordenadas no válidas (Latitud: ' . $lat . ', Longitud: ' . $lng . ') para Zona ID ' . $zona->id . '. Mapa no disponible.');
            }

            // Agregamos la zona y su mapa (o null si falló) al array que pasaremos a la vista
            $zonasConMapas[] = [
                'zona' => $zona,
                'mapa_base64' => $mapaBase64,
            ];
        }
        // --- FIN del bucle de zonas ---


        // --- INICIO: Lógica para generar UN ÚNICO QR GENERAL para todo el reporte ---
        $qrBase64General = null; // Inicializamos la variable para el QR general

        // Define el contenido del QR. Aquí apunta a la página principal de zonas de encuentro.
        $qrDataGeneral = route('zonasE.index'); // Genera la URL para la ruta 'zonasE.index'

        try {
            // Construye el objeto QR
            $qrBuilder = Builder::create()
                ->writer(new PngWriter()) // Define que el QR se generará como imagen PNG
                ->data($qrDataGeneral) // Los datos que contendrá el QR
                ->encoding(new Encoding('UTF-8')) // Codificación de caracteres
                ->size(150) // Tamaño del QR en píxeles
                ->margin(10) // Margen alrededor del QR
                ->build();

            // Convierte la imagen del QR a formato Base64 para incrustarla directamente en el HTML del PDF
            $qrBase64General = 'data:image/png;base64,' . base64_encode($qrBuilder->getString());
            Log::info('QR General del Reporte generado exitosamente con la URL: ' . $qrDataGeneral);

        } catch (\Exception $e) {
            // Captura y registra cualquier error durante la generación del QR
            Log::error('ERROR al generar el QR General del Reporte: ' . $e->getMessage());
        }
        // --- FIN de la lógica del QR general ---

        // Carga la vista del PDF, pasando las zonas con sus mapas y el QR general
        $pdf = Pdf::loadView('zonasE.reporte', [
            'zonasConMapas' => $zonasConMapas,      // Array de zonas con sus mapas Base64
            'qrBase64General' => $qrBase64General,  // El QR Base64 general
        ]);

        // Retorna el PDF para descarga
        return $pdf->download('puntos_encuentro.pdf');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
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
        } catch (\Exception $e) {
            Log::error('Error al guardar el punto de encuentro: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un error al guardar el punto de encuentro: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     * Este método es parte del Route::resource, pero su funcionalidad
     * aquí no es directamente utilizada por el QR general en este momento.
     * Si el QR general apuntara a una zona específica, se usaría.
     */
    public function show(string $id)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        $zona = ZonasEncuentro::findOrFail($id);
        // Asegúrate de tener una vista 'resources/views/zonasE/show.blade.php'
        return view('zonasE.show', compact('zona'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
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
        try {
            $zona = ZonasEncuentro::findOrFail($id);
            $zona->update([
                'nombre' => $request->nombre,
                'capacidad' => $request->capacidad,
                'responsable' => $request->responsable,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud
            ]);
            return redirect()->route('zonasE.index')->with('mensaje', 'Punto de encuentro actualizado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al actualizar el punto de encuentro: ' . $e->getMessage());
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