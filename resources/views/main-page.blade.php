         
    <!-- ***** Preloader End ***** -->
    <!-- NAVBAR -->
    <div class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container-fluid">
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                <div class="navbar-nav ml-auto">
                    <a href="#" class="nav-item nav-link active">Inicio</a>
                    <a href="#promotion" class="nav-item nav-link">Funcionalidades</a>
                    <a href="#servicios" class="nav-item nav-link">Precios</a>
                    <a href="#tempaltemo_footer" class="nav-item nav-link">Contacto</a>
                    <a href="{{ route('register') }}" class="nav-item nav-link" style="background-color: #e63946;">¡Regístrate!</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="welcome-area" id="welcome">
        <img src="{{ asset('images/banner-bg.png') }}" alt="Imagen de fondo">
        <div class="header-text">
            <h1>Hey! <span class="highlight">data</span></h1>
            <div class="mediaq">
                <em>Gestiona tu negocio </em>
                <em>con nuestra agenda digital</em>
                <h2>Agenda <span class="highlight">más</span>citas</h2>

            </div>
            <div class="medianoq">
                <em>Gestiona tu negocio con nuestra agenda digital</em><br>
                <h2>Agenda <span class="highlight">más</span> citas para tu negocio</h2>

            </div>
        </div>
    </div>
    
    
    
    
    <!-- ***** Welcome Area End ***** -->

    
    <div class="left-image-decor" ></div>

    <!-- ***** Features Big Item Start ***** -->
    <section class="section" id="promotion" >
        <div class="container" >
            <div class="price">
                <div class="container" >
                    <div class="section-header text-center">
                        <h3>Un software diseñado para tu negocio</h3>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-4 col-sm-6" data-scroll-reveal="enter left move 30px over 0.6s after 0.4s">
                            <div class="price-item">
                                <div class="price-img">
                                    <img src="{{asset('images/calendar.png')}}" alt="Image">
                                </div>
                                <div class="price-text">
                                    <h2>Agenda Digital</h2>
                                    <h3>Fácil de visualizar, intuitiva y accesible desde tus dispositivos favoritos</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-4 col-sm-6" data-scroll-reveal="enter left move 30px over 0.6s after 0.4s">
                            <div class="price-item">
                                <div class="price-img">
                                    <img src="{{ asset('images/cash-register.png') }}" alt="Image">
                                </div>
                                <div class="price-text">
                                    <h2>Punto de Venta</h2>
                                    <h3>Lleva el control de tu inventario de productos y conoce los ingresos que te generan</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-4 col-sm-6" data-scroll-reveal="enter left move 30px over 0.6s after 0.4s">
                            <div class="price-item">
                                <div class="price-img">
                                    <img src="{{asset('images/crm.png')}}" alt="Image">
                                </div>
                                <div class="price-text">
                                    <h2>Módulo de Clientes</h2>
                                    <h3>Conoce el comportamiento de tus clientes. Todo guardado de forma segura en la nube</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-4 col-sm-6" data-scroll-reveal="enter left move 30px over 0.6s after 0.4s">
                            <div class="price-item">
                                <div class="price-img">
                                    <img src="{{asset('images/form.png')}}" alt="Image">
                                </div>
                                <div class="price-text">
                                    <h2>Formularios Automáticos</h2>
                                    <h3>Envía formularios vía Whatsapp para conocer la opinión de tus clientes y mejorar su experiencia</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-4 col-sm-6" data-scroll-reveal="enter left move 30px over 0.6s after 0.4s">
                            <div class="price-item">
                                <div class="price-img">
                                    <img src="{{asset('images/historico.png')}}" alt="Image">
                                </div>
                                <div class="price-text">
                                    <h2>Reportes Históricos</h2>
                                    <h3>Observa las estadísticas de los ingresos y egresos de tu negocio</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-4 col-sm-6" data-scroll-reveal="enter left move 30px over 0.6s after 0.4s">
                            <div class="price-item">
                                <div class="price-img">
                                    <img src="{{asset('images/comisiones.png')}}" alt="Image">
                                </div>
                                <div class="price-text">
                                    <h2>Control de Comisiones</h2>
                                    <h3>Configura cuánto van a percibir tus empleados por los productos y/o servicios vendidos</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
            </div>
        </div>
    </section>
    <!-- ***** Testimonials Ends ***** -->
     
    <!-- ***** Welcome Area End ***** -->

    <!-- ***** Features Big Item Start ***** -->
    <section class="section" id="servicios">
        <div class="container" >
            <div class="service" >
                <div class="container" >
                    <div class="section-header text-center">
                        <em style="color:white">----</em>
                        <h3>Prueba Agenda máster</h3>
                        <h3>¡Totalmente GRATIS!</h3>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-6"
                        data-scroll-reveal="enter left move 30px over 0.6s after 0.4s">
                            <div class="service-item">
                                <div class="service-img">
                                    <img src="{{asset('images/free.png')}}" alt="Image">
                                </div>
                                <h3>Gratuito</h3>
                                <p>(Prueba de 7 días)</p>
                                <p>
                                    ¿Qué incluye?
                                </p>
                                <ul class="text-left ml-4">
                                    <li>Agenda Online.</li>
                                    <li>Punto de Venta.</li>
                                    <li>Expediente de clientes.</li>
                                    <li>Informe histórico.</li>
                                </ul>
                                <a href="{{ route('register') }}" class="nav-item nav-link" style="background-color: #e63946;color:white">¡Regístrate!</a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6"
                        data-scroll-reveal="enter left move 30px over 0.6s after 0.4s">
                            <div class="service-item">
                                <div class="service-img">
                                    <img src="{{asset('images/basic.png')}}" alt="Image">
                                </div>
                                <h3 style="color:#1d3557;">Agenda básica</h3>
                                <p>
                                    ¿Qué incluye?
                                </p>
                                <ul class="text-left ml-2">
                                    <li>Funciones principales.</li>
                                    <li>Sistema de puntos para clientes.</li>
                                    <li>Hasta 100 mensajes vía Whatsapp.</li>
                                </ul>
                                
                                <a onclick="crearPreferencia(0,2750)" class="nav-item nav-link" id="offer" style="cursor:pointer;background-color: #e63946;color:white">Plan anual MXN $250.00 /mes
                                    <div>
                                    <small>¡1 mes gratis!</small>
                                    </div>
                                </a>
                                <a onclick="crearPreferencia(0,250)" class="nav-item nav-link" style="cursor:pointer;background-color: transparent;color:#e63946">MXN $250.00 /mes
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6"
                        data-scroll-reveal="enter left move 30px over 0.6s after 0.4s">
                            <div class="service-item">
                                <div class="service-img">
                                    <img src="{{asset('images/master.png')}}" alt="Image">
                                </div>
                                <h3 style="color:#1d3557;">Agenda <span style="color: #e63946;">más</span>ter</h3>
                                <p>
                                    ¿Qué incluye?
                                </p>
                                <ul class="text-left ">
                                    <li>Funciones básicas.</li>
                                    <li>Envío automático de formularios.</li>
                                    <li>Hasta 1000 mensajes vía Whatsapp.</li>
                                </ul>
                                
                                <a onclick="crearPreferencia(1,4950)" class="nav-item nav-link" id="offer" style="cursor:pointer;background-color: #e63946;color:white">Plan anual MXN $450.00 /mes
                                    <div>
                                    <small>¡1 mes gratis!</small>
                                    </div>
                                </a>
                                <a onclick="crearPreferencia(1,450)" class="nav-item nav-link" style="cursor:pointer;background-color: transparent;color:#e63946">MXN $450.00 /mes
                                </a>
                            </div>
                        </div>
                        <script>
                            function crearPreferencia($type,$monto){
                                Livewire.emit('crearPreferencia',$type,$monto)
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ***** Features Big Item End ***** -->


    <!-- ***** Footer Start ***** -->
    <footer class="bg-dark" style="background-color: black !important;" id="tempaltemo_footer">
        
        <div class="container">
            <div class="row">

                <div class="col-md-4 pt-5">
                    <h2 class="h2 text-momo border-bottom pb-3 border-light logo">Agenda máster</h2>
                    <ul class="list-unstyled text-light footer-link-list">
                        <li>
                            <i class="fa fa-phone fa-fw"></i>
                            <a class="text-decoration-none" href="tel:+52 333-0258-051">+52 333-0258-051</a>
                        </li>
                        <li>
                            <i class="fa fa-envelope fa-fw"></i>
                            <a class="text-decoration-none" href="mailto:atencion@agenda-master.com.mx">atencion@agenda-master.com.mx</a>
                        </li>
                        
                        <li>
                            <i class="fas  fa-fw"></i>
                            Lunes a Viernes: 9:00 AM - 6:00 PM
                        </li>
                    </ul>
                    
                    
                </div>
                <div class="col-md-8">
                    <div class="footer">
                        <div class="container">
                            <div class="footer-content">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="contact-form">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12">
                                                    <input id="name" type="text" onchange="updateName()" required placeholder="Nombre Completo"
                                                        style="background-color: rgba(250,250,250,0.3);">
                                                </div>
                                                <div class="col-md-6 col-sm-12">
                                                    <input id="email" onchange="updateEmail()" required type="text" placeholder="Correo Electronico" style="background-color: rgba(250,250,250,0.3);">
                                                </div>
                                                <div class="col-lg-12">
                                                    <textarea maxlength="300" id="message" required rows="6" placeholder="Mensaje" onchange="updateMessage()" style="background-color: rgba(250,250,250,0.3);"></textarea>
                                                </div>
                                                <div class="col-lg-12">
                                                <button class="nav-item nav-link" style="background-color: #1d3557;color: white;" onclick="storeMessage()">¡Escríbenos!</button>
                                                </div>
                                            </div>
                                            <script>
                                                function storeMessage()
                                                {
                                                    Livewire.emit('storeMessage')
                                                }
                                                function updateMessage()
                                                {
                                                    const messageValue = document.getElementById('message').value;

                                                    Livewire.emit('updateMessage',messageValue)
                                                }
                                                function updateEmail()
                                                {
                                                    const EmailValue = document.getElementById('email').value;

                                                    Livewire.emit('updateEmail',EmailValue)
                                                }
                                                function updateName()
                                                {
                                                    const NameValue = document.getElementById('name').value;

                                                    Livewire.emit('updateName',NameValue)
                                                }
                                            </script>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        
    
                    </div>

                </div>
                
            </div>

            <div class="row text-light mb-4">
                <div class="col-12 mb-3">
                    <div class="w-100 my-3 border-top border-light"></div>
                </div>
                <!-- <div class="col-auto me-auto">
                    <ul class="list-inline text-left footer-icons">
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="http://facebook.com/"><i class="fab fa-facebook-f fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="https://www.instagram.com/"><i class="fab fa-instagram fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="https://twitter.com/"><i class="fab fa-twitter fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" href="https://www.linkedin.com/"><i class="fab fa-linkedin fa-lg fa-fw"></i></a>
                        </li>
                    </ul>
                </div> -->
                <div class="col-auto">
                    <label class="sr-only" for="subscribeEmail">Email address</label>
                </div>
            </div>
        </div>

        <div class="w-100 bg-black py-3">
            <div class="container">
                <div class="row pt-2">
                    <div class="col-12">
                        <p class="text-left text-light">
                            Copyright &copy; 2024 Agenda-máster
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </footer>