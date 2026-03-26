<div class="float-right dropdown">
    <button id="orderBy" class="btn btn-sm input-group-text dropdown-toggle"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ordenar</button>
    @include('livewire.servicios.header.ordenamientoProductos')
</div>
@if(count($selectedItems)>0)
    <div class="dropdown">
        <button id="options" class="btn-sm input-group-text dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Opciones</button>
        <div class="dropdown-menu" style="transform: translate3d(-40%, 34px, 0px);padding:1rem" aria-labelledby="options">
            <li>
                @if(count($selectedItems)==1)
                    
                <a wire:click.prevent="Edit" data-toggle="modal" data-target="#modalCreateForm"><ul><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                    <path d="M16 5l3 3" />
                    </svg> Editar</ul></a>
                @endif

                <!-- @if(count($selectedItems)==2)
                <a data-toggle="modal" data-target="#modalMergeCust" wire:click.prevent="startMerge"><ul><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-merge" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M8 7l4 -4l4 4" />
                    <path d="M12 3v5.394a6.737 6.737 0 0 1 -3 5.606a6.737 6.737 0 0 0 -3 5.606v1.394" />
                    <path d="M12 3v5.394a6.737 6.737 0 0 0 3 5.606a6.737 6.737 0 0 1 3 5.606v1.394" />
                    </svg>Unir Servicios</ul></a>
                @endif -->
                <a onclick="confirmDelete()"><ul><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M4 7l16 0" />
                    <path d="M10 11l0 6" />
                    <path d="M14 11l0 6" />
                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                    </svg> Eliminar</ul></a>

                <a data-toggle="modal" data-target="#modalCategories"><ul><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users-group" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" />
                    <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M17 10h2a2 2 0 0 1 2 2v1" />
                    <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M3 13v-1a2 2 0 0 1 2 -2h2" />
                    </svg> Unir a la categoría</ul></a>
                <a data-toggle="modal" data-target="#modalRewardForm"><ul><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-award" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 9m-6 0a6 6 0 1 0 12 0a6 6 0 1 0 -12 0" />
                    <path d="M12 15l3.4 5.89l1.598 -3.233l3.598 .232l-3.4 -5.889" />
                    <path d="M6.802 12l-3.4 5.89l3.598 -.233l1.598 3.232l3.4 -5.889" />
                    </svg> Modificar recompensa</ul></a>
                @if($orderByMostOrLessSelled !== 'archives')
                <a wire:click='archiveItem'><ul><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-archive">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                    <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10" />
                    <path d="M10 12l4 0" />
                    </svg> Archivar </ul></a>
                @else
                <a wire:click='archiveItem("visible")'><ul><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-archive">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                    <path d="M5 8v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-10" />
                    <path d="M10 12l4 0" />
                    </svg> Desarchivar </ul></a>
                @endif
            </li>
        </div>
    </div>
@else
    <div class="float-right" id="createService">
        <button wire:click="Add" onclick="next()" class="btn btn-sm save input-group-text" style="color:white" data-toggle="modal" data-target="#modalCreateForm" id="createProduct">Crear {{ $isService ? 'servicio' : 'producto' }}</button>
    </div>
@endif
<div class="float-right">
    <button wire:click="exportar" class="btn-sm excel-button input-group-text">Exportar {{ $isService ? 'servicio' : 'producto' }}s</button>
</div>


<style>
    .dropdown-item{
        color:black !important;
    }
    .swal2-html-container{
        color:#515457 !important;
    }
</style>