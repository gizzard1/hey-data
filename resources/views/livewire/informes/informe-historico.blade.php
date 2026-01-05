
@if(Auth::user()->role!=='estilista')
<table class="table table-responsive-md text-center">
    <thead>
        <tr>
            <td style="text-align: center;" colspan="10"><h4>Ventas por Empleado</h4></td>
        </tr>
        <tr>
            <td>Empleado</td>
            <td>Clientes Atendidos</td>
            <td>Duración de Visitas</td>
            <td>Ventas de Producto Bruto</td>
            <td>Ventas de Producto Neto</td>
            <td>Ventas de Servicio Bruto</td>
            <td>Ventas de Servicio Neto</td>
            <td>Total Bruto</td>
            <td>Total Neto</td>
            <td>Porcentaje de Participación</td>
        </tr>
    </thead>
    <tbody> 
    @if(isset($dataEmpleados))
        @foreach($dataEmpleados as $empleado)
            <tr>
                <td>{{ $empleado['name'] }}</td>
                <td>{{ $empleado['total_cust'] }}</td>
                @php
                    $minutes = $empleado['duration'];
                    $hours = floor($minutes / 60);
                    $remainingMinutes = $minutes % 60;
                @endphp
                <td>
                    {{ $hours }} hr {{ $remainingMinutes }} min
                </td>
                <td>${{ number_format($empleado['total_v'], 2, '.', ',') }}</td>
                <td>${{ number_format($empleado['total_v_neto'], 2, '.', ',') }}</td>
                <td>${{ number_format($empleado['total_d'], 2, '.', ',') }}</td>
                <td>${{ number_format($empleado['total_d_neto'], 2, '.', ',') }}</td>
                <td>${{ number_format($empleado['total_d'] + $empleado['total_v'], 2, '.', ',') }}</td>
                <td>${{ number_format($empleado['total_d_neto'] + $empleado['total_v_neto'], 2, '.', ',') }}</td>
                <td>{{ number_format($empleado['percent'], 2, '.') }}%</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>

@php
    $minutes_T = $totales['duration'] ?? 0;
    $hours_T = floor($minutes_T / 60);
    $remainingMinutes_T = $minutes_T % 60;
@endphp
<table>
    <thead >
        <tr>
            <td style="text-align: center;width:fit-content" colspan="2"><h4>Datos de las citas</h4></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><h5>Número de citas finalizadas: </h5></td>
            <td>{{ $totales['total_citas_pagadas'] ?? 0 }}</td>
        </tr>
        <tr>
            <td><h5>Número de citas pendientes: </h5></td>
            <td>{{ $totales['total_citas_pendientes'] ?? 0 }}</td>
        </tr>
        <tr>
            <td><h5>Número de citas canceladas: </h5></td>
            <td><h5>{{ $totales['total_citas_canceladas'] ?? 0 }}</h5></td>
        </tr>
        <tr>
            <td><h5>Duración total de visitas: </h5></td>
            <td>{{ $hours_T }} hr {{ $remainingMinutes_T }} min</td>
        </tr>
    </tbody>
</table>
         
<table class="table table-responsive-md text-center">
    <thead>
        <tr>
            <td style="text-align: center;" colspan="2"><h4>Métodos de Pago</h4></td>
        </tr>
        <tr style="color:#9D1466;font-weight:bold">
            <td>Método de Pago</td>
            <td>Cantidad</td>
        </tr>
    </thead>
    <tbody>
    @foreach($dataSalesExcel['label'] as $index => $name)
        <tr>
            <td>{{ $name }}</td>
            <td>${{ number_format($dataSalesExcel['qty'][$index] , 2, '.', ',') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="table table-responsive-md text-center">
    <thead>
        <tr>
            <td style="text-align: center;" colspan="3"><h4>Ingresos del Periodo</h4></td>
        </tr>
        <tr style="color:#9D1466;font-weight:bold">
            <td style="width: 33%;">Concepto</td>
            <td>Bruto</td>
            <td>Neto</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Ingreso Obtenido en Citas</td>
            <td>${{ number_format($totales['total_servicios'] ?? 0,2,'.',',') }}</td>
            <td>${{ number_format($totales['total_incomes_neto_s'] ?? 0,2,'.',',') }}</td>
        </tr>
        <tr>
            <td>Ingreso Obtenido en Ventas</td>
            <td>${{ number_format($totales['total_ventas'] ?? 0,2,'.',',') }}</td>
            <td>${{ number_format($totales['total_incomes_neto_p'] ?? 0,2,'.',',') }}</td>
        </tr>
        <tr>
            <td>Total</td>
            <td>${{ number_format($totales['total_incomes'] ?? 0,2,'.',',') }}</td>
            <td>${{ number_format($totales['total_incomes_neto'] ?? 0,2,'.',',') }}</td>
        </tr>
        <tr>
            <td>Propinas</td>
            <td>${{ number_format($totalTipsNeto ?? 0,2,'.',',') }}</td>
            <td>${{ number_format($totalTips ?? 0,2,'.',',') }}</td>
        </tr>
    </tbody>
</table>

<table class="table table-responsive-md text-center">
    <thead>
        <tr>
            <td style="text-align: center;" colspan="2"><h4>Métodos de Pago Para Gastos</h4></td>
        </tr>
        <tr style="color:#9D1466;font-weight:bold">
            <td>Método de Pago</td>
            <td>Cantidad</td>
        </tr>
    </thead>
    <tbody>
    @php
        $total = 0;
    @endphp
    @foreach($dataExpenses['label'] as $index => $name)
        <tr>
            <td>{{ $name }}</td>
            <td>${{ number_format($dataExpenses['qty'][$index] , 2, '.', ',') }}</td>
        </tr>
        @php
            $total += $dataExpenses['qty'][$index];
        @endphp
    @endforeach
    <tr>
        <td>Total</td>
        <td>${{ number_format($total , 2, '.', ',') }}</td>
    </tr>
    </tbody>
</table>

<table class="table table-responsive-md text-center">
    <thead>
        <tr>
            <td style="text-align: center;" colspan="5"><h4>Egresos del Periodo</h4></td>
        </tr>
        <tr style="color:#9D1466;font-weight:bold">
            <td style="width: 33%;">Tipo de gasto</td>
            <td>Deducible</td>
            <td>No deducible</td>
        </tr>
    </thead>
    <tbody>
        @foreach($dataTypeExpenses['label'] as $label)
            <tr>
                <td>{{ $label }}</td>
                <td>${{ number_format($dataTypeExpenses['qty_a'][$label] ?? 0,2,'.',',') }}</td>
                <td>${{ number_format($dataTypeExpenses['qty_na'][$label] ?? 0,2,'.',',') }}</td>
            </tr>
        @endforeach
        <tr>
            <td>Total</td>
            <td>${{ number_format($totales['gastos_a'] ?? 0,2,'.',',') }}</td>
            <td>${{ number_format($totales['gastos_n'] ?? 0,2,'.',',') }}</td>
        </tr>
    </tbody>
</table>
       


<!-- <table class="table table-responsive-md text-center">
    <thead>
        <tr>
            <td style="text-align: center;" colspan="2"><h4>Gastos</h4></td>
        </tr>
        <tr style="color:#9D1466;font-weight:bold">
            <td>Tipo</td>
            <td>Monto</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Acreditables</td>
            <td>${{ number_format($totales['gastos_a'] ?? 0,2,'.',',') }}</td>
        </tr>
        <tr>
            <td>No acreditables</td>
            <td>${{ number_format($totales['gastos_n'] ?? 0,2,'.',',') }}</td>
        </tr>
    </tbody>
</table> -->
@else
@include('livewire.sinPermisos')
@endif
