@if(Auth::user()->role !== 'estilista')
    
<div class="row" style="justify-content: center;">
    <div class="col-sm-12 col-md-12"> 
        @include('livewire.cart-view-compras')
        <livewire:marcas :action="0" />
    </div> 
</div>

@else
@include('livewire.sinPermisos')
@endif