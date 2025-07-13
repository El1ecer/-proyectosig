@extends('layout.app')
@section('contenido')

<div class="container mt-4">
    <center>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow border rounded-3">
                <div class="card-header bg-primary">
                    <h3 class="mb-0 text-white">Inicio de sesión</h3>
                </div>
                <div class="card-body text-left">
                    <form action="{{ route('inicioS.login') }}" method="POST" enctype="multipart/form-data" id="frmInicioS">
                        @csrf
                            <div class="mb-3">
                                <label for="username" class="form-label"><strong>Nombre de usuario:</strong></label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Ingrese el nombre de usuario">
                            </div>

                            <div class="mb-3">
                                <label for="latitud" class="form-label"><b>Contraseña:</b></label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Ingrese su contraseña">
                            </div>
                            <div class="mb-3 text-left">
                                <a href="{{ route('inicioS.create') }}">No tengo cuenta</a>
                            </div>
                        <div class="text-center">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-success w-100">Ingresar</button>
                            </div>
                            <div class="mb-3">
                                <a href="/" class="btn btn-secondary w-100">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </center>
</div>

<script>
    $("#frmInicioS").validate({
        rules:{
            username:{
                required: true,
            },
            password:{
                required: true,
            }
        },
        messages:{
            username:{
                required: "Por favor ingrese su usario."
            },
            password:{
                required: "Por favor ingrese su contraseña."
            }
        }
    });
</script>

@endsection