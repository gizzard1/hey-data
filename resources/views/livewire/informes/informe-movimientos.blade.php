<table class="table table-responsive-md table-hover  text-center">
    <thead class="thead-primary">
        <tr >
            <th style="color:#9D1466 !important">Tipo</th>
            <th style="color:#9D1466 !important">Estatus</th>
            <th style="color:#9D1466 !important">Fecha</th>
            <th style="color:#9D1466 !important">Descuentos</th>
            <th style="color:#9D1466 !important">Puntos generados</th>
            <th style="color:#9D1466 !important">Total</th>
            <th style="color:#9D1466 !important">Cliente</th>
            <th style="color:#9D1466 !important">Versión</th>
            <th style="color:#9D1466 !important"></th>
        </tr>
    </thead>
    <tbody >
        @if(isset($dataMovimientos))
        @forelse($dataMovimientos as $key => $value)
            @if(isset($value) && ($key == 'citas' || $key == 'ventas'))
            @foreach($value as $item)
                <tr>
                    <td style="text-transform: capitalize;">{{ $key }}</td>
                    <td>{{ $item->status }}</td>
                    <td> {{ date_format(new DateTime($item->created_at),'d-m-Y') }} </td>
                    <td> ${{ number_format($item->disccount,2,'.',',') }} </td>
                    <td> {{ number_format($item->generated_points,2,'.',',') }} </td>
                    <td> ${{ number_format($item->total,2,'.',',') }} </td>
                    <td> {{ $item->customer ? $item->customer->first_name : '' }} {{ $item->customer ? $item->customer->last_name : '' }}</td>
                    <td> Última act. {{ date_format(new DateTime($item->updated_at),'d-m-Y') }} por {{ $item->user->name }} </td>
                </tr>
            @endforeach
            @elseif(isset($value) && ($key == 'usos'))
                @foreach($value as $materials)

                @php
                    $total_pzs = 0;
                    foreach($materials as $material){
                        $total_pzs += $material->qty;
                    }    
                @endphp

                    <tr>
                        <td style="text-transform: capitalize;">uso</td>
                        <td>Aplicado</td>
                        <td> {{ date_format(new DateTime($materials[0]->created_at),'d-m-Y') }} </td>
                        <td>-</td>
                        <td>-</td>
                        <td> {{ $total_pzs }} pzs. </td>
                        @if(isset($materials[0]->customer))
                        <td> {{ $materials[0]->customer->first_name }} {{ $materials[0]->customer->last_name }}</td>
                        @else
                        <td>-</td>
                        @endif
                        <td> Última act. {{ date_format(new DateTime($materials[0]->updated_at),'d-m-Y') }} por {{ $materials[0]->user->name }} </td>   
                    </tr>
                @endforeach
            @elseif(isset($value) && ($key == 'cortes' || $key == 'aperturas'))
            @foreach($value as $item)
                <tr>
                    <td style="text-transform: capitalize;">{{ $key === 'cortes' ? 'cierres de caja' : 'aperturas de caja' }}</td>
                    @if($key == 'cortes')
                    <td>Registrado</td>
                    @elseif($key == 'aperturas')
                        @if($item->caja_corte_id == null)
                        <td>Apertura Actual</td>
                        @else
                        <td>Cerrada</td>
                        @endif
                    @endif
                    <td> {{ date_format(new DateTime($item->created_at),'d-m-Y') }} </td>
                    <td></td>
                    @if($key == 'cortes')
                    <td>{{$item->total_points}}</td>
                    <td> ${{ number_format($item->ganancia,2,'.',',') }} </td>
                    @else
                    <td></td>
                    <td></td>
                    @endif
                    <td></td>
                    <td> Última act. {{ date_format(new DateTime($item->updated_at),'d-m-Y') }} por {{ $item->user->name }} </td>
                </tr>
            @endforeach
            @endif
        @empty
        <tr>
            <td colspan="5">No hay movimientos</td>
        </tr>
        @endforelse
        @endif
    </tbody>
</table>