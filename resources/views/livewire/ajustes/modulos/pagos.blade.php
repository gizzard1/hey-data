<div class="card"style="background-color: white;" id="pagosListado">
    <div class="card-header">
        <div class="d-flex">
            <div class="separator" style="background-color:#E2BBB4"></div>
            <div class="mr-auto mt-3">
                <h4 class="card-title"><a wire:click="$emit('infoSelected','1')">Ajustes</a>/ Formas de pago</h4>
            </div>
        </div>
    </div>
    <div class="card-body mt-2 form-settings">
        <div class="mr-auto d-flex" style="justify-content:space-between">
            <h4 class="card-title">Métodos de pago</h4>
            <div class="float-right" id="pagosAdd" onclick="next()">
                <button wire:click="create" class="btn-sm input-group-text">Añadir método</button>
            </div>
        </div>
        <table class="table table-responsive-md table-hover" id="user-data">
        <thead>
            <tr>
                <th style="color:#1d3557 !important">Nombre</th>
                <th style="color:#1d3557 !important">Tipo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Efectivo</td>
                <td>Predeterminado</td>
            </tr>
            <tr>
                <td>Tarjeta</td>
                <td>Predeterminado</td>
            </tr>
            <tr>
                <td>MSI</td>
                <td>Predeterminado</td>
            </tr>
            <tr>
                <td>Puntos recompensa</td>
                <td>Predeterminado</td>
            </tr>
            
            @if(isset($metodos))
            @foreach($metodos as $metodo)
            <tr>
                <td><a wire:click="edit('{{ $metodo->id }}')">{{ $metodo->Payment_method }}</a></td>
                <td>Añadido por el salón</td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
    </div>
@include('livewire.ajustes.modulos.modal.form')
</div>
