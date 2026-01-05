<div class="card-header">

<h4 class="text-center">Uso #{{ $itemSelected[0]['id']}}</h4>

<!-- <span class="float-right"><a class="details" wire:click="$emit('editar')" data-toggle="modal" data-target="#modalEditing">Editar</a></span> -->
<span class="float-right"><a class="details" data-dismiss="modal" wire:click="deleteUso">Eliminar Uso</a></span>
</div>
<h4 class="text-center">Resumen</h4>

<div class="table-responsive">
    <table class="table table-responsive-md table-hover  text-center">
        <thead class="thead-primary">
            <tr >
                <th style="background-color:transparent;color:#1d3557 !important">Material</th>
                <th style="background-color:transparent;color:#1d3557 !important">Consumido por</th>
                <th style="background-color:transparent;color:#1d3557 !important">Piezas</th>
                <th style="background-color:transparent;color:#1d3557 !important">Precio base</th>
            </tr>
        </thead>
        <tbody>
            @foreach($itemSelected as $material)
                <tr>
                    <td>{{ $material['producto']['name'] }}</td>
                    @if(isset($material['empleado']))
                    <td>{{ $material['empleado']['first_name'] }}</td>
                    @else
                    <td></td>
                    @endif
                    <td>{{ $material['qty'] }}</td>
                    <td> ${{ number_format($material['sale_price'] * $material['qty'] ,2,'.',',') }} </td>
                </tr>
            @endforeach
            <tr>
                @if(isset($itemSelected[0]['customer']))
                    <td>Cliente: {{ $itemSelected[0]['customer']['first_name'] . ' ' . $itemSelected[0]['customer']['last_name'] }}</td>
                @else
                    <td></td>
                @endif
                <td></td>
                <td></td>
                @if(isset($itemSelected[0]['asignacion']['date']))
                <td><a wire:click="$emit('viewDetails','{{ $itemSelected[0]['asignacion']['date']['id'] }}','citas')">Ver cita</a></td>
                @else
                <td></td>
                @endif
            </tr>
        </tbody>
    </table>
</div>
<br>
<div class="card-footer">
@php
    $updated = new DateTime($itemSelected[0]['created_at']);
    $updated_at = $updated->format('Y-m-d');
@endphp
<span>Última actualización: {{ $updated_at }}</span>
<span class="float-right">Responsable: {{ $itemSelected[0]['user']['name'] }}</span>
</div>

<style>
    .campos{
        border: unset;
        background-color: unset;
        text-align: center;
        border-bottom: 1px solid black;
    }
    .tamano-especial{
        width: 5rem;
    }
</style>