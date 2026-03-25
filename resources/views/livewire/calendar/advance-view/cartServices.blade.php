
<tr>
    <td class="service-title scroll-text">{{ $item['name'] }}</td>
    <td>
        <input list="times-start" name="time-start" type="time" value="{{ $item['start'] }}" class="time-input scroll-text" wire:change.prevent="$emit('changeStartDuration','{{ $item['id'] }}', $event.target.value)">
        <datalist id="times-start">
            @foreach($times as $time)
                <option value="{{ $time }}">{{ $time }}</option>
            @endforeach
        </datalist>
    </td>
    <td>
        <input list="times-end" name="time-end" type="time" value="{{ $item['end'] }}" class="time-input scroll-text" wire:change.prevent="$emit('changeEndDuration','{{ $item['id'] }}', $event.target.value)">
        <datalist id="times-end">
            @foreach($times as $time)
                <option value="{{ $time }}">{{ $time }}</option>
            @endforeach
        </datalist>
    </td>
    <td>
        <select wire:change.prevent="$emit('changeEmpleado','servicio','{{ $item['id'] }}', $event.target.value)" class="service-title hide-text employees-title employee-selector">
            @if(!isset($item['vendedor']))
                <option value="">Seleccione un empleado</option>
            @endif
            @foreach($empleados as $empleado)
                <option value="{{ $empleado->id }}" {{ $item['vendedor'] == $empleado->id ? 'selected' : '' }}>
                    {{ $empleado->first_name }}
                </option>
            @endforeach
        </select>
    </td>
    <td class="input-container d-flex">
        <input
            wire:change="$emit('updatePercentage','servicio', '{{ $item['id'] }}', $event.target.value)"
            class="form-control text-center qty discount-input"
            value="{{ isset($item['disccount_percent']) ? $item['disccount_percent'] : '' }}">
        <!-- Select para elegir porcentaje o moneda, con flecha oculta -->
        <select class="form-control no-arrow bg-white qty discount select-discount" wire:change="updatePercentageType('servicio','{{ $item['id'] }}',$event.target.value)">
            <option value="Porcentaje" {{ $item['discount_type'] == "Porcentaje" ? 'selected' : '' }}>%</option>
            <option value="Cantidad" {{ $item['discount_type'] == "Cantidad" ? 'selected' : '' }}>$</option>
        </select>
    </td>
    <td>
        <div class="d-flex prices">
            <input
                wire:change="$emit('changeTotalCP','servicio', '{{ $item['id'] }}', $event.target.value)"
                class="form-control text-center qty" value="${{ $item['disccount_price'] ? $item['disccount_price'] : $item['sale_price'] }}">
            <input type="radio" {{ $item['base_comision'] ?  '' : 'checked' }} name="{{ $item['id'] }}" wire:click="$emit('updateBaseComision','servicio','{{ $item['id'] }}',0)">
        </div>
    </td>
    <td>
        <div class="d-flex prices">
            ${{ $item['total'] }}
            <input type="radio"{{ $item['base_comision'] ?  'checked' : '' }} name="{{ $item['id'] }}" wire:click="$emit('updateBaseComision','servicio','{{ $item['id'] }}',1)" >
        </div>
    </td>
    <td>
        <button wire:click.prevent="$emit('deleteItem', '{{ $item['id'] }}','servicio',{{ $item['sid'] }})"
            class="btn tp-btn btn-xxs btn-danger "><i class="fa fa-trash fa-lg"></i></button>
    </td>
</tr>