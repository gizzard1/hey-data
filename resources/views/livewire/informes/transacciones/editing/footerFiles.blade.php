
<input type="file" id="input-file"  wire:model="gallery" accept="image/x-png,image/jpeg,.pdf" multiple class="mb-2">

<div class="modal-footer">
    <button type="button" class="btn btn-sm float-right" data-dismiss="modal" wire:click="disableEditing">Cancelar</button>
    <button onclick="closeModals()" class="save btn btn-sm btn-info save float-right" wire:click.prevent="Store" data-dismiss="modal" >Guardar</button>
</div>