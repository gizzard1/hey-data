<div class="card" style="height:fit-content">
    <div class="card-header">
        <h4 class="card-title mb-1">
            Categorías
        </h4>
    </div>
    <div class="card-body categories-panel" id="crateCategory">
        <li style="overflow: hidden;white-space: nowrap">
            @if(isset($categorias) && count($categorias)>0)
                <a wire:click="filtrarCategoria"><ul> Mostrar todo</ul></a>
                <a wire:click.prevent="orderByMostOrLessSelled('archives')"><ul>Archivados</ul></a>
                @foreach($categorias as $categoria)
                    <a wire:click="filtrarCategoria('{{ $categoria->id }}')"><ul> {{ $categoria->name }}</ul></a>
                @endforeach
            @endif
            <a data-toggle="modal" data-target="#modalForm" onclick="next()"><ul> + Crear Categoría</ul></a>
        </li>
    </div>
</div>
