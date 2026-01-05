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
                                    <a class="nav-link" href="{{ route(name: 'informe') }}"><i class="la la-box mr-2"></i> Histórico</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" id="ventas-w"><i class="la la-store mr-2"></i> Empleados</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="ventas-w" href="{{ route(name: 'informe-movimientos') }}"><i class="la la-store mr-2"></i> Transacciones</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        <div style="display: inline-flex;cursor:pointer;" class="flatpickrInd" >
                            <div id="currentDate">
                            {{ $currentDate }}
                            </div>
                            <div id="currentDateEnd">
                            @if($is_interval) <div>- {{ $currentDateEnd }}</div>@endif
                            </div>

                        </div>
                            <div style="width:fit-content;display:inline">
                                <i type="button" class="las la-calendar dropdown-toggle" data-toggle="dropdown"></i>
                                <div class="dropdown-menu">
                                    <a style="cursor: pointer;" class="dropdown-item dropright flatpickr" data-toggle="dropdown">Elegir periodo</a>
                                    <a style="cursor: pointer;" class="dropdown-item" wire:click="returnToday">Hoy</a>
                                    <a style="cursor: pointer;" class="dropdown-item" wire:click="returnYesterday">Ayer</a>
                                    <a style="cursor: pointer;" class="dropdown-item" wire:click="setWeek">Semanal</a>
                                    <a style="cursor: pointer;" class="dropdown-item" wire:click="setMonth">Mensual</a>
                                    <a style="cursor: pointer;" class="dropdown-item" wire:click="setYear">Anual</a>
                                </div>
                            </div>
                    </div>
                    
                    @if(Auth::user()->role=='admin')
                    <div class="botones-exportar">
                        <button wire:click="generateExcel" class="btn-sm excel-button input-group-text">Exportar Informe</button>   
                        <!-- <button class="button-style" wire:click="generatePdf" style="border-width: 0;color:red"><i class="las la-file-pdf la-2x"></i></button>
                        <button class="button-style" wire:click="generateExcel" style="border-width: 0;color:#68d100"><i class="las la-file-excel la-2x"></i></button> -->
                    </div>
                    @endif
                </div>
                
                <!-- Tabla de comisiones -->
                <h4 class="text-center" style="margin-top: 3rem;">Control de Comisiones</h4>
                <table class="table table-responsive-md   text-left" style="margin-top:2rem; margin-bottom:3rem;width:-webkit-fill-available">
                    <thead>
                        <tr style="color:#60060F;font-weight:bold">
                            <td>Empleado</td>
                            <td>Ingresos por Servicios</td>
                            <td>Comisión por Servicios</td>
                            <td>Ingresos por Productos</td>
                            <td>Comisión por Productos</td>
                            <td>Ingresos Totales</td>
                            <td>Total Comisión</td>
                        </tr>
                    </thead>
                    <tbody>
                @if(isset($dataComisiones['empleado']))
                    @foreach($dataComisiones['empleado'] as $index => $nombre)
                        <tr>
                            <td>{{ $nombre }}</td>
                            <td> ${{ number_format($dataComisiones['qty_s'][$index] ?? 0, 2, '.', ',') }}   <small>Bruto</small> <br>  ${{ number_format($dataComisiones['qty_s_neto'][$index] ?? 0, 2, '.', ',') }}  <small>Neto</small></td>
                            <td> ${{ number_format($dataComisiones['qty_com_s'][$index] ?? 0, 2, '.', ',') }}   <small>Bruto</small> <br>  ${{ number_format($dataComisiones['qty_com_s_neto'][$index] ?? 0, 2, '.', ',') }}  <small>Neto</small></td>
                            <td> ${{ number_format($dataComisiones['qty_p'][$index] ?? 0, 2, '.', ',') }}   <small>Bruto</small> <br>  ${{ number_format($dataComisiones['qty_p_neto'][$index] ?? 0, 2, '.', ',') }}  <small>Neto</small></td>
                            <td> ${{ number_format($dataComisiones['qty_com_p'][$index] ?? 0, 2, '.', ',') }}   <small>Bruto</small> <br>  ${{ number_format($dataComisiones['qty_com_p_neto'][$index] ?? 0, 2, '.', ',') }}  <small>Neto</small></td>
                            <td> ${{ number_format($dataComisiones['total_incomes'][$index] ?? 0, 2, '.', ',') }}   <small>Bruto</small> <br>  ${{ number_format($dataComisiones['total_incomes_neto'][$index] ?? 0, 2, '.', ',') }}  <small>Neto</small></td>
                            <td> ${{ number_format($dataComisiones['total_comisiones'][$index] ?? 0, 2, '.', ',') }}   <small>Bruto</small> <br>  ${{ number_format($dataComisiones['total_comisiones_neto'][$index] ?? 0, 2, '.', ',') }}  <small>Neto</small></td>
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
                <!-- Chart Duraciones -->
                <div style="margin-bottom:4rem;text-align: -webkit-center;display:grid" id="chart">
                    <h4 class="text-center">Retención de Clientes en Salón (minutos)</h4>
                    <canvas class="chartjs-render-monitor grafica-gd" id="chart-durations" width="1402" height="525" ></canvas>
                </div>
                <!-- Termina chart duraciones -->
                <!-- Tabla de propinas -->
                <h4 class="text-center">Control de Propinas</h4>

                <table class="table table-responsive-md   text-center" style="margin-top:2rem; margin-bottom:3rem;width:-webkit-fill-available">
                    <thead>
                        <tr style="color:#60060F;font-weight:bold">
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
                <!-- Tabla de clientes atendidos -->
                <h4 class="text-center">Clientela</h4>

                <table class="table table-responsive-md   text-center" style="margin-top:2rem; margin-bottom:3rem;width:-webkit-fill-available">
                    <thead>
                        <tr style="color:#60060F;font-weight:bold">
                            <td>Empleado</td>
                            <td>Clientes Atendidos</td>
                            <td>Clientes Creados</td>
                            <td>Clientes que Regresan al Salón</td>
                            <td>Clientes que Regresan con el Estilista</td>
                        </tr>
                    </thead>
                    <tbody>
                    @if(isset($dataClientes['empleado']))
                    @foreach($dataClientes['empleado'] as $index => $nombre)
                        <tr>
                            <td>{{ $nombre }}</td>
                            <td class="text-center">{{ $dataClientes['qty'][$index] }} </td>
                            <td class="text-center">{{ $dataClientes['qty_clientesNew'][$index] }} </td>
                            <td class="text-center">{{ $dataClientes['qty_regresan'][$index] }} </td>
                            <td class="text-center">{{ $dataClientes['qty_regresan_w_estilista'][$index] }} </td>
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
                <!-- Termina Tabla de clientes atendidos -->
                <!-- Tabla de clientes atendidos -->
                <!-- <h4 class="text-center">Conexiones Recientes al Sistema</h4>

                <table class="table table-responsive-md   text-center" style="margin-top:2rem; margin-bottom:3rem;width:-webkit-fill-available">
                    <thead>
                        <tr style="color:#60060F;font-weight:bold">
                            <td>Usuario</td>
                            <td>Tiempo de Conexión</td>
                            <td>Dirección IP</td>
                        </tr>
                    </thead>
                    <tbody>
                    @if(isset($dataLog['users']))
                        @foreach($dataLog['users'] as $index => $nombre)
                            <tr>
                                <td>{{ $nombre }}</td>
                                @php
                                    $minutes = $dataLog['connection_time'][$index];
                                    $hours = 0;
                                    $remainingMinutes = 0;

                                    // Verificar si $minutes es un número antes de realizar operaciones matemáticas
                                    if (is_numeric($minutes)) {
                                        $hours = floor($minutes / 60);
                                        $remainingMinutes = $minutes % 60;
                                    }
                                @endphp
                                @if($hours>0||$remainingMinutes>0)
                                <td>{{ $hours }} hr {{ $remainingMinutes }} min</td>
                                @else
                                <td>{{ $dataLog['connection_time'][$index] }}</td>
                                @endif
                                <td>{{ $dataLog['ip'][$index] }}</td>
                            </tr>
                        @endforeach
                    @else -->
                        <!-- Manejo si $dataLog['users'] no está definido o está vacío -->
                    <!-- @endif
                    </tbody>
                </table> -->
                <!-- Termina Tabla de clientes atendidos -->
            </div>
        </div>
    </div>
</div>
@include('livewire.informes.js-empleados')
@else
@include('livewire.sinPermisos')
@endif
<style>
    a{
        color:#1d3557
    }
</style>