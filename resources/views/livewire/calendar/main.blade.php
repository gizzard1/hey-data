@if($totales)
<div>
    
    <div class="d-flex" style="justify-content:end;margin-right:3rem">
            
        <a type="button" id="date-time" wire:ignore.self wire:model="currentDateC" class="flatpickr" wire:change="dateSelected" onclick="pause()"><h5 class="fs-14 mb-0" style="justify-content: space-between;" >{{ $currentDate }} ({{ $this->minutes_qty }} min.)</h5></a>

        @if($vista != 'livewire.calendar.edit')
            <a id="return" onclick="prevDouble()" wire:click="returnModal" style="text-decoration: underline;cursor:pointer;margin-left:3rem">Regresar</a>
        @endif
    </div>
                
    <div class="card-header flex-wrap" style="row-gap:1rem">
        
        <div class="top-actions">
            <button id="clean-cart" wire:click="$set('ventaConstrained',1)" class="btn btn-dark btn-sm btn-block">Añadir venta</button>
        </div>
        <div class="top-actions">
            <button id="clean-cart" wire:click="clear" class="btn btn-dark btn-sm btn-block">Limpiar carrito</button>
        </div>
        <div class="top-actions">
            <button id="reprint" wire:click.prevent="reimpresion" class="btn btn-dark btn-sm btn-block">Reimprimir último ticket</button>
        </div>
        <div class="top-actions">
            <button onclick="CancelAllDate()" class="btn btn-dark btn-sm btn-block">Cancelar cita</button>
        </div>
    </div>

    

    
    @endif
    
    <div class="row">
        @include('livewire.calendar.advance-view.cart-view')
        @include('livewire.calendar.advance-view.summary')
    </div>
    @if($totales)
</div>


@section('content')
    <livewire:search />
@endsection
@endif
@include('livewire.calendar.cart-view-styles')
@include('livewire.cupones.searchCuponForm')