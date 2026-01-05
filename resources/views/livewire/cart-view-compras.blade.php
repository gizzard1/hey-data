<div>
    <div class="card">
        <div class="card-header flex-wrap">
            <div>
                <button wire:click="$emit('createProductFromCompras','1')"  data-toggle="modal" data-target="#modalCreateForm" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Crear Nuevo Producto</button>
            </div>
            <div>
                <button wire:click="clear" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Limpiar carrito</button>
            </div>

            <div x-data="{ open: false }" @click.away="open = false">
                <div style="display:flex">
                    <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                    <input style="width:30rem" wire:model="query" @focus="open=true" @click="open = true" type="text" class="form-control" autocomplete="off" placeholder="Escriba el nombre del producto"> 
                    
                    <!-- Icono de búsqueda -->
                    <div class="input-group-append">
                        <i style="background-color: white;" class="input-group-text"><i class="flaticon-381-search-2"></i></i>
                    </div>
                </div>
                
                <!-- Desplegable de resultados -->
                <div>
                    <ul x-show="open" class="list-group float-right" style="width: 30rem; position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                    @foreach ($productos as $index => $item)
                        <li wire:click="$emit('add-product', {{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action" style="cursor:pointer; color:#6E6E6E">{{ $item->name }}</li>
                    @endforeach 
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-1">
            @if(isset($cartInfo)!==null)
                @include('livewire.compras.cartProducts')
            @endif
        </div>
        
    </div>
</div>
<div class="row" style="padding: 4dvh 8dvh 4dvh 4dvh;">
    <div class="col-md-6">
        <div class="form-group">
            @if(isset($marca_id))
            <div  id="activate-reward" style="display: flex; padding:2rem;column-gap: inherit;padding-top:0">
                <div class="tag">
                    <span class="tag-name">{{ $marca->name }} {{ $marca->rfc !=null ? '| ' . $marca->rfc : '' }}</span>
                    <input type="button" value="x" class="remove-tag" wire:click="unsetMarca">
                </div>
            </div>
            @else
                <div class="form-group">
                    <label for="marcaInput">Proveedor</label>
                    <a class="float-right"  wire:click="$emit('openBrandModal')" style="text-decoration:underline;cursor:pointer">Crear proveedor</a>
                    
                    <div x-data="{ open: false }" @click.away="open = false">
                        <div style="display:flex">
                            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                            <input style="width:30rem" id="marcaInput" onclick="pause()" wire:model="queryMarcas" @focus="open=true" @click="open = true" type="text" class="form-control" autocomplete="off" placeholder="Buscar un proveedor"> 

                            <!-- Icono de búsqueda -->
                            <div class="input-group-append">
                                <i style="background-color: white;" class="input-group-text"><i class="flaticon-381-search-2"></i></i>
                            </div>
                        </div>
                        
                        <!-- Desplegable de resultados -->
                        <div>

                            <ul x-show="open" class="list-group float-right" style="width: 30rem; position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                                @foreach ($marcas as $index => $item)
                                    <li onclick="play_1()" wire:click="$emit('setMarcaId', {{ $item->id }})" @click="open = false;" class="list-group-item list-group-item-action" style="font-weight: lighter; cursor: pointer; color: #6E6E6E;">{{ $item->name }} {{ $item->contact_name!=null ? '| ' . $item->contact_name : '' }} {{ $item->rfc !=null ? '| ' . $item->rfc : '' }}</li>
                                @endforeach 
                            </ul>
                        </div>
                    </div>



                </div>
            @endif

        </div>
        
        <div class="form-group">
            <label for="interFolioInput">Folio Interno</label>
            <input id="interFolioInput" wire:model.prevent="folio_interno" type="text" class="form-control" placeholder="Escribe el folio de la factura" maxlength="36">
            @error('folio_interno') <span class="text-danger">*Corrige este campo* </span> @enderror
        </div>  
        <div class="form-group">
            <label for="folioInput">Folio Fiscal</label>
            <input id="folioInput" wire:model.prevent="folio_fiscal" type="text" class="form-control" placeholder="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX" maxlength="36" oninput="FolioOnInput()">
            @error('folio_fiscal') <span class="text-danger">*Corrige este campo* </span> @enderror
        </div>  
    </div>
    <div class="col-md-6">
        
    <div class="form-group">
            <label for="comentariosInput">Comentarios</label>
            <textarea id="comentariosInput" wire:model.prevent="comentarios" type="text" class="form-control" placeholder="Escribe algún recordatorio..." maxlength="100" style="resize: none;height:13rem"></textarea>
            @error('comentarios') <span class="text-danger">*Corrige este campo* </span> @enderror
        </div>  
        

    </div>
</div>

<div class="card-footer" style="background-color:transparent;height: 4rem">
    <input value="Guardar" type="button" class="float-right btn btn-info ml-5 save" style="background-color:#9E846D;border-color:#9E846D" wire:loading.attr="disabled" wire:click.prevent="Store">
</div>

<script>
    window.addEventListener('openBrandModal',event=>{
            $('#modalBrandForm').modal('show')
        })
    const folioInput = document.getElementById("folioInput");

    folioInput.addEventListener("input", function(event) {
        let input = event.target.value;

        // Remove all non-alphanumeric characters
        input = input.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();

        // Add hyphens to the appropriate positions
        if (input.length > 8) {
            input = input.slice(0, 8) + '-' + input.slice(8);
        }
        if (input.length > 13) {
            input = input.slice(0, 13) + '-' + input.slice(13);
        }
        if (input.length > 18) {
            input = input.slice(0, 18) + '-' + input.slice(18);
        }
        if (input.length > 23) {
            input = input.slice(0, 23) + '-' + input.slice(23);
        }

        // Set the formatted input back to the field
        event.target.value = input;
    });
    
</script>