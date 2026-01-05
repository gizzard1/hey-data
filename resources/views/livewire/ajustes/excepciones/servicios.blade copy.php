<table table class="table table-striped table-responsive-sm">
    @if($comision)
        <thead>
            <tr>
                <td><strong>Servicio</strong></td>
                <td><strong>Precio</strong></td>
                <td><strong>Cantidad</strong></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            @forelse($comision->excepcion_servicio as $excepcion)
                <tr>
                    <td>{{ $excepcion->servicio->name }}</td>
                    <td>{{ $excepcion->servicio->gross_price }}</td>
                    @if($editing && $exceptionToEdit == $excepcion->id)
                        <td>
                            <select wire:model='tipoExc' class="form-control">
                                <option value="percent" {{ $tipoExc === 'percent' ? 'selected' : '' }}>%</option>
                                <option value="qty" {{ $tipoExc === 'qty' ? 'selected' : '' }}>$</option>
                            </select>
                            @error('tipoExc') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <input wire:model="qty" type="number" class="form-control">
                            @error('qty') <span class="text-danger">*Corrige este campo* </span> @enderror
                        </td>
                    @else
                        <td>@if($excepcion->type_comission==='percent') {{ number_format($excepcion->qty,2,'.') }}% @else ${{ number_format($excepcion->qty,2,'.',',') }} @endif</td>
                    @endif
                    <td>
                    <button title="Editar" class="btn tp-btn btn-sm btn-warning" wire:click="editException('{{ $excepcion->id }}')"><i class="las la-pen la-2x"></i></button>
                    <button title="Eliminar" class="btn tp-btn btn-sm btn-danger" onclick="confirmDelete({{ $excepcion->id }},2)"><i class="las la-trash-alt la-2x"></i></button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hay excepciones para servicios almacenadas</td>
                </tr>
            @endforelse
        <thead>
            <tr>
                <td colspan="2"><strong>Categoría</strong></td>
                <td colspan="2"><strong>Cantidad</strong></td>
            </tr>
        </thead>
        <tbody>
            @forelse($comision->excepcion_cat_servicio as $excepcion)
                <tr>
                    <td colspan="2">{{ $excepcion->cat_servicio?->name }}</td>
                    <td>@if($excepcion->type_comission==='percent') {{ number_format($excepcion->qty,2,'.') }}% @else ${{ number_format($excepcion->qty,2,'.',',') }} @endif</td>
                    <td>
                    <button title="Editar" class="btn tp-btn btn-sm btn-warning" wire:click="editException('{{ $excepcion->id }}')"><i class="las la-pen la-2x"></i></button>
                    <button title="Eliminar" class="btn tp-btn btn-sm btn-danger" onclick="confirmDelete({{ $excepcion->id }},4)"><i class="las la-trash-alt la-2x"></i></button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hay excepciones para categorías almacenadas</td>
                </tr>
            @endforelse
            <tr id="add-exception-a">
                <td colspan="2">
                    <a wire:click="Add(2)">Añadir una excepción para servicios</a>
                </td>
                <td colspan="2">
                    <a wire:click="Add(4)">Añadir una excepción para una categoría de servicios</a>
                </td>
            </tr>
        </tbody>
    </tbody>
    @endif
</table>