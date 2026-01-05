<div>
    <div wire:ignore.self class="modal fade none-border" id="modalPaymentServices" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="height: 35rem;overflow: auto;">
                <div class="modal-header" style="background-color:#E2BBB4;color:white">
                    <h5 class="modal-title" style="color:white">Registre los métodos de pago</h5>
                    <button type="button" class="close" data-dismiss="modal"><span style="color:white">x</span>
                    </button>
                </div>
                <div class="modal-body col">
                    {{-- TOTAL --}}
                    <div>
                        <span class="h2"><b style="color:#9D1466">TOTAL:</b></span>
                        <span id="totalCart" class="float-right h1" style="color: #9D1466;">${{ number_format($rest, 2, '.', ',') }}</span>
                    </div>

                    {{-- Cash --}}
                    <div class=" input-group w-100">
                        <div class="input-group-append">
                            <button class="input-group-text"><i class="las la-dollar-sign white"></i></button>
                        </div>
                        <input wire:model.debounce.750ms="cash" type="number" class="form-control form-control-lg"
                            placeholder="Recibido" id="inputCash">
                        @error('cash') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <div class="input-group-append">
                            <button class="input-group-text"><i class="las la-hand-holding-usd"></i></button>
                        </div>
                        <input type="text" class="form-control form-control-lg" value="<?php echo $change ? '$' . number_format($change, 2, '.', ',') : 'Cambio'; ?>" disabled>
                    </div>

                    <div class="input-group w-100 mt-4">
                        <div class="input-group-append">
                            <button class="input-group-text"><i class="las la-percent"></i></button>
                        </div>
                        <input wire:model.debounce.750ms="global_disccount" wire:change="setMethod('Descuento','Porcentaje')"type="number" class="form-control form-control-lg"
                            placeholder="0%">
                        <!-- <div class="input-group-append" >
                            <button class="input-group-text"><i class="las la-piggy-bank"></i></button>
                        </div>
                            <input type="text" class="form-control form-control-lg" placeholder="Propina" wire:model.debounce.750ms="tips"> -->
                    </div>
                    {{-- Referencia --}}
                    <div  class="input-group w-100 mt-4" wire:ignore>
                        <div class="input-group-append" >
                            <!-- Contenido adicional del contenedor -->
                            <button class="input-group-text"><i class="las la-link"></i></button>
                        </div>
                            <input type="text" class="form-control form-control-lg" placeholder="Referencia" wire:model.debounce.750ms="reference">
                    </div>
                    <hr>

                    <div class="mt-5 row" style="margin:auto">
                        <div style="margin:auto"><button type="button" wire:click="setMethod('Efectivo','Cantidad')"
                            class="col-sm-12 col-md-15" style="background-color: seagreen;border-color:seagreen;border-style:none;border-radius: 5px;width:9rem;;height:5rem">
                            <i class="las la-money-bill la-2x" style="color: yellowgreen"></i>
                            <div><small style="color: white;margin:auto" class="p-2">EFECTIVO</small></div>
                            
                        </button></div>
                        <div style="margin:auto"><button id="showMoreButtonTarjeta" type="button" wire:click="setMethod('Card','Cantidad')"
                            class="col-sm-12 col-md-15" style="background-color: #00B1EA;border-color:#00B1EA;border-style:none;border-radius: 5px;width:9rem;;height:5rem">
                            <i class="las la-credit-card la-2x" style="color: white"></i>
                            <div><small style="color: white;margin:auto" class="p-2">TARJETA</small></div>
                            
                        </button></div>
                        <div style="margin:auto"><button id="showMoreButtonBanorte" type="button" wire:click="setMethod('Banorte','Cantidad')"
                            class="col-sm-12 col-md-15" style="background-color: #EB0029;border-color:#EB0029;border-style:none;border-radius: 5px;width:9rem;;height:5rem">
                            <i class="las la-university la-2x" style="color: white"></i>
                            <div><small style="color: white;margin:auto" class="p-2">BANORTE</small></div>
                            
                        </button></div>
                        <div style="margin:auto"><button id="showMoreButtonBanorte" type="button" wire:click="setReward"
                            class="col-sm-12 col-md-15" style="background-color: #6E6E6E;border-color:#EB0029;border-style:none;border-radius: 5px;width:9rem;;height:5rem">
                            <i class="las la-ticket-alt la-2x" style="color: white"></i>
                            <div><small style="color: white;margin:auto" class="p-2">USAR PUNTOS</small></div>
                            
                        </button></div>
                        <div style="margin:auto"><button id="showMoreButtonBanorte" type="button" wire:click="setTip"
                            class="col-sm-12 col-md-15" style="background-color: #9D1466;border-color:#EB0029;border-style:none;border-radius: 5px;width:9rem;;height:5rem">
                            <i class="las la-piggy-bank la-2x" style="color: white"></i>
                            <div><small style="color: white;margin:auto" class="p-2">PROPINA</small></div>
                            
                        </button></div>

                        
                    </div>
                    <hr>
                    <table class="table table-striped table-responsive-sm">
                        <thead>
                            <tr class="text-center">
                                <th>concepto</th>
                                <th>cantidad</th>
                                <th>referencia</th>
                                <th>empleado</th>
                                <th>Acciones</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($methodsInfo as $item)
                        <tr class="text-center">

                            <td class="text-center">{{ $item['name'] }}
                            </td>
                            @if($item['tipo']==='Cantidad')
                            <td>${{ number_format($item['qty'], 2, '.', ',') }}</td>
                            @else
                            <td>{{ number_format($item['qty'], 2, '.') }}%</td>
                            @endif
                            <td class="text-center">{{ $item['reference'] }}</td>
                            <td></td>
                            <td>
                                <button wire:click.prevent="$emit('removeMethod', '{{ $item['uid'] }}' )"
                                    class="btn tp-btn btn-xxs btn-danger "><i class="fa fa-trash fa-lg"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">AGREGA MÉTODOS DE PAGO</td>
                        </tr>
                        @endforelse
                        @forelse($propinas as $propina)
                        <tr class="text-center">
                            <td>
                                <select wire:change="cambioData('{{ $propina['uid'] }}',$event.target.value,3)" class="form-control  form-control-lg" style="background-color: transparent;border-color:transparent;">
                                    @if(!isset($propina['formaPago']))
                                        <option value="null">Seleccione un método</option>
                                    @endif
                                    <option style="text-align: center;" value="Efectivo" {{ $propina['formaPago'] == "Efectivo" ? 'selected' : '' }}>Efectivo</option>
                                    <option style="text-align: center;" value="Card" {{ $propina['formaPago'] == "Card" ? 'selected' : '' }}>Tarjeta</option>
                                    <option style="text-align: center;" value="Banorte" {{ $propina['formaPago'] == "Banorte" ? 'selected' : '' }}>Banorte</option>
                                </select>
                            </td>
                            <td>
                                <input wire:change="cambioData('{{ $propina['uid'] }}',$event.target.value,1)" class="form-control form-control-sm text-center" type="numeric" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" 
                                    value=" {{ $propina['qty'] }}" >

                            </td>
                            <td>
                            <input wire:change="cambioData('{{ $propina['uid'] }}',$event.target.value,2)"  class="form-control form-control-sm text-center" type="numeric" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" 
                                    value=" {{ $propina['reference'] }}" >
                            </td>
                            <td>
                                <select wire:change="cambioData('{{ $propina['uid'] }}',$event.target.value,4)" class="form-control  form-control-lg" style="background-color: transparent;border-color:transparent;">
                                    @foreach($empleados as $empleado)
                                        <option style="text-align: center;" value="{{ $empleado->id }}" {{ $propina['empleado'] == $empleado->id ? 'selected' : '' }}>{{ $empleado->first_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <button wire:click="removeTip('{{ $propina['uid'] }}')"
                                    class="btn tp-btn btn-xxs btn-danger "><i class="fa fa-trash fa-lg"></i></button>
                            </td>
                        </tr>
                        @empty
                        @endforelse
                        </tbody>
                    </table>
                </div>

                         
                
                <div class="modal-footer">
                    @if(!isset($customerId) && $customerId==null)
                        <span class="text-danger">*Presiona regresar para seleccionar un cliente* </span>
                    @endif
                    <button type="button" class="btn btn-dark light" data-dismiss="modal" data-toggle="modal" onclick="openCliente()">Regresar</button>
                    <input type="button" value="Guardar" data-dismiss="modal" data-toggle="modal" data-target="#reviewModal" wire:click.prevent="Store" data-dismiss="modal" class="btn btn-info ml-5" style="background-color:#9E846D;border-color:#9E846D" {{ $rest < 1 && (isset($customerId) && $customerId!==null) ? '' : 'disabled' }}>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('totalUpdated', function (value) {
                // Actualizar el contenido donde se muestra el total
                document.getElementById('totalCart').innerText = '$' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            });
        });

    </script>


    <style>
        .light:hover{
            background-color: #430007 !important;
        }
        /* estilos tom select */
        .ts-control {
            padding: 0px !important;
            border-style: none;
            border-width: 0px !important;
            background: white !important;
            font-size: 18px;
            cursor: text !important;
            height: 26px;
            color: #6C757D;
        }

        .ts-control input {
            color: #6C757D !important;
            font-size: 1rem !important;
            cursor: text !important;
        }

        .ts-wrapper.multi .ts-control>div {
            font-size: 1rem !important;
            color: white !important;
            background-color: #B59377;
        }

        .search-area .input-group-append .input-group-text i {
            font-size: 16px !important;
        }
    </style>
</div>