
<div class="card">
    <div class="card-header">
        <h4>{{ $editing ? 'Editar Proveedor' : 'Crear Proveedor' }}</h4>
    </div>
    <div class="card-body" id="basic-data">

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
    <div class="card-footer">
        <button class="btn btn-sm btn-dark light float-left hidden {{$editing ? 'd-block' : 'd-none' }}"
            wire:click="cancelEdit" id="cancel-editing" onclick="next()">Cancelar</button>
        <button id="save-info" onclick="next()" class="btn btn-sm btn-info float-right save" style="background-color: #9E846D;border-color:#9E846D" wire:click="Store">Guardar</button>
    </div>
</div>