<div class="table-responsive">
    <table class="table table-striped table-responsive-sm">
        <thead>
            <tr class="text-center">
                <th >Producto</th>
                <th width="90">unidad</th>
                <th width="90">piezas</th>
                <th width="90">iva</th>
                <th>Precio Compra (IVA incluido)</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($cartInfo as $item)
            <tr class="text-center">

                <td >{{ $item['name'] }}</td>
                <td>{{ $item['unit_type'] }}</td>
                <td>
                    <input
                        wire:change="$emit('updateQty', '{{ $item['id'] }}', $event.target.value)"
                        class="form-control text-center" type="number" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" 
                        value="{{ $item['qty'] }}" >
                </td>
                <td>
                    <select wire:change="$emit('updateIva','{{ $item['id'] }}', $event.target.value)"  style="cursor:pointer;background-color: transparent;border-color:transparent;text-align:center;font-size:smaller">
                        @foreach(['0.16' => '16%', '0.08' => '8%', '0' => 'Exento'] as $value => $label)
                            <option value="{{ $value }}" {{ $item['iva'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input wire:change="$emit('updateCost', '{{ $item['id'] }}', $event.target.value)" class="form-control text-center" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" value="${{ $item['cost'] }}">
                </td>
                <td>${{ number_format($item['subtotal'],2,'.',',') }}</td>
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