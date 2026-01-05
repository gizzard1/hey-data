<div class="card"style="background-color: white;">
    <div class="card-header">
        <div class="d-flex">
            <div class="separator" style="background-color:#E2BBB4"></div>
            <div class="mr-auto mt-3">
                <h4 class="card-title"><a wire:click="$emit('infoSelected','1')">Ajustes</a>/ <a wire:click="$emit('infoSelected','5')">Clientes</a> /Procedencia de Clientes</h4>
            </div>
        </div>
    </div>
    <div class="card-body mt-2 form-settings">
        <div class="mr-auto d-flex" style="justify-content:space-between">
            <h4 class="card-title">Procedencias</h4>
            <div class="float-right">
                <button wire:click="create" class="btn-sm input-group-text">Añadir procedencia</button>
            </div>
        </div>
        <table class="table table-responsive-md table-hover" id="user-data">
        <thead>
            <tr>
                <th style="color:#9D1466 !important">Nombre</th>
                <th style="color:#9D1466 !important">Tipo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Instagram</td>
                <td>Predeterminado</td>
            </tr>
            <tr>
                <td>Facebook</td>
                <td>Predeterminado</td>
            </tr>
            <tr>
                <td>Google</td>
                <td>Predeterminado</td>
            </tr>
            <tr>
                <td>Tiktok</td>
                <td>Predeterminado</td>
            </tr>
            <tr>
                <td>Youtube</td>
                <td>Predeterminado</td>
            </tr>
            <tr>
                <td>Cliente</td>
                <td>Predeterminado</td>
            </tr>
            <tr>
                <td>Empleado</td>
                <td>Predeterminado</td>
            </tr>
            
            @if(isset($procedencias))
            @foreach($procedencias as $procedencia)
            <tr>
                <td><a wire:click="edit('{{ $procedencia->id }}')">{{ $procedencia->name }}</a></td>
                <td>Añadido por el salón</td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
    </div>
@include('livewire.ajustes.modulos.modal.form')
</div>
