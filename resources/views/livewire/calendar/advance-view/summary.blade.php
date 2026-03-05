<div class="col-lg-4">
    <div class="panel">
        <h5 class="d-flex mb-3 center" style="align-items: center">
            <div class="money-icon p-1">💲</div>
            RESUMEN DE VENTA</h5>
        <div class="d-flex justify-content-between mb-2 top-actions">
            @if(isset($customerId) && isset($customer->tarjetaPuntos))
                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="setReward">Usar Puntos</button>
            @else
                <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="activateCardCust">Activar Recompensas</button>
            @endif
            <button class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#modalSearchCuponesForm">Canjear Gift Card</button>
            <button class="btn btn-outline-secondary btn-sm" id="input-disccount"  wire:click="setDisccount(0)">Agregar descuento</button>
        </div>
        {{-- Método de pago --}}
        <div class="form-group">
            @if(count($methods)>0)
                @foreach($methods as $method)
                    @include('livewire.calendar.advance-view.payment-method')
                @endforeach
            @endif
            <button class="btn btn-outline-secondary add-payment" wire:click="setMethod" onclick="next()" id="set-method">+ Agregar forma de pago</button>
        </div>

        {{-- Propinas --}}
        <div class="form-group">
            
            @if(count($propinas)>0)
                @foreach($propinas as $propina)
                    @include('livewire.calendar.advance-view.tips')
                @endforeach
            @endif
            <button class="btn btn-outline-secondary add-payment" wire:click="setTip" onclick="next()" id="set-propina">+ Agregar propina</button>
        </div>

        <div class="summary-box mb-3">
            <div class="d-flex justify-content-between">
                <span>Recibido:</span>
                <span>${{ number_format($recibido,2,'.',',') }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>{{ $restante >= 0 ? 'Falta por cobrar:' : 'Cambio:' }}</span>
                <span class="{{ $restante >= 0 ? 'money-danger' : 'money-success' }}">${{ number_format(abs($restante), 2, '.', ',') }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Total con propina:</span>
                <span>${{ number_format($totalCart + $propinasRecibidas, 2, '.', ',') }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Propina:</span>
                <span>${{ number_format($propinasRecibidas,2,'.',',') }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Puntos:</span>
                <span>{{ number_format($generated_points, 2, '.', ',') }}</span>
            </div>
        </div>
        <div class="summary-box mb-3">

            <div class="mb-2 d-flex justify-content-between">
                <span>Items</span>
                <span>{{ $itemsCart }}</span>
            </div>

            <div class="mb-2 d-flex justify-content-between">
                <span>Subtotal</span>
                <span>${{ number_format(floatval($subtotalCart), 2, '.', ',') }}</span>
            </div>

            <div class="mb-2 d-flex justify-content-between">
                <span>Descuento Adicional</span>
                <span class="money-success">${{ number_format($total_disccount,2,'.',',') }}</span>
            </div>

            <div class="mb-2 d-flex justify-content-between">
                <span>Impuestos</span>
                <span>${{ number_format($taxCart, 2, '.', ',') }}</span>
            </div>

            <div class="total-box d-flex justify-content-between mb-3">
                <span>TOTAL</span>
                <span class="money-success">${{ number_format($totalCart, 2, '.', ',') }}</span>
            </div>
        </div>
        <button class="btn btn-primary-custom btn-block save" id="guardar" wire:click.prevent="storeDate">{{ isset($itemSelected) ? ($itemSelected->status === 'Pagada' || $restante <= 0 ? 'Finalizar' : 'Editar') : 'Guardar' }} {{ $type_mov }}</button>
    </div>
</div>