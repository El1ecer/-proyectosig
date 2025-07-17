<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasSegura;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Builder\Builder as BuilderStatic;
use Endroid\QrCode\Encoding\Encoding;

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
        $zonas = ZonasSegura::all();
        return view('zonasS.inicio', compact('zonas'));
    }

    public function mapa()
    {
        $zonas = ZonasSegura::all();
        return view('zonasS.mapa', compact('zonas'));
    }

    
    public function exportarPDF()
    {
        $googleMapsApiKey = 'AIzaSyC9iGJnedPYn_ZU7CnKkUilE2IDVN0_7W0'; // API Key de Google

        $zonas = ZonasSegura::all();

        foreach ($zonas as $zona) {
            $lat = $zona->latitud;
            $lng = $zona->longitud;

            $staticMapUrl = "https://maps.googleapis.com/maps/api/staticmap?" . http_build_query([
                'center' => "$lat,$lng",
                'zoom' => 16,
                'size' => '300x300',
                'markers' => "color:red|label:Z|$lat,$lng",
                'key' => $googleMapsApiKey
            ]);

            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0'
                ])->get($staticMapUrl);

                if ($response->successful()) {
                    $imageContents = $response->body();
                    $zona->mapa_base64 = 'data:image/png;base64,' . base64_encode($imageContents);
                } else {
                    $zona->mapa_base64 = null;
                }
            } catch (\Exception $e) {
                $zona->mapa_base64 = null;
            }
        }

        // Generar código QR
        try {
            $qrContent = url('/zonasS');
            $result = BuilderStatic::create()
                ->data($qrContent)
                ->size(200)
                ->margin(10)
                ->encoding(new Encoding('UTF-8'))
                ->build();

            $qrBase64 = 'data:image/png;base64,' . base64_encode($result->getString());
        } catch (\Exception $e) {
            $qrBase64 = null;
        }

        $pdf = Pdf::loadView('zonasS.reporte', compact('zonas', 'qrBase64'));
        return $pdf->stream('reporte_zonas.pdf');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hubo un error al guardar la zona segura: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
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

        try {
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
        } catch (\Exception $e) {
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

        try {
            $zona = ZonasSegura::findOrFail($id);
            $zona->delete();
            return redirect()->route('zonasS.index')->with('mensaje', 'Zona eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hubo un error al eliminar la zona segura: ' . $e->getMessage());
        }
    }
}
