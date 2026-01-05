<div id="modalSearchCuponesForm" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Canjear Gift Card
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input wire:model.defer="searchPassword" type="text" class="form-control">
                            <label>Código secreto</label>
                            @error('password') <span class="text-danger">*Este campo es obligatorio </span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-dismiss="modal">Cancelar</button>
                <button class="btn-sm btn float-right save" data-dismiss="modal" wire:click.prevent="setGiftCard" style="color: white;">Canjear</button>
            </div>
        </div>
    </div>
</div>