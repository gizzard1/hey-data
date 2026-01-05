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
                                    <a class="nav-link" href="{{ route(name: 'comisiones-empleados') }}"><i class="la la-user mr-2"></i> Comisiones</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" id="ventas-w"><i class="la la-piggy-bank mr-2"></i> Propinas</a>
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
                    <!-- Tabla de propinas -->

                    <table class="table table-responsive-md text-center" style="width:-webkit-fill-available">
                        <thead>
                            <tr>
                                <td>Empleado</td>
                                <td>Propinas recibidas</td>
                                <td>Pago en Efectivo</td>
                                <td>Pago en MSI</td>
                                @foreach($terminales as $terminal)
                                    <td>{{ $terminal }}</td>
                                @endforeach
                                <td>Total Propinas</td>
                            </tr>
                        </thead>
                        <tbody>
                        @if(isset($dataPropinas['empleado']))
                        @foreach($dataPropinas['empleado'] as $index => $nombre)
                            <tr>
                                <td>{{ $nombre }}</td>
                                <td class="text-center">{{ $dataPropinas['qty'][$index] }} </td>
                                <td>${{ number_format($dataPropinas['Efectivo'][$index] ?? 0, 2, '.', ',') }} </td>
                                <td>${{ number_format($dataPropinas['MSI'][$index] ?? 0, 2, '.', ',') }} </td>
                                @foreach($terminales as $terminal)
                                    <td>${{ number_format($dataPropinas[$terminal][$index] ?? 0, 2, '.', ',') }} </td>
                                @endforeach
                                <td>${{ number_format($dataPropinas['total'][$index] ?? 0, 2, '.', ',') }} </td>
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
                    <!-- Termina Tabla de propinas -->
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