@if(Auth::user()->role !== 'estilista')
<div>
<div class="row" style="justify-content: center;">
    <div class="col-sm-12 col-md-12"> 
        @include('livewire.calendar.ventas.cart-view')
    </div>
</div>
</div>
@else
@include('livewire.sinPermisos')
@endif