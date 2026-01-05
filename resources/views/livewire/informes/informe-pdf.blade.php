@if(Auth::user()->role!=='estilista')
<div >
    <div class="row" >
        <div class="col-sm-12">
            <div class="card" id="reporte-global">
                <div class="card-header ">
                    <div class="d-flex">
                        <div class="default-tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active"><i class="la la-box mr-2"></i> Histórico</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="ventas-w" ><i class="la la-store mr-2"></i> Transacciones</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link"><i class="la la-cash-register mr-2"></i> Caja</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        @include('livewire.informes.header.botonesFecha')
                    </div>
                </div>
                <div class="card-body cuerpo-informe informe" style="margin-left: 2rem;margin-right:2rem;margin-top:2rem;">
                    @include('livewire.informes.informe-contenido')
                </div>
            </div>
        </div>
    </div>
    @include('livewire.informes.js-pdf')
</div>

@else
@include('livewire.sinPermisos')
@endif
