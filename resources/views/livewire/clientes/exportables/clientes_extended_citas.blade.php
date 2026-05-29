<table class="table table-responsive-md table-hover text-center">
    <thead class="thead-primary">
        <tr>
            <th>Cliente_id</th>
            <th>Cliente</th>
            <th>ID Cita</th>
            <th>Servicios</th>
            <th>Productos</th>
            <th>Inicio</th>
            <th>Fin</th>
            <th>Estatus</th>
            <th>Total</th>
            <th>Descuento</th>
            <th>Puntos Generados</th>
        </tr>
    </thead>
    <tbody>
    @foreach($clientes as $cliente)
        @foreach($cliente->citas ?? [] as $cita)
            <tr>
                <td>{{ $cliente->id }}</td>
                <td>{{ trim(($cliente->first_name ?? '') . ' ' . ($cliente->last_name ?? '')) }}</td>
                <td>{{ $cita->id }}</td>
                <td>@foreach($cita->details ?? [] as $detail){{ $detail->servicio->name ?? '' }}@if(!$loop->last), @endif @endforeach</td>
                <td>@foreach($cita->details_product ?? [] as $detail){{ $detail->product->name ?? '' }}@if(!$loop->last), @endif @endforeach</td>
                <td>{{ $cita->start }}</td>
                <td>{{ $cita->end }}</td>
                <td>{{ $cita->status }}</td>
                <td>{{ $cita->total }}</td>
                <td>{{ $cita->disccount }}</td>
                <td>{{ $cita->generated_points }}</td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
