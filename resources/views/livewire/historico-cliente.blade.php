@if(Auth::user()->role!=='estilista')
<div class="card">
    <div class="card-header ">
        <div class="d-flex">
            <div class="separator"></div>
            <div class="mr-auto">
                <h4 class="card-title"><a href="{{ route('clientes') }}">Clientes</a>/<a href="{{ route('clientes',['custId' => $customerSelected->id]) }}">{{ $customerSelected->first_name }} {{ $customerSelected->last_name }}</a>/Historial de visitas</h4>
                <p class="fs-14 mb-0"> Historial de visitas</p>
            </div>
        </div>
        @include('livewire.clientes.header.periods')
    </div>
            
    <div class="card-body card-body-data cuerpo-informe" style="margin-left: 2rem;margin-right:2rem;">

        @include('livewire.clientes.graficas.serviciosvsventas')
        
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
            <div  class="d-flex">
                
            <input id="child1" type="checkbox">
            <input id="child2" type="checkbox">
                <table id="col-cat-date" class="table table-responsive-md table-hover text-center">
                    <tbody>

                    <tr>
                        <td colspan="4"><label style="cursor:pointer;width:100%;height:100%" for="child2" style="cursor:pointer;width:100%;height:100%">Citas ({{ count($infoDates) }})</label></td>
                    </tr>
                
                        @forelse($infoDates as $cita)
                        <tr class="toggle-date">
                            <td>
                            @foreach($cita->details as $detail)
                            <div class="d-flex" style="align-items: center;">
                                <div class="tag">
                                    <span class="tag-name">{{ $detail->empleado->first_name }}</span>
                                </div>
                                {{ $detail->servicio ? $detail->servicio->name : 'Desconocido' }} (${{ number_format($detail->disccount_price > 0 ? $detail->disccount_price : $detail->current_price,2,'.',',') }})
                            </div>
                            @endforeach
                            @foreach($cita->details_product as $detail)
                            <div class="d-flex" style="align-items: center;">
                                <div class="tag">
                                    <span class="tag-name">{{ $detail->empleado->first_name }}</span>
                                </div>
                                {{ $detail->quantity . ' ' . $detail->product->unit_type }} {{ $detail->product ? $detail->product->name : 'Desconocido' }} (${{ number_format($detail->disccount_price > 0 ? $detail->disccount_price : $detail->current_price,2,'.',',') }})
                            </div>
                            @endforeach
                                
                            </td>
                            <td>
                                <span>Cita: {{ $cita->status }}</span>
                            </td>
                            <td>
                                <div class="text-right">
                                    <span>{{ Carbon\Carbon::parse($cita->start)->locale('es')->isoFormat('dddd, D MMMM YYYY') }}</span>
                                    <span>Desde {{ Carbon\Carbon::parse($cita->start)->format('H:i') }} hasta: {{ Carbon\Carbon::parse($cita->end)->format('H:i') }}</span>

                                </div>
                            </td>
                            <td>
                                <a href="{{ route('citas', ['cita_id' => $cita->id,'pestaña' => 1,'action'=>2]) }}">Ver</a>

                            </td>
                        </tr>
                        @empty
                        <tr class="toggle-date">
                            <td colspan="4">Sin información</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <table id="col-cat-sale" class="table table-responsive-md table-hover text-center">
                    <tbody>
                        <tr>
                            <td colspan="4"><label for="child1" style="cursor:pointer;width:100%;height:100%">Ventas ({{ count($infoSales) }})</label></td>
                        </tr>
                        @forelse($infoSales as $venta)
                        <tr class="toggle-sale">
                            <td>
                            @foreach($venta->details as $detail)
                            <div class="d-flex" style="align-items: center;">
                                <div class="tag">
                                    <span class="tag-name">{{ $detail->empleado->first_name }}</span>
                                </div>
                                {{ $detail->product ? $detail->product->name : 'Desconocido' }} (${{ number_format($detail->disccount_price > 0 ? $detail->disccount_price : $detail->current_price,2,'.',',') }})
                            </div>
                            @endforeach
                                
                            </td>
                            <td>
                                <span>Venta: {{ $venta->status }}</span>
                            </td>
                            <td>
                                <div class="text-right">
                                    <span>{{ Carbon\Carbon::parse($venta->created_at)->locale('es')->isoFormat('dddd, D MMMM YYYY hh:mm a') }}</span>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('ventas', ['venta_id' => $venta->id]) }}">Ver</a>

                            </td>
                        </tr>
                        @empty
                        <tr class="toggle-sale">
                            <td colspan="4">Sin información</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@else
@include('livewire.sinPermisos')
@endif
<style>
    
    a{
        color:black;
    }
    .tag{
        width: fit-content;
    }
    .card-body-data{
        padding-bottom: 3rem;
    }
    .toggle-sale, .toggle-date{
        display: none;
    }
    input[type=checkbox] { 
        display: none; 
    }
    #child1:checked ~ table tr.toggle-sale { 
        display: table-row; 
    } 
    #child2:checked ~ table tr.toggle-date { 
        display: table-row; 
    } 
    #child1:checked ~ #col-cat-date { 
        display: none; 
    } 
    #child2:checked ~ #col-cat-sale { 
        display: none; 
    } 
</style>
<script>
function changeTo(type){
    var pestañas = {
        1: document.getElementsByName('pestaña-all'),
        2: document.getElementsByName('pestaña-pending'),
        3: document.getElementsByName('pestaña-fin'),
        4: document.getElementsByName('pestaña-canc')
    };

    Object.keys(pestañas).forEach(function(key) {
        pestañas[key].forEach(function(pestaña) {
            if (key == type) {
                pestaña.classList.add('active');
            } else {
                pestaña.classList.remove('active');
            }
        });
    });

        Livewire.emit('windowCust', type)
    }

</script>
</script>