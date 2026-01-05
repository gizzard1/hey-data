<div class="card-header">

<div class="d-flex" style="
    align-items: anchor-center;
    column-gap: 2dvh;">

<h4 class="text-center">{{ $itemSelected[0]['folio_fiscal'] ? 'Folio: ' .  $itemSelected[0]['folio_fiscal'] : 'Sin folio fiscal'}} </h4> 
<small> Folio interno {{ $itemSelected[0]['folio_interno']}}</small>
</div>
<!-- <span class="float-right"><a class="details" wire:click="$emit('editar')" data-toggle="modal" data-target="#modalEditing">Editar</a></span> -->
<span class="float-right"><a class="details" data-dismiss="modal" wire:click="deleteEntrada">Eliminar entrada</a></span>
</div>
<h4 class="text-center">Resumen</h4>

<div class="table-responsive">
    <table class="table table-responsive-md table-hover  text-center">
        <thead class="thead-primary">
            <tr >
                <th style="background-color:transparent;color:#1d3557 !important">Producto</th>
                <th style="background-color:transparent;color:#1d3557 !important">Piezas</th>
                <th style="background-color:transparent;color:#1d3557 !important">Iva</th>
                <th style="background-color:transparent;color:#1d3557 !important">Costo (IVA incluido)</th>
                <th style="background-color:transparent;color:#1d3557 !important">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($itemSelected as $material)
                <tr>
                    <td>{{ $material['producto']['name'] }}</td>
                    <td>{{ $material['qty'] }}</td>
                    <td> {{ $material['iva']*100 }}%</td>
                    <td> ${{ number_format($material['cost'] ,2,'.',',') }} </td>
                    <td> ${{ number_format($material['cost'] * $material['qty'] ,2,'.',',') }} </td>
                </tr>
            @endforeach
            <tr>
                @if(isset($itemSelected[0]['marca']))
                    <td>Proveedor: {{ $itemSelected[0]['marca']['name'] }}</td>
                @else
                    <td></td>
                @endif
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
<br>
<div class="card-footer">
@php
    $updated = new DateTime($itemSelected[0]['created_at']);
    $updated_at = $updated->format('Y-m-d');
@endphp
<span>Última actualización: {{ $updated_at }}</span>
<span class="float-right">Responsable: {{ $itemSelected[0]['user']['name'] }}</span>
</div>

<style>
    .campos{
        border: unset;
        background-color: unset;
        text-align: center;
        border-bottom: 1px solid black;
    }
    .tamano-especial{
        width: 5rem;
    }
</style>