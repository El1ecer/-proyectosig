<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Proyecto JJJ</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Free HTML Templates" name="keywords">
    <meta content="Free HTML Templates" name="description">

    <link href="{{ asset('plantilla/img/favicon.ico') }}" rel="icon">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="{{ asset('plantilla/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

    <link href="{{ asset('plantilla/css/style.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.22.2/sweetalert2.min.css" integrity="sha512-bkb9OVJFbnXaSi8PvT9arbq1WSE9QCDkLse1RqPlhnRqmH16CgmL9HAd0W99NYfVIp66gZb4k+L1jOr+JuT8Og==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    {{-- Si no usas Bootstrap 5, puedes usar esta línea en su lugar: --}}
    {{-- <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"> --}}


    <style>
        .error{
            color: red;
            font-weight: bold;
        }
        .form-control.error{
            border: 1px solid red;
        }
        .custom-select.error{
            border: 1px solid red;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js" integrity="sha512-KFHXdr2oObHKI9w4Hv1XPKc898mE4kgYx58oqsc/JqqdLMDI4YjLHZq+EMlW8HFUd0QfjfAvxSL6sEq/a42fQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.22.2/sweetalert2.all.min.js" integrity="sha512-rBcqrtFFt2PxFGp3ffb/lECz3pYr2DoF1FWmnMLy6qVdAOnaQg2C4wK84m64K36aK0qxkImFrlb/AKgOoeTvSg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

</head>


<body>
    <div class="container-fluid p-0">
        <nav class="navbar navbar-expand-lg bg-white navbar-light py-3 py-lg-0 px-lg-5">
            <a href="/" class="navbar-brand ml-lg-3">
                <h1 class="m-0 text-uppercase text-primary"><i class="fa fa-book-reader mr-3"></i>Zonas de seguridad</h1>
            </a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between px-lg-3" id="navbarCollapse">
                <div class="navbar-nav mx-auto py-0">
                    @if(session('tipo') == 'Administrador')
                        <a href="/" class="nav-item nav-link">Inicio</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Zonas de riesgo</a>
                            <div class="dropdown-menu m-0">
                                <a href="{{ route('zonasR.index') }}" class="dropdown-item">Administración</a>
                                <a href="{{ route('zonasR.mapa') }}" class="dropdown-item">Mapa</a>
                            </div>
                        </div>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Zonas seguras</a>
                            <div class="dropdown-menu m-0">
                                <a href="{{ route('zonasS.index') }}" class="dropdown-item">Administración</a>
                                <a href="{{ route('zonasS.mapa') }}" class="dropdown-item">Mapa</a>
                            </div>
                        </div>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Puntos de encuentro</a>
                            <div class="dropdown-menu m-0">
                                <a href="{{ route('zonasE.index') }}" class="dropdown-item">Administración</a>
                                <a href="{{ route('zonasE.mapa') }}" class="dropdown-item">Mapa</a>
                            </div>
                        </div>
                        <a href="{{ route('inicioS.lista') }}" class="nav-item nav-link">Usuarios</a>
                    @else
                        <a href="/" class="nav-item nav-link">Inicio</a>
                        <a href="{{ route('zonasR.mapa') }}" class="nav-item nav-link">Zonas de riesgo</a>
                        <a href="{{ route('zonasS.mapa') }}" class="nav-item nav-link">Zonas seguras</a>
                        <a href="{{ route('zonasE.mapa') }}" class="nav-item nav-link">Puntos de encuentro</a>
                    @endif
                </div>
                @if(session('tipo') == 'Invitado')
                    <a href="{{ route('inicioS.index') }}" class="btn btn-primary py-2 px-4 d-lg-block w-lg-auto">Inicia sesión</a>
                @else
                    <a href="{{ route('inicioS.logout') }}" class="btn btn-primary py-2 px-4 d-lg-block w-lg-auto">Cerrar sesión</a>
                @endif
            </div>
        </nav>
    </div>
    @yield('contenido')

    @if(session('mensaje'))
    <script>
        Swal.fire({
            title: '¡Éxito!',
            text: "{{ session('mensaje') }}",
            icon: 'success',
            timer: 3000,
            showConfirmButton: true
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        Swal.fire({
            title: '¡Error!',
            text: "{{ session('error') }}",
            icon: 'warning',
            showConfirmButton: true
        });
    </script>
    @endif

    <div class="container-fluid position-relative overlay-top bg-dark text-white-50 py-5" style="margin-top: 90px;">
        <div class="container mt-5 pt-5">
            <div class="row">
                <div class="col-md-6 mb-5">
                    <a href="index.html" class="navbar-brand">
                        <h1 class="mt-n2 text-uppercase text-white">
                            <i class="fa fa-book-reader mr-3"></i>Proyecto SIG
                        </h1>
                    </a>
                    <p class="m-0">
                        Estudiantes de la Universidad Técnica de Cotopaxi (UTC), 7mo semestre de la carrera de Sistemas de la Información. Participando en la materia Sistemas de Información Geográfica y Gerencia.
                    </p>
                </div>
                <div class="col-md-6 mb-5">
                    <h3 class="text-white mb-4">Contáctanos</h3>
                    <div class="w-100">
                        <div class="input-group">
                            <input type="text" class="form-control border-light" style="padding: 30px;" placeholder="Ingresa tu correo UTC">
                            <div class="input-group-append">
                                <button class="btn btn-primary px-4">Suscribirse</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-5">
                    <h3 class="text-white mb-4">Información de contacto</h3>
                    <p><i class="fa fa-map-marker-alt mr-2"></i>Latacunga, Cotopaxi, Ecuador</p>
                    <p><i class="fa fa-phone-alt mr-2"></i>+593 95 877 5019 (Justin)</p>
                    <p><i class="fa fa-phone-alt mr-2"></i>+593 97 908 3083 (Johan)</p>
                    <p><i class="fa fa-phone-alt mr-2"></i>+593 99 900 4317 (Jorge)</p>
                    <p><i class="fa fa-envelope mr-2"></i>justin.alvarez4882@utc.edu.ec</p>
                    <p><i class="fa fa-envelope mr-2"></i>johan.lozada5793@utc.edu.ec</p>
                    <p><i class="fa fa-envelope mr-2"></i>jorge.medina9739@utc.edu.ec</p>
                </div>
                <div class="col-md-4 mb-5">
                    <h3 class="text-white mb-4">Cosas de interés</h3>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Zonas de Riesgos</a>
                        <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Zonas Seguras</a>
                        <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Puntos de encuentro</a>
                    </div>
                </div>
                <div class="col-md-4 mb-5">
                    <h3 class="text-white mb-4">Enlaces Rápidos</h3>
                    <div class="d-flex flex-column justify-content-start">
                        <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Política de Privacidad</a>
                        <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Términos y Condiciones</a>
                        <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Preguntas Frecuentes</a>
                        <a class="text-white-50 mb-2" href="#"><i class="fa fa-angle-right mr-2"></i>Soporte</a>
                        <a class="text-white-50" href="#"><i class="fa fa-angle-right mr-2"></i>Contacto</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid bg-dark text-white-50 border-top py-4" style="border-color: rgba(256, 256, 256, .1) !important;">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-left mb-3 mb-md-0">
                    <p class="m-0">&copy; <a class="text-white" href="#">Grupo UTC - SIG</a>. Todos los derechos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-right">
                    <p class="m-0">Diseñado por: Justin Álvarez, Johan Lozada y Jorge Medina</p>
                </div>
            </div>
        </div>
    </div>
    <a href="#" class="btn btn-lg btn-primary rounded-0 btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>


    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('plantilla/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('plantilla/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('plantilla/lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('plantilla/lib/owlcarousel/owl.carousel.min.js') }}"></script>

    <script src="{{ asset('plantilla/js/main.js') }}"></script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAT29MNkb9_YFHbuPU2S67MwWXXlGrpZtk&libraries=places&callback=initMap"></script>

    {{-- Aquí se insertarán los scripts de las vistas individuales como los de DataTables --}}
    @yield('scripts')

</body>

</html>