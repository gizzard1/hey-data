
<tr class="text-center">
    <td>
        <input list="times-start" name="time-start" type="time" value="{{ $item['start'] }}" wire:change.debounce.350ms="$emit('changeStartDuration','{{ $item['id'] }}', $event.target.value)" style="text-align:center;background-color: transparent; border-color: transparent;">
        <datalist id="times-start">
            @foreach($times as $time)
                <option value="{{ $time }}">{{ $time }}</option>
            @endforeach
        </datalist>
    </td>
    <td>
        <input list="times-end" name="time-end" type="time" value="{{ $item['end'] }}" wire:change.debounce.350ms="$emit('changeEndDuration','{{ $item['id'] }}', $event.target.value)" style="text-align:center;background-color: transparent; border-color: transparent;">
        <datalist id="times-end">
            @foreach($times as $time)
                <option value="{{ $time }}">{{ $time }}</option>
            @endforeach
        </datalist>
    </td>
    <td>{{ $item['name'] }}</td>
    <td>
      <select wire:change.prevent="$emit('changeEmpleado','servicio','{{ $item['id'] }}', $event.target.value)" style="background-color: transparent;border-color:transparent;">
        @if(!isset($item['vendedor']))
            <option value="null">Seleccione un empleado</option>
        @endif
        @foreach($empleados as $empleado)
            <option style="text-align: center;" value="{{ $empleado->id }}" {{ $item['vendedor'] == $empleado->id ? 'selected' : '' }}>
                {{ $empleado->first_name }}
            </option>
        @endforeach
        </select>
    </td>
    <td>
      @include('livewire.calendar.dropdown.color-cart')

    </td>
    <td>
        <button wire:click.prevent="$emit('deleteItem', '{{ $item['id'] }}','servicio' )"
            class="btn tp-btn btn-xxs btn-danger "><i class="fa fa-trash fa-lg"></i></button>
    </td>
</tr>

<style>
    
  .input-container {
    position: relative;
  }
  .input-container input {
    padding-right: 20px; /* Espacio para el signo de porcentaje */
  }
  .percent-sign {
    position: absolute;
    right: 24px; /* Ajusta según necesites */
    top: 50%;
    transform: translateY(-50%);
  }
  .table td{
    padding:0
  }
</style>