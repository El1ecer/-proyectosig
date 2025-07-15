@extends('layout.app')
@section('contenido')
<!-- Header Start -->
<div class="jumbotron jumbotron-fluid position-relative overlay-bottom" style="margin-bottom: 90px;">
    <div class="container text-center my-5 py-5">
        <h1 class="text-white mt-4 mb-3 fs-3">Sistema de Gestión de Zonas de Seguridad y Puntos de Encuentro</h1>
        <h1 class="text-white fs-5 mb-5">Visualiza, gestiona y consulta zonas de riesgo, zonas seguras y puntos de encuentro en tu comunidad.</h1>
    </div>
</div>
<!-- Header End -->
<!-- About Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-5 mb-5 mb-lg-0" style="min-height: 500px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute w-100 h-100" src="{{ asset('plantilla/img/about.jpg') }}" style="object-fit: cover;">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="section-title position-relative mb-4">
                        <h6 class="d-inline-block position-relative text-secondary text-uppercase pb-2">Informate</h6>
                        <h1 class="display-4">Zonas de Riesgo</h1>
                    </div>
                    <p>
                        Las <strong>zonas de riesgo</strong> son lugares donde pueden ocurrir peligros que afectan la seguridad de las personas, sus viviendas o el medio ambiente. Estos riesgos pueden ser naturales, como <strong>inundaciones, terremotos, deslizamientos</strong> o <strong>incendios forestales</strong>, o provocados por el ser humano, como <strong>derrames químicos o contaminación</strong>.
                    </p>
                    <p>
                        Conocer estas zonas permite tomar mejores decisiones, prevenir accidentes y salvar vidas. Este sistema ayuda a identificar y visualizar estas áreas en el mapa, según su <strong>nivel de riesgo</strong>, y permite a la comunidad estar informada y preparada en caso de emergencia.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->
    <!-- Feature Start -->
    <div class="container-fluid bg-image" style="margin: 90px 0;">
        <div class="container">
            <div class="row">
                <div class="col-lg-7 my-5 pt-5 pb-lg-5">
                    <div class="section-title position-relative mb-4">
                        <h6 class="d-inline-block position-relative text-secondary text-uppercase pb-2">Infórmate</h6>
                        <h1 class="display-4">Zonas Seguras</h1>
                    </div>
                    <p class="mb-4 pb-2">
                        Las <strong>zonas seguras</strong> son lugares que han sido evaluados como apropiados para resguardarse durante una emergencia, como terremotos, incendios o inundaciones. Están alejadas de peligros inmediatos y son fácilmente accesibles por la comunidad.
                    </p>
                    <p class="mb-4 pb-2">
                        Estas zonas pueden incluir parques, canchas abiertas, plazas o espacios libres de cables, muros o estructuras inestables. En caso de evacuación, conocer su ubicación es clave para proteger la vida.
                    </p>

                    <div class="d-flex mb-3">
                        <div class="btn-icon bg-primary mr-4">
                            <i class="fa fa-2x fa-shield-alt text-white"></i>
                        </div>
                        <div class="mt-n1">
                            <h4>Protección en emergencias</h4>
                            <p>Son espacios definidos para que las personas puedan refugiarse de forma segura durante eventos peligrosos.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="btn-icon bg-success mr-4">
                            <i class="fa fa-2x fa-map-marker-alt text-white"></i>
                        </div>
                        <div class="mt-n1">
                            <h4>Fácil ubicación</h4>
                            <p>Estas zonas están marcadas en mapas comunitarios y pueden consultarse desde nuestro sistema en tiempo real.</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="btn-icon bg-warning mr-4">
                            <i class="fa fa-2x fa-users text-white"></i>
                        </div>
                        <div class="mt-n1">
                            <h4>Acceso comunitario</h4>
                            <p class="m-0">Toda la comunidad puede acudir a ellas en caso de emergencia, con capacidad suficiente y buena accesibilidad.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5" style="min-height: 500px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute w-100 h-100" src="{{ asset('plantilla/img/feature.jpg') }}" style="object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Start -->
    <!-- Testimonial Start -->
    <div class="container-fluid bg-image py-5" style="margin: 90px 0;">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <div class="section-title position-relative mb-4">
                        <h6 class="d-inline-block position-relative text-secondary text-uppercase pb-2">Infórmate</h6>
                        <h1 class="display-4">Puntos de Encuentro</h1>
                    </div>
                    <p class="m-0">
                        Los <strong>puntos de encuentro</strong> son lugares seguros y designados donde las personas deben reunirse en caso de una emergencia, como un sismo, incendio o evacuación.
                        Estos puntos son esenciales para facilitar la organización, brindar ayuda y garantizar la seguridad de todos.
                    </p>
                </div>
                <div class="col-lg-7">
                    <div class="owl-carousel testimonial-carousel">
                        <div class="bg-white p-5">
                            <i class="fa fa-3x fa-map-marked-alt text-primary mb-4"></i>
                            <p>
                                Un buen punto de encuentro debe ser amplio, accesible, sin obstáculos ni cables eléctricos cerca, y estar claramente identificado con señalética visible.
                            </p>
                            <div class="d-flex flex-shrink-0 align-items-center mt-4">
                                <img class="img-fluid mr-4" src="{{ asset('plantilla/img/punto1.jpg') }}" alt="">
                                <div>
                                    <h5>Parque Central</h5>
                                    <span>Capacidad: 200 personas</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-5">
                            <i class="fa fa-3x fa-users text-primary mb-4"></i>
                            <p>
                                Cada comunidad debe conocer con anticipación dónde están ubicados sus puntos de encuentro. Nuestro sistema permite verlos en el mapa y saber cuántas personas puede albergar cada uno.
                            </p>
                            <div class="d-flex flex-shrink-0 align-items-center mt-4">
                                <img class="img-fluid mr-4" src="{{ asset('plantilla/img/punto2.jpg') }}" alt="">
                                <div>
                                    <h5>Cancha del barrio</h5>
                                    <span>Capacidad: 120 personas</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Testimonial Start -->
    <!-- Team Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="section-title text-center position-relative mb-5">
                <h6 class="d-inline-block position-relative text-secondary text-uppercase pb-2">Equipo de trabajo</h6>
                <h1 class="display-4">Conoce a los desarrolladores</h1>
            </div>
            <div class="owl-carousel team-carousel position-relative" style="padding: 0 30px;">
                <div class="team-item">
                    <img class="img-fluid w-100" src="{{ asset('plantilla/img/1.png') }}" alt="Justin Álvarez">
                    <div class="bg-light text-center p-4">
                        <h5 class="mb-3">Justin Álvarez</h5>
                        <p class="mb-2">Desarrollador Backend</p>
                        <div class="d-flex justify-content-center">
                            <a class="mx-1 p-1" href="#"><i class="fab fa-github"></i></a>
                            <a class="mx-1 p-1" href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a class="mx-1 p-1" href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-item">
                    <img class="img-fluid w-100" src="{{ asset('plantilla/img/2.png') }}" alt="Johan Lozada">
                    <div class="bg-light text-center p-4">
                        <h5 class="mb-3">Johan Lozada</h5>
                        <p class="mb-2">Diseñador Frontend</p>
                        <div class="d-flex justify-content-center">
                            <a class="mx-1 p-1" href="#"><i class="fab fa-github"></i></a>
                            <a class="mx-1 p-1" href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a class="mx-1 p-1" href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
                <div class="team-item">
                    <img class="img-fluid w-100" src="{{ asset('plantilla/img/team-3.jpg') }}" alt="Jorge Medina">
                    <div class="bg-light text-center p-4">
                        <h5 class="mb-3">Jorge Medina</h5>
                        <p class="mb-2">Integrador de APIs & Reportes</p>
                        <div class="d-flex justify-content-center">
                            <a class="mx-1 p-1" href="#"><i class="fab fa-github"></i></a>
                            <a class="mx-1 p-1" href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a class="mx-1 p-1" href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Team End -->

    <!-- Contact Start -->
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <div class="bg-light d-flex flex-column justify-content-center px-5" style="height: 450px;">
                        <div class="d-flex align-items-center mb-5">
                            <div class="btn-icon bg-primary mr-4">
                                <i class="fa fa-2x fa-map-marker-alt text-white"></i>
                            </div>
                            <div class="mt-n1">
                                <h4>Our Location</h4>
                                <p class="m-0">123 Street, New York, USA</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-5">
                            <div class="btn-icon bg-secondary mr-4">
                                <i class="fa fa-2x fa-phone-alt text-white"></i>
                            </div>
                            <div class="mt-n1">
                                <h4>Call Us</h4>
                                <p class="m-0">+012 345 6789</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="btn-icon bg-warning mr-4">
                                <i class="fa fa-2x fa-envelope text-white"></i>
                            </div>
                            <div class="mt-n1">
                                <h4>Email Us</h4>
                                <p class="m-0">info@example.com</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="section-title position-relative mb-4">
                        <h6 class="d-inline-block position-relative text-secondary text-uppercase pb-2">Need Help?</h6>
                        <h1 class="display-4">Send Us A Message</h1>
                    </div>
                    <div class="contact-form">
                        <form>
                            <div class="row">
                                <div class="col-6 form-group">
                                    <input type="text" class="form-control border-top-0 border-right-0 border-left-0 p-0" placeholder="Your Name" required="required">
                                </div>
                                <div class="col-6 form-group">
                                    <input type="email" class="form-control border-top-0 border-right-0 border-left-0 p-0" placeholder="Your Email" required="required">
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control border-top-0 border-right-0 border-left-0 p-0" placeholder="Subject" required="required">
                            </div>
                            <div class="form-group">
                                <textarea class="form-control border-top-0 border-right-0 border-left-0 p-0" rows="5" placeholder="Message" required="required"></textarea>
                            </div>
                            <div>
                                <button class="btn btn-primary py-3 px-5" type="submit">Send Message</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Contact End -->

@endsection