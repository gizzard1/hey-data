@if(Auth::user()->role!=='estilista')
<div class="reporte-global">
    <div class="row">
        <div class="col-sm-12">
            <div class="card" id="reporte-global">
                <div class="card-header ">
                    <div class="d-flex">
                        <div class="default-tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route(name: 'empleados') }}"><i class="la la-user mr-2"></i> Empleados</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" id="ventas-w"><i class="la la-chart-bar mr-2"></i> Comisiones</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route(name: 'propinas-empleados') }}"><i class="la la-piggy-bank mr-2"></i> Propinas</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        @include('livewire.informes.header.botonesFecha')
                    </div>
                    
                    @if(Auth::user()->role=='admin')
                    <div class="botones-exportar">
                        <button wire:click="generateExcel" class="btn-sm excel-button input-group-text">Exportar Informe</button>   
                    </div>
                    @endif
                </div>
                <div class="card-body data-comissions">
                    <div>
                        <!-- Tabla de comisiones -->
                        <table class="table table-responsive-md table-hover text-left" style="width:-webkit-fill-available">
                            <thead>
                                <tr>
                                    <td><strong> Empleado</strong></td>
                                    <td><strong> Comisión por Servicios</strong></td>
                                    <td><strong> Comisión por Productos</strong></td>
                                    <td><strong> Total Comisión</strong></td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                        @if(isset($dataComisiones['empleado']))
                            @foreach($dataComisiones['empleado'] as $index => $nombre)
                                <tr>
                                    <td>{{ $nombre }}</td>
                                    <td> ${{ number_format($dataComisiones['qty_com_s'][$index] ?? 0, 2, '.', ',') }}</td>
                                    <td> ${{ number_format($dataComisiones['qty_com_p'][$index] ?? 0, 2, '.', ',') }}</td>
                                    <td> ${{ number_format($dataComisiones['total_comisiones'][$index] ?? 0, 2, '.', ',') }}</td>
                                    <td>
                                        @if($nombre !== 'Total')
                                            <a wire:click="employeeSelecter('{{ $dataComisiones['id'][$index] }}')">Detalles</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @else
                            <tr>
                                <td></td>
                                <td></td>
                                <td>No hay datos para mostrar</td>
                            </tr>
                            @endif
                            </tbody>
                        </table>
                        <!-- Termina tabla de comisiones -->
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@else
@include('livewire.sinPermisos')
@endif
<style>
    a{
        color:#1d3557
    }
</style>

@include('livewire.informes.js-flatpickr')

<script>
    
document.addEventListener('DOMContentLoaded', function(){
    initFlats();
})
</script>