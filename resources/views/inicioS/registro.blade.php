@extends('layout.app')
@section('contenido')

<div class="container mt-4">
    <center>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow border rounded-3">
                <div class="card-header bg-primary">
                    <h3 class="mb-0 text-white">Regístrate</h3>
                </div>
                <div class="card-body text-left">
                    <form action="{{ route('inicioS.store') }}" method="POST" enctype="multipart/form-data" id="frmInicioS">
                        @csrf
                            <div class="mb-3">
                                <label for="username" class="form-label"><strong>Nombre de usuario:</strong></label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Ingrese el nombre de usuario">
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label"><b>Correo electrónico:</b></label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Ingrese su email">
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label"><b>Contraseña:</b></label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Ingrese su contraseña">
                            </div>
                            @if(session('tipo') == 'Administrador')
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Rol:</b></label>
                                    <select name="tipo" id="tipo" class="form-control">
                                        <option value="Visitante" selected>Visitante</option>
                                        <option value="Administrador">Administrador</option>
                                    </select>
                                </div>
                            @endif
                        <div class="text-center">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-success w-100">Crear</button>
                            </div>
                            <div class="mb-3">
                                @if(session('tipo') == 'Administrador')
                                    <a href="{{ route('inicioS.lista') }}" class="btn btn-secondary w-100">Cancelar</a>
                                @else
                                    <a href="{{ route('inicioS.index') }}" class="btn btn-secondary w-100">Cancelar</a>
                                @endif
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
            email:{
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
            email:{
                required: "Por favor ingrese su email.",
                email: "Por favor ingrese un email válido.",
            },
            password:{
                required: "Por favor ingrese su contraseña."
            }
        }
    });
</script>

@endsection