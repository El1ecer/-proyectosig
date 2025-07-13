@extends('layout.app')
@section('contenido')

<div class="container mt-4">
    <center>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card shadow border rounded-3">
                <div class="card-header bg-primary">
                    <h3 class="mb-0 text-white">Edita al usuario</h3>
                </div>
                <div class="card-body text-left">
                    <form action="{{ route('inicioS.update', $usuario->id) }}" method="POST" enctype="multipart/form-data" id="frmInicioS">
                        @csrf
                        @method('PUT')
                            <div class="mb-3">
                                <label for="username" class="form-label"><strong>Nombre de usuario:</strong></label>
                                <input type="text" class="form-control" value="{{ $usuario->username }}" name="username" id="username" placeholder="Ingrese el nombre de usuario">
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label"><b>Correo electrónico:</b></label>
                                <input type="email" class="form-control" value="{{ $usuario->email }}" name="email" id="email" placeholder="Ingrese su email">
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label"><b>Contraseña:</b></label>
                                <input type="text" class="form-control" value="{{ $usuario->password }}" name="password" id="password" placeholder="Ingrese su contraseña">
                            </div>

                            @if(session('tipo') == 'Administrador')
                                <div class="mb-3">
                                    <label for="" class="form-label"><b>Rol:</b></label>
                                    <select name="tipo" id="tipo" class="form-control">
                                        <option value="Visitante" {{ $usuario->tipo == 'Visitante' ? 'selected' : '' }}>Visitante</option>
                                        <option value="Administrador" {{ $usuario->tipo == 'Administrador' ? 'selected' : '' }}>Administrador</option>
                                    </select>
                                </div>
                            @endif

                        <div class="text-center">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-success w-100">Actualizar</button>
                            </div>
                            <div class="mb-3">
                                <a href="{{ route('inicioS.lista') }}" class="btn btn-secondary w-100">Cancelar</a>
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