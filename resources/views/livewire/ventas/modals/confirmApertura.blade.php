

<div wire:ignore.self id="modalConfirmApertura" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" style="align-self:center">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Aperturar Caja</h4>
            </div>
            <div class="modal-body" style="
            display: flex;
            flex-direction: column;
            width: fit-content;
            align-self: center;" id="aperturarCaja">
                <h4>Caja Cerrada</h4>
                <div class="form-group d-flex">
                    <div class="input-group-append">
                        <i style="background-color: white;" class="input-group-text">
                            <i class="las la-dollar-sign"></i>
                        </i>
                    </div>
                    <input wire:model.defer="cajaChica" class="form-control" placeholder="Caja chica" type="number">
                </div>
                <button wire:click="aperturaCaja" onclick="next()" class="btn btn-sm btn-block save w-auto" style="color:white">Abrir Caja</button>
            </div>
        </div>
    </div>
</div>