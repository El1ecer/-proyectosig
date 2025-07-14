@extends('layout.app')
@section('contenido')
<div class="container mt-4">
    <div class="card shadow border rounded-3">
        <div class="card-header bg-primary">
            <h3 class="mb-0 text-white">Nueva zona segura</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('zonasS.store') }}" method="POST" enctype="multipart/form-data" id="frmZonaS">
                @csrf
                <div class="row">
                    <div class="col-md-6 d-flex flex-column justify-content-between">
                        <div class="mb-3">
                            <label for="nombre" class="form-label"><strong>Nombre de la zona:</strong></label>
                            <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Ingrese el nombre de la zona a registrar">
                        </div>

                        <div class="mb-3">
                            <label for="descripcion" class="form-label"><strong>Radio (metros):</strong></label>
                            <input type="number" class="form-control" name="radio" id="radio" placeholder="Ingrese el radio de la zona segura">
                        </div>

                        <div class="mb-3">
                            <label for="tipoSeguridad" class="form-label"><strong>Tipo de seguridad:</strong></label>
                            <select class="custom-select" name="tipoSeguridad" id="tipoSeguridad">
                                <option value="" disabled selected>Seleccione un nivel</option>
                                <option value="Bajo">Bajo</option>
                                <option value="Medio">Medio</option>
                                <option value="Alto">Alto</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="latitud" class="form-label"><b>Coordenadas:</b></label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitud" class="form-label"><strong>Latitud:</strong></label>
                                    <input type="text" class="form-control" name="latitud" id="latitud" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label"><strong>Longitud:</strong></label>
                                    <input type="text" class="form-control" name="longitud" id="longitud" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4 h-100">
                            <label class="form-label"><strong>Seleccione la ubicación en el mapa:</strong></label>
                            <div id="mapaZona" style="border:1px solid #ccc; height:100%; width:100%; border-radius: 8px;"></div>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-success">Guardar</button> &nbsp;&nbsp;&nbsp;
                    <a href="{{ route('zonasS.index')}}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    let mapa, marcador, circulo;

    function initMap() {
        const centro = { lat: -0.9374805, lng: -78.6161327 };

        // Inicializa el mapa
        mapa = new google.maps.Map(document.getElementById('mapaZona'), {
            zoom: 15,
            center: centro,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        // Coloca el marcador central
        marcador = new google.maps.Marker({
            position: centro,
            map: mapa,
            draggable: true,
            title: "Centro de la zona segura"
        });

        // Inicializa coordenadas en los inputs
        document.getElementById('latitud').value = centro.lat;
        document.getElementById('longitud').value = centro.lng;

        // Evento al mover el marcador
        marcador.addListener('dragend', function () {
            const pos = this.getPosition();
            document.getElementById('latitud').value = pos.lat();
            document.getElementById('longitud').value = pos.lng();
            dibujarCirculo();
        });

        // Evento al escribir el radio
        document.getElementById('radio').addEventListener('input', dibujarCirculo);

        dibujarCirculo();
    }

    function dibujarCirculo() {
        // Si ya hay un círculo dibujado, lo quitamos
        if (circulo) {
            circulo.setMap(null);
        }

        const centro = marcador.getPosition();
        const radio = parseFloat(document.getElementById('radio').value) || 0;

        let color = "#0476D9";
        const nivel = document.getElementById('tipoSeguridad').value;
        if (nivel === "Bajo") color = "#FF0000";
        else if (nivel === "Medio") color = "#FFFF00";

        // Dibujar el círculo
        circulo = new google.maps.Circle({
            strokeColor: "#000000",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: color,
            fillOpacity: 0.35,
            map: mapa,
            center: centro,
            radius: radio // en metros
        });
    }

    // Redibujar el círculo si se cambia el tipo de seguridad
    document.getElementById('tipoSeguridad').addEventListener('change', dibujarCirculo);
</script>


<script>
    $("#frmZonaS").validate({
        rules:{
            nombre:{
                required: true,
                minlength: 3,
                maxlength: 25
            },
            radio:{
                required: true
            },
            tipoSeguridad:{
                required: true,
            },
            latitud:{
                required: true
            },
            longitud:{
                required: true
            }
        },
        messages:{
            nombre:{
                required: "Por favor ingrese un nombre para la zona segura.",
                minlength: "No puede tener menos de 3 caracteres.",
                maxlength: "No puede tener más de 25 caracteres."
            },
            radio:{
                required: "Por favor ingrese el radio de la zona segura.",
            },
            tipoSeguridad:{
                required: "Por favor seleccione un nivel de seguridad.",
            },
            latitud:{
                required: "Por favor ingrese una latitud para la zona segura."
            },
            longitud:{
                required: "Por favor ingrese una longitud para la zona segura."
            }
        }
    });
</script>

@endsection