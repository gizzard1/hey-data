<h4 class="text-center"  style="margin-top: 1rem;">Formas de Pago</h4>


<div class="table-responsive">
    <table class="table table-responsive-md table-hover  text-center">
        <thead class="thead-primary">
            <tr >
                <th style="background-color:transparent;color:#1d3557 !important;padding-left:10%">Concepto</th>
                <th style="background-color:transparent;color:#1d3557 !important">Cantidad o Porcentaje</th>
                <th style="background-color:transparent;color:#1d3557 !important">Referencia</th>
            </tr>
        </thead>
        <tbody>
        @if(count($methods)>0)
            @foreach($methods as $metodo)
            <tr>
                @if($metodo['paymentMethod'] == "5")
                    <tr>
                        <td>
                        
                        <button wire:click.prevent="removeRewardMethods"class="btn tp-btn btn-xxs btn-danger " style="height: fit-content;margin: auto;margin-right: 10%;">x </button>    
                        Puntos Recompensa</td>
                        <td> ${{ number_format($metodo['amount'],2,'.',',') }} </td>
                        <td></td>
                    </tr>
                @else
                    
            <td>
                
                <button wire:click.prevent="$emit('removeItem', '{{ $metodo['uid'] }}' , 'method' )"
                    class="btn tp-btn btn-xxs btn-danger " style="height: fit-content;margin: auto;margin-right: 10%;">x </button>
                        <select class="tamano-metodos" style="border:none;background-color:transparent"  wire:change="$emit('cambioDataMethods','{{ $metodo['uid'] }}',$event.target.value,3,'metodos')" {{ $metodo['paymentMethod'] == 99999 ? 'disabled' : '' }}>
                            <option style="text-align: center;" value="1" {{ $metodo['paymentMethod'] == "1" ? 'selected' : '' }}>Efectivo</option>
                            <option style="text-align: center;" value="2" {{ $metodo['paymentMethod'] == "2" ? 'selected' : '' }}>Tarjeta</option>
                            <option style="text-align: center;" value="3" {{ $metodo['paymentMethod'] == "3" ? 'selected' : '' }}>MSI</option>
                            <option style="text-align: center;" value="99999" {{ $metodo['paymentMethod'] == "99999" ? 'selected' : '' }}>Gift Card</option>
                            @if($metodo['paymentMethod']=='4')
                            <option style="text-align: center;" value="4" {{ $metodo['paymentMethod'] == "4" ? 'selected' : '' }}>Descuento</option>
                            @endif
                            @foreach($metodosSalon as $metodoSalon)
                                <option style="text-align: center;" value="{{ $metodoSalon['id'] }}" {{ $metodo['paymentMethod'] == $metodoSalon['id'] ? 'selected' : '' }}>{{ $metodoSalon['Payment_method'] }}</option>
                            @endforeach
                        </select>
                    </td>
                    @if($metodo['paymentMethod'] == '4')
                        <td class="input-container d-flex" style="justify-content: center;overflow:revert">
                            <div class="d-flex">
                                <input class="form-control tamano-metodos" value="{{ $metodo['amount'] }}" wire:change="$emit('cambioDataMethods','{{ $metodo['uid'] }}',$event.target.value,1,'metodos')">

                                <!-- Select para elegir porcentaje o moneda, con flecha oculta -->
                                <select class="form-control no-arrow" style="width: 3rem;" wire:change="$emit('cambioDataMethods','{{ $metodo['uid'] }}',$event.target.value,6,'metodos')">
                                    <option value="Porcentaje" {{ $metodo['tipo'] == "Porcentaje" ? 'selected' : '' }}>%</option>
                                    <option value="Cantidad" {{ $metodo['tipo'] == "Cantidad" ? 'selected' : '' }}>$</option>
                                </select>

                            </div>
                        </td>
                    @else
                        <td class="input-container d-flex" style="justify-content: center;overflow:revert">
                            <div class="d-flex">
                                <input {{ $metodo['paymentMethod'] == 99999 ? 'disabled' : '' }} class="form-control tamano-metodos" value="{{ $metodo['amount'] }}" wire:change="$emit('cambioDataMethods','{{ $metodo['uid'] }}',$event.target.value,1,'metodos')">
                        
                                <!-- Select para elegir porcentaje o moneda, con flecha oculta -->
                                <select class="form-control no-arrow" style="width: 3rem;" disabled>
                                    <option value="Cantidad" selected>$</option>
                                </select>
                            </div>
                        </td>
                    @endif
                    <td class="input-container" style="justify-items: center;">
                        <input {{ $metodo['paymentMethod'] == 99999 ? 'disabled' : '' }} class="form-control tamano-metodos" type="text" value="{{ $metodo['reference'] ?? $metodo['reference'] }}" wire:change="$emit('cambioDataMethods','{{ $metodo['uid'] }}',$event.target.value,2,'metodos')" >
                    </td>
                @endif
            </tr>
            @endforeach
        @endif
            <tr>
            <td colspan="6" style="cursor: pointer;"><input type="button" value="Añadir un Método de Pago" wire:click.prevent="$emit('newMethod')" style="width: 100%;border: none;background-color: transparent;"></td>
            </tr>
            @if(isset($total_disccount) && $total_disccount > 0)
            <tr class="text-right">
                <td></td>
                <td></td>
                <td>Descuentos: ${{ number_format($total_disccount,2,'.',',') }}</td>
            </tr>
            @endif
            @if(isset($recibido))
            <tr class="text-right">
                <td></td>
                <td></td>
                <td>Recibido: ${{ number_format($recibido,2,'.',',') }} </td>
            </tr>
            @endif
            <tr class="text-right">
                <td></td>
                <td></td>
                <td>
                @if($restante >= 0)
                    <strong>Restante: ${{ number_format($restante, 2, '.', ',') }}</strong>
                @else
                    Cambio: ${{ number_format(abs($restante), 2, '.', ',') }}
                @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>

@if(isset($informe))

<div class="mt-5 d-flex " style="column-gap:1rem;justify-content:end">
    <button class="btn btn-sm float-right" wire:click="disableEditing"  data-dismiss="modal">Cancelar</button>
    <button onclick="closeModals()" class="save btn btn-sm btn-info save float-right" wire:click.prevent="Store" data-dismiss="modal" >Guardar</button>
</div>

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
      right: 110px; /* Ajusta según necesites */
      top: 50%;
      transform: translateY(-50%);
    }
    .money-sign {
      position: absolute;
      right: 162px; /* Ajusta según necesites */
      top: 50%;
      transform: translateY(-50%);
    }
    .tamano-metodos{
        width: 12rem;
    }
</style>