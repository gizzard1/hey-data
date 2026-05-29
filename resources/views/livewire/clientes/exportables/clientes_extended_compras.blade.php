<table class="table table-responsive-md table-hover text-center">
    <thead class="thead-primary">
        <tr>
            <th>Cliente_id</th>
            <th>Cliente</th>
            <th>ID Compra</th>
            <th>Productos</th>
            <th>Fecha</th>
            <th>Estatus</th>
            <th>Total</th>
            <th>Descuento</th>
            <th>Puntos Generados</th>
        </tr>
    </thead>
    <tbody>
    @foreach($clientes as $cliente)
        @foreach($cliente->compras ?? [] as $compra)
            <tr>
                <td>{{ $cliente->id }}</td>
                <td>{{ trim(($cliente->first_name ?? '') . ' ' . ($cliente->last_name ?? '')) }}</td>
                <td>{{ $compra->id }}</td>
                <td>@foreach($compra->details ?? [] as $detail){{ $detail->product->name ?? '' }}@if(!$loop->last), @endif @endforeach</td>
                <td>{{ $compra->created_at }}</td>
                <td>{{ $compra->status }}</td>
                <td>{{ $compra->total }}</td>
                <td>{{ $compra->disccount }}</td>
                <td>{{ $compra->generated_points }}</td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
