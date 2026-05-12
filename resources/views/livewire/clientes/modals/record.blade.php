<div id="modalRecord" class="modal fade" role="dialog" wire:ignore.self>
    <div class="modal-dialog modal-lg">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Expediente de {{ $customerSelected->first_name }} {{ $customerSelected->last_name }}</h4>
            </div>
            <div class="modal-body" id="modal-body-record">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" id="record" wire:ignore></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer mt-5">
                <button type="button" class="btn btn-sm" data-dismiss="modal">Cancelar</button>
                <button class="btn-sm btn save float-right" style="color: white;" onclick="saveRecord()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<style>
    em {
        font-style: italic;
        color: black;
    }
    .ql-editor {
        max-height: 24dvh;
    }
</style>