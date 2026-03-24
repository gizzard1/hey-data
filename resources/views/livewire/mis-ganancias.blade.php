<div class="row" wire:ignore.self>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex">
                    <div class="separator"></div>
                    <div class="mr-auto">
                        <h4 class="card-title mb-1">Mis ganancias</h4>
                        <p class="fs-14 mb-0"> Listado Registrado</p>
                    </div>
                </div>
                <div>
                    @include('livewire.informes.header.botonesFecha')
                </div>
                    
                <div class="float-right dropdown">
                    <button id="filtro" class="btn btn-sm input-group-text dropdown-toggle"  data-toggle="dropdown" aria-haspopup="true">Ver</button>
                    @include('livewire.comisiones.filtros')
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="table-responsive">
                            <table class="table table-responsive-md table-hover">
                                <thead class="thead-primary">
                                    <tr >
                                        <th style="background-color:transparent;color:#1D3557!important">Tipo</th>
                                        <th style="background-color:transparent;color:#1D3557!important">Descripción</th>
                                        <th style="background-color:transparent;color:#1D3557!important">Cliente</th>
                                        <th style="background-color:transparent;color:#1D3557!important">Total</th>
                                        <th style="background-color:transparent;color:#1D3557!important">Mi ganancia</th>
                                        <th style="background-color:transparent;color:#1D3557!important">Fecha</th>
                                    </tr>
                                </thead>
                                <tbody >
                                    @if(isset($dataGanancias))
                                    @foreach($dataGanancias['ingresosCitas'] as $asignacion)
                                        @php
                                            $final_price = 0;
                                            $priceOrDiscount = $asignacion->disccount_price && $asignacion->disccount_price > 0 ? $asignacion->disccount_price : $asignacion->current_price;
                                            $totalPrice = $priceOrDiscount * ($asignacion->quantity ?? 1);
                                            $final_price = $asignacion->discount_qty ? ($asignacion->discount_type == "Porcentaje" ? $totalPrice - ($totalPrice * ($asignacion->discount_qty / 100)) : $totalPrice - $asignacion->discount_qty) : $totalPrice;
                                        @endphp
                                        <tr>
                                            <td style="text-transform: capitalize;">Servicio</td>
                                            <td>{{ $asignacion->servicio->name }}</td>
                                            <td> {{ $asignacion->date->customer->first_name }} {{ $asignacion->date->customer->last_name }}</td>
                                            <td> ${{ number_format($asignacion->final_price,2,'.',',') }}</td>
                                            <td> ${{ number_format($asignacion->comission,2,'.',',') }} </td>
                                            <td> {{ date_format(new DateTime($asignacion->created_at),'d-m-Y') }} </td>
                                        </tr>
                                    @endforeach
                                    @foreach($dataGanancias['ingresosVentas'] as $asignacion)
                                        @php
                                            $final_price = 0;
                                            $priceOrDiscount = $asignacion->disccount_price && $asignacion->disccount_price > 0 ? $asignacion->disccount_price : $asignacion->current_price;
                                            $totalPrice = $priceOrDiscount * ($asignacion->quantity ?? 1);
                                            $final_price = $asignacion->discount_qty ? ($asignacion->discount_type == "Porcentaje" ? $totalPrice - ($totalPrice * ($asignacion->discount_qty / 100)) : $totalPrice - $asignacion->discount_qty) : $totalPrice;
                                        @endphp
                                        <tr>
                                            <td style="text-transform: capitalize;">Producto</td>
                                            <td>{{ $asignacion->product->name }}</td>
                                            @if(isset($asignacion->cita))
                                            <td> {{ $asignacion->cita->customer->first_name }} {{ $asignacion->cita->customer->last_name }}</td>
                                            <td> ${{ number_format($final_price,2,'.',',') }}</td>
                                            @elseif(isset($asignacion->sale))
                                            <td> {{ $asignacion->sale->customer->first_name }} {{ $asignacion->sale->customer->last_name }}</td>
                                            <td> ${{ number_format($final_price,2,'.',',') }}</td>
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
                                            <td> ${{ number_format($propina->venta->total - $propina->venta->disccount,2,'.',',') }} </td>
                                            @elseif(isset($propina->cita))
                                            <td> {{ $propina->cita->customer->first_name }} {{ $propina->cita->customer->last_name }}</td>
                                            <td> ${{ number_format($propina->cita->total - $propina->cita->disccount,2,'.',',') }} </td>
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
                    <div class="col-md-4">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-primary">
                                    <tr >
                                        <th style="background-color:transparent;color:#1D3557!important">Concepto</th>
                                        <th style="background-color:transparent;color:#1D3557!important">Total</th>
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
                </div>
            </div>
            <div class="card-footer">
                <span class="float-right">Balance: ${{ number_format($total,2,'.',',') }}</span>
            </div>
        </div>
    </div>
</div>
@include('livewire.informes.js-movimientos')
@include('livewire.informes.js-flatpickr')
