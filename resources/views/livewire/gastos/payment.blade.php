<div>
    <div wire:ignore.self class="modal fade none-border" id="modalPaymentGastos" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
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

                    {{-- cash --}}
                    <div class=" input-group w-100" wire:ignore>
                        <div class="input-group-append">
                            <button class="input-group-text"><i class="las la-dollar-sign white"></i></button>
                        </div>
                        <input wire:model.debounce.750ms="cash" type="number" class="form-control form-control-lg"
                            placeholder="Recibido" id="inputCash">
                        @error('cash') <span class="text-danger">{{ $message }}</span> @enderror
                        <div class="input-group-append" >
                            <!-- Contenido adicional del contenedor -->
                            <button class="input-group-text"><i class="las la-link"></i></button>
                        </div>
                            <input type="text" class="form-control form-control-lg" placeholder="Referencia" wire:model.debounce.750ms="reference">
                    </div>
                    <hr>

                    <div class="mt-5 row" style="margin:auto">
                        <div style="margin:auto"><button type="button" wire:click="setMethod('Efectivo')"
                            class="col-sm-12 col-md-15" style="background-color: seagreen;border-color:seagreen;border-style:none;border-radius: 5px;width:10rem;;height:5rem">
                            <i class="las la-money-bill la-2x" style="color: yellowgreen"></i>
                            <div><small style="color: white;margin:auto" class="p-2">EFECTIVO</small></div>
                            
                        </button></div>
                        <div style="margin:auto"><button id="showMoreButtonTarjeta" type="button" wire:click="setMethod('Card')"
                            class="col-sm-12 col-md-15" style="background-color: #00B1EA;border-color:#00B1EA;border-style:none;border-radius: 5px;width:10rem;;height:5rem">
                            <i class="las la-credit-card la-2x" style="color: white"></i>
                            <div><small style="color: white;margin:auto" class="p-2">TARJETA</small></div>
                            
                        </button></div>
                        <div style="margin:auto"><button id="showMoreButtonBanorte" type="button" wire:click="setMethod('Banorte')"
                            class="col-sm-12 col-md-15" style="background-color: #EB0029;border-color:#EB0029;border-style:none;border-radius: 5px;width:10rem;;height:5rem">
                            <i class="las la-university la-2x" style="color: white"></i>
                            <div><small style="color: white;margin:auto" class="p-2">BANORTE</small></div>
                            
                        </button></div>

                        
                    </div>
                    <hr>
                    <table class="table table-striped table-responsive-sm">
                        <thead>
                            <tr class="text-center">
                                <th>concepto</th>
                                <th>cantidad o porcentaje</th>
                                <th>referencia</th>
                                <th>Acciones</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($methodsInfo as $item)
                        <tr class="text-center">

                            <td class="text-center">{{ $item['name'] }}
                            </td>
                            <td>${{ number_format($item['qty'], 2, '.', ',') }}</td>
                            <td class="text-center">{{ $item['reference'] }}</td>
                            <td>
                                <button wire:click="removeMethod('{{ $item['uid'] }}')"
                                    class="btn tp-btn btn-xxs btn-danger "><i class="fa fa-trash fa-lg"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">AGREGA MÉTODOS DE PAGO</td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                         
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark light" data-dismiss="modal">Cancelar</button>
                    <button type="button" wire:click.prevent="Store" class="btn btn-info ml-5" style="background-color:#9E846D;border-color:#9E846D"  data-dismiss="modal" wire:loading.attr="disabled" {{ $rest < 1 ? '' : 'disabled' }}>
                        Guardar
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