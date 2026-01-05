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
                                    <a class="nav-link" id="ventas-w" href="{{ route(name: 'comisiones-empleados') }}"><i class="la la-chart-bar mr-2"></i> Comisiones</a>
                                </li>
                                <li class="nav-item" wire:ignore>
                                    <a class="nav-link active"><i class="la la-user mr-2"></i> {{ $selectedEmployee->name }}</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        @include('livewire.informes.header.botonesFecha')
                    </div>
                </div>
                <div class="card-body card-body-data cuerpo-informe" style="margin-left: 2rem;margin-right:2rem;">
                    <div class="default-tab">
                        <ul class="nav nav-tabs" role="tablist" style="width:fit-content">
                            <li class="nav-item">
                                <a class="nav-link {{ $pestaña == 1 ? 'active' : '' }}" name="pestaña-servicios" onclick="changeTo(1)"><i class="la la-calendar-check mr-2"></i> Servicios</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $pestaña == 2 ? 'active' : '' }}" name="pestaña-productos" onclick="changeTo(2)"><i class="la la-clock mr-2"></i> Productos</a>
                            </li>
                        </ul>
                        <div  class="d-flex">
                            <table class="table table-responsive-md table-hover text-left">
                                <thead>
                                    <tr>
                                        <td><strong>Nombre</strong> </td>
                                        <td><strong>Precio</strong> </td>
                                        <td><strong>Tipo de comisión</strong> </td>
                                        <td><strong>Comisión</strong> </td>
                                        <td><strong>Fecha</strong> </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($movs as $mov)
                                        <tr class="data {{ 'methods' . $mov->id }}" onclick="toggleMethods({{ $mov->id }})" style="cursor: pointer;">
                                            <td>{{$pestaña==2 ? '('.$mov->quantity.')' : ''}} {{ $mov->name }}</td>
                                            <td>{{ '$' . number_format($mov->price,2,'.',',') }}</td>
                                            <td>{{ $mov->type_comision_calculated=='qty' ? '$' . number_format($mov->type_comission,2,'.',',') : number_format($mov->type_comission,2,'.',',') . '%' }}</td>
                                            <td>{{ '$' . number_format($mov->comission*($mov->quantity??1),2,'.',',') }}</td>
                                            <td>{{ $mov->date }}</td>
                                        </tr>
                                        <tr class="methods-row methods{{ $mov->id }} toggled text-center">
                                            <td></td>
                                            <td colspan="2">
                                                <a href="{{ route('clientes', ['custId'=>$mov->customer->id]) }}">
                                                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                    </svg>
                                                    {{ $mov->customer->first_name . ' ' . $mov->customer->last_name }}
                                                </a>
                                            </td>
                                            <td>
                                                <td class="text-left"><a wire:click="editar('{{ $mov->id }}')">Vista avanzada</a></td>
                                            </td>
                                        </tr>
                                        @if(count($mov->metodosPago) > 0)
                                        <tr class="methods-row methods{{ $mov->id }} toggled">
                                            <th></th>
                                            <th style="background-color:transparent;color:#1d3557 !important">Concepto</th>
                                            <th style="background-color:transparent;color:#1d3557 !important">Cantidad</th>
                                            <th style="background-color:transparent;color:#1d3557 !important">Referencia</th>
                                            <th style="background-color:transparent;color:#1d3557 !important">Fecha</th>
                                        </tr>
                                        @else
                                        <tr class="methods-row methods{{ $mov->id }} toggled">
                                            <td colspan="5">Sin método de pago</td>
                                        </tr>
                                        @endif
                                        @foreach ($mov->metodosPago as $metodoPago)
                                            @if($metodoPago->payment_method_id!==4)
                                                <tr class="methods-row methods{{ $mov->id }} toggled">
                                                    <td></td>
                                                    <td style="background-color:transparent;color:#1d3557 !important">{{ $metodoPago->metodoPago->Payment_method }}</td>
                                                    <td style="background-color:transparent;color:#1d3557 !important">${{ number_format($metodoPago->amount-$metodoPago->change,2,'.',',') }}</td>
                                                    <td style="background-color:transparent;color:#1d3557 !important">{{ $metodoPago->reference }}</td>
                                                    <td style="background-color:transparent;color:#1d3557 !important">{{ $metodoPago->created_at }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        <tr>
                                            <td colspan="5" class="methods-row methods{{ $mov->id }} toggled"></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5">Sin información</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row d-flex justify-content-between">
                        <div>
                            {{$movs->links()}}
                        </div>
                        <div class="align-content-center">
                            <span class="float-right">Balance: ${{ number_format($balance,2,'.',',') }}</span>
                        </div>
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
    
    .methods-row.toggled {
        display: none;
    }
</style>

@include('livewire.empleados.js-com')