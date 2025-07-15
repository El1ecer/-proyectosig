<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 15px; }
        .map-img { width: 300px; height: auto; }
    </style>
</head>
<body>
    <div class="title">Reporte de Puntos de Encuentro</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Capacidad</th>
                <th>Responsable</th>
                <th>Coordenadas</th>
                <th>Mapa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($zonas as $index => $zona)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $zona->nombre }}</td>
                <td>
                    @if($zona->capacidad == 100)
                        1 - 100
                    @elseif($zona->capacidad == 500)
                        101 - 500
                    @else
                        501 - 1000
                    @endif
                </td>
                <td>{{ $zona->responsable }}</td>
                <td>{{ $zona->latitud }}, {{ $zona->longitud }}</td>
                <td>
                    @if($zona->mapa_base64)
                        <img src="{{ $zona->mapa_base64 }}" class="map-img" />
                    @else
                        No disponible
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
