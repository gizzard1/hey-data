<div wire:ignore.self id="modalException" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Agregar excepción</h4>
            </div>
            <div class="modal-body">
                <div x-data="{ open: false }" @click.away="open = false">
                    <div style="display:flex">
                        <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                        <input wire:model="query" @focus="open=true" @keydown.escape.window="open=false" type="text" class="form-control" autocomplete="off">
                            
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
                        @foreach ($listado as $index => $item)
                        @if($tipo!=5)
                            <li wire:click="selectedItem({{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action">{{ $item->name }}</li>
                        @else
                            <li wire:click="selectedItem({{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action">{{ $item->first_name }} {{ $item->last_name }} {{ $item->phone ? '| ' . $item->phone : '' }}</li>
                        @endif
                        @endforeach
                        </ul>
                    </div>
                    <label>Nombre</label>

                </div>

                <div class="form-group d-flex" style="column-gap:1rem">
                    <div>
                        <select wire:model='tipoExc' class="form-control" style="width:14rem">
                            <option value="percent">%</option>
                            <option value="qty">$</option>
                        </select>
                        @error('tipoExc') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <label>Tipo</label>
                    </div>
                    <div>
                        <input wire:model="qty" type="text" class="form-control">
                        @error('qty') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <label>Cantidad</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-dark float-left  mb-3" style="background-color: transparent;color:#1d3557" wire:click.prevent="cancel" data-dismiss="modal">Cancelar</button>
                <button id="save-button-2" class="btn btn-sm btn-info float-right save  mb-3" wire:click.prevent="StoreException" style="background-color:#1d3557; border-color:#B59377">Guardar</button>
            </div>
        </div>
    </div>
</div>
