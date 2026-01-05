<h4 class="text-center" >Formas de Pago</h4>


<div class="table-responsive">
    <table class="table table-responsive-md table-hover  text-center">
        <thead class="thead-primary">
            <tr >
                <th style="background-color:transparent;color:#9D1466 !important">Concepto</th>
                <th style="background-color:transparent;color:#9D1466 !important">Cantidad</th>
                <th style="background-color:transparent;color:#9D1466 !important">Referencia</th>
            </tr>
        </thead>
        <tbody>
        @php
            $totalMethods = 0;
        @endphp
            @foreach($itemSelected->metodosPago as $metodo)
            @php 
            if($metodo->metodoPago->Payment_method!='Descuento'){
                $totalMethods += $metodo->amount; 
            }
            @endphp
            <tr>
                <td>{{ $metodo->metodoPago->Payment_method }}</td>
                @if($metodo->metodoPago->Payment_method == 'Descuento')
                
                <td>
                    {{ 
                        ($metodo->tipo == 'Cantidad' ? '$' : '') . 
                        number_format($metodo->amount, 2, '.', ',') . 
                        ($metodo->tipo == 'Porcentaje' ? '%' : '') 
                    }}
                </td>
                @else
                <td> ${{ number_format($metodo->amount,2,'.',',') }}</td>
                @endif
                <td>{{ $metodo->reference ?? $metodo->reference }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td>Total: ${{ number_format($totalMethods,2,'.',',') }} </td>
            </tr>
        </tbody>
    </table>
</div>

@if(count($itemSelected->propinas) > 0)
<h4 class="text-center">Propinas</h4>

<div class="table-responsive">
    <table class="table table-responsive-md table-hover  text-center">
        <thead class="thead-primary">
            <tr >
                <th style="background-color:transparent;color:#9D1466 !important">Concepto</th>
                <th style="background-color:transparent;color:#9D1466 !important">Cantidad</th>
                <th style="background-color:transparent;color:#9D1466 !important">Referencia</th>
            </tr>
        </thead>
        <tbody>
        @php
            $totalPropinas = 0;
        @endphp
            @foreach($itemSelected->propinas as $propina)
            @php 
                $totalPropinas += $propina->amount; 
            @endphp
            
            <tr>
                <td>{{ $propina->metodoPago->Payment_method }}</td>
                <td> ${{ number_format($propina->amount,2,'.',',') }} </td>
                <td>{{ $propina->reference ?? $propina->reference }}</td>
            </tr>
            
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td>Total: ${{ number_format($totalPropinas,2,'.',',') }} </td>
            </tr>
        </tbody>
    </table>
</div>
@endif

<style>
    .tamano-metodos{
        width: 12rem;
    }
</style>