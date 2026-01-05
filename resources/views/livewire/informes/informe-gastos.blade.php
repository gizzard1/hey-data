<table class="table table-responsive-md text-center" style="background-color:aqua">
    <thead>
        <tr>
            <th>Tipo de gasto</th>
            <th>Descripción</th>
            <th>Categoría</th>
            <th>Monto (bruto)</th>
            <th>Monto (neto)</th>
            <th>Clasif. fiscal</th>
            <th>Forma de Pago</th>
            <th>Proveedor</th>
            <th>Folio fiscal</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody> 
    @php
        $total = 0;
        $total_neto = 0;
    @endphp
    @foreach($gastos as $item)
    <tr>
        <td>{{ $item->description }}</td>
        <td>{{ $item->note }}</td>
        <td>{{ $item->categoria->name ?? '' }}</td>
        <td>${{ number_format(floatval($item->total),2,".",",") }}</td>
        <td>${{ number_format(floatval($item->total - ($item->total * $item->iva)),2,".",",") }}</td>
        <td> {{ $item->type == "Acreditable" ? 'Deducible' : 'No deducible' }} </td>
        <td>{{ $item->payment_method }}</td>
        <td>{{ $item->marca->name ?? '' }}</td>
        <td>{{ $item->folio_fiscal }}</td>
        <td>{{ date_format(new DateTime($item->date), 'd-m-Y') }}</td>
    </tr>
    @php
        $total+=$item->total;
        $total_neto+=$item->total - ($item->total * $item->iva);
    @endphp
    @endforeach
    
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>${{ number_format(floatval($total ),2)}}</td>
        <td>${{ number_format(floatval($total_neto),2)}}</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    </tbody>
</table>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
    }
    thead {
        background-color: #e6e6f2;
    }
    thead th {
        padding: 10px;
        border-bottom: 2px solid #d3d3e2;
        font-size: 16px;
        font-weight: bold;
    }
    tbody tr:nth-child(odd) {
        background-color: #fbfbfd;
    }
    tbody tr:nth-child(even) {
        background-color: #e9e9ef;
    }
    td {
        padding: 8px;
        border-bottom: 1px solid #dcdce0;
        font-size: 14px;
        text-align: center;
    }
    .center {
        text-align: center;
    }
</style>