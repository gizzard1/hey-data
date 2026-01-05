<div  wire:ignore.self id="modalFormTags" class="modal fade" role="dialog">
<div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Crear etiqueta</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input wire:model.defer="name" type="text" placeholder="Nombre" class="form-control form-control">
                            <label>Nombre</label>
                            @error('name') <span class="text-danger">*Este campo es obligatorio </span> @enderror
                        </div>
                        <div class="form-group">
                            <div>
                                <select  wire:model="color" style="color:white;background-color:{{ $color }}" class="form-control" name="color" id="presetColors">
                                    
                                @php
                                    $presetColors = ['#6f42c1', '#e83e8c', '#e63946', '#1d3557', '#278d46', '#3A82EF', '#FFAB2D'];
                                @endphp

                                @foreach($presetColors as $colorOption)
                                    <option {{ $color == $colorOption ? 'selected' : '' }} value="{{ $colorOption }}" style="color:white;background-color: {{ $colorOption }};">Elige un color
                                    </option>
                                @endforeach
                                </select>
                            </div>
                            
                            <label>Color</label>
                            @error('color') <span class="text-danger">*Este campo es obligatorio </span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-dismiss="modal">Cancelar</button>
                <button id="save-info" class="btn-sm btn save float-right" wire:click="$emit('storeTag')" style="color: white;">Guardar</button>
                <button wire:click="delete" class="btn btn-sm"><i class="fa fa-trash fa-lg"></i></button>
            </div>
        </div>
    </div>
</div>
