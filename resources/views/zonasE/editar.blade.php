@extends('layout.app')
@section('contenido')
<div class="container mt-4">
    <div class="card shadow border rounded-3">
        <div class="card-header bg-primary">
            <h3 class="mb-0 text-white">Editar el punto de encuentro</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('zonasE.update', $zona->id) }}" method="POST" enctype="multipart/form-data" id="frmZonaE">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 d-flex flex-column justify-content-between">
                        <div class="mb-3">
                            <label for="nombre" class="form-label"><strong>Nombre del punto de encuentro:</strong></label>
                            <input type="text" class="form-control" value="{{ $zona->nombre }}" name="nombre" id="nombre" placeholder="Ingrese el nombre del punto de encuentro a registrar.">
                        </div>

                        <div class="mb-3">
                            <label for="capacidad" class="form-label"><strong>Capacidad:</strong></label>
                            <input type="number" class="form-control" value="{{ $zona->capacidad }}" name="capacidad" id="capacidad" placeholder="Ingrese la capacidad del punto de encuentro.">
                        </div>

                        <div class="mb-3">
                            <label for="responsable" class="form-label"><strong>Responsable:</strong></label>
                            <input type="text" class="form-control" value="{{ $zona->responsable }}" name="responsable" id="responsable" placeholder="Ingrese el nombre del responsable.">
                        </div>

                        <div class="mb-3">
                            <label for="latitud" class="form-label"><b>Coordenadas:</b></label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitud" class="form-label"><strong>Latitud:</strong></label>
                                    <input type="text" class="form-control" value="{{ $zona->latitud }}" name="latitud" id="latitud" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label"><strong>Longitud:</strong></label>
                                    <input type="text" class="form-control" value="{{ $zona->longitud }}" name="longitud" id="longitud" readonly>
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
                    <a href="{{ route('zonasE.index')}}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    let mapa, marcador, circulo;

    function initMap() {
        const centro = { lat: parseFloat("{{ $zona->latitud }}"), lng: parseFloat("{{ $zona->longitud }}") };

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
            title: "Punto de encuentro"
        });

        // Inicializa coordenadas en los inputs
        document.getElementById('latitud').value = centro.lat;
        document.getElementById('longitud').value = centro.lng;

        // Evento al mover el marcador
        marcador.addListener('dragend', function () {
            const pos = this.getPosition();
            document.getElementById('latitud').value = pos.lat();
            document.getElementById('longitud').value = pos.lng();
        });
    }

</script>


<script>
    $("#frmZonaE").validate({
        rules:{
            nombre:{
                required: true,
                minlength: 3,
                maxlength: 25
            },
            capacidad:{
                required: true,
                min: 50,
                max: 1000
            },
            responsable:{
                required: true,
                minlength: 3,
                maxlength: 100
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
                required: "Por favor ingrese un nombre para el punto de encuentro.",
                minlength: "No puede tener menos de 3 caracteres.",
                maxlength: "No puede tener más de 25 caracteres."
            },
            capacidad:{
                required: "Por favor ingrese la capacidad del punto de encuentro.",
                min: "Ningun punto de encuentro tiene menos capacidad de 50 personas.",
                max: "Ningun punto de encuentro tiene mas de 1000 personas de capacidad.",
            },
            responsable:{
                required: "Por favor ingrese el nombre del responsable.",
                minlength: "No puede tener menos de 3 caracteres.",
                maxlength: "No puede tener más de 100 caracteres."
            },
            latitud:{
                required: "Por favor ingrese una latitud para el punto de encuentro."
            },
            longitud:{
                required: "Por favor ingrese una longitud para el punto de encuentro."
            }
        }
    });
</script>

@endsection