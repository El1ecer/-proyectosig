@extends('layout.app')

@section('contenido')
<br>
<div class="container-fluid text-right">
    <div class="py-3 py-lg-0 px-lg-5">
        <a href="#" class="btn btn-danger" id="btn-exportar-pdf">
            <i class="fa fa-file-pdf"></i> Exportar PDF
        </a>
        <a href="{{ route('zonasR.create') }}" class="btn btn-danger">Nueva zona de riesgo</a>
    </div>
</div>

<div class="container-fluid mt-4 overflow-hidden">
    <div class="py-3 py-lg-0 px-lg-5">
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
                        <td>{{ str($zona->descripcion)->limit(50) }}</td>
                        <td>{{ $zona->nivelRiesgo }}</td>
                        @for ($i = 1; $i <= 5; $i++)
                            <td>
                                <div>
                                    <h5 style="color: #2878EB">Latitud:</h5> {{ $zona["latitud$i"] }}
                                    <h5 style="color: #2878EB">Longitud:</h5> {{ $zona["longitud$i"] }}
                                </div>
                            </td>
                        @endfor
                        <td class="text-center">
                            <a href="{{ route('zonasR.edit', $zona->id) }}" class="btn btn-sm btn-warning btn-pencil" title="Editar">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form class="form-eliminar d-inline" action="{{ route('zonasR.destroy', $zona->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted">No hay zonas de riesgo registradas.</p>
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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- PDFMake -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

<script>
    // Inicializar DataTable
    $(document).ready(function () {
        $('#tblZonaR').DataTable({
            responsive: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            }
        });

        // Confirmación de eliminación con SweetAlert
        document.querySelectorAll('.form-eliminar').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
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
    });

    // Función para obtener imágenes como base64
    function getBase64ImageFromURL(url) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.crossOrigin = "anonymous";
            img.onload = function () {
                const canvas = document.createElement("canvas");
                canvas.width = this.width;
                canvas.height = this.height;
                const ctx = canvas.getContext("2d");
                ctx.drawImage(this, 0, 0);
                resolve(canvas.toDataURL("image/png"));
            };
            img.onerror = function () {
                reject("No se pudo cargar la imagen");
            };
            img.src = url;
        });
    }

    // Función exportar PDF
    document.getElementById("btn-exportar-pdf").addEventListener("click", async function () {
        const rows = Array.from(document.querySelectorAll("#tblZonaR tbody tr"));
        const qrUrl = 'https://quickchart.io/qr?text={{ urlencode(url('/mapaR')) }}&size=150';
        const qrImage = await getBase64ImageFromURL(qrUrl).catch(() => null);

        const content = [];

        if (qrImage) {
            content.push(
                { text: 'Código QR para consulta completa', style: 'subheader', alignment: 'center' },
                { image: qrImage, width: 100, alignment: 'center' },
                { text: '\n' }
            );
        }

        const body = [
            [
                { text: '#', style: 'tableHeader' },
                { text: 'Nombre', style: 'tableHeader' },
                { text: 'Descripción', style: 'tableHeader' },
                { text: 'Nivel de riesgo', style: 'tableHeader' },
                { text: 'Mapa', style: 'tableHeader' }
            ]
        ];

        const MAPBOX_TOKEN = 'pk.eyJ1IjoidmludGFpbHN6IiwiYSI6ImNtY3MzajdkMTB0MngyanEyc2o5bjAwOHEifQ.FkEeSTHc8LB9ws0_jaQ6FA';

        for (const row of rows) {
            const cells = row.querySelectorAll("td");
            const nombre = cells[1].innerText.trim();
            const descripcion = cells[2].innerText.trim();
            const riesgo = cells[3].innerText.trim();

            const coords = [];
            for (let i = 4; i <= 8; i++) {
                const lat = parseFloat(cells[i].querySelectorAll("div")[1]?.innerText);
                const lng = parseFloat(cells[i].querySelectorAll("div")[3]?.innerText);
                if (!isNaN(lat) && !isNaN(lng)) {
                    coords.push({ lat, lng });
                }
            }

            const centerLat = (coords.reduce((s, c) => s + c.lat, 0) / coords.length).toFixed(5);
            const centerLng = (coords.reduce((s, c) => s + c.lng, 0) / coords.length).toFixed(5);
            const markers = coords.map((c, i) => `pin-s-${String.fromCharCode(97 + i)}+f00(${c.lng},${c.lat})`).join(",");
            const mapUrl = `https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/${markers}/${centerLng},${centerLat},15/400x200?access_token=${MAPBOX_TOKEN}`;
            const mapImage = await getBase64ImageFromURL(mapUrl).catch(() => null);

            body.push([
                cells[0].innerText.trim(),
                nombre,
                descripcion,
                riesgo,
                mapImage ? { image: mapImage, width: 180, height: 90 } : 'No disponible'
            ]);
        }

        const docDefinition = {
            content: [
                { text: 'Reporte de Zonas de Riesgo', style: 'header', alignment: 'center', margin: [0, 0, 0, 10] },
                ...content,
                {
                    table: {
                        widths: ['auto', 'auto', '*', 'auto', 200],
                        body: body
                    }
                }
            ],
            styles: {
                header: { fontSize: 18, bold: true },
                subheader: { fontSize: 14, bold: true },
                tableHeader: { bold: true, fillColor: '#eeeeee' }
            }
        };

        pdfMake.createPdf(docDefinition).download('zonas_riesgo.pdf');
    });
</script>
@endsection
