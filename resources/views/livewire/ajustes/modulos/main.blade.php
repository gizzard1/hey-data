<div class="card"style="background-color: white">
    <div class="card-header ">
        <div class="d-flex">
            <div class="separator" style="background-color:#E2BBB4"></div>
            <div class="mr-auto mt-3">
                <h4 class="card-title"><a wire:click="$set('infoSelected','1')">Ajustes</a>/ Comisiones</h4>
            </div>
        </div>
    </div>
    <div class="card-body mt-2 form-settings">
        <table class="table table-responsive-md table-hover  text-center" id="user-data">
            <thead>
                <tr class="text-center">
                    <th style="color:#1d3557 !important">Empleado</th>
                    <th style="color:#1d3557 !important">Comisiones por Servicios</th>
                    <th style="color:#1d3557 !important">Excepciones por Servicios</th>
                    <th style="color:#1d3557 !important">Comisiones por Productos</th>
                    <th style="color:#1d3557 !important">Excepciones por Productos</th>
                </tr>
            </thead>
            <tbody>
                
                @forelse($empleados as $empleado)
                <tr onclick="next()" class="text-center" wire:click="View({{ $empleado }})" style="cursor: pointer;">

                    <td ><a>{{ $empleado->first_name }} {{ $empleado->last_name }}</a></td>
                    @if($empleado->comision!=null)
                    <th >@if($empleado->comision->type_comission_s==='percent') {{ number_format($empleado->comision->qty_s,2,'.') }}% @else ${{ number_format($empleado->comision->qty_s,2,'.',',') }} @endif</th>
                    <td >{{ count($empleado->comision->excepcion_servicio) + count($empleado->comision->excepcion_cat_servicio) }}</td>
                    <th >@if($empleado->comision->type_comission_p==='percent') {{ number_format($empleado->comision->qty_p,2,'.') }}% @else ${{ number_format($empleado->comision->qty_p,2,'.',',') }} @endif</th>
                    <td >{{ count($empleado->comision->excepcion_producto) + count($empleado->comision->excepcion_cat_producto) }}</td>
                    @else
                    <td > 0 </td>
                    <td > 0 </td>
                    <td > 0 </td>
                    <td > 0 </td>
                    @endif
                @empty
                <tr>
                    <td colspan="8" class="text-center">SIN EMPLEADOS</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>