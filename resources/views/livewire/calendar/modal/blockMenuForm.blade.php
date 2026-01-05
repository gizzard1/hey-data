<div wire:ignore.self class="modal fade none-border" id="modalBlockForm" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="height: auto; overflow: auto;">
        @include('livewire.calendar.modal.blockMenuHeader')
        <div class="modal-body">
            @include('livewire.calendar.modal.blockMenuDetailsTable')
            <textarea wire:model.prevent="description" type="text" class="form-control" placeholder="Motivo (opcional)..." maxlength="100" style="resize: none;"></textarea>
        </div>

        <div class="modal-footer">
            <div style="display:-webkit-box">
                <div style="display:inline-flex;column-gap:1rem">
                    <button class="btn btn-sm btn-dark" data-dismiss="modal" data-toggle="modal" wire:click.defer="cancelarCaptura">Regresar</button>
                    <button class="btn-sm input-group-text" {{ $asignacion_id ? 'hidden' : '' }} wire:click="storeBlock">Añadir bloqueo</button>
                    @if($asignacion_id!==null)
                        <button class="btn-sm input-group-text" wire:click="storeBlock(true)" data-dismiss="modal">Editar bloqueo</button>
                    @endif
                    @if($asignacion_id!==null)
                    <button onclick="CancelDate('bloqueo')" class="btn tp-btn btn-xxs btn-danger "><i class="fa fa-trash fa-lg"></i></button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>