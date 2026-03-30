<div wire:ignore.self id="modalMerge" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" wire:click="cancelMerge">×</button>
                <h4 class="modal-title">Unión de Servicios</h4>
            </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            Elige la información definitiva que quieres almacenar del servicio. Lo que no se seleccione se perderá permanentemente.
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
                                                @error('service.name') <span class="text-danger">*Corrige este campo* </span> @enderror 
                                            </td>
                                
                                            @foreach($mergeItems as $item)
                                                <td class="scroll-text">
                                                    <input class="form-check-input" type="radio" name="name" wire:model.defer="service.name" value="{{ $item->name ?? $item['name'] }}" id="name{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="name{{$item->id ?? $item['id']}}">{{ $item->name ?? $item['name'] }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Descripción
                                                @error('service.description') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td class="scroll-text">
                                                    <input class="form-check-input" type="radio" name="description" wire:model.defer="service.description" value="{{ $item->description ?? $item['description'] }}" id="description{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="description{{$item->id ?? $item['id']}}">{{ $item->description ?? $item['description'] }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Precio público
                                                @error('service.gross_price') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="gross_price" wire:model.defer="service.gross_price" value="{{ $item->gross_price ?? $item['gross_price'] }}" id="gross_price{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="gross_price{{$item->id ?? $item['id']}}">${{ number_format($item->gross_price ?? $item['gross_price'],2,'.',',') }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Precio descuento
                                                @error('service.disccount_price') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="disccount_price" wire:model.defer="service.disccount_price" value="{{ $item->disccount_price ?? $item['disccount_price'] }}" id="disccount_price{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="disccount_price{{$item->id ?? $item['id']}}">${{ number_format($item->disccount_price ?? $item['disccount_price'],2,'.',',') }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>IVA
                                                @error('service.iva') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="iva" wire:model.defer="service.iva" value="{{ $item->iva ?? $item['iva'] }}" id="iva{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="iva{{$item->id ?? $item['id']}}">{{ ($item->iva ?? $item['iva'])*100 }}%</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Proveedor
                                                @error('service.brand_id') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="brand_id" wire:model.defer="service.brand_id" value="{{ $item->brand_id ?? $item['brand_id'] }}" id="brand_id{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="brand_id{{$item->id ?? $item['id']}}">{{ $item->marca?->name ?? $item['marca']['name'] }}</label>
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Duración
                                                @error('service.duration') <span class="text-danger">*Corrige este campo* </span> @enderror
                                            </td>
                                            @foreach($mergeItems as $item)
                                                <td>
                                                    <input class="form-check-input" type="radio" name="duration" wire:model.defer="service.duration" value="{{ $item->duration ?? $item['duration'] }}" id="duration{{$item->id ?? $item['id']}}">
                                                    <label class="form-check-label" for="duration{{$item->id ?? $item['id']}}">{{ $item->duration ?? $item['duration'] }} min.</label>
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