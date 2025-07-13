@extends('layout.app')
@section('contenido')
<br>
<div class="container-fluid text-right">
    <div class="py-3 py-lg-0 px-lg-5">
        <a href="{{ route('inicioS.create') }}" class="btn btn-primary">Nuevo usuario</a>
    </div>
</div>
<div class="container-fluid mt-4 overflow-hidden">
    <div class="py-3 py-lg-0 px-lg-5">
        @if($usuarios->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-hover shadow rounded-2 w-100" id="tblUser">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>#</th>
                        <th>Nombre de usuario</th>
                        <th>Email</th>
                        <th>Contraseña</th>
                        <th>Rol del usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-bordered">
                    @foreach($usuarios as $index => $usuario)
                    <tr>
                        <td>{{ $index + 1 }}</td>   
                        <td>{{ $usuario->username }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>{{ $usuario->password }}</td>
                        <td>{{ $usuario->tipo }}</td>
                        <td class="text-center">
                            <div>
                                <a href="{{ route('inicioS.edit', $usuario->id) }}" class="btn btn-sm btn-warning btn-pencil">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div><br>
                            <div>
                                <form class="form-eliminar" action="{{ route('inicioS.destroy', $usuario->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p class="text-muted">No hay usuarios registrados.</p>
        @endif
    </div>
</div>
<br>

<script>
    document.querySelectorAll('.form-eliminar').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); //Detiene el envio para hacer la confirmacion

            //Se esta sacando el nombre del registro a eliminar, si no lo encuentra lo deja como "esta zona"
            const zonaNombre = this.closest('tr')?.querySelector('td:nth-child(2)')?.innerText ?? 'este usuario';

            Swal.fire({
                title: '¿Estás seguro?',
                text: `Vas a eliminar ${zonaNombre}. Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>

@endsection