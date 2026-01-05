
<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-1">
            Ordenar Clientes
        </h4>
    </div>
    <div class="card-body filters-panel">
    
        <li>
            <ul>Nombre
                <li class="float-right">
                    <a wire:click.defer="$emit('orderBy', 'first_name',0)"><ul>A-Z</ul></a>
                    <a wire:click.defer="$emit('orderBy', 'first_name',1)"><ul>Z-A</ul></a>
                </li>
            </ul>
            <br>
            <hr>
            <ul>Apellido
                <li class="float-right">
                    <a wire:click.defer="$emit('orderBy', 'last_name',0)"><ul>A-Z</ul></a>
                    <a wire:click.defer="$emit('orderBy', 'last_name',1)"><ul>Z-A</ul></a>
                </li>
            </ul>
            <br>
            <hr>
            <ul>Edad
                <li class="float-right">
                    <a wire:click.defer="$emit('orderBy', 'edad',1)"><ul>Mayor</ul></a>
                    <a wire:click.defer="$emit('orderBy', 'edad',0)"><ul>Menor</ul></a>
                </li>
            </ul>
            <br>
            <hr>
            <ul>Añadido
                <li class="float-right">
                    <a wire:click.defer="$emit('orderBy', 'añadido',1)"><ul>Más reciente</ul></a>
                    <a wire:click.defer="$emit('orderBy', 'añadido',0)"><ul>Más antiguo</ul></a>
                </li>
            </ul>
            <br>
            <hr>
            <ul>Cumpleaños
                <li class="float-right">
                    <a wire:click.defer="$emit('orderBy', 'cumpleaños',0)"><ul>Más próximo</ul></a>
                </li>
            </ul>
        </li>
    </div>
</div>