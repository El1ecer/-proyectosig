<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Puntos de Encuentro</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
            vertical-align: top; /* Asegura que el contenido se alinee arriba */
        }
        th {
            background-color: #f2f2f2;
        }
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 25px;
            color: #333;
        }
        .map-img {
            max-width: 250px; /* Tamaño máximo para la imagen del mapa */
            height: auto;
            display: block; /* Para centrar si es necesario, y evitar espacio extra */
            margin: 0 auto;
        }
        .qr-container {
            text-align: center;
            margin-top: 40px; /* Margen para separarlo de la tabla */
            padding-top: 20px;
            border-top: 1px solid #eee; /* Línea divisoria opcional */
        }
        .qr-img-general {
            width: 150px; /* Tamaño fijo para el QR general */
            height: 150px;
            display: block;
            margin: 10px auto; /* Centra el QR */
        }
        .footer-text {
            text-align: center;
            font-size: 10px;
            color: #777;
            margin-top: 20px;
        }
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
                {{-- La columna del QR individual se ha quitado de la tabla, ya que el QR es general --}}
            </tr>
        </thead>
        <tbody>
            {{-- Iteramos sobre $zonasConMapas, que contiene cada zona y su mapa --}}
            @foreach($zonasConMapas as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item['zona']->nombre }}</td>
                <td>
                    {{-- Lógica para mostrar el rango de capacidad --}}
                    @if($item['zona']->capacidad == 100)
                        1 - 100 Personas
                    @elseif($item['zona']->capacidad == 500)
                        101 - 500 Personas
                    @elseif($item['zona']->capacidad == 1000)
                        501 - 1000 Personas
                    @else
                        {{ $item['zona']->capacidad }} Personas
                    @endif
                </td>
                <td>{{ $item['zona']->responsable }}</td>
                <td>{{ $item['zona']->latitud }}, {{ $item['zona']->longitud }}</td>
                <td>
                    {{-- Mostramos la imagen del mapa si está disponible --}}
                    @if($item['mapa_base64'])
                        <img src="{{ $item['mapa_base64'] }}" class="map-img" alt="Mapa de {{ $item['zona']->nombre }}" />
                    @else
                        No disponible
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Contenedor para el QR General, ubicado al final del reporte --}}
    <div class="qr-container">
        @if(isset($qrBase64General) && $qrBase64General)
            <p>Escanea este código QR para acceder a la lista completa de puntos de encuentro:</p>
            <img src="{{ $qrBase64General }}" class="qr-img-general" alt="Código QR del Reporte General" />
        @else
            <p>Código QR general no disponible.</p>
        @endif
        <p class="footer-text">Reporte generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>