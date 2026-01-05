<div wire:ignore.self id="modalRewardForm" class="modal fade" role="dialog">
<div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Modificar recompensas</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Tipo</label>
                            <select wire:model.defer="rewardType" id='inputFocus' type="text" placeholder="Cantidad" class="form-control">
                                <option value="0">Porcentaje</option>
                                <option value="1">Cantidad</option>
                            </select>
                            @error('rewardType') <span class="text-danger">*Campo obligatorio </span> @enderror
                        </div>
                        <div class="form-group">
                            <label>{{ $rewardType ? 'Cantidad' : 'Porcentaje' }}</label>
                            <input wire:model.defer="rewardQty" type="number" placeholder={{ $rewardType ? 'Cantidad' : 'Porcentaje' }} class="form-control">
                            @error('rewardQty') <span class="text-danger">*Campo obligatorio </span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="save-info" class="btn btn-sm btn-info float-right save" wire:click="recalculateReward" style="background-color: #9E846D; border-color: #9E846D;">Guardar</button>
                <button type="button" class="btn btn-sm btn-dark" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>