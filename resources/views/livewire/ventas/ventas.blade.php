@if(Auth::user()->role !== 'estilista')
<div>
    
@include('livewire.ventas.configurar')
@include('livewire.ventas.reseña')
<div class="row" style="justify-content: center;">
    @if($totales)
    <div class="col-sm-12 col-md-9"> 
        <livewire:cart-view/>
    </div> 
    @endif
        @include('livewire.ventas.totales')
    <div class="col-sm-12 col-md-9"> 
    </div> 
    @if($totales)
    <div class="col-sm-12 col-md-3">
        @include('livewire.ventas.caja')
    </div>
    @endif
    </div>

    <div>
        <livewire:payment :totalCart="$totalCart" :itemsCart="$itemsCart" :key="$totalCart" />
    </div>


    @include('livewire.ventas.js')
</div>
@if($isAdmin && $salonNameIsNull)
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#modalConfig').modal('show')
});
</script>
@endif

@else
@include('livewire.sinPermisos')
@endif