@extends('layout.app')
@section('contenido')

<h1 class="mt-4 text-center">Ubicación de los puntos de encuentro</h1>

<div class="container mb-3">
    <label for="capacidad" class="form-label"><strong>Capacidad:</strong></label>
    <select class="custom-select" name="capacidad" id="capacidad">
        <option value="Todos" selected>Todos</option>
        <option value="100">1 - 100</option>
        <option value="500">101 - 500</option>
        <option value="1000">501 - 1000</option>
    </select>
</div>

<div class="container mt-4">
    <div id="mapaE" style="border:1px solid #ccc; height:500px; width:100%; border-radius: 8px;"></div>
</div>

<script>
    let mapa;
    let marcadores = [];

    function initMap(){
        const centro = { lat: -0.932537, lng: -78.624448 };

        mapa = new google.maps.Map(document.getElementById('mapaE'), {
            zoom: 10,
            center: centro,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        @foreach($zonas as $zona)
            const centro_{{ $zona->id }} = { lat: {{ $zona->latitud }}, lng: {{ $zona->longitud }} };

            @php
                if($zona->capacidad == 100){
                    $capacidad = "1 - $zona->capacidad";
                }
                elseif($zona->capacidad == 500){
                    $capacidad = "101 - $zona->capacidad";
                }
                else{
                    $capacidad = "501 - $zona->capacidad";
                }
                $icono = 'pin100.png';
                if ($zona->capacidad === 500) {
                    $icono = 'pin500.png';
                } elseif ($zona->capacidad === 1000) {
                    $icono = 'pin1000.png';
                }
            @endphp
            const infoWindow_{{ $zona->id }} = new google.maps.InfoWindow({
                content: `<b>Nombre:</b> {{ $zona->nombre }}<br>
                <b>Capacidad:</b> {{ $capacidad }}<br>
                <b>Responsable:</b> {{ $zona->responsable }}`
            });

            let iconoPersonalizado_{{ $zona->id }} = {
                url: "{{ asset('plantilla/pin/' . $icono) }}",
                scaledSize: new google.maps.Size(40, 40) // ← ancho, alto en píxeles
            };

            const marcador_{{ $zona->id }} = new google.maps.Marker({
                position: centro_{{ $zona->id }},
                map: mapa,
                icon: iconoPersonalizado_{{ $zona->id }},
                title: "{{ $zona->nombre }}"
            });

            google.maps.event.addListener(marcador_{{ $zona->id }}, 'click', function (event) {
                infoWindow_{{ $zona->id }}.setPosition(event.latLng);
                infoWindow_{{ $zona->id }}.open(mapa);
            });

            marcadores.push({
                marcador: marcador_{{ $zona->id }},
                capacidad: "{{ $zona->capacidad }}"
            });

        @endforeach

        document.getElementById("capacidad").addEventListener("change", filtrarCapacidad);
    }

    function filtrarCapacidad() {
        const seleccion = document.getElementById("capacidad").value;

        marcadores.forEach(obj => {
            if (seleccion === "Todos" || obj.capacidad === seleccion) {
                obj.marcador.setMap(mapa);
            } else {
                obj.marcador.setMap(null);
            }
        });
    }
</script>

@endsection