<div wire:ignore.self id="modalForm" class="modal fade" role="dialog">
<div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Crear 
                    @if ($type==1)
                    método de pago
                    @elseif($type==2)
                    procedencia
                    @elseif($type==3)
                    categoría
                    @elseif($type==4)
                    tipo de gasto
                    @endif
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input wire:model.defer="name" id='inputFocus' type="text" placeholder="Nombre" class="form-control form-control">
                            <label>Nombre</label>
                            @error('name') <span class="text-danger">*Este campo es obligatorio </span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-dismiss="modal">Cancelar</button>
                <button id="save-info" class="btn-sm btn save float-right" wire:click="store" style="color: white;">Guardar</button>
                @if(isset($itemSelected->salon))
                    <button wire:click="delete" class="btn btn-sm"><i class="fa fa-trash fa-lg"></i></button>
                @endif
            </div>
        </div>
    </div>
</div>