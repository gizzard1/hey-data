<div class="card">
    <div class="card-header">
        <h4 class="card-title mb-1">
            Filtros
        </h4>
    </div>
    <div class="card-body filters-panel">


        @if(isset($filtros) && count($filtros)>0)
        <div>
            <button class="btn btn-sm save" style="color:white" wire:click="cleanFilters">Limpiar Filtros</button>
        </div>
        @endif


        @if(isset($filtros))
        @foreach($filtros as $index => $filtro)
        <div class="card" style="
                    height: fit-content;
                    margin-top: 3dvh;">
            <div class="card-header">
                <select disabled class="form-control filter-input" style="font-weight: bold;">
                    <option value="atencion" {{ $filtro['type']=="atencion" ? 'selected' : '' }}>Atendido por</option>
                    <option value="visitas" {{ $filtro['type']=="visitas" ? 'selected' : '' }}>Número de visitas
                    </option>
                    <option value="sexo" {{ $filtro['type']=="sexo" ? 'selected' : '' }}>Sexo</option>
                    <option value="edad" {{ $filtro['type']=="edad" ? 'selected' : '' }}>Edad</option>
                    <option value="postcode" {{ $filtro['type']=="postcode" ? 'selected' : '' }}>Código Postal</option>
                    <option value="cat" {{ $filtro['type']=="cat" ? 'selected' : '' }}>Categoría</option>
                    <option value="productos" {{ $filtro['type']=="productos" ? 'selected' : '' }}>Productos comprados
                    </option>
                    <option value="categoria-productos" {{ $filtro['type']=="categoria-productos" ? 'selected' : '' }}>
                        Categoría de productos</option>
                    <option value="proveedor-productos" {{ $filtro['type']=="proveedor-productos" ? 'selected' : '' }}>
                        Proveedor de productos</option>
                    <option value="servicios" {{ $filtro['type']=="servicios" ? 'selected' : '' }}>Servicios usados
                    </option>
                    <option value="categoria-servicios" {{ $filtro['type']=="categoria-servicios" ? 'selected' : '' }}>
                        Categoría de servicios</option>
                    <option value="proveedor-servicios" {{ $filtro['type']=="proveedor-servicios" ? 'selected' : '' }}>
                        Proveedor de servicios</option>
                    <option value="birth_date" {{ $filtro['type']=="birth_date" ? 'selected' : '' }}>Cumpleaños</option>
                    <option value="created_at" {{ $filtro['type']=="created_at" ? 'selected' : '' }}>Fecha de creación
                    </option>
                    <option value="procedencia_id" {{ $filtro['type']=="procedencia_id" ? 'selected' : '' }}>Procedencia
                    </option>
                    <option value="hasReward" {{ $filtro['type']=="hasReward" ? 'selected' : '' }}>Tienen tarjeta de
                        puntos</option>
                    <option value="hasCancelled" {{ $filtro['type']=="hasCancelled" ? 'selected' : '' }}>Han cancelado
                        citas</option>
                    <option value="gastado" {{ $filtro['type']=="gastado" ? 'selected' : '' }}>Han gastado en el salón
                    </option>
                    <option value="inactivo" {{ $filtro['type']=="inactivo" ? 'selected' : '' }}>Tiempo de inactividad
                    </option>
                    <option value="descontadoCitas" {{ $filtro['type']=="descontadoCitas" ? 'selected' : '' }}>
                        Descuentos en citas</option>
                    <option value="descontadoVentas" {{ $filtro['type']=="descontadoVentas" ? 'selected' : '' }}>
                        Descuentos en compras</option>
                    <option value="horario" {{ $filtro['type']=="horario" ? 'selected' : '' }}>Horario de visita
                    </option>
                </select>
                <div class="filter-icons">
                    <button class="close-button" type="button"
                        wire:click.prevent="$emit('deleteFilter', '{{ $filtro['uid'] }}')">
                        <span>x</span>
                    </button>
                    <button class="close-button" type="button" onclick="openFilter('{{ $filtro['uid'] }}')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                            fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-caret-down">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M18 9c.852 0 1.297 .986 .783 1.623l-.076 .084l-6 6a1 1 0 0 1 -1.32 .083l-.094 -.083l-6 -6l-.083 -.094l-.054 -.077l-.054 -.096l-.017 -.036l-.027 -.067l-.032 -.108l-.01 -.053l-.01 -.06l-.004 -.057v-.118l.005 -.058l.009 -.06l.01 -.052l.032 -.108l.027 -.067l.07 -.132l.065 -.09l.073 -.081l.094 -.083l.077 -.054l.096 -.054l.036 -.017l.067 -.027l.108 -.032l.053 -.01l.06 -.01l.057 -.004l12.059 -.002z" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="card-body toggled" id="{{ $filtro['uid'] }}" wire:ignore.self>
                <div class="form-group">
                    @if($filtro['type'] == 'postcode')
                    <input type="number" value="{{ $filtro['query'] }}" class="form-control"
                        wire:change.defer="$emit('updateQuery', '{{ $filtro['uid'] }}',$event.target.value,0 )">
                    @elseif($filtro['type'] == 'hasCancelled')
                    <input type="number" value="{{ $filtro['query'] }}" class="mb-3 form-control"
                        placeholder="Cantidad de visitas"
                        wire:change.defer="$emit('updateQuery', '{{ $filtro['uid'] }}',$event.target.value,0 )">
                    @include('livewire.clientes.sidebar.dropdown')
                    @elseif($filtro['type'] == 'edad')
                    <div class="form-group d-flex" style="column-gap:1rem">
                        <div>
                            <input
                                wire:change.defer="$emit('updateQuery', '{{ $filtro['uid'] }}',null,1,$event.target.value)"
                                class="form-control" type="number" value="{{ $filtro['min'] ?? '' }}">
                            <label>desde</label>
                        </div>
                        <div>
                            <input class="form-control" type="number"
                                wire:change.defer="$emit('updateQuery', '{{ $filtro['uid'] }}',null,1, null,$event.target.value)"
                                value="{{ $filtro['max'] ?? '' }}">
                            <label>hasta</label>
                        </div>
                    </div>
                    @elseif($filtro['type'] == 'horario')
                    <div class="form-group" style="column-gap:1rem">
                        <div>
                            <input
                                wire:change.defer="$emit('updateQuery', '{{ $filtro['uid'] }}',null,1,$event.target.value)"
                                class="form-control" type="time" value="{{ $filtro['min'] ?? '' }}">
                            <label>desde</label>
                        </div>
                        <div>
                            <input
                                wire:change.defer="$emit('updateQuery', '{{ $filtro['uid'] }}',null,1, null,$event.target.value)"
                                class="form-control" type="time" value="{{ $filtro['max'] ?? '' }}">
                            <label>hasta</label>
                        </div>
                    </div>
                    @include('livewire.clientes.sidebar.dropdown')
                    @elseif($filtro['type'] == 'gastado')
                    <div class="form-group d-flex" style="column-gap:1rem">
                        <div>
                            <input
                                wire:change.defer="$emit('updateQueryMoney', '{{ $filtro['uid'] }}',$event.target.value)"
                                class="form-control" value="${{ $filtro['min'] ?? '' }}">
                            <label>desde</label>
                        </div>
                        <div>
                            <input class="form-control"
                                wire:change.defer="$emit('updateQueryMoney', '{{ $filtro['uid'] }}',null,$event.target.value)"
                                value="${{ $filtro['max'] ?? '' }}">
                            <label>hasta</label>
                        </div>
                    </div>
                    @include('livewire.clientes.sidebar.dropdown')
                    @elseif($filtro['type'] == 'visitas')
                    <div class="form-group d-flex" style="column-gap:1rem">
                        <div>
                            <input
                                wire:change.defer="$emit('updateQueryMoney', '{{ $filtro['uid'] }}',$event.target.value)"
                                class="form-control" value="{{ $filtro['min'] ?? '' }}">
                            <label>desde</label>
                        </div>
                        <div>
                            <input class="form-control"
                                wire:change.defer="$emit('updateQueryMoney', '{{ $filtro['uid'] }}',null,$event.target.value)"
                                value="{{ $filtro['max'] ?? '' }}">
                            <label>hasta</label>
                        </div>
                    </div>
                    @include('livewire.clientes.sidebar.dropdown')
                    @elseif($filtro['type'] == 'descontadoCitas')
                    <div class="form-group d-flex" style="column-gap:1rem">
                        <div>
                            <input
                                wire:change.defer="$emit('updateQueryMoney', '{{ $filtro['uid'] }}',$event.target.value)"
                                class="form-control" value="${{ $filtro['min'] ?? '' }}">
                            <label>desde</label>
                        </div>
                        <div>
                            <input class="form-control"
                                wire:change.defer="$emit('updateQueryMoney', '{{ $filtro['uid'] }}',null,$event.target.value)"
                                value="${{ $filtro['max'] ?? '' }}">
                            <label>hasta</label>
                        </div>
                    </div>
                    @include('livewire.clientes.sidebar.dropdown')
                    @elseif($filtro['type'] == 'descontadoVentas')
                    <div class="form-group d-flex" style="column-gap:1rem">
                        <div>
                            <input
                                wire:change.defer="$emit('updateQueryMoney', '{{ $filtro['uid'] }}',$event.target.value)"
                                class="form-control" value="${{ $filtro['min'] ?? '' }}">
                            <label>desde</label>
                        </div>
                        <div>
                            <input class="form-control"
                                wire:change.defer="$emit('updateQueryMoney', '{{ $filtro['uid'] }}',null,$event.target.value)"
                                value="${{ $filtro['max'] ?? '' }}">
                            <label>hasta</label>
                        </div>
                    </div>
                    @include('livewire.clientes.sidebar.dropdown')
                    @elseif($filtro['type'] == 'birth_date')
                    @include('livewire.clientes.sidebar.dropdown')
                    @elseif($filtro['type'] == 'created_at')
                    <div class="form-group" style="column-gap:1rem">
                        @include('livewire.clientes.sidebar.dropdown')
                    </div>
                    @elseif($filtro['type'] == 'inactivo')
                    <div class="form-group" style="column-gap:1rem">
                        @include('livewire.clientes.sidebar.dropdown')
                    </div>
                    @elseif($filtro['type'] == 'atencion')
                    @include('livewire.clientes.sidebar.dropdown')
                    <select class="form-control"
                        wire:change.defer="$emit('updateQuery', '{{ $filtro['uid'] }}',$event.target.value,0 )">
                        <option value="null" {{ $filtro['query']=="NULL" ? 'selected' : '' }}>Seleccione una opción
                        </option>
                        @foreach($empleados as $empleado)
                        <option value="{{ $empleado->id }}" {{ $filtro['query']==$empleado->id ? 'selected' : '' }}>{{
                            $empleado->first_name }} {{ $empleado->last_name }}</option>
                        @endforeach
                    </select>
                    @elseif($filtro['type'] == 'sexo')
                    <select class="form-control"
                        wire:change.defer="$emit('updateQuery', '{{ $filtro['uid'] }}',$event.target.value,0 )">
                        <option value="null" {{ $filtro['query']=="NULL" ? 'selected' : '' }}>Seleccione una opción
                        </option>
                        <option value="femenino" {{ $filtro['query']=="femenino" ? 'selected' : '' }}>Femenino</option>
                        <option value="masculino" {{ $filtro['query']=="masculino" ? 'selected' : '' }}>Masculino
                        </option>
                        <option value="noBinario" {{ $filtro['query']=="noBinario" ? 'selected' : '' }}>No binario
                        </option>
                    </select>
                    @elseif($filtro['type'] == 'procedencia_id')
                    <select class="form-control"
                        wire:change.defer="$emit('updateQuery', '{{ $filtro['uid'] }}',$event.target.value,0 )">
                        <option value="null" {{ $filtro['query']=="NULL" ? 'selected' : '' }}>Seleccione una opción
                        </option>
                        @foreach($procedencias as $procedencia)
                        <option value="{{ $procedencia->id }}" {{ $filtro['query']==$procedencia->id ? 'selected' : ''
                            }}>{{ $procedencia->name }}</option>
                        @endforeach
                    </select>
                    @elseif($filtro['type'] == 'cat')
                    <select class="form-control"
                        wire:change.defer="$emit('updateQuery', '{{ $filtro['uid'] }}',$event.target.value,0 )">
                        <option value="null" {{ $filtro['query']=="NULL" ? 'selected' : '' }}>Seleccione una opción
                        </option>
                        @foreach($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ $filtro['query']==$categoria->id ? 'selected' : '' }}>{{
                            $categoria->name }}</option>
                        @endforeach
                    </select>
                    @elseif($filtro['type'] == 'productos')

                    @include('livewire.clientes.sidebar.dropdown')
                    <div x-data="{ open: false }" @click.away="open = false">
                        <div style="display:flex">
                            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                            <input wire:model="filtros.{{ $index }}.query" @focus="open = true" @click="open = true"
                                type="text" class="form-control form-control" autocomplete="off"
                                placeholder="Escriba el nombre del producto"
                                wire:keydown.debounce.300ms="mostrarListadoProductos('{{ $filtro['uid'] }}')">

                            <!-- Icono de búsqueda -->
                            <div class="input-group-append">
                                <i style="background-color: white;" class="input-group-text">
                                    <i class="flaticon-381-search-2"></i>
                                </i>
                            </div>
                        </div>

                        <!-- Desplegable de resultados -->
                        <div>
                            <ul x-show="open" class="list-group float-right"
                                style="position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                                @if (isset($productos[$filtro['uid']]))
                                @foreach ($productos[$filtro['uid']] as $item)
                                <li wire:click="filtroPSActualizado('{{ $filtro['uid'] }}','{{ $item['id'] }}')"
                                    @click="open = false;" class="list-group-item list-group-item-action"
                                    style="cursor: pointer; color: #6E6E6E">
                                    {{ $item['name'] }}
                                </li>
                                @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>

                    @elseif($filtro['type'] == 'categoria-productos')

                    @include('livewire.clientes.sidebar.dropdown')
                    <div x-data="{ open: false }" @click.away="open = false">
                        <div style="display:flex">
                            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                            <input wire:model="filtros.{{ $index }}.query" @focus="open = true" @click="open = true"
                                type="text" class="form-control form-control" autocomplete="off"
                                placeholder="Escriba el nombre de la categoría"
                                wire:keydown.debounce.300ms="mostrarListadoCategoriaProductos('{{ $filtro['uid'] }}')">

                            <!-- Icono de búsqueda -->
                            <div class="input-group-append">
                                <i style="background-color: white;" class="input-group-text">
                                    <i class="flaticon-381-search-2"></i>
                                </i>
                            </div>
                        </div>

                        <!-- Desplegable de resultados -->
                        <div>
                            <ul x-show="open" class="list-group float-right"
                                style="position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                                @if (isset($categoriaProductos[$filtro['uid']]))
                                @foreach ($categoriaProductos[$filtro['uid']] as $item)
                                <li wire:click="filtroPSActualizado('{{ $filtro['uid'] }}','{{ $item['id'] }}')"
                                    @click="open = false;" class="list-group-item list-group-item-action"
                                    style="cursor: pointer; color: #6E6E6E">
                                    {{ $item['name'] }}
                                </li>
                                @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                    @elseif($filtro['type'] == 'proveedor-productos' || $filtro['type'] == 'proveedor-servicios')

                    @include('livewire.clientes.sidebar.dropdown')
                    <div x-data="{ open: false }" @click.away="open = false">
                        <div style="display:flex">
                            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                            <input wire:model="filtros.{{ $index }}.query" @focus="open = true" @click="open = true"
                                type="text" class="form-control form-control" autocomplete="off"
                                placeholder="Escriba el nombre del proveedor"
                                wire:keydown.debounce.300ms="mostrarListadoProveedores('{{ $filtro['uid'] }}')">

                            <!-- Icono de búsqueda -->
                            <div class="input-group-append">
                                <i style="background-color: white;" class="input-group-text">
                                    <i class="flaticon-381-search-2"></i>
                                </i>
                            </div>
                        </div>

                        <!-- Desplegable de resultados -->
                        <div>
                            <ul x-show="open" class="list-group float-right"
                                style="position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                                @if (isset($proveedores[$filtro['uid']]))
                                @foreach ($proveedores[$filtro['uid']] as $item)
                                <li wire:click="filtroPSActualizado('{{ $filtro['uid'] }}','{{ $item['id'] }}')"
                                    @click="open = false;" class="list-group-item list-group-item-action"
                                    style="cursor: pointer; color: #6E6E6E">
                                    {{ $item['name'] }}
                                </li>
                                @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>

                    @elseif($filtro['type'] == 'servicios')
                    @include('livewire.clientes.sidebar.dropdown')

                    <div x-data="{ open: false }" @click.away="open = false">
                        <div style="display:flex">
                            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                            <input wire:model="filtros.{{ $index }}.query" @focus="open = true" @click="open = true"
                                type="text" class="form-control form-control" autocomplete="off"
                                placeholder="Escriba el nombre del servicio"
                                wire:keydown.debounce.300ms="mostrarListadoServicios('{{ $filtro['uid'] }}')">

                            <!-- Icono de búsqueda -->
                            <div class="input-group-append">
                                <i style="background-color: white;" class="input-group-text">
                                    <i class="flaticon-381-search-2"></i>
                                </i>
                            </div>
                        </div>

                        <!-- Desplegable de resultados -->
                        <div>
                            <ul x-show="open" class="list-group float-right"
                                style="position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                                @if (isset($servicios[$filtro['uid']]))
                                @foreach ($servicios[$filtro['uid']] as $item)
                                <li wire:click="filtroPSActualizado('{{ $filtro['uid'] }}','{{ $item['id'] }}')"
                                    @click="open = false;" class="list-group-item list-group-item-action"
                                    style="cursor: pointer; color: #6E6E6E">
                                    {{ $item['name'] }}
                                </li>
                                @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                    @elseif($filtro['type'] == 'categoria-servicios')
                    @include('livewire.clientes.sidebar.dropdown')

                    <div x-data="{ open: false }" @click.away="open = false">
                        <div style="display:flex">
                            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                            <input wire:model="filtros.{{ $index }}.query" @focus="open = true" @click="open = true"
                                type="text" class="form-control form-control" autocomplete="off"
                                placeholder="Escriba el nombre de la categoría"
                                wire:keydown.debounce.300ms="mostrarListadoCategoriaServicios('{{ $filtro['uid'] }}')">

                            <!-- Icono de búsqueda -->
                            <div class="input-group-append">
                                <i style="background-color: white;" class="input-group-text">
                                    <i class="flaticon-381-search-2"></i>
                                </i>
                            </div>
                        </div>

                        <!-- Desplegable de resultados -->
                        <div>
                            <ul x-show="open" class="list-group float-right"
                                style="position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                                @if (isset($categoriaServicios[$filtro['uid']]))
                                @foreach ($categoriaServicios[$filtro['uid']] as $item)
                                <li wire:click="filtroPSActualizado('{{ $filtro['uid'] }}','{{ $item['id'] }}')"
                                    @click="open = false;" class="list-group-item list-group-item-action"
                                    style="cursor: pointer; color: #6E6E6E">
                                    {{ $item['name'] }}
                                </li>
                                @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>

                    @endif
                </div>
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>
<style>
    .close-button {
        border: transparent;
        background-color: transparent;
    }

    .filter-input {
        border: transparent;
        background-color: transparent !important;
        -webkit-appearance: none;
    }

    .toggled {
        display: none;
    }

    .filter-icons {
        display: flex;
        column-gap: 2dvh;
    }
</style>

<script>
    function openFilter(uid,rm=false)
    {
        var filterBody = document.getElementById(uid);
        filterBody.classList.toggle('toggled');

        if (rm) {
            filterBody.remove(); // Esto lo elimina del DOM
        }
    }

    window.addEventListener('openFilter', (e) => {
        openFilter(e.detail.uid,true);
    })
</script>