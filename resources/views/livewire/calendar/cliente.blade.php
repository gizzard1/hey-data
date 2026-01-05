<div>
    <div class="card-body col">
        <div class=" input-group w-100" id="clientes-input">
            @if(isset($customer) && $customer!==null)
                <div wire:ignore.self class="tag" style="width:20rem">
                    <span class="tag-name">{{ $customer->first_name }} {{ $customer->last_name }}</span>
                    <input type="button" value="x" class="remove-tag" wire:click="unsetCustomer">
                </div>
            @else
                <div class="d-flex flex-wrap" style="column-gap:5rem; row-gap:1rem">
                    <div x-data="{ open: false }" @click.away="open = false">
                        <div style="display:flex">
                            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                        
                            <input onclick="pause()" class="form-control" type="text" placeholder="Buscar un Cliente" wire:model="query" @focus="open = true" @click="open = true" autocomplete="off" style="width: 15rem">
                            
                            <!-- Icono de búsqueda -->
                            <button class="input-group-append" style="border: transparent;"  wire:click="$emit('activateModal')" onclick="next()">
                                <i class="input-group-text">
                                    <i class="las la-user-plus"></i>
                                </i>
                            </button>
                        </div>
                        
                        <!-- Desplegable de resultados -->
                        <div>
                            <ul x-show="open" class="list-group float-right" style="width: 15rem; position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                                @foreach ($clientes as $index => $item)
                                    <li wire:click="$emit('setCustomerId', {{ $item->id }})" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">{{ $item->first_name }} {{ $item->last_name }} | {{ $item->phone }}</li>
                                @endforeach 
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>


    <style>
        .light:hover{
            background-color: #430007 !important;
        }
        /* estilos tom select */
        .ts-control {
            padding: 0px !important;
            border-style: none;
            border-width: 0px !important;
            background: white !important;
            font-size: 18px;
            cursor: text !important;
            height: 26px;
            color: #6C757D;
        }

        .ts-control input {
            color: #6C757D !important;
            font-size: 1rem !important;
            cursor: text !important;
        }

        .ts-wrapper.multi .ts-control>div {
            font-size: 1rem !important;
            color: white !important;
            background-color: #B59377;
        }

        .search-area .input-group-append .input-group-text i {
            font-size: 16px !important;
        }
    </style>
