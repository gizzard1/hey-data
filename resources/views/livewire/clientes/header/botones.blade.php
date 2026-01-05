@if(count($selectedItems)>0)
    <div class="dropdown">
        <button id="options" class="btn-sm input-group-text dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Opciones</button>
        <div class="dropdown-menu" style="transform: translate3d(-40%, 34px, 0px);padding:1rem" aria-labelledby="options">
            <li>
                @if(count($selectedItems)==1)
                <a data-toggle="modal" data-target="#modalActivateCard" wire:click.prevent="activateCard"><ul><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pig-money" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M15 11v.01" />
                    <path d="M5.173 8.378a3 3 0 1 1 4.656 -1.377" />
                    <path d="M16 4v3.803a6.019 6.019 0 0 1 2.658 3.197h1.341a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-1.342c-.336 .95 -.907 1.8 -1.658 2.473v2.027a1.5 1.5 0 0 1 -3 0v-.583a6.04 6.04 0 0 1 -1 .083h-4a6.04 6.04 0 0 1 -1 -.083v.583a1.5 1.5 0 0 1 -3 0v-2l0 -.027a6 6 0 0 1 4 -10.473h2.5l4.5 -3h0z" />
                    </svg> Recompensas</ul></a>
                    
                <a wire:click.prevent="Edit"><ul><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                    <path d="M16 5l3 3" />
                    </svg> Editar</ul></a>
                @endif

                @if(count($selectedItems)==3 || count($selectedItems)==2)
                <a data-toggle="modal" data-target="#modalMergeCust" wire:click.prevent="startMerge"><ul><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-merge" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M8 7l4 -4l4 4" />
                    <path d="M12 3v5.394a6.737 6.737 0 0 1 -3 5.606a6.737 6.737 0 0 0 -3 5.606v1.394" />
                    <path d="M12 3v5.394a6.737 6.737 0 0 0 3 5.606a6.737 6.737 0 0 1 3 5.606v1.394" />
                    </svg>Unir clientes</ul></a>
                @endif
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
                    </svg> Unir al grupo</ul></a>
                <!-- <a wire:click.prevent="changeNorma"><ul><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users-group" width="25" height="25" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" />
                    <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M17 10h2a2 2 0 0 1 2 2v1" />
                    <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M3 13v-1a2 2 0 0 1 2 -2h2" />
                    </svg> Cambiar procedencia</ul></a> -->

                
            </li>
        </div>
    </div>
@else
    <div class="float-right">
        <button wire:click="crearCliente" id="create-cust" onclick="next()" class="btn-sm save input-group-text"style="color:white">Crear Cliente</button>
    </div>
@endif
<div class="float-right dropdown">
    <button id="filterBy" class="btn btn-sm input-group-text dropdown-toggle"  data-toggle="dropdown" aria-haspopup="true">Filtrar</button>
    @include('livewire.clientes.header.filtrosClientes')
</div>
<div class="float-right dropdown">
    <button id="orderBy" class="btn btn-sm input-group-text dropdown-toggle"  data-toggle="dropdown" aria-haspopup="true">Ordenar</button>
    @include('livewire.clientes.header.ordenamientoClientes')
</div>
<div class="float-right dropdown"  style="background-color: #278d46;border-radius:5px">
    <button id="exportar" class="button-style" style="border-width: 0;color:white"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="las la-file-excel la-2x"></i></button>
    <div class="dropdown-menu" style="transform: translate3d(-40%, 34px, 0px);padding:1rem" aria-labelledby="options">
        <li>
            @if(Auth::user()->salon_id==5)
            <a wire:click="exportarFormulario"><ul>Exportar formulario</ul></a>
            @endif
            <a wire:click="exportarClientes"><ul>Exportar clientes</ul></a>
        </li>
    </div>
</div>