@if(Auth::user()->role !== 'estilista')
    <div>
        <div class="row" style="justify-content: center;">
            <div class="col-sm-12 col-md-12"> 
                @include('livewire.cart-view')
            </div> 
            <div class="col-sm-12 col-md-9">
                <livewire:payment :total_disccount="$total_disccount" :totalCart="$totalCart" :itemsCart="$itemsCart" :key="$totalCart" />
            </div>
            @include('livewire.ventas.totales')
        </div>

        @include('livewire.ventas.js')
        @include('livewire.ventas.modals.confirmApertura')
        @include('livewire.cupones.cuponesForm')
    </div>
@else
    @include('livewire.sinPermisos')
@endif