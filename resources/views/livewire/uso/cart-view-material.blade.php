
            <tr class="text-center">

                <td >{{ $item['name'] }}</td>
                <td>
                    <select wire:change="updateEmpleado('{{ $item['id'] }}', $event.target.value)"  style="background-color: transparent;border-color:transparent;">
                        @if(!isset($item['vendedor']))
                            <option value="null">Seleccione un empleado</option>
                        @endif
                        @foreach($empleados as $empleado)
                            <option style="text-align: center;" value="{{ $empleado->id }}" {{ $item['vendedor'] == $empleado->id ? 'selected' : '' }}>{{ $empleado->first_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input
                        wire:change="updateQty( '{{ $item['id'] }}', $event.target.value)"
                        class="form-control text-center" type="number" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" 
                        value="{{ $item['qty'] }}" >

                </td>
                <td>{{ $item['stock'] }}</td>
                <td>
                    <button wire:click.prevent="removeItemCart( '{{ $item['id'] }}' )"
                        class="btn tp-btn btn-xxs btn-danger "><i class="fa fa-trash fa-lg"></i></button>
                </td>
            </tr>
