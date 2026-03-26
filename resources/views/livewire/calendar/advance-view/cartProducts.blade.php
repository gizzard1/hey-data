<tr>
    @if($item['pid']==99999)
    <td ><a wire:click.prevent="editarCupon('{{ $item['id'] }}')">{{ $item['name'] }}</a></td>
    @else
        <td class="service-title scroll-text" title="{{ $item['name'] }}">{{ $item['name'] }}</td>
    @endif
    <td>
        <select wire:change="updateEmpleado('{{ $item['id'] }}', $event.target.value)"  class="service-title hide-text employees-title employee-selector">
            @if(!isset($item['vendedor']))
                <option value="">Seleccione un empleado</option>
            @endif
            @foreach($empleados as $empleado)
                <option value="{{ $empleado->id }}" {{ $item['vendedor'] == $empleado->id ? 'selected' : '' }}>{{ $empleado->first_name }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input
            wire:change="updateQty( '{{ $item['id'] }}', $event.target.value)"
            class="form-control text-center qty pzs" type="number" min="1"
            value="{{ $item['qty'] }}" >
    </td>
    <td class="input-container d-flex">
        <input
            wire:change="updatePercentage( '{{ $item['id'] }}', $event.target.value)"
            class="form-control text-center qty"
            value="{{ isset($item['disccount_percent']) ? $item['disccount_percent'] : '' }}">

        <!-- Select para elegir porcentaje o moneda, con flecha oculta -->
        <select class="form-control no-arrow bg-white qty discount" style="width: 3rem;" wire:change="updatePercentageType('{{ $item['id'] }}',$event.target.value)">
            <option value="Porcentaje" {{ $item['discount_type'] == "Porcentaje" ? 'selected' : '' }}>%</option>
            <option value="Cantidad" {{ $item['discount_type'] == "Cantidad" ? 'selected' : '' }}>$</option>
        </select>
    </td>
    <td>
        <div class="d-flex prices">
            <input
                wire:change="updateTotalCP( '{{ $item['id'] }}', $event.target.value)"
                class="form-control text-center qty" value="${{ $item['disccount_price'] ? $item['disccount_price'] : $item['sale_price'] }}">
            <input type="radio" {{ $item['base_comision'] ?  '' : 'checked' }} name="{{ $item['id'] }}" wire:click="updateBaseComision('{{ $item['id'] }}',0)">
        </div>
    </td>
    <td>
        <div class="d-flex prices">
            ${{ number_format($item['total'] / $item['qty'],2,'.',',') }}
            <input type="radio" {{ $item['base_comision'] ?  'checked' : '' }} name="{{ $item['id'] }}" wire:click="updateBaseComision('{{ $item['id'] }}',1)" >
        </div>
    </td>
    <td>
        <div class="d-flex prices">
            ${{ number_format($item['total'],2,'.',',') }}
        </div>
    </td>
    <td>
        @if($item['pid']!=99999)
        <button wire:click.prevent="removeItemCart( '{{ $item['id'] }}' )"
            class="btn tp-btn btn-xxs btn-danger "><i class="fa fa-trash fa-lg"></i></button>
        @endif
    </td>
</tr>