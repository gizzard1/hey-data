<div class="table-responsive">
    <table class="table table-responsive-md table-hover" style="height:47dvh">
        <thead class="thead-primary">
            <tr >
                <th style="background-color:transparent;color:#1d3557 !important" width = "70">Estatus</th>
                <th style="background-color:transparent;color:#1d3557 !important" width ="152" id="fecha">Fecha</th>
                <th style="background-color:transparent;color:#1d3557 !important">Total</th>
                <th style="background-color:transparent;color:#1d3557 !important">Cliente</th>
                <th style="background-color:transparent;color:#1d3557 !important">Versión</th>
            </tr>
        </thead>
        <tbody class="scroll">
            <tr>
                <td onclick="toggleMethods('citas')" colspan="5" class="text-center">Citas</td>
            </tr>
            @if(isset($citas))
                @foreach($citas as $item)
                    <tr onclick="toggleMethods({{ $item->id }})" class="data methodscitas toggled">
                        <td class="badge" style="display: revert;background-color:{{ $item->status == 'Agendada' ? '#4978BC' : ($item->status == 'Pagada' ? '#28A745' : ($item->status == 'Cancelada' ? '#E63946' : ($item->status == 'Confirmada' ? '#4978BC' : ($item->status == 'Pendiente' ? '#FFAB2D' : '#e63946')))) }}"><button
                                class="details-badge"
                                data-original-text="{{ $item->status }}" 
                                onclick="event.stopPropagation()" >{{ $item->status }}</button></td>
                        <td> {{ date_format(new DateTime($item->created_at),'d-m-Y') }} </td>
                        <td> ${{ number_format($item->total,2,'.',',') }} </td>
                        <td> {{ isset($item->customer) ? $item->customer->first_name : 'Cliente eliminado' }} {{ isset($item->customer) ? $item->customer->last_name : '' }}</td>
                        <td> Última act. {{ date_format(new DateTime($item->updated_at),'d-m-Y') }} por {{ $item->user ?$item->user->name : 'Desconocido' }} </td>
                    </tr>
                    @if(count($item->metodosPago) > 0)
                    <tr class="methods-row methods{{ $item->id }} toggled">
                        <th></th>
                        <th style="background-color:transparent;color:#1d3557 !important">Concepto</th>
                        <th style="background-color:transparent;color:#1d3557 !important">Cantidad</th>
                        <th style="background-color:transparent;color:#1d3557 !important">Referencia</th>
                        <th></th>
                    </tr>
                    @else
                    <tr class="methods-row methods{{ $item->id }} toggled">
                        <td colspan="5">Sin método de pago</td>
                    </tr>
                    @endif
                    @foreach ($item->metodosPago as $metodoPago)
                        @if($metodoPago->payment_method_id!==4)
                            <tr class="methods-row methods{{ $item->id }} toggled">
                                <td></td>
                                <td style="background-color:transparent;color:#1d3557 !important">{{ $metodoPago->metodoPago->Payment_method }}</td>
                                <td style="background-color:transparent;color:#1d3557 !important">${{ number_format($metodoPago->amount-$metodoPago->change,2,'.',',') }}</td>
                                <td style="background-color:transparent;color:#1d3557 !important">{{ $metodoPago->reference }}</td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            @endif
            <tr>
                <td onclick="toggleMethods('ventas')" colspan="5" class="text-center">Ventas</td>
            </tr>
            @if(isset($ventas))
                @foreach($ventas as $item)
                    <tr onclick="toggleMethods({{ $item->id }})" class="data methodsventas toggled">
                        <td class="badge" style="display: revert;background-color:{{ $item->status == 'Agendada' ? '#4978BC' : ($item->status == 'Pagada' ? '#28A745' : ($item->status == 'Cancelada' ? '#E63946' : ($item->status == 'Confirmada' ? '#4978BC' : ($item->status == 'Pendiente' ? '#FFAB2D' : '#e63946')))) }}"><button
                                class="details-badge"
                                data-original-text="{{ $item->status }}" 
                                onclick="event.stopPropagation()" >{{ $item->status }}</button></td>
                        <td> {{ date_format(new DateTime($item->created_at),'d-m-Y') }} </td>
                        <td> ${{ number_format($item->total,2,'.',',') }} </td>
                        <td> {{ isset($item->customer) ? $item->customer->first_name : 'Cliente eliminado' }} {{ isset($item->customer) ? $item->customer->last_name : '' }}</td>
                        <td> Última act. {{ date_format(new DateTime($item->updated_at),'d-m-Y') }} por {{ $item->user ?$item->user->name : 'Desconocido' }} </td>
                    </tr>
                    @if(count($item->metodosPago) > 0)
                    <tr class="methods-row methods{{ $item->id }} toggled">
                        <th></th>
                        <th style="background-color:transparent;color:#1d3557 !important">Concepto</th>
                        <th style="background-color:transparent;color:#1d3557 !important">Cantidad</th>
                        <th style="background-color:transparent;color:#1d3557 !important">Referencia</th>
                        <th></th>
                    </tr>
                    @else
                    <tr class="methods-row methods{{ $item->id }} toggled">
                        <td colspan="5">Sin método de pago</td>
                    </tr>
                    @endif
                    @foreach ($item->metodosPago as $metodoPago)
                        @if($metodoPago->payment_method_id!==4)
                            <tr class="methods-row methods{{ $item->id }} toggled">
                                <td></td>
                                <td style="background-color:transparent;color:#1d3557 !important">{{ $metodoPago->metodoPago->Payment_method }}</td>
                                <td style="background-color:transparent;color:#1d3557 !important">${{ number_format($metodoPago->amount-$metodoPago->change,2,'.',',') }}</td>
                                <td style="background-color:transparent;color:#1d3557 !important">{{ $metodoPago->reference }}</td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                @endforeach
            @endif
            <tr>
                <td onclick="toggleMethods('gastos')" colspan="5" class="text-center">Gastos</td>
            </tr>
            @if(isset($gastos))
                @foreach($gastos as $item)
                    <tr class="data methodsgastos toggled">
                        <td class="badge" style="display: revert;background-color:{{ $item->status == 'Agendada' ? '#4978BC' : ($item->status == 'vigente' ? '#28A745' : ($item->status == 'cancelado' ? '#E63946' : ($item->status == 'default' ? '#4978BC' : ($item->status == 'Pendiente' ? '#FFAB2D' : '#e63946')))) }}"><button
                                class="details-badge"
                                data-original-text="{{ $item->status }}" 
                                onclick="event.stopPropagation()" >{{ $item->status }}</button></td>
                        <td> {{ date_format(new DateTime($item->created_at),'d-m-Y') }} </td>
                        <td> ${{ number_format($item->total,2,'.',',') }} </td>
                        <td> -</td>
                        <td> Última act. {{ date_format(new DateTime($item->updated_at),'d-m-Y') }} </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>


<style>
    a{
        color:#1d3557
    }
    .table-responsive{
       max-height: 65dvh;
    }
    .details-badge {
        display: inline-block;
        transition: all 0.3s ease; /* Transición suave */
        min-width: 80px; /* Ajusta este valor al texto más largo */
        text-align: center;
        padding:initial;
    }
    button.details-badge {
        background: transparent;
        border: 0 solid transparent;
        color: white;
    }
    .methods-row {
        display: table-row;
    }
    .methods-row.toggled, .data.toggled {
        display: none;
    }
    thead.thead-primary {
        position: sticky;
        top: 0;
        background: white;
    }
</style>

<script>
    
function toggleMethods(id)
{
    const elements = document.querySelectorAll('.methods' + id);
    elements.forEach(element => {
        element.classList.toggle('toggled');
    });

}
function changeBadgeText(element, state) {
    const hoverText = element.getAttribute('data-hover-text');
    const originalText = element.getAttribute('data-original-text');

    if (state === 'hover' && element.textContent !== hoverText) {
        element.textContent = hoverText;
    } else if (state === 'original' && element.textContent !== originalText) {
        element.textContent = originalText;
    }
}    
</script>