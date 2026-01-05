
<div class="card-header ">
<h4 class="text-center">Apertura de Caja #{{ $itemSelected->id}}</h4>
</div>


<div class="table-responsive">
    <table class="table table-responsive-md table-hover  text-center">
        <thead class="thead-primary">
            <tr>
                <th style="background-color:transparent;color:#1d3557 !important">Detalle</th>
                <th style="background-color:transparent;color:#1d3557 !important">Monto</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Caja chica</td>
                <td> ${{ number_format($itemSelected->caja_chica,2,'.',',') }} </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="card-footer">
<span>Última actualización: {{ $itemSelected->created_at }}</span>
<span class="float-right">Responsable: {{ $itemSelected->user->name }}</span>
</div>