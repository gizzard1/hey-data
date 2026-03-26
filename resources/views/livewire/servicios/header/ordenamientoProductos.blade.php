<div class="dropdown-menu filtersWindow" style="padding:1rem" onclick="event.stopPropagation()" aria-labelledby="orderBy">
    <h4 class="card-title mb-1" style="color: #1d3557;">
        Ordenar por
    </h4>
    <hr>
    <li>
        <ul>Nombre
            <li class="float-right">
                <a wire:click.defer="orderBy('name','asc')"><ul>A-Z</ul></a>
                <a wire:click.defer="orderBy('name','desc')"><ul>Z-A</ul></a>
            </li>
        </ul>
        <br>
        <hr>
        <ul>Proveedor
            <li class="float-right">
                <a wire:click.defer="orderBy('marca.name','asc')"><ul>A-Z</ul></a>
                <a wire:click.defer="orderBy('marca.name','desc')"><ul>Z-A</ul></a>
            </li>
        </ul>
        <br>
        <hr>
        <ul>Ventas
            <li class="float-right">
                <a wire:click.prevent="orderByMostOrLessSelled('desc')"><ul>Más vendidos</ul></a>
                <a wire:click.prevent="orderByMostOrLessSelled('asc')"><ul>Menos vendidos</ul></a>
                <a wire:click.prevent="orderByMostOrLessSelled('noSales')"><ul>Sin ventas</ul></a>
            </li>
        </ul>
        <br>
        <hr>
    </li>
</div>
<style>
    .filtersWindow{
        height: 27dvh;
        overflow: auto;
        padding: 1rem;
        width: 40dvh;
    }
</style>