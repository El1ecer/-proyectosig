@extends('layout.app')
@section('contenido')

<h1 class="mt-4 text-center">Ubicación de zonas de riesgo</h1>

<div class="container mb-3">
    <label for="nivelRiesgo" class="form-label"><strong>Nivel de riesgo:</strong></label>
    <select class="custom-select" name="nivelRiesgo" id="nivelRiesgo">
        <option value="Todos" selected>Todos</option>
        <option value="Bajo">Bajo</option>
        <option value="Medio">Medio</option>
        <option value="Alto">Alto</option>
    </select>
</div>

<div class="container mt-4">
    <div id="mapaR" style="border:1px solid #ccc; height:500px; width:100%; border-radius: 8px;"></div>
</div>

<script>
    let poligonos = [];
    let mapa;

    function initMap() {
        const centroEcuador = { lat: -0.932537, lng: -78.624448 };
        mapa = new google.maps.Map(document.getElementById('mapaR'), {
            zoom: 10,
            center: centroEcuador,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        @foreach($zonas as $zona)
            const coordenadas_{{ $zona->id }} = [
                { lat: {{ $zona->latitud1 }}, lng: {{ $zona->longitud1 }} },
                { lat: {{ $zona->latitud2 }}, lng: {{ $zona->longitud2 }} },
                { lat: {{ $zona->latitud3 }}, lng: {{ $zona->longitud3 }} },
                { lat: {{ $zona->latitud4 }}, lng: {{ $zona->longitud4 }} },
                { lat: {{ $zona->latitud5 }}, lng: {{ $zona->longitud5 }} },
            ];

            let color_{{ $zona->id }} = "#0476D9"; // Bajo
            @if($zona->nivelRiesgo === "Alto")
                color_{{ $zona->id }} = "#FF0000";
            @elseif($zona->nivelRiesgo === "Medio")
                color_{{ $zona->id }} = "#FFFF00";
            @endif

            const poligono_{{ $zona->id }} = new google.maps.Polygon({
                paths: coordenadas_{{ $zona->id }},
                strokeColor: "#000000",
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: color_{{ $zona->id }},
                fillOpacity: 0.35,
                map: mapa,
                clickable: true
            });

            // Guardar en arreglo global
            poligonos.push({
                poligono: poligono_{{ $zona->id }},
                riesgo: "{{ $zona->nivelRiesgo }}"
            });

            const infoWindow_{{ $zona->id }} = new google.maps.InfoWindow({
                content: `<strong>Nombre:</strong> {{ $zona->nombre }}
                <br>
                <strong>Nivel de riesgo:</strong> {{ $zona->nivelRiesgo }}`,
                
            });

            google.maps.event.addListener(poligono_{{ $zona->id }}, 'click', function (event) {
                infoWindow_{{ $zona->id }}.setPosition(event.latLng);
                infoWindow_{{ $zona->id }}.open(mapa);
            });
        @endforeach
        function filtrarPorRiesgo() {
            const valorSeleccionado = document.getElementById("nivelRiesgo").value;

            poligonos.forEach(obj => {
                if (valorSeleccionado === "Todos" || obj.riesgo === valorSeleccionado) {
                    obj.poligono.setMap(mapa);
                } else {
                    obj.poligono.setMap(null);
                }
            });
        }
        document.getElementById("nivelRiesgo").addEventListener("change", filtrarPorRiesgo);
    }
</script>
@endsection