
<div class="card">
    <div class="card-header">
        <h4 style="color:#1d3557">{{ $editing ? 'Editar Gasto' : 'Crear Gasto' }}</h4>
    </div>
    <div class="card-body form-panel">
        <div class="form-group">
            @if(isset($type) && $type!==null)
                <div class="tag" style="width:20rem">
                    <span class="tag-name">{{ $type->name }}</span>
                    <input type="button" value="x" class="remove-tag" wire:click="unsetType">
                </div>
                
                <label>Tipo de gasto </label>
            @else
                <div x-data="{ open: false }" @click.away="open = false">
                    <div style="display:flex">
                        <input wire:model="queryType" @focus="open=true" @click="open = true" type="text" class="form-control" autocomplete="off" placeholder="Escriba el tipo de gasto"> 
                        
                    </div>
                    
                    <!-- Desplegable de resultados -->
                    <div>
                        <ul x-show="open" class="list-group float-right" style="width:100%; position: relative; z-index:1; max-height: 300px; overflow-y: auto;">
                        @foreach ($tipos as $index => $item)
                            <li wire:click="setTypeId({{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action" style="cursor:pointer; color:#6E6E6E">{{ $item->name }}</li>
                        @endforeach
                        <li wire:click="createType" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">Crear '{{$queryType}}'</li>
                        </ul>
                    </div>
                </div>

                <label>Tipo de gasto </label>
                <!-- <a class="float-right" wire:click="$emit('openBrandModal')" style="text-decoration:underline;cursor:pointer">Crear proveedor</a> -->
            @endif
        </div>
        <div class="form-group">
            <input wire:model.defer="gasto.note" class="form-control" placeholder="Descripción">
        @error('gasto.note') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Descripción</label>
        </div>
        
        <div class="form-group">
                
            @if(isset($category) && $category!==null)
                <div class="tag" style="width:20rem">
                    <span class="tag-name">{{ $category->name }}</span>
                    <input type="button" value="x" class="remove-tag" wire:click="unsetCategory">
                </div>
                
                <label>Categoría </label>
            @else
                <div x-data="{ open: false }" @click.away="open = false">
                    <div style="display:flex">
                        <input wire:model="queryCat" @focus="open=true" @click="open = true" type="text" class="form-control" autocomplete="off" placeholder="Escriba la categoría"> 
                        
                    </div>
                    
                    <!-- Desplegable de resultados -->
                    <div>
                        <ul x-show="open" class="list-group float-right" style="width:100%; position: relative; z-index:1; max-height: 300px; overflow-y: auto;">
                        @foreach ($categorias as $index => $item)
                            <li wire:click="setCategoryId({{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action" style="cursor:pointer; color:#6E6E6E">{{ $item->name }}</li>
                        @endforeach
                        <li wire:click="createCat" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">Crear '{{$queryCat}}'</li>
                        </ul>
                    </div>
                </div>

                <label>Categoría </label>
                <!-- <a class="float-right" wire:click="$emit('openBrandModal')" style="text-decoration:underline;cursor:pointer">Crear proveedor</a> -->

            @endif
        </div>

        <div class="form-group">
            <select wire:model.prevent="gasto.type" class="form-control ">
                <option value="Acreditable">Deducible</option>
                <option value="No acreditable">No deducible</option>
            </select>
            <label>Clasif. fiscal</label>
        </div>
        
        <div style="display: {{$gasto->type=='Acreditable' ? 'block' : 'none'}};">
            <div class="form-group">
                <input id="folioInput" wire:model.defer="gasto.folio_fiscal" type="text" class="form-control" placeholder="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX" maxlength="36" oninput="FolioOnInput()">
                @error('gasto.folio_fiscal') <span class="text-danger">*Corrige este campo* </span> @enderror
                <label for="folioInput">Folio Fiscal</label>
            </div>

            <script>
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

            <div class="form-group">
                
                @if(isset($brand) && $brand!==null)
                    <div class="tag" style="width:20rem">
                        <span class="tag-name">{{ $brand->name }} | {{ $brand->rfc }}</span>
                        <input type="button" value="x" class="remove-tag" wire:click="unsetBrand">
                    </div>
                    <label>Emisor </label>
                @else
                    <div x-data="{ open: false }" @click.away="open = false">
                        <div style="display:flex">
                            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                            
                            <input wire:model="query" @focus="open=true" @click="open = true" type="text" class="form-control" autocomplete="off" placeholder="RFC o nombre de proveedor"> 
                            
                        </div>
                        
                        <!-- Desplegable de resultados -->
                        <div>
                            <ul x-show="open" class="list-group float-right" style="width:100%; position: relative; z-index:1; max-height: 300px; overflow-y: auto;">
                            @foreach ($proveedores as $index => $item)
                                <li wire:click="$emit('setBrandId', {{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action" style="cursor:pointer; color:#6E6E6E">{{ $item->name }} {{ $item->rfc ? '| ' . $item->rfc : ''}}</li>
                            @endforeach 
                            </ul>
                        </div>
                    </div>


                    <label>Emisor </label>
                    <a class="float-right" onclick="openBrandModal()" style="text-decoration:underline;cursor:pointer">Crear proveedor</a>

                @endif
            </div>
            
            <div class="form-group">
                <select wire:model.defer="gasto.iva" class="form-control">
                    <option value=0.16>16%</option>
                    <option value=0.08>8%</option>
                    <option value=0>Exento</option>
                </select>
                <label>IVA</label>
                @error('gasto.iva') <span class="text-danger">*Corrige este campo* </span> @enderror
            </div>
            
        </div>

        <div class="form-group">
            <input wire:model.defer="gasto.total" type="number" class="form-control"
                placeholder="Monto">
        @error('gasto.total') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Monto</label>
        </div>
        <div class="form-group">
            <input wire:model.defer="gasto.date"  class="form-control" type="date">
            @error('gasto.date') <span class="text-danger">*Corrige este campo* </span> @enderror
            <label>Fecha</label>
        </div>
        @error('gasto.date') <span class="text-danger">*Corrige este campo* </span> @enderror
        <div class="form-group">
            <select class="form-control" id="formaPago" wire:model.defer="gasto.payment_method">
                <option value="">Seleccione una opción</option>
                <option value="Caja chica">00 Caja chica</option>
                <option value="Efectivo">01 Efectivo</option>
                <option value="Cheque nominativo">02 Cheque nominativo</option>
                <option value="Transferencia electrónica de fondos">03 Transferencia electrónica de fondos</option>
                <option value="Tarjeta de crédito">04 Tarjeta de crédito</option>
                <option value="Monedero electrónico">05 Monedero electrónico</option>
                <option value="Dinero electrónico">06 Dinero electrónico</option>
                <option value="Vales de despensa">08 Vales de despensa</option>
                <option value="Dación en pago">12 Dación en pago</option>
                <option value="Pago por subrogación">13 Pago por subrogación</option>
                <option value="Pago por consignación">14 Pago por consignación</option>
                <option value="Condonación">15 Condonación</option>
                <option value="Compensación">17 Compensación</option>
                <option value="Novación">23 Novación</option>
                <option value="Confusión">24 Confusión</option>
                <option value="Remisión de deuda">25 Remisión de deuda</option>
                <option value="Prescripción o caducidad">26 Prescripción o caducidad</option>
                <option value="A satisfacción del acreedor">27 A satisfacción del acreedor</option>
                <option value="Tarjeta de débito">28 Tarjeta de débito</option>
                <option value="Tarjeta de servicios">29 Tarjeta de servicios</option>
                <option value="Aplicación de anticipos">30 Aplicación de anticipos</option>
                <option value="Intermediario pagos">31 Intermediario pagos</option>
                <option value="Por definir">99 Por definir</option>
            </select>
            <label for="formaPago">Forma de pago</label>
        @error('gasto.payment_method') <span class="text-danger">*Corrige este campo* </span> @enderror
        </div>
        <div class="form-group">
            <input type="file" class="form-control" style="border:transparent;" id="input-file" wire:model="gallery" accept="image/x-png,image/jpeg,.pdf" multiple id="inputImg">
        </div>
                
        @error('gallery.*')
        <span style="color: red;">{{ $message }}</span>
        @enderror
        
        
        <div wire:loading wire:target="gallery">Cargando imágenes...</div>
        @if ($gallery!=null)
        <div class="row">
            @foreach ($gallery as $photo)
                <div class="col-6 col-sm-4">
                    <div class="media">
                        @if (in_array($photo->getMimeType(), ['image/jpeg', 'image/png']))
                            <img src="{{ $photo->temporaryUrl() }}" class="img-fluid rounded" alt="img">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 right-0"  wire:click="removeFile('{{ $photo->getFilename() }}',1)">x</button>
                        @else
                            <p>{{ $photo->getClientOriginalName() }}</p>
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 right-0"  wire:click="removeFile('{{ $photo->getFilename() }}')">x</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        @endif
        @if($pictures != null)
        <div class="row">
            @foreach ($pictures as $photo)
                <div class="col-6 col-sm-4">
                    <img src="{{ $photo }}" class="img-fluid rounded" alt="Archivo pdf">
                    <button type="button" class="btn btn-danger btn-sm float-right"  wire:click="removeFile('{{ $photo }}',0)">x</button>
                    <button type="button" onclick="openPath('{{ asset($photo) }}')" class="btn btn-secondary btn-sm float-right"><i class="las la-eye"></i></button>
                </div>
            @endforeach
        </div>
        @endif
    </div>
    <div class="card-footer">
        <button class="btn btn-sm btn-dark light float-left hidden {{$editing ? 'd-block' : 'd-none' }}"
            wire:click="cancelEdit">Cancelar</button>
        <button class="btn btn-sm btn-info float-right save" wire:click="Store" >Guardar</button>
    </div>
</div>

<script>
    
    function openBrandModal(){
        $('#modalBrandForm').modal('show')
    }
    
    window.addEventListener('closeModalBrand',event=>{
        $('#modalBrandForm').modal('hide')
    })
</script>