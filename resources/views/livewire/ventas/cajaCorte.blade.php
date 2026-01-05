<div class="p-3">
    <div>
        <span style="color: #60060F;"><b>Ingreso en efectivo:</b></span>
        <span class="float-right " style="color:#3B484C">
            <b>${{ number_format($efectivoCorte, 2, '.', ',') }}</b>
        </span>
    </div>
    <div>
        <span style="color: #60060F;"><b>Propinas en Efectivo:</b></span>
        <span class="float-right " style="color:#3B484C">
            <b>${{ number_format($propinasEfectivo, 2, '.', ',') }}</b>
        </span>
    </div>
    <div>
        <span style="color: #60060F;"><b>Caja chica:</b></span>
        <span class="float-right " style="color:#3B484C">
            <b>${{ number_format($caja_chica, 2, '.', ',') }}</b>
        </span>
    </div>
    <div>
        <span style="color: #60060F;"><b>Gastos:</b></span>
        <span class="float-right " style="color:#3B484C">
            <b>{{ $total_gastos > 0 ? '-' : '' }}${{ number_format($total_gastos, 2, '.', ',') }}</b>
        </span>
    </div>
    <hr>
    <div>
        <span style="color: #60060F;"><b>Efectivo total:</b></span>
        <span class="float-right " style="color:#3B484C">
            <b>${{ number_format($caja_chica+$efectivoCorte+$propinasEfectivo-$total_gastos, 2, '.', ',') }}</b>
        </span>
    </div>
    <hr>
    <div>
        <span style="color: #60060F;"><b>Efectivo en caja:</b></span>
        <span class="float-right " style="color:#3B484C">
            <input wire:change.prevent="changeCash($event.target.value)" value="${{ number_format($totalCashReal,2,'.',',') }}" class="text-right form-control" placeholder="Efectivo en caja" autocomplete="nope"  style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;">
        </span>
    </div>
    <hr>
    <div>
        <span style="color: #60060F;"><b>Diferencia:</b></span>
        <span class="float-right {{ $totalCashReal-$caja_chica-$efectivoCorte-$propinasEfectivo+$total_gastos >=0 ? 'label-success' : 'label-danger' }} rounded-sm">${{ number_format($totalCashReal-$caja_chica-$efectivoCorte-$propinasEfectivo+$total_gastos ?? 0, 2, '.', ',') }}</span>
    </div>
    <hr>
    <div>
        <textarea wire:model.defer="description" type="text" class="form-control" placeholder="Agregar nota..."></textarea>
    </div>
    @error('description') <span class="text-danger">*Corrige este campo* </span> @enderror
    <div class="form-group mt-5" style="text-align: -webkit-center;">
        <button wire:click="guardarCorte" class="btn btn-sm btn-block w-auto save" style="background-color: #9E846D;color:white">Guardar</button>
    </div>
</div>