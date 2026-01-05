<div wire:ignore.self id="modalEditCardCustomer" class="modal fade" role="dialog">
<div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Editar tarjeta de puntos</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <img src="{{ asset('storage/TP.gif') }}" class="rounded" style="width:12rem;margin:auto;display:block;padding:1rem">
                            <input wire:keydown.enter="saveCard" id='inputFocus' wire:model.defer="barcode" type="text" placeholder="Deslice o escanee la tarjeta..." class="form-control form-control-lg">
                            @error('barcode') <span class="text-danger">*Es posible que estés duplicando la tarjeta* </span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-dismiss="modal">Cerrar</button>
                <button wire:click="saveCard" class="btn btn-sm save w-auto" style="background-color: #9E846D;color:white">Guardar tarjeta</button>
                <button wire:click="saveCard(0)" class="btn btn-sm excel-button w-auto" style="background-color: #9E846D;color:white">Guardar sin registrar tarjeta</button>
            </div>
        </div>
    </div>
</div>