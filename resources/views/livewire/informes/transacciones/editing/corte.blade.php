<div class="table-responsive">
    <table class="table table-responsive-md table-hover  text-center">
        <thead class="thead-primary">
            <tr >
                <th style="background-color:transparent;color:#1d3557 !important">Detalle</th>
                <th style="background-color:transparent;color:#1d3557 !important">Monto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Ingreso en efectivo</td>
                <td> ${{ number_format($itemSelected->total_cash,2,'.',',') }} </td>
            </tr>
            <tr>
                <td>Propinas en efectivo</td>
                <td> ${{ number_format($itemSelected->propinas_efectivo,2,'.',',') }} </td>
            </tr>
            <tr>
                <td>Caja chica</td>
                <td> ${{ number_format($itemSelected->caja_chica,2,'.',',') }} </td>
            </tr>
            <tr>
                <td>Gastos</td>
                <td> ${{ number_format($itemSelected->gastos,2,'.',',') }} </td>
            </tr>
            <tr>
                <td>Efectivo total</td>
                <td> ${{ number_format($itemSelected->total_cash+$itemSelected->propinas_efectivo+$itemSelected->caja_chica-$itemSelected->gastos,2,'.',',') }} </td>
            </tr>
            <tr>
                <td>Efectivo en caja</td>
                <td>
                    <input wire:change="$emit('changeQty','forma_pago', 'cash', $event.target.value)" class="form-control text-center" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" value="${{ $total_cash_real }}">

                </td>
            </tr>
            <tr>
                <td>Diferencia</td>
                <td class="{{ $total_cash_real-$itemSelected->total_cash-$itemSelected->propinas_efectivo-$itemSelected->caja_chica+$itemSelected->gastos >=0 ? 'label-success' : 'label-danger' }} rounded-sm"> ${{ number_format($total_cash_real-$itemSelected->total_cash-$itemSelected->propinas_efectivo-$itemSelected->caja_chica+$itemSelected->gastos,2,'.',',') }} </td>
            </tr>
        </tbody>
    </table>
    
    <h4 class="text-center">Notas</h4>

    <textarea style="
        width: -webkit-fill-available;
        background-color: unset;" maxlength="100" type="text" value="{{ $itemSelected->description }}" wire:model.defer="description">
    </textarea>

</div>

<div class="d-flex " style="column-gap:1rem;justify-content:end">
    <button class="btn btn-sm float-right" wire:click="cleanFormasPago"  data-dismiss="modal">Cancelar</button>
    <button class="save btn btn-sm btn-info float-right" wire:click="StoreCorte" data-dismiss="modal">Guardar</button>
</div>