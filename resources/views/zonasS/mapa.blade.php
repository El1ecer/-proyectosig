@extends('layout.app')
@section('contenido')

<h1 class="mt-4 text-center">Ubicación de zonas seguras</h1>

<div class="container mb-3">
    <label for="tipoSeguridad" class="form-label"><strong>Nivel de seguridad:</strong></label>
    <select class="custom-select" name="tipoSeguridad" id="tipoSeguridad">
        <option value="Todos" selected>Todos</option>
        <option value="Bajo">Bajo</option>
        <option value="Medio">Medio</option>
        <option value="Alto">Alto</option>
    </select>
</div>

<div class="container mt-4">
    <div id="mapaS" style="border:1px solid #ccc; height:500px; width:100%; border-radius: 8px;"></div>
</div>

<script>
    let mapa;
    let circulos = [];

    function initMap() {
        const centro = { lat: -0.932537, lng: -78.624448 };

        mapa = new google.maps.Map(document.getElementById('mapaS'), {
            zoom: 10,
            center: centro,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        @foreach($zonas as $zona)
            let color_{{ $zona->id }} = "#0476D9"; // Alto
            @if($zona->tipoSeguridad === "Medio")
                color_{{ $zona->id }} = "#FFFF00";
            @elseif($zona->tipoSeguridad === "Bajo")
                color_{{ $zona->id }} = "#FF0000";
            @endif

            const centro_{{ $zona->id }} = { lat: {{ $zona->latitud }}, lng: {{ $zona->longitud }} };

            const circulo_{{ $zona->id }} = new google.maps.Circle({
                strokeColor: "#000000",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: color_{{ $zona->id }},
                fillOpacity: 0.35,
                map: mapa,
                center: centro_{{ $zona->id }},
                radius: {{ $zona->radio }},
                clickable: true
            });

            // InfoWindow para mostrar el nombre
            const infoWindow_{{ $zona->id }} = new google.maps.InfoWindow({
                content: `<b>Nombre:</b> {{ $zona->nombre }}<br>
                <b>Nivel de seguridad:</b> {{ $zona->tipoSeguridad }}<br>`
            });

            google.maps.event.addListener(circulo_{{ $zona->id }}, 'click', function (event) {
                infoWindow_{{ $zona->id }}.setPosition(event.latLng);
                infoWindow_{{ $zona->id }}.open(mapa);
            });

            @php
                $icono = 'pinazul.png';
                if ($zona->tipoSeguridad === 'Bajo') {
                    $icono = 'pinrojo.png';
                } elseif ($zona->tipoSeguridad === 'Medio') {
                    $icono = 'pinamarillo.png';
                }
            @endphp
            let iconoPersonalizado_{{ $zona->id }} = {
                url: "{{ asset('plantilla/pin/' . $icono) }}",
                scaledSize: new google.maps.Size(30, 50) // ← ancho, alto en píxeles
            };

            const marcador_{{ $zona->id }} = new google.maps.Marker({
                position: centro_{{ $zona->id }},
                map: mapa,
                icon: iconoPersonalizado_{{ $zona->id }},
                title: "{{ $zona->nombre }}"
            });

            // Guarda el círculo y el marcador para filtrar después
            circulos.push({
                circulo: circulo_{{ $zona->id }},
                marcador: marcador_{{ $zona->id }},
                seguridad: "{{ $zona->tipoSeguridad }}"
            });
        @endforeach

        // Agrega el filtro cuando se cambie el select
        document.getElementById("tipoSeguridad").addEventListener("change", filtrarZonasSeguras);
    }

    function filtrarZonasSeguras() {
        const seleccion = document.getElementById("tipoSeguridad").value;

        circulos.forEach(obj => {
            if (seleccion === "Todos" || obj.seguridad === seleccion) {
                obj.circulo.setMap(mapa);
                obj.marcador.setMap(mapa);
            } else {
                obj.circulo.setMap(null);
                obj.marcador.setMap(null);
            }
        });
    }
</script>

@endsection