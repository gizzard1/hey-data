<div class="card-body col">
    {{-- tomSelect --}}
    <div class="input-group w-100 flex-wrap" style="display: flex; flex-direction: row; column-gap: 3rem; margin-bottom:2rem" > 
        <div class="flex-wrap" style="display: flex; column-gap: inherit" id="add-cust">
            @if(isset($customerId) && $customer!==null)
            <div  id="activate-reward" class=" flex-wrap" style="display: flex; padding:2rem;column-gap: inherit;padding-top:0">
                <div wire:ignore.self class="tag" style="width:20rem">
                    <span class="tag-name">{{ $customer->first_name }} {{ $customer->last_name }}</span>
                    <input type="button" value="x" class="remove-tag" wire:click="unsetCustomer">
                </div>
                @if(isset($customerId) && isset($customer->tarjetaPuntos))
                <div class="input-group-append" style="height: fit-content;">
                    <button type="button" class="input-group-text" wire:click="setReward">Usar Puntos</button>
                </div>
                @else
                <div class="input-group-append" style="height: fit-content;">
                    <button type="button" class="input-group-text" wire:click="activateCardCust">Activar Recompensas</button>
                </div>
                @endif
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
                        <input onclick="pause()" class="form-control" type="text" placeholder="Buscar un Cliente" wire:model="query" @focus="open = true" @click="open = true" autocomplete="off" style="width: 20rem">
                    </div>
                    
                    <!-- Desplegable de resultados -->
                    <div>
                        <ul x-show="open" class="list-group float-right" style="width: 20rem; position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                            @foreach ($clientes as $index => $item)
                                <li wire:click="$emit('setCustomerId', {{ $item->id }})" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">{{ $item->first_name }} {{ $item->last_name }} | {{ $item->phone }}</li>
                            @endforeach 
                        </ul>
                    </div>
                </div>

                <div class="input-group-append" style="height: fit-content;">
                    <button class="input-group-text" wire:click="$emit('activateModalForm')">Añadir Cliente Nuevo</button>
                </div>
            @endif
            <div class="input-group-append" style="height: fit-content;" id="set-giftCard">
                <button class="input-group-text" data-toggle="modal" data-target="#modalSearchCuponesForm">Canjear GiftCard</button>
            </div>
        </div>

    </div>
    
    <div class=" input-group w-100" style="
    display: flex;
    flex-direction: row;
    column-gap: 3rem;" wire:ignore>
        <div class="flex-wrap"  style="display:flex;column-gap:inherit;row-gap:1rem">
            <div style="display:flex"  id="global-disccount">
                <!-- Select para elegir porcentaje o moneda, con flecha oculta -->
                <select id="discount-type" class="form-control no-arrow" style="width: 3rem;" wire:model.defer="type_disccount">
                    <option value="percent">%</option>
                    <option value="currency">$</option>
                </select>
                <input id="input-disccount"  wire:change="setDisccount($event.target.value)" class="form-control" style="width:20rem" placeholder="Descuento Global">
            </div>
            <div class="input-group-append" style="height: fit-content; " id="set-method">
                <button class="input-group-text" wire:click="setMethod" onclick="next()">Añadir Forma de Pago</button>
            </div>
            <div class="input-group-append" style="height: fit-content;" id="set-propina">
                <button class="input-group-text" wire:click="setTip" onclick="next()">Añadir Propina</button>
            </div>
        </div>
    </div>
    
    <hr>
    @if(count($methods)>0)
    <div id="methods">
        @include('livewire.informes.transacciones.editing.metodos-pago')
    </div>
    @endif
    @if(count($propinas)>0)
    <div id="propinas">
        @include('livewire.informes.transacciones.editing.propinas')
    </div>
    @endif

    <div class="card-footer" style="background-color:transparent">
        <input id="guardar" value="{{ isset($itemSelected) ? ($itemSelected->status === 'Pagada' || $restante <= 0 ? 'Finalizar cita' : 'Editar cita') : 'Guardar' }}" type="button"onclick="next()" class="float-right btn btn-info ml-5 save" style="background-color:#9E846D;border-color:#9E846D" wire:click.prevent="storeDate">
        
        <div class="d-flex" style="justify-content:space-between;align-items:baseline">
            <div wire:ignore>
                <label for="billingDate">Requiere factura </label>
                <input type="checkbox" id="billingDate" name="billingDate" wire:model="billRequired">
            </div>
            @if(isset($customerId) && $customer!==null && $billRequired)
                <div>
                    <label for="billedDate">Marcar como facturado </label>
                    <input type="checkbox" id="billedDate" name="billedDate" wire:model="billed">
                </div>
            @endif
            <div style="display: {{ $billRequired ? '' : 'none' }};">
                <select name="usos" id="usoscfdi" class="form-control" wire:model.defer="usoCfdi">
                    <option value="">Seleccione una opción</option>
                    @foreach ($usos as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @error('usoCfdi') <span class="text-danger">*Seleccione un uso de cfdi</span> @enderror
        @if(isset($customerId) && $customer!==null)
            <div class="mt-3" style="display: {{ $billRequired ? '' : 'none' }};">
                <a id="tax_data_modal" wire:click="editTaxData">{{ $customer && count($customer->datosFacturacion) > 0  ? 'Consultar' : 'Agregar' }} datos fiscales</a>
            </div>
        @endif
        <livewire:clientes :action="3"/>
    </div>
    @include('livewire.cupones.searchCuponForm')
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
        background-color: #9D1466;
    }

    .search-area .input-group-append .input-group-text i {
        font-size: 16px !important;
    }
</style>

<script>
document.addEventListener('livewire:load', function () {
    Livewire.on('reseñaCliente',function(){
        abrirReview()
    })
})
function abrirReview() {
    $('#reviewModal').modal('show')
}

window.addEventListener('next', event => {  
    driverObj.moveNext();
})

</script>