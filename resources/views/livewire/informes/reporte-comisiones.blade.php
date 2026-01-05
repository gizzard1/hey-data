<!-- Comienza tabla comisiones                 -->
<table class="table table-responsive-md text-left">
    <thead>
        <tr>
            <td colspan="7" style="text-align: center;"><h4>Control de Comisiones</h4></td>
        </tr>
        <tr>
            <td>Empleado</td>
            <td>Comisión por Servicios</td>
            <td>Comisión por Productos</td>
            <td>Total Comisión</td>
        </tr>
    </thead>
    <tbody>
@if(isset($dataComisiones['empleado']))
    @foreach($dataComisiones['empleado'] as $index => $nombre)
        <tr>
            <td rowspan="2">{{ $nombre }}</td>
            <td>${{ number_format($dataComisiones['qty_com_s'][$index] ?? 0, 2, '.', ',') }} Bruto</td>  
            <td>${{ number_format($dataComisiones['qty_com_p'][$index] ?? 0, 2, '.', ',') }} Bruto</td>  
            <td>${{ number_format($dataComisiones['total_comisiones'][$index] ?? 0, 2, '.', ',') }} Bruto</td>  
        </tr>
        <tr>
            <td>${{ number_format($dataComisiones['qty_com_s_neto'][$index] ?? 0, 2, '.', ',') }} Neto </td>
            <td>${{ number_format($dataComisiones['qty_com_p_neto'][$index] ?? 0, 2, '.', ',') }} Neto </td>
            <td>${{ number_format($dataComisiones['total_comisiones_neto'][$index] ?? 0, 2, '.', ',') }} Neto </td>
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
<!-- Termina tabla de comisiones -->