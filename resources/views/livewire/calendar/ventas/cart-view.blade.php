<div>
    <div class="mb-2 searching-container">
        <div x-data="{ open: false }" @click.away="open = false">
            <div style="display:flex">
                <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                <input wire:model="query" @focus="open=true" @click="open = true" type="text" class="form-control" autocomplete="off" placeholder="Escriba el nombre del producto" style="width: 30rem;"> 
                
                <!-- Icono de búsqueda -->
                <div class="input-group-append">
                    <i style="background-color: white;" class="input-group-text">
                        <i class="flaticon-381-search-2"></i>
                    </i>
                </div>
            </div>
            <!-- Desplegable de resultados -->
            <div>
                <ul x-show="open" class="list-group float-right searching-results">
                @foreach ($productos as $index => $item)
                    <li wire:click="$emit('add-product', {{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action" style="cursor:pointer; color:#6E6E6E">{{ $item->name }}</li>
                @endforeach 
                </ul>
            </div>
        </div>
    </div>
    <table class="table table-sm">
        <thead>
            <tr>
                <th class="service-title scroll-text">Producto</th>
                <th class="service-title hide-text employees-title">Personal</th>
                <th>Piezas</th>
                <th>Descuento</th>
                <th>Precio
                    <a data-toggle="popover" data-trigger="hover" data-content="Activa la casilla para elegir este precio como base para calcular la comisión de este servicio." style="
                        color: #858585;
                        font-size: smaller;">?</a></th>
                <th class="service-title hide-text unit-price-title">Precio unitario
                    <a data-toggle="popover" data-trigger="hover" data-content="Activa la casilla para elegir este precio como base para calcular la comisión de este servicio. Nota: este precio es actualizado cuando se modifica el descuento de este servicio." style="
                        color: #858585;
                        font-size: smaller;">?</a></th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($cartInfo as $item)
                @include('livewire.calendar.advance-view.cartProducts')
            @empty
                <tr>
                    <td colspan="8" class="text-center">AGREGA PRODUCTOS</td>
                </tr>
            @endforelse
        </tbody>
    </table>
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
