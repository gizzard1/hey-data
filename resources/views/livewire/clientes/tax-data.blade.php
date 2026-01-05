<div wire:ignore.self id="taxDataModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-third">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title" style="color: #60060F;text-align:center">Datos de facturación</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="d-flex">
                        <div class="col">
                            <div class="form-group">
                                <label>Razón social*</label>
                                <input wire:model.defer="tax_data.company_name" type="text" class="form-control" placeholder="Razón social o nombre" autocomplete="nope">
                                @error('tax_data.company_name') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Régimen fiscal*</label>
                                <select wire:model.defer="tax_data.tax_system" class="form-control">
                                    <option value="">Seleccione una opción</option>
                                    @foreach($tax_systems as $value => $label)
                                        <option value="{{ $value }}">{{$value}} | {{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('tax_data.tax_system') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="col">
                            <div class="form-group">
                                <label>RFC*</label>
                                <input wire:model.defer="tax_data.rfc" type="text" class="form-control" placeholder="RFC">
                                @error('tax_data.rfc') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Código postal*</label>
                                <input wire:model.defer="tax_data.postcode" type="number" class="form-control" placeholder="Código postal">
                                @error('tax_data.postcode') <span class="text-danger">*Corrige este campo</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="col">
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input wire:model.defer="tax_data.phone" type="number" class="form-control" placeholder="Teléfono">
                                @error('tax_data.phone') <span class="text-danger">*Corrige este campo</span> @enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Email</label>
                                <input wire:model.defer="tax_data.email" type="email" class="form-control" placeholder="Email">
                                @error('tax_data.email') <span class="text-danger">*Corrige este campo</span> @enderror
                            </div>
                        </div>
                    </div>
                
                    <div class="mt-5 d-flex " style="column-gap:1rem;justify-content:end">
                        <button class="btn btn-sm float-right" data-dismiss="modal">Cancelar</button>
                        <button id="save-info" class="save btn btn-sm btn-info float-right" {{ $editingTaxData ? 'wire:click=updateDelivery':'wire:click=saveDelivery' }}>Guardar</button>
                    </div>
                </div>

                @if(isset($customerSelected->datosFacturacion))
                <div class="mt-4">
                    <div class="col">
                        <div class="table-responsive">
                            <table class="table table-responsive-md text-center">
                                <thead style="background-color: black;">
                                    <tr>
                                        <th></th>
                                        <th style="color: white;">Razón social</th>
                                        <th style="color: white;">RFC</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody style="text-transform: uppercase;">
                                    @if($customerSelected !=null)
                                        @foreach ($customerSelected->datosFacturacion as $data)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" wire:click="$emit('rfcSelected',{{ $data->id }})" data-dismiss="modal" {{ session()->has('rfcSelected') && session('rfcSelected') == $data->id ? 'checked' : ''}}>
                                                    
                                                </td>
                                                <td><a wire:click.prevent="editDelivery({{ $data->id }})">{{$data->company_name}}</a></td>
                                                <td><a wire:click.prevent="editDelivery({{ $data->id }})">{{$data->rfc}}</a></td>
                                                <td><a wire:click.prevent="removeDelivery({{ $data->id }})">Eliminar</a></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>