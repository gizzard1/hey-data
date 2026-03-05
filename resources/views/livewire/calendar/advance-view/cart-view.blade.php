
<div class="col-lg-8">
    <div class="panel">
        <div class="d-flex p-3 text-center client-name-container">
            @include('livewire.calendar.advance-view.cust-section')
        </div>
        <div class="mb-2 searching-container">
            <div x-data="{ open: false }" @click.away="open = false">
                <div style="display:flex">
                    <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                    <input wire:model="queryServices" @focus="open=true" @click="open = true" type="text" class="form-control" autocomplete="off" placeholder="Escriba el nombre del servicio" style="width: 30rem;"> 
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
                    @foreach ($servicios as $index => $item)
                        <li wire:click="$emit('addNewService', {{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action" style="cursor:pointer; color:#6E6E6E">{{ $item->name }} {{ $item->duration }} min. | ${{ number_format($item->gross_price, 2, '.', ',') }}</li>
                    @endforeach 
                    </ul>
                </div>
            </div>
        </div>
        <table class="table table-sm">
            <thead>
                <tr>
                    <th class="service-title">Servicio</th>
                    <th>Inicio</th>
                    <th>Finaliza</th>
                    <th class="service-title employees-title">Personal</th>
                    <th>Desc.</th>
                    <th>Precio
                        <a data-toggle="popover" data-trigger="hover" data-content="Activa la casilla para elegir este precio como base para calcular la comisión de este servicio." style="
                            color: #858585;
                            font-size: smaller;">?</a></th>
                    <th class="service-title unit-price-title">Precio unitario
                        <a data-toggle="popover" data-trigger="hover" data-content="Activa la casilla para elegir este precio como base para calcular la comisión de este servicio. Nota: este precio es actualizado cuando se modifica el descuento de este servicio." style="
                            color: #858585;
                            font-size: smaller;">?</a></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($cartS as $item)
                    @include('livewire.calendar.advance-view.cartServices')
                @empty
                <tr>
                    <td colspan="10" class="text-center">AGREGA SERVICIOS</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($ventaConstrained)
            <livewire:ventas :constrained="1"/>
        @endif
    </div>
    @include('livewire.calendar.advance-view.billing')
    @include('livewire.calendar.advance-view.files')
</div>