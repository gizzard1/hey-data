<div>
    <div wire:ignore.self class="modal fade none-border" id="modalCliente" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-m" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color:#E2BBB4;color:#6E6E6E">
                    <h5 class="modal-title" style="color:white">Agendar cita</h5>
                    <button type="button" class="close" data-dismiss="modal"><span style="color:white">x</span>
                    </button>
                </div>
                <div class="modal-body col">
                    {{-- tomSelect --}}
                    <h4 style="color:#60060F;text-align:center">Seleccione un cliente</h4>
                    <p></p>
                    <hr>
                    <div class=" input-group w-100" wire:ignore>
                        <div class="input-group-append">
                            <button class="input-group-text"><i class="las la-user-alt"></i></button>
                        </div>
                        <input type="text" class="form-control form-control-lg" placeholder="Cliente" id="tomCustomer" autocomplete="off" value="{{ $customer }}" autofocus>
                    </div>
                    <p></p>
                    <label>Recordar cita </label>
                    <input type="checkbox" wire:model="remember" value="1">
                        
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark light float-center" data-dismiss="modal">Regresar</button>
                    <button type="button" wire:click="Store" class="btn btn-info float-center" style="background-color:#9E846D;border-color:#9E846D" data-dismiss="modal">Agendar</button>
                    @if($this->isOpened)
                    <input value="Cobrar" type="button" class="btn btn-info float-center" data-dismiss="modal" data-toggle="modal" data-target="#modalPaymentServices" style="background-color:#60060F;border-color:#60060F;color:white"></input>
                    @endif
                </div>
            </div>
        </div>
    </div>

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