@if($configuration)
<div class="card"style="background-color: white;">
    <div class="card-header">
        <div class="d-flex">
            <div class="separator" style="background-color:#E2BBB4"></div>
            <div class="mr-auto mt-3">
                @if($sub==3)
                <h4 class="card-title"><a wire:click="$emit('infoSelected','1')">Ajustes</a>/ <a wire:click="$emit('infoSelected','5')">Clientes</a>/ Categorías</h4>
                @elseif($sub==1)
                <h4 class="card-title"><a wire:click="$emit('infoSelected','1')">Ajustes</a>/ <a wire:click="$emit('infoSelected','7')">Categorías</a>/ Productos</h4>
                @elseif($sub==2)
                <h4 class="card-title"><a wire:click="$emit('infoSelected','1')">Ajustes</a>/ <a wire:click="$emit('infoSelected','7')">Categorías</a>/ Servicios</h4>
                @elseif($sub==4)
                <h4 class="card-title"><a wire:click="$emit('infoSelected','1')">Ajustes</a>/ <a wire:click="$emit('infoSelected','10')">Gastos</a>/ Categorías</h4>
                @elseif($sub==5)
                <h4 class="card-title"><a wire:click="$emit('infoSelected','1')">Ajustes</a>/ <a wire:click="$emit('infoSelected','10')">Gastos</a>/ Tipos</h4>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body mt-2  form-settings">
        <div class="mr-auto d-flex" style="justify-content:space-between">
            <h4 class="card-title">{{ $sub ==5 ? 'Tipos de gasto' : 'Categorías' }}</h4>
            <div class="float-right">
                <button wire:click="create" class="btn-sm input-group-text">Nuevo</button>
            </div>
        </div>
        <table class="table table-responsive-md table-hover" id="user-data">
        <thead>
            <tr>
                <th style="color:#9D1466 !important">Nombre</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($categorias))
            @forelse($categorias as $categoria)
            <tr>
                @if($categoria->salon_id)

                <td><a wire:click="edit('{{ $categoria->id }}')">{{ $categoria->name }}</a></td>
                @else
                <td>{{ $categoria->name }}</td>
                @endif
            </tr>
            @empty
            <tr>
                <td>Sin datos</td>
            </tr>
            @endforelse
            @endif
        </tbody>
    </table>
    </div>
@include('livewire.ajustes.modulos.modal.form')
</div>
@else
    @include('livewire.ajustes.modulos.modal.form')
@endif      