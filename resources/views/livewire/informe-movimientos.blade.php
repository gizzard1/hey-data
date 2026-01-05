@if(Auth::user()->role!='estilista')
<div class="row" wire:ignore.self>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header ">
                <div class="d-flex">
                    <div class="default-tab">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route(name: 'informe') }}"><i class="la la-box mr-2"></i> Histórico</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" id="ventas-w"><i class="la la-store mr-2"></i> Transacciones</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route(name: 'informe-caja') }}"><i class="la la-cash-register mr-2"></i> Caja</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div>
                    @include('livewire.informes.header.botonesFecha')
                </div>
                <div class="float-right dropdown">
                    <button id="orderBy" class="btn btn-sm input-group-text dropdown-toggle"  data-toggle="dropdown" aria-haspopup="true">Ver</button>
                    @include('livewire.informes.header.filtrosHistorico')
                </div>
                
                @if(Auth::user()->role=='admin')
                <div class="botones-exportar">
                    <button wire:click="generateExcel" class="btn-sm excel-button input-group-text">Exportar Informe</button>   
                    <!-- <button class="button-style" wire:click="generatePdf" style="border-width: 0;color:red"><i class="las la-file-pdf la-2x"></i></button>
                    <button class="button-style" wire:click="generateExcel" style="border-width: 0;color:#68d100"><i class="las la-file-excel la-2x"></i></button> -->
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover" style="height:47dvh">
                        <thead class="thead-primary">
                            <tr >
                                <th style="background-color:transparent;color:#1d3557 !important" width = "70">Estatus</th>
                                <th style="background-color:transparent;color:#1d3557 !important" width ="152" id="fecha">Fecha</th>
                                <th style="background-color:transparent;color:#1d3557 !important">Total</th>
                                <th style="background-color:transparent;color:#1d3557 !important">Cliente</th>
                                <th style="background-color:transparent;color:#1d3557 !important">Versión</th>
                            </tr>
                        </thead>
                        <tbody class="scroll">
                            @if(isset($dataMovimientos))
                            @forelse($dataMovimientos as $key => $value)
                                @if(count($value)>0)
                                <tr>
                                    <td class="data-type" onclick="toggleMethods('{{ $key }}')" colspan="5" >{{ $key === 'cortes' ? 'cierres de caja' : ($key === 'aperturas' ? 'aperturas de caja' : $key) }}</td>
                                </tr>
                                @endif
                                @if(isset($value) && ($key == 'citas' || $key == 'ventas'))
                                @foreach($value as $item)
                                    <tr onclick="toggleMethods({{ $item->id }})" class="data {{ 'methods' . $key }} toggled">
                                        <td class="badge" style="display: revert;background-color:{{ $item->status == 'Agendada' ? '#4978BC' : ($item->status == 'Pagada' ? '#28A745' : ($item->status == 'Cancelada' ? '#E63946' : ($item->status == 'Confirmada' ? '#4978BC' : ($item->status == 'Pendiente' ? '#FFAB2D' : '#e63946')))) }}"><button
                                                class="details-badge"
                                                wire:click="$emit('viewDetails','{{ $item->id }}','{{ $key }}')" 
                                                data-original-text="{{ $item->status }}" 
                                                data-hover-text="Detalles"
                                                onclick="event.stopPropagation()" 
                                                onmouseover="changeBadgeText(this, 'hover')" 
                                                onmouseout="changeBadgeText(this, 'original')">{{ $item->status }}</button></td>
                                        <td> {{ date_format(new DateTime($item->created_at),'d-m-Y') }} </td>
                                        <td> ${{ number_format($item->total,2,'.',',') }} </td>
                                        <td> {{ isset($item->customer) ? $item->customer->first_name : 'Cliente eliminado' }} {{ isset($item->customer) ? $item->customer->last_name : '' }}</td>
                                        <td> Última act. {{ date_format(new DateTime($item->updated_at),'d-m-Y') }} por {{ $item->user->name }} </td>
                                    </tr>
                                    @if(count($item->metodosPago) > 0)
                                    <tr class="methods-row methods{{ $item->id }} toggled">
                                        <th></th>
                                        <th style="background-color:transparent;color:#1d3557 !important">Concepto</th>
                                        <th style="background-color:transparent;color:#1d3557 !important">Cantidad</th>
                                        <th style="background-color:transparent;color:#1d3557 !important">Referencia</th>
                                        <th style="background-color:transparent;color:#1d3557 !important">Fecha</th>
                                    </tr>
                                    @else
                                    <tr class="methods-row methods{{ $item->id }} toggled">
                                        <td colspan="5">Sin método de pago</td>
                                    </tr>
                                    @endif
                                    @foreach ($item->metodosPago as $metodoPago)
                                        @if($metodoPago->payment_method_id!==4)
                                            <tr class="methods-row methods{{ $item->id }} toggled">
                                                <td></td>
                                                <td style="background-color:transparent;color:#1d3557 !important">{{ $metodoPago->metodoPago->Payment_method }}</td>
                                                <td style="background-color:transparent;color:#1d3557 !important">${{ number_format($metodoPago->amount-$metodoPago->change,2,'.',',') }}</td>
                                                <td style="background-color:transparent;color:#1d3557 !important">{{ $metodoPago->reference }}</td>
                                                <td style="background-color:transparent;color:#1d3557 !important">{{ $metodoPago->created_at }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                                @elseif(isset($value) && ($key == 'usos' || $key == 'entradas'))
                                @foreach($value as $materials)

                                @php
                                    $total_pzs = 0;
                                    foreach($materials as $material){
                                        $total_pzs += $material->qty;
                                    }    
                                @endphp

                                    <tr class="data {{ 'methods' . $key }} toggled"> 
                                            <td class="badge badge-success" style="display: revert"><button
                                                class="details-badge"
                                                wire:click="$emit('viewDetails','{{ $materials }}','{{ $key }}')"
                                                data-original-text="{{ $key == 'usos' ? 'Uso' : 'Entrada' }}" 
                                                data-hover-text="Detalles"
                                                onmouseover="changeBadgeText(this, 'hover')" 
                                                onmouseout="changeBadgeText(this, 'original')">{{ $key == 'usos' ? 'Uso' : 'Entrada' }}</button></td>

                                        <td> {{ date_format(new DateTime($materials[0]->created_at),'d-m-Y') }} </td>
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
                                    <tr class="data {{ 'methods' . $key }} toggled">
                                        @if($key == 'cortes')
                                        
                                        <td class="badge badge-success" style="display: revert"><button
                                                class="details-badge"
                                                wire:click="$emit('viewDetails','{{ $item->id }}','{{ $key }}')"
                                                data-original-text="Registrado" 
                                                data-hover-text="Detalles"
                                                onmouseover="changeBadgeText(this, 'hover')" 
                                                onmouseout="changeBadgeText(this, 'original')">Registrado</button></td>
                                        @elseif($key == 'aperturas')
                                            @if($item->caja_corte_id == null)
                                            
                                        <td class="badge" style="display: revert;background-color:#1D3557;color:white"><button
                                                class="details-badge"
                                                wire:click="$emit('viewDetails','{{ $item->id }}','{{ $key }}')"
                                                data-original-text="Abierta" 
                                                data-hover-text="Detalles"
                                                onmouseover="changeBadgeText(this, 'hover')" 
                                                onmouseout="changeBadgeText(this, 'original')">Abierta</button></td>
                                            @else
                                            <td class="badge" style="display: revert;background-color:#63686C;color:white"><button
                                                class="details-badge"
                                                wire:click="$emit('viewDetails','{{ $item->id }}','{{ $key }}')"
                                                data-original-text="Cerrada" 
                                                data-hover-text="Detalles"
                                                onmouseover="changeBadgeText(this, 'hover')" 
                                                onmouseout="changeBadgeText(this, 'original')">Cerrada</button></td>

                                            @endif
                                        @endif
                                        <td> {{ date_format(new DateTime($item->created_at),'d-m-Y') }} </td>
                                        @if($key == 'cortes')
                                        <td> ${{ number_format($item->total_cash+$item->propinas_efectivo+$item->caja_chica-$item->gastos,2,'.',',') }} </td>
                                        @else
                                        <td>-</td>
                                        @endif
                                        <td>-</td>
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
                </div>
            </div>
            <div class="card-footer">
                <p>
                    <span class="float-right">Balance: ${{ number_format($balance,2,'.',',') }}</span>
                    <span class="float-right mr-4">Descuentos: ${{ number_format($balanceDisccounts,2,'.',',') }}</span>
                </p>
                <br>
                <p>
                    <span class="float-right ml-4">Total: {{ $itemsQty }}</span>
                    @if(isset($dataMovimientos))
                        @foreach ($dataMovimientos as $key => $value)
                            @if(count($value)>0)
                                <span class="float-right ml-4" style="text-transform: capitalize;">{{ $key }}: {{ $value->count() }}</span>
                            @endif
                        @endforeach
                    @endif
                </p>
            </div>
        </div>
    </div>
@if(isset($itemSelected))
    @include('livewire.informes.detail-transaccion')
@endif
@push('my-scripts')
@include('livewire.informes.js-movimientos')
@include('livewire.informes.js-flatpickr')
@endpush
</div>
@else
@include('livewire.sinPermisos')
@endif
<style>
    .details{
        cursor: pointer;
    }
    .details:hover{
        text-decoration: underline !important;
        color:#1d3557 !important;
    }
    .filter_label{
        display:flex;
        justify-content: space-between;
        margin: 0 6% 0 6%;
    }
    .input[type=checkbox]{
        appearance: none !important;
    }
    .input[type=checkbox]{
        appearance: none !important;
    }
    a{
        color:#1d3557
    }
    .table-responsive{
       max-height: 50dvh;
    }
    .details-badge {
        display: inline-block;
        transition: all 0.3s ease; /* Transición suave */
        min-width: 80px; /* Ajusta este valor al texto más largo */
        text-align: center;
        padding:initial;
    }
    button.details-badge {
        background: transparent;
        border: 0 solid transparent;
        color: white;
    }
    .methods-row {
        display: table-row;
    }
    .methods-row.toggled, .data.toggled {
        display: none;
    }
    thead.thead-primary {
        position: sticky;
        top: 0;
        background: white;
    }
    td.data-type::first-letter{
        text-transform: capitalize;
    }
</style>