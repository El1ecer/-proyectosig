@extends('layout.app')
@section('contenido')
<br>
<div class="container-fluid text-end">
    <a href="{{ route('zonasR.create') }}" class="btn btn-danger">Nueva zona de riesgo</a>
</div>
<div class="container-fluid mt-4 overflow-hidden">
    @if($zonas->isNotEmpty())
    <div class="table-responsive">
        <table class="table table-hover shadow rounded-2 w-100" id="tblZonaR">
            <thead class="bg-primary text-white">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Nivel de riesgo</th>
                    <th>Coord 1</th>
                    <th>Coord 2</th>
                    <th>Coord 3</th>
                    <th>Coord 4</th>
                    <th>Coord 5</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="table-bordered">
                @foreach($zonas as $index => $zona)
                <tr>
                    <td>{{ $index + 1 }}</td>   
                    <td>{{ $zona->nombre }}</td>
                    <td>{{ Str::limit($zona->descripcion, 50) }}</td>
                    <td>{{ $zona->nivelRiesgo }}</td>
                    <td>
                        <div>
                            <h5 style="color: #2878EB">Latitud:</h5>
                            {{ $zona->latitud1 }}
                            <h5 style="color: #2878EB">Longitud:</h5>
                            {{ $zona->longitud1 }}
                        </div>
                    </td>
                    <td>
                        <div>
                            <h5 style="color: #2878EB">Latitud:</h5>
                            {{ $zona->latitud2 }}
                            <h5 style="color: #2878EB">Longitud:</h5>
                            {{ $zona->longitud2 }}
                        </div>
                    </td>
                    <td>
                        <div>
                            <h5 style="color: #2878EB">Latitud:</h5>
                            {{ $zona->latitud3 }}
                            <h5 style="color: #2878EB">Longitud:</h5>
                            {{ $zona->longitud3 }}
                        </div>
                    </td>
                    <td>
                        <div>
                            <h5 style="color: #2878EB">Latitud:</h5>
                            {{ $zona->latitud4 }}
                            <h5 style="color: #2878EB">Longitud:</h5>
                            {{ $zona->longitud4 }}
                        </div>
                    </td>
                    <td>
                        <div>
                            <h5 style="color: #2878EB">Latitud:</h5>
                            {{ $zona->latitud5 }}
                            <h5 style="color: #2878EB">Longitud:</h5>
                            {{ $zona->longitud5 }}
                        </div>
                    </td>
                    <td>
                        <a href="{{ route('zonasR.edit', $zona->id) }}" class="btn btn-sm btn-warning btn-pencil">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <form class="form-eliminar" action="{{ route('zonasR.destroy', $zona->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <p class="text-muted">No hay puntos de interés registrados.</p>
    @endif
</div>
<br>

<script>
    document.querySelectorAll('.form-eliminar').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Evita que se envíe el formulario inmediatamente

            // Aquí podrías identificar algo del `target`, por ejemplo, el nombre de la zona
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
                    this.submit(); // Ahora sí se envía el formulario
                }
            });
        });
    });
</script>

@endsection