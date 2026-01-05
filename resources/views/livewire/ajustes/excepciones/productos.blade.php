<table table class="table table-striped table-responsive-sm">
    @if($comision)
        <thead>
            <tr>
                <td><strong>Producto</strong></td>
                <td><strong>Precio</strong></td>
                <td><strong>Cantidad</strong></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            @forelse($comision->excepcion_producto as $excepcion)
                <tr>
                    <td>{{ $excepcion->producto->name }}</td>
                    <td>{{ $excepcion->producto->gross_price }}</td>

                    @if($editing && $exceptionToEdit == $excepcion->id && $tipo==1)
                        <td>
                            <div class="d-flex">
                                <select wire:model='tipoExc' class="form-control w-auto">
                                    <option value="percent" {{ $tipoExc === 'percent' ? 'selected' : '' }}>%</option>
                                    <option value="qty" {{ $tipoExc === 'qty' ? 'selected' : '' }}>$</option>
                                </select>
                                @error('tipoExc') <span class="text-danger">*Corrige este campo* </span> @enderror
                                <input wire:model="qty" type="number" class="form-control w-auto">
                                @error('qty') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                        </td>
                    @else
                        <td>@if($excepcion->type_comission==='percent') {{ number_format($excepcion->qty,2,'.') }}% @else ${{ number_format($excepcion->qty,2,'.',',') }} @endif</td>
                    @endif
                    <td>
                    @if($editing && $exceptionToEdit == $excepcion->id && $tipo==1)
                        <button title="Guardar" class="btn tp-btn btn-sm btn-success" wire:click="StoreException"><i class="las la-save la-2x"></i></button>
                        <button title="Eliminar" class="btn tp-btn btn-sm btn-danger" onclick="confirmDelete('{{ $excepcion->id }}',1)"><i class="las la-trash-alt la-2x"></i></button>
                        <button title="Cancelar" class="btn tp-btn btn-sm btn-dark" wire:click="editException('{{ $excepcion->id }}',1)">x</button>
                    @else
                        <button title="Editar" class="btn tp-btn btn-sm btn-warning" wire:click="editException('{{ $excepcion->id }}',1)"><i class="las la-pen la-2x"></i></button>
                    @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" >No hay excepciones para productos almacenadas</td>
                </tr>
            @endforelse
        </tbody>
        <thead>
            <tr>
                <th colspan="2"><strong>Categoría</strong></th>
                <th colspan="2"><strong>Cantidad</strong></th>
            </tr>
        </thead>
        <tbody>
            @forelse($comision->excepcion_cat_producto as $excepcion)
                <tr>
                    <td colspan="2">{{ $excepcion->cat_producto?->name }}</td>
                    <td>
                    @if($editing && $exceptionToEdit == $excepcion->id && $tipo==3)
                        <div class="d-flex">
                            <select wire:model='tipoExc' class="form-control w-auto">
                                <option value="percent" {{ $tipoExc === 'percent' ? 'selected' : '' }}>%</option>
                                <option value="qty" {{ $tipoExc === 'qty' ? 'selected' : '' }}>$</option>
                            </select>
                            @error('tipoExc') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <input wire:model="qty" type="number" class="form-control w-auto">
                            @error('qty') <span class="text-danger">*Corrige este campo* </span> @enderror
                        </div>
                    @else
                        @if($excepcion->type_comission==='percent') {{ number_format($excepcion->qty,2,'.') }}% @else ${{ number_format($excepcion->qty,2,'.',',') }} @endif
                    @endif
                    </td>
                    <td>
                    @if($editing && $exceptionToEdit == $excepcion->id && $tipo==3)
                        <button title="Guardar" class="btn tp-btn btn-sm btn-success" wire:click="StoreException"><i class="las la-save la-2x"></i></button>
                        <button title="Eliminar" class="btn tp-btn btn-sm btn-danger" onclick="confirmDelete('{{ $excepcion->id }}',3)"><i class="las la-trash-alt la-2x"></i></button>
                        <button title="Cancelar" class="btn tp-btn btn-sm btn-dark" wire:click="editException('{{ $excepcion->id }}',3)">x</button>
                    @else
                        <button title="Editar" class="btn tp-btn btn-sm btn-warning" wire:click="editException('{{ $excepcion->id }}',3)"><i class="las la-pen la-2x"></i></button>
                    @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hay excepciones para categorías almacenadas</td>
                </tr>
            @endforelse
            <tr id="add-exception-a">
                <td colspan="2">
                    <a wire:click="Add(1)" id="ind_comision_p" onclick="next()">Añadir una excepción para productos</a>
                </td>
                <td colspan="2">
                    <a wire:click="Add(3)">Añadir una excepción para una categoría de productos</a>
                </td>
            </tr>
        </tbody>
    @endif
</table>