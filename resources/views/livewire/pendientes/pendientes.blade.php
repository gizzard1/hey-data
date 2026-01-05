@if(Auth::user()->role!='estilista')
<div>
<div class="card-header">
    <div class="d-flex">
        <div class="separator"></div>
        <div class="mr-auto">
            <h4 class="card-title mb-1">Movimientos Pendientes</h4>
            <p class="fs-14 mb-0"> Listado Registrado</p>
        </div>
    </div>
    
    @include('livewire.pendientes.botones')
</div>
<div class="card-body form-config">
    <div class="table-responsive">
        <table class="table table-responsive-md table-hover text-center">
            <thead class="thead-primary">
                <tr >
                    <th style="background-color:transparent;color:#9D1466 !important">Cliente</th>
                    <th style="background-color:transparent;color:#9D1466 !important" width ="152" id="fecha">Fecha</th>
                    <th style="background-color:transparent;color:#9D1466 !important">Recibido</th>
                    <th style="background-color:transparent;color:#9D1466 !important">Descuentos</th>
                    <th style="background-color:transparent;color:#9D1466 !important">Por pagar</th>
                    <th style="background-color:transparent;color:#9D1466 !important">Total</th>
                    <th style="background-color:transparent;color:#9D1466 !important" class="ult-ver">Versión</th>
                    <th id='desaparecerColumna' style="background-color:transparent;color:#9D1466 !important"></th>
                </tr>
            </thead>
            <tbody >
                @foreach($transacciones as $index => $movimientos)
                    @forelse ($movimientos as $movimiento)
                        <tr>
                            <td> {{ isset($movimiento->customer) ? $movimiento->customer->first_name : 'Cliente eliminado' }} {{ isset($movimiento->customer) ? $movimiento->customer->last_name : '' }}</td>
                            <td> {{ date_format(new DateTime($movimiento->created_at),'d-m-Y') }} </td>
                            <td> ${{ number_format($movimiento->recibido,2,'.',',') }} </td>
                            <td> ${{ number_format($movimiento->disccount,2,'.',',') }} </td>
                            <td> <strong class="text-danger"> ${{ number_format($movimiento->pendiente,2,'.',',') }} </strong> </td>
                            <td> ${{ number_format($movimiento->total,2,'.',',') }} </td>
                            <td class="ult-ver"> Última act. {{ date_format(new DateTime($movimiento->updated_at),'d-m-Y') }} por {{ $movimiento->user->name }} </td>
                            <td ><a wire:click.prevent="cobrar('{{ $movimiento->id }}')" id="cobrar">
                            @switch($index)
                                @case(0 || 3)
                                    Ver{{ $movimiento->status == 'Pendiente' || $movimiento->status == 'Agendada' ? '/ Cobrar' : '' }}
                                    @break
                                @case(1)
                                    Marcar facturado{{ $movimiento->status == 'Pendiente' || $movimiento->status == 'Agendada' ? '/ Cobrar' : '' }}
                                    @break
                                @case(2)
                                    Cobrar{{ $movimiento->billing === 1 ? '/ Marcar facturado' : ($movimiento->billing === 2 ? '/ Ver factura' : '' ) }}
                                    @break
                            @endswitch
                        </a></td>
                        </tr>
                    @empty
                    @if($index === 0 && $billed)
                    <tr>
                        <td colspan="5">No hay {{ $type ? 'ventas' : 'citas' }} por ver</td>
                    </tr>
                    @elseif($index === 1 && $no_billed && !$no_payed)
                    <tr>
                        <td colspan="5">No hay {{ $type ? 'ventas' : 'citas' }} por cobrar</td>
                    </tr>
                    @elseif($index === 2 && $no_payed && !$no_billed)
                    <tr>
                        <td colspan="5">No hay {{ $type ? 'ventas' : 'citas' }} por facturar</td>
                    </tr>
                    @elseif($index === 3 && ($no_payed || $no_billed))
                    <tr>
                        <td colspan="5">No hay citas futuras</td> 
                    </tr>
                    @endif
                    @endforelse
                @endforeach
            </tbody>
        </table>
    </div>
</div>

</div>
@else
@include('livewire.sinPermisos')
@endif

<style>
    .details{
        cursor: pointer;
    }
    .details:hover{
        text-decoration: underline !important;
        color:#9D1466 !important;
    }
</style>
