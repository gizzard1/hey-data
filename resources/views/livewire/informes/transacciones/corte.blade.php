<div class="card-header">

<h4 class="text-center">Cierre de Caja #{{ $itemSelected->id}}</h4>

<span class="float-right"><a class="details" wire:click="$emit('editar')" data-toggle="modal" data-target="#modalEditing">Editar</a></span>
</div>

<div class="table-responsive">
    <table class="table table-responsive-md table-hover  text-center">
        <thead class="thead-primary">
            <tr>
                <th style="background-color:transparent;color:#1d3557 !important">Detalle</th>
                <th style="background-color:transparent;color:#1d3557 !important">Monto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Efectivo del día</td>
                <td> ${{ number_format($itemSelected->total_cash+$itemSelected->propinas_efectivo+$itemSelected->caja_chica-$itemSelected->gastos,2,'.',',') }} </td>
            </tr>
            <tr>
                <td>Efectivo en caja</td>
                <td> ${{ number_format($itemSelected->total_cash_real,2,'.',',') }} </td>
            </tr>
            <tr>
                <td>Diferencia</td>
                <td class="{{ $itemSelected->total_cash_real-$itemSelected->total_cash-$itemSelected->propinas_efectivo-$itemSelected->caja_chica+$itemSelected->gastos >=0 ? 'label-success' : 'label-danger' }} rounded-sm"> ${{ number_format($itemSelected->total_cash_real-$itemSelected->total_cash-$itemSelected->propinas_efectivo-$itemSelected->caja_chica+$itemSelected->gastos,2,'.',',') }} </td>
            </tr>
        </tbody>
    </table>
</div>
@if($itemSelected->description)
<h4 class="text-center">Notas</h4>

<input style="
    width: -webkit-fill-available;
    text-align: center;
    background-color: unset;
    border-style: hidden;" type="text" disabled value="{{ $itemSelected->description }}">
<br>
@endif
<div class="card-footer">

<span>Última actualización: {{ $itemSelected->created_at }}</span>
<span class="float-right">Responsable: {{ $itemSelected->user->name }}</span>
</div>