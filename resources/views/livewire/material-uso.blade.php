@if(Auth::user()->role !== 'estilista')
<div>

<div class="row" style="justify-content: center;">
    <div class="col-sm-12 col-md-12"> 
        <div>
            <div class="card">
                <div class="card-header flex-wrap" >
                    @if(!$type)
                    
                    <div>
                        <button wire:click="$emit('changeWindow','6')" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Ver historial</button>
                    </div>
                    <div>
                        <button wire:click="$emit('clear-cart')" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Limpiar carrito</button>
                    </div>
                    @else
                    <div>
                        <button wire:click="clear" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Limpiar carrito</button>
                    </div>
                    @endif
                    
                @if(!$type)


                    <div x-data="{ open: false }" @click.away="open = false">
                        <div style="display:flex">
                            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                            
                            <input style="width:30rem" wire:model="queryMaterial" @focus="open=true" @click="open = true" type="text" class="form-control" autocomplete="off" placeholder="Escriba el nombre del material"> 
                            
                            <!-- Icono de búsqueda -->
                            <div class="input-group-append">
                                <i style="background-color: white;" class="input-group-text">
                                    <i class="flaticon-381-search-2"></i>
                                </i>
                            </div>
                        </div>
                        
                        <!-- Desplegable de resultados -->
                        <div>

                            <ul x-show="open" class="list-group float-right" style="width: 30rem; position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                            @foreach ($productos as $index => $item)
                                <li wire:click="$emit('add', {{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action" style=" cursor:pointer; color:#6E6E6E">{{ $item->name }}{{ $item->type_product == 'simple' ? ' (Mercancía)' : '' }}</li>
                            @endforeach 
                            </ul>

                        </div>
                    </div>
                    
                @endif
                </div>
                <div class="card-body p-1">
                    <div class="table-responsive">
                    @if(isset($cartInfo)!==null && !$type)
                        @include('livewire.uso.cart-view')
                    @elseif($type)
                        @include('livewire.uso.cart-view-service')
                    @endif
                    </div>
                </div>
                
                
            </div>
        </div>
        
        @if(!$type)
            
        <div>
            <div class="input-group w-100 p-4" style="display: flex; flex-direction: row; column-gap: 3rem;" > 
                <div style="display: flex; column-gap: inherit" class="flex-wrap">
                    @if(isset($customerId))
                    <div style="display: flex; padding:2rem;column-gap: inherit;padding-top:0">
                        <div class="tag" style="width:20rem">
                            <span class="tag-name">{{ $customer->first_name }} {{ $customer->last_name }}</span>
                            <input type="button" value="x" class="remove-tag" wire:click="unsetCustomer">
                        </div>
                    </div>
                    @else

                        <div x-data="{ open: false }" @click.away="open = false">
                            <div style="display:flex">
                                <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                            
                                <input class="form-control" type="text" placeholder="Buscar un Cliente" wire:model="queryCust" @focus="open = true" @click="open = true" autocomplete="off" style="width: 20rem;">
                                
                                <!-- Icono de búsqueda -->
                                <div class="input-group-append">
                                    <i style="background-color: white;" class="input-group-text">
                                        <i class="las la-user-alt"></i>
                                    </i>
                                </div>
                            </div>
                            
                            <!-- Desplegable de resultados -->
                            <div>
                                <ul x-show="open" class="list-group float-right" style="width: 20rem; position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                                    @foreach ($clientes as $index => $item)
                                        <li wire:click="$emit('setCustomerId', {{ $item->id }})" @click="open = false;" class="list-group-item list-group-item-action" style=" cursor: pointer; color: #6E6E6E;">{{ $item->first_name }} {{ $item->last_name }} | {{ $item->phone }}</li>
                                    @endforeach 
                                </ul>
                            </div>
                        </div>
                    
                        <div class="input-group-append" style="height: fit-content;">
                            <button class="input-group-text" wire:click="newCust">Añadir Cliente Nuevo</button>
                        </div>
                    @endif
                </div>
            
            </div>
            <hr>
            <div class="float-right" style="padding: 0 1rem 1rem 0;">
                <input value="Guardar" type="button" class="btn btn-info save" 
                style="background-color:#9E846D;border-color:#9E846D" 
                wire:loading.attr="disabled" 
                wire:click.prevent="Store">

            </div>
        </div>
        

    </div> 

</div>

@else
    <div class="card-footer" style="background-color:transparent;height: 4rem">
        <button type="button" class="float-right btn btn-sm ml-5 save" style="color:white" wire:click="$emit('storeDate')" >Finalizar cita</button>
    </div>
@endif
@else
@include('livewire.sinPermisos')
@endif

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
