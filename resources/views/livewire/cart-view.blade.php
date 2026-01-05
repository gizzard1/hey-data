<div>
    <div class="card" >
        <div class="card-header flex-wrap" >
            <div>
                <button wire:click="$emit('clear-cart')" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Limpiar carrito</button>
            </div>
            <div>
                <button wire:click.prevent="$emit('reimpresion')" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Reimprimir último ticket</button>
            </div>
            <div>
                <button wire:click.prevent="crearCupon" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Generar Giftcard</button>
            </div>

            <div x-data="{ open: false }" @click.away="open = false" id="buscador-prod">
                <div style="display:flex">
                    <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                    <input wire:model="query" @focus="open=true" @click="open = true" onclick="pause()" type="text" id="searchBox" class="form-control step-input" autocomplete="off" placeholder="Escriba el nombre del producto"> 
                    
                    <!-- Icono de búsqueda -->
                    <div class="input-group-append">
                        <i style="background-color: white;" class="input-group-text">
                            <i class="las la-user-alt"></i>
                        </i>
                    </div>
                </div>
                
                <!-- Desplegable de resultados -->
                <div>
                    <ul x-show="open" class="list-group float-right" style="position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                    @foreach ($productos as $index => $item)
                        <li onclick="play()" wire:click="$emit('add-product', {{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action" style="cursor:pointer; color:#6E6E6E">{{ $item->name }}</li>
                    @endforeach 
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-1" id="cart-products">
            @if(isset($cartInfo)!==null)
                @include('livewire.ventas.cartProducts')
            @endif
        </div>
        
    </div>
</div>


@section('content')
    <livewire:search />
@endsection

@push('my-scripts')
    <script>
        document.addEventListener('livewire:load', function () {
            document.getElementById('inputFocus').focus(); // Enfoca el campo de búsqueda al cargar el componente
        });
        window.addEventListener('view-product', event => {   
            $('#modalViewProduct').modal('show')
        })
    </script>
@endpush
