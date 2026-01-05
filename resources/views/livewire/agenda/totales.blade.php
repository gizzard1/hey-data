@if($totales)
    
    <div class="col-sm-12 col-md-3"> 
        <div class="card">
    <div class="card-header">
        <span class="h3" style="color:#60060F;margin:auto">Total</span>
    </div>
    <div class="card-body p-3" >
        <div>
            <span style="color: #60060F;">Productos:</span>
            <span class="float-right " style="color:#3B484C">
                {{ $productsCart }}
            </span>
        </div>
        <div>
            <span style="color: #60060F;">Servicios:</span>
            <span class="float-right " style="color:#3B484C">
                {{ $itemsCart }}
            </span>
        </div>
        <div>
            <span style="color: #60060F;">Subtotal:</span>
            <span class="float-right " style="color:#3B484C">
                ${{ number_format(floatval($subtotalCart), 2, '.', ',') }}
            </span>
        </div>
        <div>
            <span style="color: #60060F;">Impuestos:</span>
            <span class="float-right " style="color:#3B484C">
                ${{ number_format($taxCart, 2, '.', ',') }}
            </span>
        </div>
        <hr>
        <div>
            <span style="color: #60060F;"><b>Total</b></span>
            <span class="float-right " style="color:#3B484C">
                <b>${{ number_format($totalCart, 2, '.', ',') }}</b>
            </span>
        </div>
        <hr>
        <div>
            <span style="color: #60060F;"><b>Puntos generados</b></span>
            <span class="float-right " style="color:#3B484C">
                <b>{{ number_format($this->cita->generated_points ?? $generated_points, 2, '.', ',') }}</b>
            </span>
        </div>

        <div class="form-group mt-5">
            <input type="button" value="Seleccionar cliente" data-toggle="modal" onclick="openCliente()" class="btn btn-sm btn-block" style="background-color: white;color:#60060F;border-color:#E2BBB4" wire:click="solicitarFechas" {{ $totalCart > 0  ? '' : 'disabled' }}></input>
        </div>
        <!-- <div class="form-group mt-5">
            <button onclick="openPayment()" class="btn btn-sm btn-block" style="background-color:#9E846D;color:white;" {{ $totalCart > 0 && $caja  ? '' : 'disabled' }}>Cobrar</button>
        </div> -->
        <div class="form-group mt-5">
            <button  onclick="CancelAllDate()" class="btn btn-dark btn-sm btn-block" style="background-color: #60060F;" {{ $totalCart > 0  ? '' : 'disabled' }}>Cancelar cita</button>
        </div>
        <div class="form-group mt-5">
            <button  onclick="CancelDate()" class="btn btn-dark btn-sm btn-block" style="background-color: #3C212A;">Cerrar sin guardar cambios</button>
        </div>
    </div>
    </div>
        @else
        <div class="col-md-5">
            <div class="card">
                @include('livewire.ventas.formCorte')
            </div>
        </div>
    </div>
        <div class="col-md-5">
            <div class="card" style="height: auto;">
                @include('livewire.corte')
            </div>
        </div>
        @endif
    </div>

<style>
    .swal2-input, .swal2-file, .swal2-textarea {
        background: #F1F1F1;
        color:#3B484C;
    }
</style>