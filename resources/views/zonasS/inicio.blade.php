@extends('layout.app')
@section('contenido')
<br>
<div class="container-fluid text-right">
    <div class="py-3 py-lg-0 px-lg-5">
        <a href="{{ route('zonasS.reporte') }}" class="btn btn-success"><i class="fa fa-file-pdf"></i> Exportar PDF</a>
        <a href="{{ route('zonasS.create') }}" class="btn btn-success">Nueva zona segura</a>
    </div>
</div>
<div class="container-fluid mt-4 overflow-hidden">
    <div class="py-3 py-lg-0 px-lg-5">
        @if($zonas->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-hover shadow rounded-2 w-100" id="tblZonaS">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Radio (metros)</th>
                        <th>Tipo de seguridad</th>
                        <th>Coordenadas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-bordered">
                    @foreach($zonas as $index => $zona)
                    <tr>
                        <td>{{ $index + 1 }}</td>   
                        <td>{{ $zona->nombre }}</td>
                        <td>{{ $zona->radio }}</td>
                        <td>{{ $zona->tipoSeguridad }}</td>
                        <td>
                            <div>
                                <h5 style="color: #2878EB">Latitud:</h5>
                                {{ $zona->latitud }}
                                <h5 style="color: #2878EB">Longitud:</h5>
                                {{ $zona->longitud }}
                            </div>
                        </td>
                        <td class="text-center">
                            <div>
                                <a href="{{ route('zonasS.edit', $zona->id) }}" class="btn btn-sm btn-warning btn-pencil">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div><br>
                            <div>
                                <form class="form-eliminar" action="{{ route('zonasS.destroy', $zona->id) }}" method="POST" class="d-inline">
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
            <p class="text-muted">No hay zonas seguras registradas.</p>
        @endif
    </div>
</div>
<br>

<script>
    document.querySelectorAll('.form-eliminar').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); //Detiene el envio para hacer la confirmacion

            //Se esta sacando el nombre del registro a eliminar, si no lo encuentra lo deja como "esta zona"
            const zonaNombre = this.closest('tr')?.querySelector('td:nth-child(2)')?.innerText ?? 'esta zona';

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