<table class="table table-responsive-md table-hover  text-center">
    @if($cat != null)
    <thead class="thead-primary">
        <tr>
            <th>Categoría seleccionada:</th>
            <th>{{ $cat }}</th>
        </tr>
    </thead>
    <tr></tr>
    @endif
    

    <thead class="thead-primary">
        <tr>
            <th>Nombre</th>
            <th>Proveedor</th>
            <th>Descripción</th>
            <th>Precio público</th>
            <th>Precio descuento</th>
            <th>IVA</th>
            <th>Duración</th>
            <th>Fecha de creación</th>
        </tr>
    </thead>
    <tbody>
    @foreach($servicios as $servicio)
        <tr>
            <td>{{ $servicio->name }}</td>
            <td>{{ $servicio->marca?->name }}</td>
            <td>{{ $servicio->description }}</td>
            <td>{{ $servicio->gross_price }}</td>
            <td>{{ $servicio->disccount_price }}</td>
            <td>{{ $servicio->iva }}</td>
            <td>{{ $servicio->duration }}</td>
            <td>{{ $servicio->created_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>