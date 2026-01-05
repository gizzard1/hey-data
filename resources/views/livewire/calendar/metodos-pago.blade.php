<h4 class="text-center">Formas de Pago</h4>


<div class="table-responsive">
    <table class="table table-responsive-md table-hover  text-center">
        <thead class="thead-primary">
            <tr >
                <th style="background-color:transparent;color:#9D1466 !important;padding-left:10%">Concepto</th>
                <th style="background-color:transparent;color:#9D1466 !important">Cantidad o Porcentaje</th>
                <th style="background-color:transparent;color:#9D1466 !important">Referencia</th>
            </tr>
        </thead>
        <tbody>
        @if($methods!=null)
            @foreach($methods as $metodo)
            <tr>
                @if($metodo['paymentMethod'] == "Puntos Recompensa")
                    <tr>
                        <td>Puntos Recompensa</td>
                        <td> ${{ number_format($metodo['amount'],2,'.',',') }} </td>
                        <td></td>
                    </tr>
                @else
                    
            <td style="display: flex;">
                
                <button wire:click.prevent="$emit('removeItem', '{{ $metodo['uid'] }}' , 'method' )"
                    class="btn tp-btn btn-xxs btn-danger " style="height: fit-content;margin: auto;margin-right: 10%;">x </button>
                        <select class="tamano-metodos" style="border:none;background-color:transparent"  wire:change="$emit('cambioDataMethods','{{ $metodo['uid'] }}',$event.target.value,3,'metodos')">
                            <option style="text-align: center;" value="Efectivo" {{ $metodo['paymentMethod'] == "Efectivo" ? 'selected' : '' }}>Efectivo</option>
                            <option style="text-align: center;" value="Card" {{ $metodo['paymentMethod'] == "Card" ? 'selected' : '' }}>Tarjeta</option>
                            <option style="text-align: center;" value="Banorte" {{ $metodo['paymentMethod'] == "Banorte" ? 'selected' : '' }}>Banorte</option>
                            <option style="text-align: center;" value="Descuento" {{ $metodo['paymentMethod'] == "Descuento" ? 'selected' : '' }}>Descuento</option>
                        </select>
                    </td>
                    @if($metodo['paymentMethod'] == 'Descuento')
                        <td class="input-container"><input class="campos tamano-metodos" value="{{ $metodo['amount'] }}%" wire:change="$emit('cambioDataMethods','{{ $metodo['uid'] }}',$event.target.value,1,'metodos')"></td>
                    @else
                        <td class="input-container"><input class="campos tamano-metodos" value="${{ $metodo['amount'] }}" wire:change="$emit('cambioDataMethods','{{ $metodo['uid'] }}',$event.target.value,1,'metodos')"></td>
                    @endif
                    <td><input class="campos tamano-metodos" type="text" value="{{ $metodo['reference'] ?? $metodo['reference'] }}" wire:change="$emit('cambioDataMethods','{{ $metodo['uid'] }}',$event.target.value,2,'metodos')" ></td>
                @endif
            </tr>
            @endforeach
            @endif
            <tr>
            <td colspan="6" style="cursor: pointer;"><input type="button" value="Añadir un Método de Pago" wire:click.prevent="$emit('newMethod')" style="width: 100%;border: none;background-color: transparent;"></td>
            </tr>
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
                    Restante: ${{ number_format($restante, 2, '.', ',') }}
                @else
                    Cambio: ${{ number_format(abs($restante), 2, '.', ',') }}
                @endif

                </td>
            </tr>
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