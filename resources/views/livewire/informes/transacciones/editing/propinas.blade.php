<h4 class="text-center"  style="margin-top: 1rem;">Propinas</h4>


<div class="table-responsive">
    <table class="table table-responsive-md table-hover  text-center">
        <thead class="thead-primary">
            <tr >
                <th style="background-color:transparent;color:#1d3557 !important;padding-left:10%">Concepto</th>
                <th style="background-color:transparent;color:#1d3557 !important">Cantidad</th>
                <th style="background-color:transparent;color:#1d3557 !important">Referencia</th>
                <th style="background-color:transparent;color:#1d3557 !important">Empleado</th>
            </tr>
        </thead>
        <tbody>
        @if(count($propinas)>0)
            @foreach($propinas as $propina)
            <tr>
            <td style="display: flex;">
                
                <button wire:click.prevent="$emit('removeItem', '{{ $propina['uid'] }}' , 'propina' )"
                    class="btn tp-btn btn-xxs btn-danger " style="height: fit-content;margin: auto;margin-right: 10%;">x </button>
                        <select class="tamano-metodos" style="border:none;background-color:transparent" wire:change="$emit('cambioDataMethods','{{ $propina['uid'] }}',$event.target.value,3,'propinas')">
                            <option style="text-align: center;" value="1" {{ $propina['paymentMethod'] == "1" ? 'selected' : '' }}>Efectivo</option>
                            <option style="text-align: center;" value="2" {{ $propina['paymentMethod'] == "2" ? 'selected' : '' }}>Tarjeta</option>
                            <option style="text-align: center;" value="3" {{ $propina['paymentMethod'] == "3" ? 'selected' : '' }}>MSI</option>
                            @foreach($metodosSalon as $metodoSalon)
                                <option style="text-align: center;" value="{{ $metodoSalon['id'] }}" {{ $propina['paymentMethod'] == $metodoSalon['id'] ? 'selected' : '' }}>{{ $metodoSalon['Payment_method'] }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="input-container" style="justify-items: center;"><input class="form-control tamano-metodos" value="${{ $propina['amount'] }}" wire:change="$emit('cambioDataMethods','{{ $propina['uid'] }}',$event.target.value,1,'propinas')"> </td>
                    
                    <td class="input-container" style="justify-items: center;"><input class="form-control tamano-metodos" type="text" value="{{ $propina['reference'] ?? $propina['reference'] }}" wire:change="$emit('cambioDataMethods','{{ $propina['uid'] }}',$event.target.value,2,'propinas')" ></td>
            
                    <td>
                        <select wire:change="$emit('cambioDataMethods','{{ $propina['uid'] }}',$event.target.value,4,'propinas')" style="background-color: transparent;border-color:transparent;max-width: 122px;">
                            @if(!isset($propina['empleado']))
                                <option value="null">Seleccione un empleado</option>
                            @endif
                            @foreach($empleados as $empleado)
                                <option style="text-align: center;" value="{{ $empleado->id }}" {{ $propina['empleado'] == $empleado->id ? 'selected' : '' }}>{{ $empleado->first_name }}</option>
                            @endforeach
                        </select>
                    </td>
            </tr>
            @endforeach
        @endif
            <tr>
            <td colspan="6" style="cursor: pointer;"><input type="button" value="Añadir Propina" wire:click.prevent="$emit('newPropina')" style="width: 100%;border: none;background-color: transparent;"></td>
            </tr>
            @if(isset($propinasRecibidas))
            <tr class="text-right">
                <td></td>
                <td></td>
                <td></td>
                <td>Recibido: ${{ number_format($propinasRecibidas,2,'.',',') }} </td>
            @endif
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