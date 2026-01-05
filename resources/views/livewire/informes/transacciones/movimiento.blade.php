@if(Auth::user()->role!=='estilista')

<div class="card-header">

<h4 class="text-center">{{ $type == 'venta' ? 'Venta' : 'Cita' }} #{{ $itemSelected->id}}</h4>

<span class="float-right"><a class="details" wire:click="$emit('editar')">Editar</a></span>
<span class="float-right"><a class="details" data-dismiss="modal" wire:click="deleteMov">Eliminar Venta</a></span>
</div>
<div class="p-3 text-center">
    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" /></svg>
    <label><a href="{{ route('clientes', ['search' => $itemSelected->customer->first_name,'custId'=>$itemSelected->customer_id]) }}">{{ $itemSelected->customer->first_name }} {{ $itemSelected->customer->last_name }}</a></label>
</div>

<div class="table-responsive">
    <table class="table table-responsive-md table-hover  text-center">
        <thead class="thead-primary">
            <tr >
                <th style="background-color:transparent;color:#1d3557 !important">Nombre</th>
                <th style="background-color:transparent;color:#1d3557 !important">Vendedor</th>
                <th style="background-color:transparent;color:#1d3557 !important">Piezas</th>
                <th style="background-color:transparent;color:#1d3557 !important">Descuento</th>
                <th style="background-color:transparent;color:#1d3557 !important">IVA</th>
                <th style="background-color:transparent;color:#1d3557 !important">Precio</th>
                <th style="background-color:transparent;color:#1d3557 !important">Subtotal</th>
            </tr>
        </thead>
        <tbody>
        @foreach($itemSelected->details as $detail)
            <tr>
                <td>{{ $detail->product->name }}</td>
                <td>{{ $detail->empleado->first_name }}</td>
                <td>{{ $detail->quantity }}</td>
                <td>
                    {{ 
                        ($detail->discount_type == 'Cantidad' ? '$' : '') . 
                        number_format($detail->discount_qty, 2, '.', ',') . 
                        ($detail->discount_type == 'Porcentaje' ? '%' : '') 
                    }}
                </td>
                <td> {{ number_format(($detail->iva)*100,2,'.') }}% </td>
                <td> ${{ number_format($detail->current_price,2,'.',',') }} </td>
                <td> ${{ number_format($detail->current_price*$detail->quantity,2,'.',',') }} </td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>Descuento total:</td>
                <td> ${{ number_format($itemSelected->disccount,2,'.',',') }} </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>Total:</td>
                <td> ${{ number_format($itemSelected->total,2,'.',',') }} </td>
            </tr>
        </tbody>
    </table>
</div>
@include('livewire.informes.transacciones.metodos-pago')

<br>
<div class="card-footer">

<span>Última actualización: {{ $itemSelected->created_at }}</span>
<span class="float-right">Responsable: {{ $itemSelected->user->name }}</span>
</div>
@else
@include('livewire.sinPermisos')
@endif
