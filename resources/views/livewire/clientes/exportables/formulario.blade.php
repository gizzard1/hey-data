<table class="table table-responsive-md table-hover  text-center">
    <thead class="thead-primary">
        <tr >
            <th style="color:#9D1466 !important">Nombre</th>
            <th style="color:#9D1466 !important">Apellido</th>
            <th style="color:#9D1466 !important">Teléfono</th>
            <th style="color:#9D1466 !important">Correo</th>
            <th style="color:#9D1466 !important">Cumpleaños</th>
            <th style="color:#9D1466 !important">Código postal</th>
            <th style="color:#9D1466 !important">Sexo</th>
            <th style="color:#9D1466 !important">Calificación</th>
            <th style="color:#9D1466 !important">Comentario</th>
            @foreach($preguntas as $pregunta)
                @if($pregunta->id != 2)
                <th style="color:#9D1466 !important">{{ $pregunta->title }}</th>
                @endif
            @endforeach
            <th style="color:#9D1466 !important">¿Qué servicio te brindaron en el salón?</th>
            <th style="color:#9D1466 !important">Fecha en que se respondió</th>
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
            @if(count($cliente->reviews)>0)
            <td>{{ $cliente->reviews->first()->puntaje }}</td>
            <td>{{ $cliente->reviews->first()->comentario }}</td>
            @else
            <td></td>
            <td></td>
            @endif
            @foreach($cliente->respuestas as $respuesta)
                @if($respuesta->pregunta_id == 1)
                    <td>{{ $respuesta->eleccion }}</td>
                @elseif($respuesta->pregunta_id == 3 || $respuesta->pregunta_id == 4)
                    <td>
                        @if($respuesta->eleccion)
                            Sí
                        @else
                            No
                        @endif
                    </td>
                @endif
            @endforeach
            <td>
                {{-- Iterar respuestas de la pregunta 2 en una misma celda --}}
                @foreach($cliente->respuestas as $multiRespuesta)
                @if($multiRespuesta->pregunta_id == 2)
                    @if($multiRespuesta->eleccion == 'A')
                        highlights / Balayage / Efecto de Color
                    @elseif($multiRespuesta->eleccion == 'B')
                        Tinte
                    @elseif($multiRespuesta->eleccion == 'C')
                        Corte de Cabello
                    @elseif($multiRespuesta->eleccion == 'D')
                        Peinado
                    @elseif($multiRespuesta->eleccion == 'E')
                        Tratamiento Capilar
                    @elseif($multiRespuesta->eleccion == 'F')
                        Manicure / Pedicure
                    @elseif($multiRespuesta->eleccion == 'G')
                        Aplicación de Gel en uñas
                    @elseif($multiRespuesta->eleccion == 'H')
                        Maquillaje
                    @elseif($multiRespuesta->eleccion == 'I')
                        Acrílico
                    @endif
                    ,
                @endif
                @endforeach
            </td>
            <td>{{ $cliente->respuestas->first()->created_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>