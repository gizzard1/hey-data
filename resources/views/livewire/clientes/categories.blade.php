<div wire:ignore.self id="modalCategories" class="modal fade" role="dialog">
<div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Unir a la categoría</h4>
            </div>
            <div class="modal-body">
                <li>
                @if($categorias!=null)
                @foreach($categorias as $categoria)
                <ul>
                    <a data-dismiss="modal" wire:click.prevent="joinGroup('{{ $categoria->id }}')">- {{ $categoria->name }}</a>
                </ul>
                @endforeach
                @endif
                </li>
            </div>
            <div class="modal-footer">

                <button class="btn btn-sm float-right" style="color:white;background-color:#1D3557" data-toggle="modal" data-target="#modalForm">Crear Categoría</button>
                <button type="button" class="btn-sm btn" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
