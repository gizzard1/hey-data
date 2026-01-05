<div class="table-responsive">
    <table class="table table-striped table-responsive-sm">
        <thead>
            <tr class="text-center">
                <th >Producto</th>
                <th >Vendedor</th>
                <th width="90">piezas</th>
                <th width="90">descuento</th>
                <th width="200">IVA</th>
                <th>Precio</th>
                <th width="200">Precio Unitario</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($cartInfo as $item)
            <tr class="text-center">

                <td >{{ $item['name'] }}
                </td>
                    <td>
                        <select wire:change="$emit('updateEmpleado','{{ $item['id'] }}', $event.target.value)"  style="background-color: transparent;border-color:transparent;">
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
                        wire:change="$emit('updateQty', '{{ $item['id'] }}', $event.target.value)"
                        class="form-control text-center" type="number" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" 
                        value="{{ $item['qty'] }}" >

                </td>
                <td class="input-container">
                    <input
                        wire:change="$emit('updatePercentage', '{{ $item['id'] }}', $event.target.value)"
                        class="form-control text-center" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" 
                        value="{{ isset($item['disccount_percent']) ? $item['disccount_percent'] : '' }}%">

                </td>
                <td><select wire:change="$emit('updateIva','{{ $item['id'] }}', $event.target.value)"  style="background-color: transparent;border-color:transparent;text-align:center;font-size:smaller">
                    @foreach(['0.16' => '16%', '0.08' => '8%', '0' => 'Exento'] as $value => $label)
                        <option value="{{ $value }}" {{ $item['ind_iva'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select></td>
                <td><input
                        wire:change="$emit('updateTotalCP', '{{ $item['id'] }}', $event.target.value)"
                        class="form-control text-center" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" value="${{ $item['disccount_price'] ? $item['disccount_price'] : $item['sale_price'] }}"></td>
                
                    <td>${{ number_format($item['gross_price'],2,'.',',') }}</td>
                    <td>${{ number_format($item['total'],2,'.',',') }}</td>
                <td>
                    <button wire:click.prevent="$emit('removeItemCart', '{{ $item['id'] }}' )"
                        class="btn tp-btn btn-xxs btn-danger "><i class="fa fa-trash fa-lg"></i></button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">AGREGA PRODUCTOS</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

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