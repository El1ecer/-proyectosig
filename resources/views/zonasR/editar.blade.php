@extends('layout.app')
@section('contenido')
<div class="container mt-4">
    <div class="card shadow border rounded-3">
        <div class="card-header bg-primary">
            <h3 class="mb-0 text-white">Editar la zona de riesgo</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('zonasR.update', $zona->id) }}" method="POST" enctype="multipart/form-data" id="frmZonaR">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 d-flex flex-column justify-content-between">
                        <div class="mb-3">
                            <label for="nombre" class="form-label"><strong>Nombre de la zona:</strong></label>
                            <input type="text" class="form-control" value="{{ $zona->nombre }}" name="nombre" id="nombre" placeholder="Ingrese el nombre de la zona a registrar">
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label"><strong>Descripción:</strong></label>
                            <textarea class="form-control" name="descripcion" id="descripcion" placeholder="De una breve descripción de la zona" rows="4">{{ $zona->descripcion }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="nivelRiesgo" class="form-label"><strong>Nivel de riesgo:</strong></label>
                            <select class="custom-select" name="nivelRiesgo" id="nivelRiesgo">
                                <option value="Bajo" {{ $zona->nivelRiesgo == 'Bajo' ? 'selected' : '' }}>Bajo</option>
                                <option value="Medio" {{ $zona->nivelRiesgo == 'Medio' ? 'selected' : '' }}>Medio</option>
                                <option value="Alto" {{ $zona->nivelRiesgo == 'Alto' ? 'selected' : '' }}>Alto</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4 h-100">
                            <label class="form-label"><strong>Seleccione la ubicación en el mapa:</strong></label>
                            <div id="mapaZona" style="border:1px solid #ccc; height:100%; width:100%; border-radius: 8px;"></div>
                        </div>
                    </div>
                </div><br><br>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="latitud" class="form-label"><b>Coord 1:</b></label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitud" class="form-label"><strong>Latitud:</strong></label>
                                    <input type="text" value="{{ $zona->latitud1 }}" class="form-control" name="latitud1" id="latitud1" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label"><strong>Longitud:</strong></label>
                                    <input type="text" value="{{ $zona->longitud1 }}" class="form-control" name="longitud1" id="longitud1" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="latitud" class="form-label"><b>Coord 2:</b></label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitud" class="form-label"><strong>Latitud:</strong></label>
                                    <input type="text" value="{{ $zona->latitud2 }}" class="form-control" name="latitud2" id="latitud2" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label"><strong>Longitud:</strong></label>
                                    <input type="text" value="{{ $zona->longitud2 }}" class="form-control" name="longitud2" id="longitud2" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="latitud" class="form-label"><b>Coord 3:</b></label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitud" class="form-label"><strong>Latitud:</strong></label>
                                    <input type="text" value="{{ $zona->latitud3 }}" class="form-control" name="latitud3" id="latitud3" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label"><strong>Longitud:</strong></label>
                                    <input type="text" value="{{ $zona->longitud3 }}" class="form-control" name="longitud3" id="longitud3" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="latitud" class="form-label"><b>Coord 4:</b></label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitud" class="form-label"><strong>Latitud:</strong></label>
                                    <input type="text" value="{{ $zona->latitud4 }}" class="form-control" name="latitud4" id="latitud4" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label"><strong>Longitud:</strong></label>
                                    <input type="text" value="{{ $zona->longitud4 }}" class="form-control" name="longitud4" id="longitud4" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="latitud" class="form-label"><b>Coord 5:</b></label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitud" class="form-label"><strong>Latitud:</strong></label>
                                    <input type="text" value="{{ $zona->latitud5 }}" class="form-control" name="latitud5" id="latitud5" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label"><strong>Longitud:</strong></label>
                                    <input type="text" value="{{ $zona->longitud5 }}" class="form-control" name="longitud5" id="longitud5" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex text-end">
                    <button type="submit" class="btn btn-success">Guardar</button> &nbsp;&nbsp;&nbsp;
                    <a href="{{ route('zonasR.index')}}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const coordenadasZona = [
        { lat: parseFloat("{{ $zona->latitud1 }}"), lng: parseFloat("{{ $zona->longitud1 }}") },
        { lat: parseFloat("{{ $zona->latitud2 }}"), lng: parseFloat("{{ $zona->longitud2 }}") },
        { lat: parseFloat("{{ $zona->latitud3 }}"), lng: parseFloat("{{ $zona->longitud3 }}") },
        { lat: parseFloat("{{ $zona->latitud4 }}"), lng: parseFloat("{{ $zona->longitud4 }}") },
        { lat: parseFloat("{{ $zona->latitud5 }}"), lng: parseFloat("{{ $zona->longitud5 }}") },
    ];
</script>

<script>
    
    let mapa, mapaPoligono;
    let marcadores = [];
    let poligono;

    function initMap() {
        const centro = coordenadasZona[0];
        mapaPoligono = new google.maps.Map(document.getElementById('mapaZona'), {
            zoom: 15,
            center: centro,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        coordenadasZona.forEach((coord, i) => {
            const marcador = new google.maps.Marker({
                position: coord,
                map: mapaPoligono,
                draggable: true,
                title: `Punto ${i + 1}`
            });

            marcador.addListener('dragend', function() {
                const pos = this.getPosition();
                document.getElementById(`latitud${i + 1}`).value = pos.lat();
                document.getElementById(`longitud${i + 1}`).value = pos.lng();
                dibujarPoligono();
            });

            marcadores.push(marcador);
        });

        dibujarPoligono();
    }

    function dibujarPoligono() {
        if (poligono) poligono.setMap(null);

        const coordenadas = marcadores.map(m => m.getPosition());

        const riesgo = document.getElementById('nivelRiesgo').value;
        let color = "#00FF00"; 

        if (riesgo === "Alto") color = "#FF0000";
        else if (riesgo === "Medio") color = "#FFFF00";

        poligono = new google.maps.Polygon({
            paths: coordenadas,
            strokeColor: "#000000",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: color,
            fillOpacity: 0.35,
        });

        poligono.setMap(mapaPoligono);
    }


    document.getElementById('nivelRiesgo').addEventListener('change', function () {
        dibujarPoligono();
    });
</script>

<script>
    $("#frmZonaR").validate({
        rules:{
            nombre:{
                required: true,
                minlength: 3,
                maxlength: 25
            },
            descripcion:{
                required: true,
                minlength: 3,
                maxlength: 200
            },
            nivelRiesgo:{
                required: true
            },
            latitud1:{
                required: true
            },
            longitud1:{
                required: true
            },
            latitud2:{
                required: true
            },
            longitud2:{
                required: true
            },
            latitud3:{
                required: true
            },
            longitud3:{
                required: true
            },
            latitud4:{
                required: true
            },
            longitud4:{
                required: true
            },
            latitud5:{
                required: true
            },
            longitud5:{
                required: true
            }
        },
        messages:{
            nombre:{
                required: "Por favor ingrese un nombre para la zona de riesgo.",
                minlength: "No puede tener menos de 3 caracteres.",
                maxlength: "No puede tener más de 25 caracteres."
            },
            descripcion:{
                required: "Por favor ingrese una descripción para la zona de riesgo.",
                minlength: "No puede tener menos de 3 caracteres.",
                maxlength: "No puede tener más de 200 caracteres."
            },
            nivelRiesgo:{
                required: "Por favor seleccione un nivel de peligro."
            },
            latitud1:{
                required: "Por favor ingrese una latitud para la zona de riesgo."
            },
            longitud1:{
                required: "Por favor ingrese una longitud para la zona de riesgo."
            },
            latitud2:{
                required: "Por favor ingrese una latitud para la zona de riesgo."
            },
            longitud2:{
                required: "Por favor ingrese una longitud para la zona de riesgo."
            },
            latitud3:{
                required: "Por favor ingrese una latitud para la zona de riesgo."
            },
            longitud3:{
                required: "Por favor ingrese una longitud para la zona de riesgo."
            },
            latitud4:{
                required: "Por favor ingrese una latitud para la zona de riesgo."
            },
            longitud4:{
                required: "Por favor ingrese una longitud para la zona de riesgo."
            },
            latitud5:{
                required: "Por favor ingrese una latitud para la zona de riesgo."
            },
            longitud5:{
                required: "Por favor ingrese una longitud para la zona de riesgo."
            }
        }
    });
</script>

@endsection