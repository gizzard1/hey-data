<div class="card"style="background-color: white;">
    <div class="card-header">
        <div class="d-flex">
            <div class="separator" style="background-color:#E2BBB4"></div>
            <div class="mr-auto mt-3">
                <h4 class="card-title"><a wire:click="$emit('infoSelected','1')">Ajustes</a>/ <a wire:click="$emit('infoSelected','11')">Agenda</a>/ Etiquetas</h4>
            </div>
        </div>
    </div>
    <div class="card-body mt-2 form-settings">
        <div class="mr-auto d-flex" style="justify-content:space-between">
            <h4 class="card-title">Etiquetas</h4>
            <div class="float-right">
                <button wire:click="create" class="btn-sm input-group-text">Crear etiqueta</button>
            </div>
        </div>
        <table class="table table-responsive-md table-hover" id="user-data">
        <thead>
            <tr>
                <th style="color:#9D1466 !important">Nombre</th>
                <th style="color:#9D1466 !important">Color</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Primer cita</td>
                <td><span class="color-box" style="background-color: #278d46;"></span></td>
            </tr>
            <tr>
                <td>Cumpleaños</td>
                <td><span class="color-box" style="background-color: #e83e8c;"></span></td>
            </tr>
            @if(isset($etiquetas))
            @forelse($etiquetas as $etiqueta)
            <tr>
                <td><a wire:click="edit('{{ $etiqueta->id }}')">{{ $etiqueta->name }}</a></td>
                <td><span class="color-box" style="background-color: {{ $etiqueta->color }};"></span></td>
            </tr>
            @empty
            <tr>
                <td>Sin categorías</td>
            </tr>
            @endforelse
            @endif
        </tbody>
    </table>
    </div>
    @include('livewire.ajustes.modulos.modal.etiquetas')
    </div>
<style>
.color-box {
    display: block;
    width: 30px;
    height: 30px;
    border-radius: 4px;
}
</style>