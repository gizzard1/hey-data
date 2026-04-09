<div class="table-responsive">
    <table class="table table-striped table-responsive-sm">
        <thead>
            <tr class="text-center">
                <th >Producto</th>
                <th width="260">Vendedor</th>
                <th width="90">piezas</th>
                <th width="90">Descuento</th>
                <th width="200">IVA</th>
                <th width="200">Precio</th>
                <th width="200">Precio Unitario</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($cartInfo as $item)
            <tr class="text-center">

            @if($item['pid']==99999)
            <td ><a wire:click.prevent="editarCupon('{{ $item['id'] }}')">{{ $item['name'] }}</a></td>
            
            @else
                <td >{{ $item['name'] }}
                </td>
            @endif
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
                        value="{{ $item['qty'] }}" min="1"
                        {{ $item['pid'] === 99999 ? 'readonly' : '' }}>

                </td>
                <td class="input-container d-flex">
                    <input
                        wire:change="updatePercentage( '{{ $item['id'] }}', $event.target.value)"
                        class="form-control text-center" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" 
                        value="{{ isset($item['disccount_percent']) ? $item['disccount_percent'] : '' }}">
                    
                    <!-- Select para elegir porcentaje o moneda, con flecha oculta -->
                    <select class="form-control no-arrow bg-white" style="width: 3rem;" wire:change="updatePercentageType('{{ $item['id'] }}',$event.target.value)">
                        <option value="Porcentaje" {{ $item['discount_type'] == "Porcentaje" ? 'selected' : '' }}>%</option>
                        <option value="Cantidad" {{ $item['discount_type'] == "Cantidad" ? 'selected' : '' }}>$</option>
                    </select>

                </td>
                <td><select wire:change="updateIva('{{ $item['id'] }}', $event.target.value)"  style="background-color: transparent;border-color:transparent;text-align:center;font-size:smaller">
                    @foreach(['0.16' => '16%', '0.08' => '8%', '0' => 'Exento'] as $value => $label)
                        <option value="{{ $value }}" {{ $item['ind_iva'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select></td>
                <td>
                    <div class="d-flex prices">
                        <input
                            wire:change="updateTotalCP( '{{ $item['id'] }}', $event.target.value)"
                            class="form-control text-center" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" value="${{ $item['disccount_price'] ? $item['disccount_price'] : $item['sale_price'] }}">
                        <input type="radio"  {{ $item['base_comision'] ?  '' : 'checked' }} name="{{ $item['id'] }}" wire:click="updateBaseComision('{{ $item['id'] }}',0)" >
                    </div>
                </td>
                <td>
                    <div class="d-flex prices">
                        ${{ number_format($item['total'] / $item['qty'],2,'.',',') }}
                        <input type="radio" {{ $item['base_comision'] ?  'checked' : '' }} name="{{ $item['id'] }}" wire:click="updateBaseComision('{{ $item['id'] }}',1)" >
                    </div>
                </td>
                <td>
                    ${{ number_format($item['total'],2,'.',',') }}
                </td>
                <td>
                    @if($item['pid']!=99999)
                    <button wire:click.prevent="removeItemCart( '{{ $item['id'] }}' )"
                        class="btn tp-btn btn-xxs btn-danger "><i class="fa fa-trash fa-lg"></i></button>
                    @endif
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
  .prices{
    justify-content: center;
    column-gap: 1rem;
  }
</style>