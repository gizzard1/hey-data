<div style="display: {{ $action!=1 ? 'block' : 'none' }}">
<div>
<div class="row" style="justify-content: center;">


    @if($totales)
    <div class="col-sm-12 col-md-9"> 
        <livewire:cart-view-services/>
    </div> 
    @endif
        @include('livewire.calendar.totales')
    
    <div class="col-sm-12 col-md-9"> 
    </div>  
    @if($totales)
    <div class="col-sm-12 col-md-3">
        @include('livewire.ventas.caja')
    </div>
    </div>
    @endif

    </div>

    <div>
        <livewire:payment-services :totalCart="$totalCart" :itemsCart="$itemsCart" :key="$totalCart" />
    </div>


    @include('livewire.calendar.jsCitas')

</div>
@include('livewire.calendar.cliente')
</div>
