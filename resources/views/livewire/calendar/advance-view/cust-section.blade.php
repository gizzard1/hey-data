
@if(isset($customerId) && $customer!==null)
<div class="client-name">
    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
    </svg>
    {{ $customer->first_name }} {{ $customer->last_name }} ~ {{ $customer->phone }}
    <input type="button" value="x" class="remove-tag p-1" wire:click="unsetCustomer">
</div>
@else
    <div x-data="{ open: false }" @click.away="open = false">
        <div style="display:flex">
            <!-- Icono de búsqueda -->
            <div class="input-group-append" style="cursor:pointer"  wire:click="$emit('activateModalForm')">
                <i style="background-color: white;" class="input-group-text">
                    <i class="las la-user-plus"></i>
                </i>
            </div>
            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
            <input onclick="pause()" class="form-control" type="text" placeholder="Buscar un Cliente" wire:model="query" @focus="open = true" @click="open = true" autocomplete="off" style="width: 30rem">
        </div>
        <!-- Desplegable de resultados -->
        <div style="justify-items: end">
            <ul x-show="open" class="list-group float-right searching-results text-left">
                @foreach ($clientes as $index => $item)
                    <li wire:click="$emit('setCustomerId', {{ $item->id }})" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">{{ $item->first_name }} {{ $item->last_name }} | {{ $item->phone }}</li>
                @endforeach 
            </ul>
        </div>
    </div>
@endif