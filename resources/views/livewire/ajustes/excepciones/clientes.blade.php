<table table class="table table-striped table-responsive-sm">
    @if($comision)
        <thead>
            <tr>
                <th style="color:#1d3557">Cliente</th>
                <th style="color:#1d3557">Cantidad</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($comision->excepcion_cliente as $excepcion)
                <tr>
                    <td>{{ $excepcion->cliente->first_name }} {{ $excepcion->cliente->last_name }} | {{ $excepcion->cliente->phone }}</td>
                    <td>@if($excepcion->type_comission==='percent') {{ number_format($excepcion->qty,2,'.') }}% @else ${{ number_format($excepcion->qty,2,'.',',') }} @endif</td>
                    <td>
                    <button title="Eliminar" class="btn tp-btn btn-sm btn-danger" onclick="confirmDelete({{ $excepcion->id }},5)"><i class="las la-trash-alt la-2x"></i></button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hay excepciones para clientes almacenadas</td>
                </tr>
            @endforelse
        <thead>
            <tr>
                <th style="color:#1d3557">Categoría</th>
                <th style="color:#1d3557">Cantidad</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($comision->excepcion_cat_cliente as $excepcion)
                <tr>
                    <td>{{ $excepcion->cat_cliente?->name }}</td>
                    <td>@if($excepcion->type_comission==='percent') {{ number_format($excepcion->qty,2,'.') }}% @else ${{ number_format($excepcion->qty,2,'.',',') }} @endif</td>
                    <td>
                    <button title="Eliminar" class="btn tp-btn btn-sm btn-danger" onclick="confirmDelete({{ $excepcion->id }},6)"><i class="las la-trash-alt la-2x"></i></button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hay excepciones para categorías almacenadas</td>
                </tr>
            @endforelse
            <tr id="add-exception-a">
                <td colspan="2">
                    <a wire:click="Add(5)">Añadir una excepción para clientes</a>
                </td>
                <td colspan="2">
                    <a wire:click="Add(6)">Añadir una excepción para una categoría de clientes</a>
                </td>
            </tr>
        </tbody>
    </tbody>
    @endif
</table>