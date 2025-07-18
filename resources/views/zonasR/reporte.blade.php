<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 15px; }
        .map-img { width: 200px; height: auto; }
    </style>
</head>
<body>

<div class="title">Reporte de Zonas de Riesgo</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Nivel de Riesgo</th>
            <th>Mapa</th>
        </tr>
    </thead>
    <tbody>
        @foreach($zonas as $index => $zona)
        @php
            $coordenadas = [];

            if ($zona->latitud1 && $zona->longitud1) $coordenadas[] = [$zona->longitud1, $zona->latitud1];
            if ($zona->latitud2 && $zona->longitud2) $coordenadas[] = [$zona->longitud2, $zona->latitud2];
            if ($zona->latitud3 && $zona->longitud3) $coordenadas[] = [$zona->longitud3, $zona->latitud3];
            if ($zona->latitud4 && $zona->longitud4) $coordenadas[] = [$zona->longitud4, $zona->latitud4];
            if ($zona->latitud5 && $zona->longitud5) $coordenadas[] = [$zona->longitud5, $zona->latitud5];

            if (count($coordenadas) > 2) {
                $coordenadas[] = $coordenadas[0]; // cerrar el polígono
            }

            $fill = '0476D9'; // Bajo
            if ($zona->nivelRiesgo === 'Alto') $fill = 'FF0000';
            elseif ($zona->nivelRiesgo === 'Medio') $fill = 'FFFF00';

            $centerLat = collect(array_column($coordenadas, 1))->avg();
            $centerLng = collect(array_column($coordenadas, 0))->avg();

            // Construir path sin usar urlencode
            $pathCoords = collect($coordenadas)->map(fn($c) => $c[0] . ' ' . $c[1])->implode(',');
            $path = "path-3+000000-$fill($pathCoords)";

            $mapUrl = "https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/" . $path .
                    "/$centerLng,$centerLat,15/400x200?access_token=pk.eyJ1IjoidmludGFpbHN6IiwiYSI6ImNtY3MzajdkMTB0MngyanEyc2o5bjAwOHEifQ.FkEeSTHc8LB9ws0_jaQ6FA";
        @endphp



        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $zona->nombre }}</td>
            <td>{{ $zona->descripcion }}</td>
            <td>{{ $zona->nivelRiesgo }}</td>
            <td>
                @if($zona->mapa_base64)
                    <img src="{{ $zona->mapa_base64 }}" class="map-img" alt="Mapa">
                @else
                    <span>No disponible</span>
                @endif
            </td>

        </tr>
        @endforeach
    </tbody>
</table>
<div style="text-align: center; margin: 20px o;">
            qr identificador para zonas de Riesgo
        @if ($qrBase64)
            <img src="{{ $qrBase64 }}" alt="Código QR" class="qr-img" />
        @else
            <p style="text-align: center;">[QR no disponible]</p>
        @endif

    </div>

</body>
</html>
