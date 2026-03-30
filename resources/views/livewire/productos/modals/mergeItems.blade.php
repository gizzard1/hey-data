<div wire:ignore.self id="modalMerge" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" wire:click="cancelMerge">×</button>
                <h4 class="modal-title">Unión de Productos</h4>
            </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            Elige la información definitiva que quieres almacenar del producto. Lo que no se seleccione se perderá permanentemente.
                            <br>
                            @if(count($mergeItems)==3 || count($mergeItems)==2)
                            <div class="table-responsive">
                                <table class="table table-responsive-md table-hover text-left">
                                    <thead class="thead-primary">
                                        <tr>
                                            <th style="background-color:transparent;color:#1D3557 !important"></th>
                                            @foreach($mergeItems as $item)
                                                <th style="background-color:transparent;color:#1D3557 !important">{{ $item->name ?? $item['name'] }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Nombre 
                                                @error('product.name') <span class="text-danger">*Corrige este campo* </span> @enderror 
                                            </td>
                                
                                            @foreach($mergeItems as $item)
                                                <td class="scroll-text">
                                                    <input class="form-check-input" type="radio" name="name" wire:model.defer="product.name" value="{{ $item->name ?? $item['name'] }}" id="name{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="name{{$item->id ?? $item['id']}}">{{ $item->name ?? $item['name'] }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Descripción
                                                @error('product.description') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td class="scroll-text">
                                                    <input class="form-check-input" type="radio" name="description" wire:model.defer="product.description" value="{{ $item->description ?? $item['description'] }}" id="description{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="description{{$item->id ?? $item['id']}}">{{ $item->description ?? $item['description'] }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Precio público
                                                @error('product.gross_price') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="gross_price" wire:model.defer="product.gross_price" value="{{ $item->gross_price ?? $item['gross_price'] }}" id="gross_price{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="gross_price{{$item->id ?? $item['id']}}">${{ number_format($item->gross_price ?? $item['gross_price'],2,'.',',') }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Precio descuento
                                                @error('product.disccount_price') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="disccount_price" wire:model.defer="product.disccount_price" value="{{ $item->disccount_price ?? $item['disccount_price'] }}" id="disccount_price{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="disccount_price{{$item->id ?? $item['id']}}">${{ number_format($item->disccount_price ?? $item['disccount_price'],2,'.',',') }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>IVA
                                                @error('product.iva') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="iva" wire:model.defer="product.iva" value="{{ $item->iva ?? $item['iva'] }}" id="iva{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="iva{{$item->id ?? $item['id']}}">{{ ($item->iva ?? $item['iva'])*100 }}%</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Proveedor
                                                @error('product.brand_id') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="brand_id" wire:model.defer="product.brand_id" value="{{ $item->brand_id ?? $item['brand_id'] }}" id="brand_id{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="brand_id{{$item->id ?? $item['id']}}">{{ $item->marca?->name ?? $item['marca']['name'] }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Costo
                                                @error('product.cost') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="cost" wire:model.defer="product.cost" value="{{ $item->cost ?? $item['cost'] }}" id="cost{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="cost{{$item->id ?? $item['id']}}">${{ number_format($item->cost ?? $item['cost'],2,'.',',') }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Sku
                                                @error('product.sku') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="sku" wire:model.defer="product.sku" value="{{ $item->sku ?? $item['sku'] }}" id="sku{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="sku{{$item->id ?? $item['id']}}">{{ $item->sku ?? $item['sku'] }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Tipo de unidad
                                                @error('product.unit_type') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="unit_type" wire:model.defer="product.unit_type" value="{{ $item->unit_type ?? $item['unit_type'] }}" id="unit_type{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="unit_type{{$item->id ?? $item['id']}}">{{ $item->unit_type ?? $item['unit_type'] }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Tipo
                                                @error('product.type_product') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="type_product" wire:model.defer="product.type_product" value="{{ $item->type_product ?? $item['type_product'] }}" id="type_product{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="type_product{{$item->id ?? $item['id']}}">{{ ($item->type_product ?? $item['type_product']) == 'simple' ? 'Mercancía' : 'Uso' }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Existencias
                                                @error('product.stock_qty') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="stock_qty" wire:model.defer="product.stock_qty" value="{{ $item->stock_qty ?? $item['stock_qty'] }}" id="stock_qty{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="stock_qty{{$item->id ?? $item['id']}}">{{ number_format($item->stock_qty ?? $item['stock_qty'],2) }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Stock mínimo
                                                @error('product.min_stock') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="min_stock" wire:model.defer="product.min_stock" value="{{ $item->min_stock ?? $item['min_stock'] }}" id="min_stock{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="min_stock{{$item->id ?? $item['id']}}">{{ number_format($item->min_stock ?? $item['min_stock'],2) }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            <div class="modal-footer">
                <button class="btn input-group-text" wire:click="merge" data-dismiss="modal">Unir</button>
                <button type="button" class="btn btn-dark" data-dismiss="modal" wire:click="cancelMerge">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@include('livewire.clientes.modals.mergeStyles')
@include('livewire.servicios.modals.mergePStyles')