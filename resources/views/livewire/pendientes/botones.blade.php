<div class="float-right dropdown">
    <button class="btn btn-sm input-group-text dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Mostrar</button>
    <div class="dropdown-menu" style="padding: 1rem;transform: translate3d(-52px, 37px, 0px)!important">
        <li class="orderByMenu">
            <a wire:click.prevent="selectFilters(1, 0, 0)">Facturados</a>
            <a wire:click.prevent="selectFilters(0, 1, 0)">Pendiente facturar</a>
            <a wire:click.prevent="selectFilters(0, 0, 1)">No pagados</a>
            @if($type===0)
            <a wire:click.prevent="selectFilters(0, 1, 1)">Citas futuras</a>
            @endif
        </li>

    </div>
</div>

<style>
    .dropdown-item{
        color:black !important;
    }
    .swal2-html-container{
        color:#515457 !important;
    }
    .orderByMenu{
        display: grid;
        row-gap: 1rem;
    }
</style>