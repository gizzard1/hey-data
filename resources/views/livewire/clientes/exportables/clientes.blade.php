<table class="table table-responsive-md table-hover  text-center">
    @if(count($filtros)>0)
    <thead class="thead-primary">
        <tr>
            <th colspan="3">
                Filtros
            </th>
        </tr>
        <tr>
            <th>Filtro</th>
            <th>Parámetro 1</th>
            <th>Parámetro 2</th>
        </tr>
    </thead>
    <tbody>
        @foreach($filtros as $filtro)
        <tr>
            @if($filtro['type'] == 'postcode')
            <td>Código postal</td>
            @elseif($filtro['type'] == 'edad')
            <td>Edad</td>
            @elseif($filtro['type'] == 'birth_date')
            <td>Cumpleaños</td>
            @elseif($filtro['type'] == 'atencion')
            <td>Atendido por</td>
            @elseif($filtro['type'] == 'sexo')
            <td>Sexo</td>
            @elseif($filtro['type'] == 'procedencia')
            <td>Procedencia</td>
            @elseif($filtro['type'] == 'cat')
            <td>Categoría</td>
            @endif

            @if($filtro['query']!=null)
                <td>{{ $filtro['query'] }}</td>
            @else
                <td>{{ $filtro['min'] }}</td>
                <td>{{ $filtro['max'] }}</td>
            @endif
        </tr>
        @endforeach
        <tr></tr>
    </tbody>
    @endif
    

    <thead class="thead-primary">
        <tr>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Teléfono</th>
            <th>Correo</th>
            <th>Cumpleaños</th>
            <th>Código postal</th>
            <th>Sexo</th>
            <th>Desea mensajes personalizados</th>
            <th>Desea ofertas</th>
            <th>Procedencia</th>
            <th>Respondió el cuestionario</th>
            <th>Fecha de creación</th>
        </tr>
    </thead>
    <tbody>
    @foreach($clientes as $cliente)
        <tr>
            <td>{{ $cliente->first_name }}</td>
            <td>{{ $cliente->last_name }}</td>
            <td>{{ $cliente->phone }}</td>
            <td>{{ $cliente->email }}</td>
            <td>{{ $cliente->birth_date }}</td>
            <td>{{ $cliente->postcode }}</td>
            <td>{{ $cliente->sexo }}</td>
            <td>{{ $cliente->want_custom_messages }}</td>
            <td>{{ $cliente->want_offers }}</td>
            <td>{{ $cliente->procedencia ? $cliente->procedencia->name : '' }}</td>
            <td>{{ $cliente->form_answered }}</td>
            <td>{{ $cliente->created_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>