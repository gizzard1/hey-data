<!-- review -->
<div class="modal fade" id="reviewModal" data-backdrop="static" data-keyboard="false" wire:ignore.self>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Califica a tu Cliente</h5>
                <button type="button" class="close" data-dismiss="modal"><span>x</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group" wire:ignore>
                        <div class="rating-widget mb-4 text-center">
                            <!-- Rating Stars Box -->
                            <div class="rating-stars">
                                <ul id="stars" wire:model.prevent="calificacion">
                                    <li class="star" title="Poor" data-value="1">
                                        <i class="fa fa-star fa-fw"></i>
                                    </li>
                                    <li class="star" title="Fair" data-value="2">
                                        <i class="fa fa-star fa-fw"></i>
                                    </li>
                                    <li class="star" title="Good" data-value="3">
                                        <i class="fa fa-star fa-fw"></i>
                                    </li>
                                    <li class="star" title="Excellent" data-value="4">
                                        <i class="fa fa-star fa-fw"></i>
                                    </li>
                                    <li class="star" title="WOW!!!" data-value="5">
                                        <i class="fa fa-star fa-fw"></i>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div x-data="{ open: false }" @click.away="open = false">
                            <div style="display:flex">
                                <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                                <input class="form-control" type="text" placeholder="Agrega Etiquetas (opcional)" wire:model="queryTag" @focus="open = true" @click="open = true" autocomplete="off">
                                
                                <!-- Icono de búsqueda -->
                                <div class="input-group-append">
                                    <i style="background-color: white;" class="input-group-text">
                                        <i class="las la-tag"></i>
                                    </i>
                                </div>
                            </div>
                            
                            <!-- Desplegable de resultados -->
                            <div>
                                <ul x-show="open" class="list-group float-right" style="position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                                    @foreach ($categoriasTag as $index => $item)
                                    <li wire:click="addTag({{ $item->id }}, '{{ $item->name }}')" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">
                                        {{ $item->name }}
                                    </li>
                                    @endforeach 
                                    <li wire:click="createTag" @click="open = false;" class="list-group-item list-group-item-action" style="cursor: pointer; color: #6E6E6E;">Crear '{{$queryTag}}'</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="tags-container" wire:ignore.self>
                        @if($categoriasCliente)
                            @foreach($categoriasCliente as $index => $categoria)
                                <div class="tag">
                                    <span class="tag-name">{{ $categoria }}</span>
                                    <input type="button" value="x" class="remove-tag" wire:click="eliminarCategoria('{{ $categoria }}')">
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button class="btn btn-info save btn-block" wire:click.prevent="StoreReview">Calificar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    
  
// Obtener todas las estrellas
const stars = document.querySelectorAll('.star');

// Agregar un controlador de eventos clic a cada estrella
stars.forEach(star => {
    star.addEventListener('click', function() {
        // Obtener el valor de la estrella seleccionada
        const ratingValue = parseInt(this.getAttribute('data-value'));

        // Actualizar la variable 'calificacion' (si estás utilizando Livewire, llama a un método de Livewire para actualizar la propiedad)
        // Ejemplo usando Livewire:
        Livewire.emit('actualizarCalificacion', ratingValue);
    });
});


</script>