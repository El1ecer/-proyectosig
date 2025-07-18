<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ZonasSegura;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use Endroid\QrCode\Builder\Builder ;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
class ControllerZonasSeguras extends Controller
{
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
        $zonas = ZonasSegura::all();

        // Asegúrate que esta carpeta exista
        $mapasDir = storage_path('app/public/mapas');

        if (!file_exists($mapasDir)) {
            mkdir($mapasDir, 0777, true);
        }

        // Token de Mapbox proporcionado por ti
        $mapboxToken = 'pk.eyJ1IjoiZWxpby0xIiwiYSI6ImNtZDgwcnNrbzAxMDMycXB0ZTI3dHNuZzMifQ.2Le6GSlbTKiUPYnvPylIog';

        foreach ($zonas as $zona) {
            // Construcción de la URL del mapa estático de Mapbox
            $url = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/pin-l+ff0000({$zona->longitud},{$zona->latitud})/{$zona->longitud},{$zona->latitud},14,0/300x300?access_token={$mapboxToken}";

            $mapaNombre = "mapa_{$zona->id}.png";
            $mapaPath = $mapasDir . '/' . $mapaNombre;

            try {
                $mapaContenido = file_get_contents($url);
                file_put_contents($mapaPath, $mapaContenido);

                // Convertir a base64 para mostrar en el PDF
                $zona->mapa_base64 = 'data:image/png;base64,' . base64_encode($mapaContenido);
            } catch (\Exception $e) {
                $zona->mapa_base64 = null;
            }
        }

        // Generar QR que redirija a alguna URL de tu sistema
        $urlQR = url('/zonas-seguras');

        $qr = Builder::create()
            ->writer(new PngWriter())
            ->data($urlQR)
            ->encoding(new Encoding('UTF-8'))
            ->size(200)
            ->margin(10)
            ->build();

        $qrBase64 = 'data:image/png;base64,' . base64_encode($qr->getString());

        // Generar PDF
        $pdf = Pdf::loadView('zonasS.reporte', compact('zonas', 'qrBase64'));

        return $pdf->download('reporte_zonas_seguras.pdf');
    }


    public function create()
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        return view('zonasS.nuevaZona');
    }

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

    public function edit(string $id)
    {
        if (session('tipo') !== 'Administrador') {
            return redirect('/')->with('error', 'Acceso no autorizado.');
        }
        $zona = ZonasSegura::findOrFail($id);
        return view('zonasS.editar', compact('zona'));
    }

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
