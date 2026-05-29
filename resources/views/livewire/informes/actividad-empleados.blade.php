
<!-- Tabla de propinas -->
<table class="table table-responsive-md   text-center" style="margin-top:2rem; margin-bottom:3rem;width:-webkit-fill-available">
    <thead>
        <tr>
            <td colspan="5" style="text-align: center;"><h4>Control de Propinas</h4></td>
        </tr>
        <tr>
            <td>Empleado</td>
            <td>Propinas recibidas</td>
            <td>Pago en Efectivo</td>
            <td>Pago en MSI</td>
            @foreach($terminales as $terminal)
                <td>{{ $terminal }}</td>
            @endforeach
            <td>Total Propinas</td>
        </tr>
    </thead>
    <tbody>
    @if(isset($dataPropinas['empleado']))
    @foreach($dataPropinas['empleado'] as $index => $nombre)
        <tr>
            <td>{{ $nombre }}</td>
            <td class="text-center">{{ $dataPropinas['qty'][$index] }} </td>
            <td>${{ number_format($dataPropinas['Efectivo'][$index] ?? 0, 2, '.', ',') }} </td>
            <td>${{ number_format($dataPropinas['Banorte'][$index] ?? 0, 2, '.', ',') }} </td>
            @foreach($terminales as $terminal)
                <td>${{ number_format($dataPropinas[$terminal][$index] ?? 0, 2, '.', ',') }} </td>
            @endforeach
            <td>${{ number_format($dataPropinas['total'][$index] ?? 0, 2, '.', ',') }} </td>
        </tr>
    @endforeach
    @else
    <tr>
        <td></td>
        <td></td>
        <td>No hay datos para mostrar</td>
    </tr>
    @endif
    </tbody>
</table>
<!-- Termina Tabla de propinas -->