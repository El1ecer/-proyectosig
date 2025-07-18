<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 15px; }
        .map-img { width: 200px; height: auto; }
        .qr-img { width: 150px; height: 150px; margin-top: 15px; }
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
        @foreach($zonasConMapas as $index => $item)
            @php
                $zona = $item['zona'];
                $mapaBase64 = $item['mapa_base64'];
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $zona->nombre }}</td>
                <td>{{ $zona->descripcion }}</td>
                <td>{{ $zona->nivelRiesgo }}</td>
                <td>
                    @if($mapaBase64)
                        <img src="{{ $mapaBase64 }}" class="map-img" alt="Mapa de la zona" />
                    @else
                        <span>No disponible</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div style="text-align: center;">
    <p>QR identificador para Zonas de Riesgo</p>
    @if ($qrBase64)
        <img src="{{ $qrBase64 }}" alt="Código QR" class="qr-img" />
    @else
        <p>[QR no disponible]</p>
    @endif
</div>

</body>
</html>
