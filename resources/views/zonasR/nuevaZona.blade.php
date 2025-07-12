@extends('layout.app')
@section('contenido')
<div class="container mt-4">
    <div class="card shadow border rounded-3">
        <div class="card-header bg-primary">
            <h3 class="mb-0 text-white">Nueva zona de riesgo</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('zonasR.store') }}" method="POST" enctype="multipart/form-data" id="frmZonaR">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nombre" class="form-label"><strong>Nombre de la zona:</strong></label>
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ingrese el nombre de la zona a registrar">
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label"><strong>Descripción:</strong></label>
                            <textarea class="form-control" name="descripcion" id="descripcion" placeholder="De una breve descripción de la zona" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="nivelRiesgo" class="form-label"><strong>Nivel de riesgo:</strong></label>
                            <select class="custom-select" name="nivelRiesgo" id="nivelRiesgo">
                                <option value="" disabled selected>Seleccione un nivel</option>
                                <option value="Bajo">Bajo</option>
                                <option value="Medio">Medio</option>
                                <option value="Alto">Alto</option>
                            </select>
                        </div>
                        
                        
                        
                        
                        
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <label class="form-label"><strong>Seleccione la ubicación en el mapa:</strong></label>
                            <div id="mapaZona" style="border:1px solid #ccc; height:300px; width:100%; border-radius: 8px;"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="latitud" class="form-label"><b>Coord 1:</b></label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitud" class="form-label"><strong>Latitud:</strong></label>
                                    <input type="text" class="form-control" name="latitud1" id="latitud1" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label"><strong>Longitud:</strong></label>
                                    <input type="text" class="form-control" name="longitud1" id="longitud1" readonly>
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
                                    <input type="text" class="form-control" name="latitud2" id="latitud2" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label"><strong>Longitud:</strong></label>
                                    <input type="text" class="form-control" name="longitud2" id="longitud2" readonly>
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
                                    <input type="text" class="form-control" name="latitud3" id="latitud3" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label"><strong>Longitud:</strong></label>
                                    <input type="text" class="form-control" name="longitud3" id="longitud3" readonly>
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
                                    <input type="text" class="form-control" name="latitud4" id="latitud4" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label"><strong>Longitud:</strong></label>
                                    <input type="text" class="form-control" name="longitud4" id="longitud4" readonly>
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
                                    <input type="text" class="form-control" name="latitud5" id="latitud5" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label"><strong>Longitud:</strong></label>
                                    <input type="text" class="form-control" name="longitud5" id="longitud5" readonly>
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
    
    let mapa, mapaPoligono;
    let marcadores = [];
    let poligono;

    function initMap() {
        const centro = { lat: -0.9374805, lng: -78.6161327 };
        mapaPoligono = new google.maps.Map(document.getElementById('mapaZona'), {
            zoom: 15,
            center: centro,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        for (let i = 0; i < 5; i++) {
            const marcador = new google.maps.Marker({
                position: centro,
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
        }

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
@endsection