@extends('layout.app')

@section('contenido')
<br>
<div class="container-fluid text-right">
    <div class="py-3 py-lg-0 px-lg-5">
        <a href="{{ route('zonasE.reporte') }}" class="btn btn-primary" target="_blank">
            <i class="fa fa-file-pdf"></i> Exportar PDF
        </a>
        <a href="{{ route('zonasE.create') }}" class="btn btn-primary">Nuevo punto de encuentro</a>
    </div>
</div>

<div class="container-fluid mt-4 overflow-hidden">
    <div class="py-3 py-lg-0 px-lg-5">
        @if($zonas->isNotEmpty())
        <div class="table-responsive">
            <table class="table table-hover shadow rounded-2 w-100" id="tblZonaE">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Capacidad</th>
                        <th>Responsable</th>
                        <th>Coordenadas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-bordered">
                    @foreach($zonas as $index => $zona)
                    <tr>
                        <td>{{ $index + 1 }}</td>   
                        <td>{{ $zona->nombre }}</td>
                        <td>
                            @if($zona->capacidad == 100)
                                {{ "1 - $zona->capacidad" }}
                            @elseif($zona->capacidad == 500)
                                {{ "101 - $zona->capacidad" }}
                            @else
                                {{ "501 - $zona->capacidad" }}
                            @endif
                        </td>
                        <td>{{ $zona->responsable }}</td>
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
                                <a href="{{ route('zonasE.edit', $zona->id) }}" class="btn btn-sm btn-warning btn-pencil" title="Editar">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                            </div><br>
                            <div>
                                <form class="form-eliminar d-inline" action="{{ route('zonasE.destroy', $zona->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p class="text-muted">No hay puntos de encuentro.</p>
        @endif
    </div>
</div>
<br>
@endsection

@section('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- DataTables y Bootstrap 5 -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- SweetAlert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        $('#tblZonaE').DataTable({
            responsive: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });

        // SweetAlert para confirmación de eliminación
        document.querySelectorAll('.form-eliminar').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const zonaNombre = this.closest('tr')?.querySelector('td:nth-child(2)')?.innerText ?? 'este punto de encuentro';

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
    });
</script>
@endsection
