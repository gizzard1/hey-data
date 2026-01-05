@if(Auth::user()->role!=='estilista')
<div>
    <div class="row">
        <div class="col-sm-12" style="justify-items:center">
            <div class="card" style="width: 100%;">
                <div class="card-header">
                    <div class="d-flex">
                        <div class="default-tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route(name: 'informe') }}"><i class="la la-box mr-2"></i> Histórico</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route(name: 'informe-movimientos') }}"><i class="la la-store mr-2"></i> Transacciones</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active"><i class="la la-cash-register mr-2" ></i> Caja</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                @if($isOpened)
                <div class="row">
                    <div class="col-sm-8">
                        <div class="card-body" style="height: auto;">
                            @include('livewire.ventas.cajaCorteTransacciones')
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="card-body" style="height: auto;">
                            @include('livewire.ventas.cajaCorte')
                        </div>
                    </div>
                </div>
                @else
                <div class="tab-content" style="text-align: -webkit-center;">
                    <div style="
                    display: flex;
                    flex-direction: column;
                    width: fit-content;
                    padding: 8rem;">
                        <h4>Caja Cerrada</h4>
                        <div class="form-group d-flex">
                            <!-- Icono de búsqueda -->
                            <div class="input-group-append">
                                <i style="background-color: white;" class="input-group-text">
                                    <i class="las la-dollar-sign"></i>
                                </i>
                            </div>
                            <input wire:model.defer="cajaChica" class="form-control" placeholder="Caja chica">
                        </div>
                        <button wire:click="apertura" class="btn btn-sm btn-block save w-auto" style="background-color: #9E846D;color:white">Abrir Caja</button>
                    </div>

                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@else
@include('livewire.sinPermisos')
@endif

<style>
    
    a{
        color:#1d3557
    }
</style>