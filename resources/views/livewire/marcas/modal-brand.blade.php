<div wire:ignore.self class="modal fade none-border" id="modalBrandForm" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="height: auto; overflow: auto;">
            <div class="modal-header">
                <h4>Datos del Proveedor</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>x</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input wire:model.defer="marca.name" id='inputFocus' type="text"
                                    class="form-control" placeholder="Nombre">
                                @error('marca.name') <span class="text-danger">*Corrige este campo* </span> @enderror
                                <label>Nombre</label>
                            </div>
                            <div class="form-group">
                                <input wire:model.defer="marca.phone_number"  type="phone"
                                    class="form-control" placeholder="Teléfono">
                                @error('marca.phone_number') <span class="text-danger">*Corrige este campo* </span> @enderror
                                <label>Teléfono</label>
                            </div>
                            <div class="form-group">
                                <input wire:model.defer="marca.contact_name"  type="text"
                                    class="form-control" placeholder="Persona de contacto">
                                @error('marca.contact_name') <span class="text-danger">*Corrige este campo* </span> @enderror
                                <label>Persona de contacto</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input wire:model.defer="marca.email"  type="email"
                                    class="form-control" placeholder="Email">
                                @error('marca.email') <span class="text-danger">*Corrige este campo* </span> @enderror
                                <label>Email</label>
                            </div>
                            <div class="form-group">
                                <input wire:model.defer="marca.rfc"  type="rfc"
                                    class="form-control" placeholder="RFC">
                                @error('marca.rfc') <span class="text-danger">*Corrige este campo* </span> @enderror
                                <label>RFC</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm float-left" data-dismiss="modal">Cancelar</button>
                <button id="save-info" onclick="next()" class="btn btn-sm save float-right" wire:click="Store" style="color:white">Guardar</button>
            </div>
        </div>
    </div>
</div>