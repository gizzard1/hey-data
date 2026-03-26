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
            <th>Sku</th>
            <th>Descripción</th>
            <th>Stock</th>
            <th>Precio público</th>
            <th>Precio descuento</th>
            <th>Costo</th>
            <th>IVA</th>
            <th>Tipo</th>
            <th>Fecha de creación</th>
        </tr>
    </thead>
    <tbody>
    @foreach($servicios as $producto)
        <tr>
            <td>{{ $producto->name }}</td>
            <td>{{ $producto->marca?->name }}</td>
            <td>{{ $producto->sku }}</td>
            <td>{{ $producto->description }}</td>
            <td>{{ $producto->stock_qty }}</td>
            <td>{{ $producto->gross_price }}</td>
            <td>{{ $producto->disccount_price }}</td>
            <td>{{ $producto->costo }}</td>
            <td>{{ $producto->iva }}</td>
            <td>{{ $producto->type_product == 'simple' ? 'Mercancía' : 'Uso' }}</td>
            <td>{{ $producto->created_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>