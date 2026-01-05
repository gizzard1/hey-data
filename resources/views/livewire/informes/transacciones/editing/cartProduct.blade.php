

<div class="table-responsive">
    <table class="table table-responsive-md table-hover  text-center">
        <thead class="thead-primary">
            <tr >
                <th style="background-color:transparent;color:#1d3557 !important;padding-left:8%">Nombre</th>
                <th style="background-color:transparent;color:#1d3557 !important;">Vendedor</th>
                <th style="background-color:transparent;color:#1d3557 !important">Piezas</th>
                <th style="background-color:transparent;color:#1d3557 !important">Descuento</th>
                <th style="background-color:transparent;color:#1d3557 !important">IVA</th>
                <th style="background-color:transparent;color:#1d3557 !important">Precio</th>
                <th style="background-color:transparent;color:#1d3557 !important">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @if($cartP!=null)
            @foreach($cartP as $item)
            
                <tr>
                    <td style="display: flex;">
                
                    <button wire:click.prevent="$emit('removeItem', '{{ $item['id'] }}','producto' )"
                        class="btn tp-btn btn-xxs btn-danger " style="
    height: fit-content;
    margin: auto;
    margin-right: 10%;
">x </button>
                {{ $item['name'] }} </td>
                    <td>
                        <select wire:change="$emit('updateEmpleado','producto','{{ $item['id'] }}', $event.target.value)"  style="background-color: transparent;border-color:transparent;max-width: 122px;">
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
                            wire:change="$emit('updateQty','producto', '{{ $item['id'] }}', $event.target.value)"
                            class="form-control form-control-sm text-center" type="number" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" 
                            value="{{ $item['qty'] }}" >

                    </td>
                    <td class="d-flex">
                            <input
                                wire:change="$emit('updatePercentage','producto', '{{ $item['id'] }}', $event.target.value)"
                                class="form-control form-control-sm text-center" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" 
                                value="{{ isset($item['disccount_percent']) ? $item['disccount_percent'] : '' }}">

                            <!-- Select para elegir porcentaje o moneda, con flecha oculta -->
                            <select class="form-control no-arrow bg-white" style="width: 3rem;" wire:change="updatePercentageType('producto','{{ $item['id'] }}',$event.target.value)">
                                <option value="Porcentaje" {{ $item['discount_type'] == "Porcentaje" ? 'selected' : '' }}>%</option>
                                <option value="Cantidad" {{ $item['discount_type'] == "Cantidad" ? 'selected' : '' }}>$</option>
                            </select>
                    </td>
                    <td>
                        <select wire:change="$emit('updateIva','producto','{{ $item['id'] }}', $event.target.value)"  style="cursor:pointer;background-color: transparent;border-color:transparent;text-align:center;font-size:smaller">
                            @foreach(['0.16' => '16%', '0.08' => '8%', '0' => 'Exento'] as $value => $label)
                                <option value="{{ $value }}" {{ $item['ind_iva'] == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>${{ number_format($item['disccount_price'] ? $item['disccount_price'] : $item['sale_price'], 2, '.', ',') }}</td>
                    <td>${{ number_format($item['total'],2,'.',',') }}</td>

                </tr>

            @endforeach
            <tr>
            <td colspan="7" style="cursor: pointer;"><input type="button" value="Añadir un Producto" data-toggle="modal" data-target="#modalItems" wire:click="$emit('loadItems')" style="width: 100%;border: none;background-color: transparent;"></td>
            </tr>
            @endif
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>Subtotal:</td>
                <td> ${{ number_format($subtotalCart,2,'.',',') }} </td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>Impuestos:</td>
                <td> ${{ number_format($impuestos,2,'.',',') }} </td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>Total:</td>
                <td> ${{ number_format($totalCart,2,'.',',') }} </td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

@if(isset($informe))

<div class="mt-5 d-flex " style="column-gap:1rem;justify-content:end">
    <button class="btn btn-sm float-right" wire:click="disableEditing"  data-dismiss="modal">Cancelar</button>
    <button onclick="closeModals()" class="save btn btn-sm btn-info save float-right" wire:click.prevent="Store" data-dismiss="modal">Guardar</button>
</div>

@endif


<style>
    .campos{
        border: unset;
        background-color: unset;
        text-align: center;
        border-bottom: 1px solid black;
    }
    .tamano-especial{
        width: 5rem;
    }
    .btn.tp-btn.btn-danger:hover {
        background-color: #1d3557 !important;
        border-color: #1d3557 !important;
        color: #fff;
    }
    .percent-product{
      position: absolute;
      right: 44px; /* Ajusta según necesites */
      top: 50%;
      transform: translateY(-50%);
    }
</style>