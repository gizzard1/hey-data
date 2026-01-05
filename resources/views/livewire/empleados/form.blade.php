
<div class="card-header">
    <h4>{{ $editing ? 'Editar Empleado' : 'Crear Empleado' }}</h4>
</div>
<div class="card-body form-panel" id="data-employee">
    <div class="form-group" id="basic-data">
        <input wire:model="empleado.first_name" type="text"
            class="form-control form-control" placeholder="Nombre">
        @error('empleado.first_name') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Nombre(s)</label>

        <input wire:model="empleado.last_name"  type="text"
            class="form-control form-control" placeholder="Apellido">
        @error('empleado.last_name') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Apellido</label>

        <input wire:model="empleado.phone_number" type="text" class="form-control form-control"
            placeholder="Teléfono">
        @error('empleado.phone_number') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Teléfono</label>
        
        <input wire:model="empleado.birth_date" class="form-control form-control" type="date" placeholder="<?php echo date('Y-m-d'); ?>">
        @error('empleado.birth_date') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Cumpleaños</label>
        
    </div>
    
    <hr>

    <div class="form-group" id="user-data">
        <input wire:model="username" type="text"
            class="form-control form-control" placeholder="Nombre de usuario"  autocomplete="nope" autocomplete="off">
        @error('username') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Nombre de usuario</label>
        
        <input wire:model="email" type="text" class="form-control form-control"
            placeholder="Email">
        @error('email') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Email</label>
        
        <input wire:model="password"
            class="form-control form-control" placeholder="Contraseña" type="password" autocomplete="new-password">
        @error('password') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Contraseña</label>
        
        <div >
        
            @if($editing && isset($empleado->user->role))
                <select wire:model.defer="role" class="form-control form-control">
                    <option value="estilista" {{ $empleado->user->role == 'estilista' ? 'selected' : '' }}>Empleado</option>
                    <option value="recepcionista" {{ $empleado->user->role == 'recepcionista' ? 'selected' : '' }}>Recepcionista</option>
                </select>
            @else
                <select wire:model.defer="role" class="form-control form-control">
                    <option value="null">Seleccionar un rol</option>
                    <option value="admin">Administrador</option>
                    <option value="estilista">Empleado</option>
                    <option value="recepcionista">Recepcionista</option>
                </select>
            @endif
            @error('role') <span class="text-danger">*Corrige este campo* </span> @enderror
            <label>Rol</label>
            
        </div>

    </div>

</div>
<div class="card-footer">
    <button class="btn btn-sm float-left hidden {{$editing ? 'd-block' : 'd-none' }}"
        wire:click="cancelEdit" id="cancel-editing" onclick="next()">Cancel</button>
    <button class="btn btn-sm btn-info float-right save" style="background-color: #9E846D;border-color:#9E846D" wire:click="Store" id="save-info" onclick="next()">Guardar</button>
</div>

<style>
    
.color-box {
    width: fit-content;
    border-radius: 4px;
    color: white;
    padding: 5px;
}
.color-selector {
    width: 1.5rem;
    height: 1.5rem;
    padding: revert;
}
</style>
