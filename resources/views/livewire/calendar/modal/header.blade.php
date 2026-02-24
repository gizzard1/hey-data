
<div class="card-header flex-wrap">
    <div class="d-flex">
        <div class="separator" style="background-color:#E2BBB4"></div>
        <div class="mr-auto">
            <h4>Nueva Cita ({{ $minutes_qty }} min.)</h4>
            <a id="fechaCita" type="button" wire:ignore.self wire:model="currentDateC" class="flatpickr" wire:change="dateSelected"><h5 class="fs-14 mb-0" style="justify-content: space-between;" >{{ $currentDate }}</h5>
        
            <div>
                <a wire:click.prevent="showAdvanced" style="text-decoration:underline;cursor:pointer" onclick="next()">Vista avanzada</a>
            </div>
            
        </a>

        </div>
    </div>
    <div id="buscar-serv" x-data="{ open: false }" @click.away="open = false" >
        <div style="display:flex">
            <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                
            <input wire:model="queryServices" @focus="open=true" style="width: 20rem;" @click="open = true" onclick="next()" type="text" class="form-control" autocomplete="off" placeholder="Escriba el nombre del servicio"> 
            
            <!-- Icono de búsqueda -->
            <div class="input-group-append">
                <i style="background-color: white;" class="input-group-text">
                    <i class="flaticon-381-search-2"></i>
                </i>
            </div>
        </div>
        <!-- Desplegable de resultados -->
        <div>
            <ul x-show="open" class="list-group float-right" style="width: 20rem; position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                @foreach ($servicios as $index => $item)
                    <li wire:click="$emit('addNewService', {{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action" style="cursor:pointer; color:#6E6E6E">
                        {{ $item->name }} {{ $item->duration }} min. | ${{ number_format($item->gross_price, 2, '.', ',') }}
                    </li>
                @endforeach 
            </ul>
        </div>

    </div>

</div>