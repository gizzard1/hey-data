@if(!$show)                                
<div class="price">
        <div class="row">
            <div class="col-sm-12" data-scroll-reveal="enter left move 30px over 0.6s after 0.4s">
                <div class="price-item">
                    <div class="price-img">
                        
                            @if($plan=='free')
                                <img width="100" src="{{asset('images/free.png')}}" alt="Image">
                            @elseif($plan=='basic')
                                <img width="100" src="{{asset('images/basic.png')}}" alt="Image">
                            @elseif($plan=='master')
                                <img width="100" src="{{asset('images/master.png')}}" alt="Image">
                            @endif
                    </div>
                    <div class="price-text">
                        <h2>Estás suscrito al plan 
                            @if($plan=='free')
                                Gratuito (prueba gratis {{ $free_status }})
                            @elseif($plan=='basic')
                                Básico
                            @elseif($plan=='master')
                                Máster
                            @endif
                        </h2>
                        <h3>Por ahora tienes acceso a :</h3>
                        @if($plan=='free')
                            <li>Agenda Online.</li>
                            <li>Punto de Venta.</li>
                            <li>Expediente de clientes.</li>
                            <li>Informe histórico.</li>
                        @elseif($plan=='basic')
                            <li>Funciones principales.</li>
                            <li>Reporte de empleados.</li>
                            <li>Sistema de puntos para clientes.</li>
                            <li>Hasta 100 mensajes vía Whatsapp.</li>
                        @elseif($plan=='master')
                            <li>Funciones básicas.</li>
                            <li>Envío automático de formularios.</li>
                            <li>Hasta 1000 mensajes vía Whatsapp.</li>
                            <li>Control de entradas para empleados.</li>
                        @endif
                    </div>
                    <div class="suscription-buttons" style="width:11rem">
                    <button class="btn save" style="color:white" wire:click.prevent="$set('show',1)">Modificar</button>
                    @if($plan!='free')
                    <button onclick="confirmCancelacion()" class="btn btn-danger" style="color:white">Cancelar</button>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        
        function confirmCancelacion() {      
            Swal.fire({
            title: '¿SEGURO QUE DESEAS CANCELAR TU SUSCRIPCIÓN ACTUAL?',
                input: "textarea",
                inputPlaceholder: "¿Nos podrías brindar información al respecto?",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
            if (result.isConfirmed) {    
                // Captura el valor del textarea
                const motivo = result.value
                // Emite el evento Livewire junto con el valor
                Livewire.emit('cancelarSuscripcion', motivo)
            }
            })
        }
    </script>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-responsive-md table-hover  text-center">
                <thead class="thead-primary">
                    <tr >
                        <th style="background-color:transparent;color:#1d3557 !important">Plan</th>
                        <th style="background-color:transparent;color:#1d3557 !important">Tipo</th>
                        <th style="background-color:transparent;color:#1d3557 !important">Precio</th>
                        <th style="background-color:transparent;color:#1d3557 !important">Fecha de pago</th>
                        <th style="background-color:transparent;color:#1d3557 !important">Próximo pago</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suscripciones as $suscripcion)
                    <tr>
                        <td style="background-color:transparent;">{{$suscripcion->plan}}</td>
                        <td style="background-color:transparent;">{{$suscripcion->period}}</td>
                        <td style="background-color:transparent;">{{$suscripcion->gross_price}}</td>
                        <td style="background-color:transparent;">{{$suscripcion->created_at}}</td>
                        <td style="background-color:transparent;">{{$suscripcion->period == 'anual' ? Carbon\Carbon::parse($suscripcion->created_at)->addYear() : Carbon\Carbon::parse($suscripcion->created_at)->addMonth()}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="price">
    <div style="
    display: flow-root;
    padding: 2rem 2rem 0 0;">
    <span class="float-right pr-4 mb-4"><a wire:click="$set('show',false)">Regresar</a></span>
    </div>
    @if($plan!='master')
    <div class="container">
        <div class="row">
            <div class="col-sm-12" data-scroll-reveal="enter left move 30px over 0.6s after 0.4s">
                <div class="price-item">
                    <div class="price-img">
                        <img width="100" src="{{asset('images/master.png')}}" alt="Image">
                    </div>
                    <div class="price-text">
                        <h2>Suscripción al plan Máster</h2>
                        <h3>Tendrías acceso a:</h3>
                        <li>Funciones básicas.</li>
                        <li>Envío automático de formularios.</li>
                        <li>Hasta 1000 mensajes vía Whatsapp.</li>
                        <li>Control de entradas para empleados.</li>
                    </div>
                    <div class="suscription-buttons">
                        <button wire:click="modificarSuscripcion('basic',2750,1)" class="btn btn-danger btn-floating" style="color:white">Plan anual <br>¡1 mes gratis!</button>
                        <button class="btn save" style="color:white" wire:click="modificarSuscripcion('basic',250,0)">Plan mensual</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($plan!='basic')
    <div class="container">
        <div class="row">
            <div class="col-sm-12" data-scroll-reveal="enter left move 30px over 0.6s after 0.4s">
                <div class="price-item">
                    <div class="price-img">
                        <img width="100" src="{{asset('images/basic.png')}}" alt="Image">
                    </div>
                    <div class="price-text">
                        <h2>Suscripción al plan Básico</h2>
                        <h3>Tendrías acceso a:</h3>
                        <li>Funciones principales.</li>
                        <li>Reporte de empleados.</li>
                        <li>Sistema de puntos para clientes.</li>
                        <li>Hasta 100 mensajes vía Whatsapp.</li>
                    </div>
                    <div class="suscription-buttons" style="width:11rem">
                        <button wire:click="modificarSuscripcion('master',4950,1)" class="btn btn-danger btn-floating" style="color:white">Plan anual <br>¡1 mes gratis!</button>
                        <button class="btn save" style="color:white" wire:click="modificarSuscripcion('master',450,0)">Plan mensual</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endif

<style>
    
.suscription-buttons{
    display: flex;
    flex-direction: column;
    row-gap: 1rem;
}
.price {
    position: relative;
    width: 100%;
}

.price .price-item {
    position: relative;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    flex-direction: row;
    margin-bottom: 30px;
    background: rgba(39, 46, 61, 0.04);
    transition: .3s;
    padding:2rem;
}


.price .price-img {
    position: relative;
    width: 100px;
}

.price .price-img img {
    width: 100%;
}


.price .price-text {
    position: relative;
    padding: 0 15px;
    width: calc(100% - 100px);
    overflow: hidden;
}

.price .price-text h2 {
    position: relative;
    margin-bottom: 8px;
    font-size: 1.5rem;
    font-weight: 600;
    white-space: nowrap;
    color: #1d3557;
}

.price .price-text h3 {
    position: relative;
    margin: 0;
    font-size: 16px;
    font-weight: 400;
    color: #333333;
}
/*******************************/
/********* Service CSS *********/
/*******************************/
.service {
    width: 100%;
}

.service .service-item {
    position: relative;
    width: 100%;
    text-align: center;
    margin-bottom: 30px;
    background: rgba(29, 36, 52, .04);
}

.service .service-img {
    position: relative;
    width: 100%;
}

.service .service-img img {
    position: relative;
    width: 100%;
    height: 100%;
    object-fit: cover;
}


.service .service-item h3 {
    margin: 0;
    padding: 25px 15px 15px 15px;
    font-size: 25px;
    font-weight: 700;
    color:black
}

.service .service-item p {
    margin: 0;
    padding: 0 25px 25px 25px;
    font-size: 16px;
}

.service .service-item a.btn {
    position: relative;
    margin-bottom: 30px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 600;
    color: #1d2434;
    border: 2px solid #1d2434;
    border-radius: 0;
    background: none;
    transition: .3s;
}

.service .service-item:hover a.btn {
    color: #D5B981;
    background: #1d2434;
    border-color: #1d2434;
}

.swal2-textarea{
    background-color: transparent;
    color: black;
}

.btn-floating {
    animation: pulse 1s infinite;
    cursor: pointer;
}
</style>