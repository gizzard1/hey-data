@if(Auth::user()->role!=='estilista')
<div class="card">
    <div class="card-header ">
        <div class="d-flex">
            <div class="separator"></div>
            <div class="mr-auto">
                <h4 class="card-title"><a wire:click="regresarListado">Clientes</a>/<a wire:click="viewCust({{ $customerSelected->id }})">{{ $customerSelected->first_name }} {{ $customerSelected->last_name }}</a>/Historial de visitas</h4>
                <p class="fs-14 mb-0"> Historial de visitas</p>
            </div>
        </div>
        @include('livewire.clientes.header.periods')
    </div>
            
    <div class="card-body card-body-data cuerpo-informe" style="margin-left: 2rem;margin-right:2rem;">
        
        <div class="default-tab">
 
            <ul class="nav nav-tabs" role="tablist" style="width:fit-content">
                <li class="nav-item">
                    <a class="nav-link {{ $pestaña == 1 ? 'active' : '' }}" name="pestaña-all" onclick="changeTo(1)"><i class="la la-calendar-check mr-2"></i> Todas las visitas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $pestaña == 2 ? 'active' : '' }}" name="pestaña-pending" onclick="changeTo(2)"><i class="la la-clock mr-2"></i> Pendientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $pestaña == 3 ? 'active' : '' }}" name="pestaña-fin" onclick="changeTo(3)"><i class="la la-check-circle mr-2"></i> Finalizadas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $pestaña == 4 ? 'active' : '' }}" name="pestaña-canc" onclick="changeTo(4)"><i class="la la-times-circle mr-2"></i> Canceladas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $pestaña == 5 ? 'active' : '' }}" name="pestaña-canc" onclick="changeTo(5)"><i class="la la-calendar mr-2"></i> Agendadas</a>
                </li>
                
            </ul>

                
            <ul class="list-group mt-4">
                @foreach($infoDates as $cita)
                <li class="list-group-item">
                    @foreach($cita->details as $detail)
                    <p>
                    <span class="float-right">Cita: {{ $cita->status }}</span>
                    <div class="tag">
                        <span class="tag-name">{{ $detail->empleado->first_name }}</span>
                    </div>
                        {{ $detail->servicio->name }} (${{ $cita->total }})
                        <span class="float-right"> {{ ' ' . $detail->start }}</span><small class="float-right">Inicia:  </small> 
                    </p>
                    @endforeach
                </li>
                <hr>
                @endforeach
                @foreach($infoSales as $sale)
                <li class="list-group-item">
                    @foreach($sale->details as $detail)
                    <p>
                    <span class="float-right">Venta: {{ $sale->status }}</span>
                    <div class="tag">
                        <span class="tag-name">{{ $detail->empleado->first_name }}</span>
                    </div>
                        {{ $detail->product->name }} (${{ $sale->total }})
                        <span class="float-right"> {{ ' ' . $detail->created_at }}</span><small class="float-right">Creada:  </small> 
                    </p>
                    @endforeach
                </li>
                <hr>
                @endforeach
                @if(count($infoDates)==0 && count($infoSales)==0)
                <li class="list-group-item">
                    <p>Sin información</p>    
                </li>   
                @endif
            </ul>

        </div>
    </div>
</div>
@else
@include('livewire.sinPermisos')
@endif
<style>
    .tag{
        width: fit-content;
    }
    .card-body-data{
        padding-bottom: 3rem;
    }
</style>