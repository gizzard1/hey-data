<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="">
    <meta name="facebook-domain-verification" content="rmw93ngccd0c7vknlgo7kx9t4s37rh" />
    <meta property="og:image" content="{{ asset('images/BleniQIcon-17.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ config('app.name', 'Laravel') }}">
    <meta property="og:description" content="Bleniq es una plataforma de gestión para negocios de belleza y bienestar. Ofrecemos herramientas para administrar citas, ventas, inventario y clientes, todo en un solo lugar. Optimiza tu negocio con Bleniq y brinda una experiencia excepcional a tus clientes.">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/BleniQIcon-13.png') }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    @include('layouts.theme.loginstyles')
    <livewire:styles />

</head>

<body>

    <!-- ***** Preloader Start ***** -->
    <div id="preloader">
        <div class="jumper">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <div>
        <div class="authincation d-flex flex-column flex-lg-row flex-column-fluid">
            <div class="container flex-row-fluid d-flex flex-column justify-content-center position-relative overflow-hidden p-7 mx-auto">
                <div class="d-flex justify-content-center h-100 align-items-center">
                    <div class="authincation-content style-2">
                        <div class="row no-gutters">
                            <div class="col-xl-12 tab-content">
                                <div class="auth-form tab-pane fade show active  form-validation">
                                
                                        {{ $slot }}
                                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <footer>
                    <div class="container">
                        <div class="row">
                            <div class="col-12 text-center">
                                <p class="mb-0 text-black">Copyright &copy; 2026 Momó sales. All rights reserved.</p>
                                <p class="mb-0 text-black">Bleniq es una marca de Momó Sales</p>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        
    </div>
    
 
    @include('layouts.theme.loginscripts')
    <livewire:scripts />

    @yield('scripts')
    @stack('my-scripts')

</body>
<style>
    .text-black {
        color: black !important;
    }
</style>
</html>