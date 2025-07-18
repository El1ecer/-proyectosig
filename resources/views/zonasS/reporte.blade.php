<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 15px; }
        img { max-width: 300px; height: auto; }
        .qr-img { display: block; margin: 20px auto; width: 200px; }
    </style>
</head>
<body>
    <div class="title">Reporte de zonas seguras</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Radio (m)</th>
                <th>Tipo de Seguridad</th>
                <th>Coordenadas</th>
                <th>Mapa</th>
            </tr>
        </thead>
        <tbody>
            @foreach($zonas as $index => $zona)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $zona->nombre }}</td>
                    <td>{{ $zona->radio }}</td>
                    <td>{{ $zona->tipoSeguridad }}</td>
                    <td>{{ $zona->latitud }}, {{ $zona->longitud }}</td>
                    <td>
                        @if ($zona->mapa_base64)
                            <img src="{{ $zona->mapa_base64 }}" alt="Mapa" />
                        @else
                            <span>No disponible</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="text-align: center; margin 20px o;">
            qr identificador para zonas seguras
        @if ($qrBase64)
            <img src="{{ $qrBase64 }}" alt="Código QR" class="qr-img" />
        @else
            <p style="text-align: center;">[QR no disponible]</p>
        @endif

    </div>
    
</body>
</html>
