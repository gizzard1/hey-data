
<tr class="text-center">
    <td>
        <i class="las la-download" style="cursor: pointer;" wire:click="mostrarMateriales({{ $item['sid'] }})"></i>
    </td>

    <td>{{ $item['name'] }}
    </td>
    <td class="text-center" >{{ $item['duration'] }} min
    </td>
    <td>
        <select wire:change.prevent="$emit('changeEmpleado','{{ $item['id'] }}', $event.target.value)" style="background-color: transparent;border-color:transparent;">
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
    <td class="input-container">
        <input
            wire:change="$emit('changePercentage', '{{ $item['id'] }}', $event.target.value)"
            class="form-control text-center" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" 
            value="{{ isset($item['disccount_percent']) ? $item['disccount_percent'] : '' }}%">

    </td>
    <td><select wire:change="$emit('changeIva','{{ $item['id'] }}', $event.target.value)" style="background-color: transparent;border-color:transparent;text-align:center;font-size:smaller">
        @foreach(['0.16' => '16%', '0.08' => '8%', '0' => 'Exento'] as $value => $label)
            <option value="{{ $value }}" {{ $item['ind_iva'] == $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select></td>
    <td>${{ number_format($item['disccount_price'] ? $item['disccount_price'] : $item['sale_price'], 2, '.', ',') }}</td>
    <td>
        <button wire:click.prevent="$emit('deleteItem', '{{ $item['id'] }}' )"
            class="btn tp-btn btn-xxs btn-danger "><i class="fa fa-trash fa-lg"></i></button>
    </td>
</tr>
@if($show==$item['sid'])
<tr>
    <thead>
        <tr class="text-center">
            <th><a  style="cursor: pointer;text-decoration:underline" wire:click="close()">Cerrar</a></th>
            <th width="280">Material</th>
            <th width="90">Cantidad</th>
            <th width="100">Unidad</th>
            <th width="90">Stock</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @forelse($materials as $material)
        <tr class="text-center">
            <td></td>
            <td >{{ $material->producto->name }}
            </td>
            <td>
                <input
                    wire:change="$emit('updateQty', '{{ $material['id'] }}', $event.target.value)"
                     type="number" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" 
                    value=" {{ $material->qty }}" disabled>

            </td>
            <td>{{ $material->producto->unit_type }}</td>
            <td>{{ $material->producto->stock_qty }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center">NO HAY PRODUCTOS DE USO AGREGADOS</td>
        </tr>
        @endforelse
    </tbody>
</tr>
@endif

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
</style>