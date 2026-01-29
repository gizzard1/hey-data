<div class="dropdown-menu filtersWindow" style="padding:1rem" onclick="event.stopPropagation()">
    <h4 class="card-title mb-1" style="color: #1d3557;">
        Ordenar por
    </h4>
    <hr>
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
        <ul>
            Visitas
            <li class="float-right">
                <a wire:click.defer="$emit('orderBy', 'visitas',1)"><ul>Más visitas</ul></a>
                <a wire:click.defer="$emit('orderBy', 'visitas',0)"><ul>Menos visitas</ul></a>
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
<style>
    .filtersWindow{
        height: 40dvh;
        overflow: auto;
        padding: 1rem;
        width: 40dvh;
    }
</style>