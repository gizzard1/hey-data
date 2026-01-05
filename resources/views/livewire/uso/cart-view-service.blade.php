@foreach($cartServices as $service)
    <table class="table table-striped table-responsive-sm">
        <thead>
            <tr class="text-center">
                <th colspan="2">Materiales usados en: {{ $service['name'] }}</th>
                <th colspan="3">

                
                    <div x-data="{ open: false }" @click.away="open = false">
                        <div style="display:flex">
                            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->

                            
                            <input 
                                id="query-{{ $service['id'] }}" 
                                style="width: auto" 
                                wire:model="query.{{ $service['id'] }}" 
                                @focus="open = '{{ $service['id'] }}'" 
                                type="text" 
                                class="form-control" 
                                autocomplete="off" 
                                placeholder="Escriba el nombre del material"
                                wire:keydown.debounce.300ms="mostrarListado('{{ $service['id'] }}')">
                                
                            
                            <!-- Icono de búsqueda -->
                            <div class="input-group-append">
                                <i style="background-color: white;" class="input-group-text">
                                    <i class="flaticon-381-search-2"></i>
                                </i>
                            </div>
                        </div>
                        
                        <!-- Desplegable de resultados -->
                            
                            <ul x-show="open === '{{ $service['id'] }}'" class="list-group float-right" style="position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                                
                                @if (isset($productos[$service['id']]))
                                    @foreach ($productos[$service['id']] as $item)
                                        <li 
                                            wire:click="$emit('add','{{ $item['id'] }}', '{{ $service['vendedor'] }}' , '{{ $service['sid'] }}', '{{ $service['id'] }}')" 
                                            @click="open = false;" 
                                            class="list-group-item list-group-item-action" 
                                            style="font-weight: lighter; cursor: pointer; color: #6E6E6E">
                                            {{ $item['name'] }}{{ $item['type_product'] == 'simple' ? ' (Mercancía)' : '' }}
                                        </li>
                                    @endforeach 
                                @endif
                            </ul>

                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            <thead>
                <tr class="text-center">
                    <th>Producto<thh>
                    <th>Consumido por</th>
                    <th width="90">Piezas</th>
                    <th>Stock</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($cartInfo as $item)
                    @if($item['uid'] == $service['id'])
                        @include('livewire.uso.cart-view-material')
                    @endif
                @empty
                    <tr>
                        <td colspan="5" class="text-center">AGREGA MATERIALES</td>
                    </tr>
                @endforelse
            </tbody>
        </tbody>
    </table>
@endforeach