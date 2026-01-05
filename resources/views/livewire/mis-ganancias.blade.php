<div class="row" wire:ignore.self>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header ">
                <div class="d-flex">
                    <div class="separator" style="background-color:#E2BBB4"></div>
                    <div class="mr-auto">
                        <h4 class="card-title mb-1" style="color: #9D1466;">Mis ganancias</h4>
                        <p class="fs-14 mb-0"> Listado Registrado</p>
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
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover  text-center">
                        <thead class="thead-primary">
                            <tr >
                                <th style="background-color:transparent;color:#9D1466 !important">Concepto</th>
                                <th style="background-color:transparent;color:#9D1466 !important">Total</th>
                            </tr>
                        </thead>
                        <tbody >
                            @if($dataGanancias)
                                <tr>
                                    <td style="text-transform: capitalize;">Propinas</td>
                                    <td> ${{ number_format($dataGanancias['totalPropinas'],2,'.',',') }} </td>
                                </tr>
                                <tr>
                                    <td style="text-transform: capitalize;">Comisiones</td>
                                    <td> ${{ number_format($dataGanancias['totalComissions'],2,'.',',') }} </td>
                                </tr>
                                <tr>
                                    <td style="text-transform: capitalize;">Total</td>
                                    <td> ${{ number_format($dataGanancias['total'],2,'.',',') }} </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-header ">
                <div class="d-flex">
                    <div class="separator" style="background-color:#E2BBB4"></div>
                    <div class="mr-auto">
                        <h4 class="card-title mb-1" style="color: #9D1466;">Detalles</h4>
                        <p class="fs-14 mb-0"> Listado Registrado</p>
                    </div>
                </div>
                <div>
                    
                </div>
                    <i type="button" id="filtro" class="las la-filter dropdown-toggle" data-toggle="dropdown"><h6 style="display: inline;font-weight: normal;font-family: 'poppins', sans-serif;">Filtros</h6></i>
                
                    <div class="dropdown-menu">
                        <div style="display: grid;grid-row-gap: 10px;padding: 5px;">
                            <div>
                                <h8 style="margin-left: 1rem;padding: 0;" >Ventas</h8> 
                                <input type="checkbox" style="margin-left: 36%;" wire:model.defer="ventasFilter">
                            </div>
                            <div>
                                <h8 style="margin-left: 1rem;padding: 0;" >Citas</h8> 
                                <input type="checkbox" style="margin-left: 46%;" wire:model.defer="citasFilter">
                            </div>
                            <div>
                                <h8 style="margin-left: 1rem;padding: 0;" >Propinas</h8> 
                                <input type="checkbox" style="margin-left: 27%;" wire:model.defer="propinasFilter">
                            </div>
                        </div>
                        
                        <button class="btn btn-sm btn-info save" wire:click="aplicarFiltros" style="background-color: #9E846D;margin-left: 52%;border-color:#9E846D">
                            Aplicar
                        </button>
                    </div>
            </div>



            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover  text-center">
                        <thead class="thead-primary">
                            <tr >
                                <th style="background-color:transparent;color:#9D1466 !important">Tipo</th>
                                <th style="background-color:transparent;color:#9D1466 !important">Descripción</th>
                                <th style="background-color:transparent;color:#9D1466 !important">Cliente</th>
                                <th style="background-color:transparent;color:#9D1466 !important">Total</th>
                                <th style="background-color:transparent;color:#9D1466 !important">Fecha</th>
                            </tr>
                        </thead>
                        <tbody >
                            @if(isset($dataGanancias))
                            @foreach($dataGanancias['ingresosCitas'] as $asignacion)
                                <tr>
                                    <td style="text-transform: capitalize;">Servicio</td>
                                    <td>{{ $asignacion->servicio->name }}</td>
                                    <td> {{ $asignacion->date->customer->first_name }} {{ $asignacion->date->customer->last_name }}</td>
                                    <td> ${{ number_format($asignacion->comission,2,'.',',') }} </td>
                                    <td> {{ date_format(new DateTime($asignacion->created_at),'d-m-Y') }} </td>
                                </tr>
                            @endforeach
                            @foreach($dataGanancias['ingresosVentas'] as $asignacion)
                                <tr>
                                    <td style="text-transform: capitalize;">Producto</td>
                                    <td>{{ $asignacion->product->name }}</td>
                                    @if(isset($asignacion->cita))
                                    <td> {{ $asignacion->cita->customer->first_name }} {{ $asignacion->cita->customer->last_name }}</td>
                                    @elseif(isset($asignacion->sale))
                                    <td> {{ $asignacion->sale->customer->first_name }} {{ $asignacion->sale->customer->last_name }}</td>
                                    @endif
                                    <td> ${{ number_format($asignacion->comission,2,'.',',') }} </td>
                                    <td> {{ date_format(new DateTime($asignacion->created_at),'d-m-Y') }} </td>
                                </tr>
                            @endforeach
                            @foreach($dataGanancias['propinas'] as $propina)
                                <tr>
                                    <td style="text-transform: capitalize;">Propina</td>
                                    <td>{{ $propina->Payment_method }}</td>
                                    @if(isset($propina->venta))
                                    <td> {{ $propina->venta->customer->first_name }} {{ $propina->venta->customer->last_name }}</td>
                                    @elseif(isset($propina->cita))
                                    <td> {{ $propina->cita->customer->first_name }} {{ $propina->cita->customer->last_name }}</td>
                                    @endif
                                    <td> ${{ number_format($propina->amount,2,'.',',') }} </td>
                                    <td> {{ date_format(new DateTime($propina->created_at),'d-m-Y') }} </td>
                                </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer">
                <span class="float-right">Balance: ${{ number_format($total,2,'.',',') }}</span>
            </div>
        </div>
    </div>
</div>
@include('livewire.informes.js-movimientos')
