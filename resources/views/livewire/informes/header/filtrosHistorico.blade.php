<div class="dropdown-menu filtersWindow" style="padding:1rem" onclick="event.stopPropagation()">
    <div class="d-flex" style="justify-content: space-between;">
        <h4 class="card-title mb-1" style="color: #1d3557;">
            Filtros
        </h4>
        <span>
            <a wire:click="deactivateCheckers">Limpiar</a>
        </span>
    </div>
    <hr>
    <li class="orderByMenu">
        <div class="checker">
            <div>
                <label onclick="event.stopPropagation()" for="Aperturas">Aperturas de caja</label> 
            </div>
            <div>
                <input id="Aperturas" type="checkbox" wire:model.defer="aperturasFilter">
            </div>
        </div>
        <div class="checker">
            <div>
                <label onclick="event.stopPropagation()" for="Cortes">Cierres de caja</label>  
            </div>
            <div>
                <input id="Cortes" type="checkbox" wire:model.defer="cortesFilter">
            </div>    
        </div>
        <div class="checker">
            <div>
                <label onclick="event.stopPropagation()" for="Citas">Citas</label> 
            </div>
            <div>
                <input id="Citas" type="checkbox" wire:model.defer="citasFilter">
            </div>    
        </div>
        <div class="checker">
            <div>
                <label onclick="event.stopPropagation()" for="Ventas">Ventas</label> 
            </div>
            <div>
                <input id="Ventas" type="checkbox" wire:model.defer="ventasFilter">
            </div>    
        </div>
        <div class="checker">
            <div>
                <label onclick="event.stopPropagation()" for="Entradas">Entradas</label> 
            </div>
            <div>
                <input id="Entradas" type="checkbox" wire:model.defer="entradasFilter">
            </div>    
        </div>
        <div class="checker">
            <div>
                <label onclick="event.stopPropagation()" for="Uso">Uso</label> 
            </div>
            <div>
                <input id="Uso" type="checkbox" wire:model.defer="usosFilter">
            </div>    
        </div>
        
        <div class="checker">
            <div>
                <input wire:model.defer="min" class="form-control">
                <label>Min</label>
            </div>
            <div>
                <input wire:model.defer="max" class="form-control">
                <label>Máx</label>
            </div>
        </div>
        
        <button class="btn btn-sm btn-info float-right save" wire:click="aplicarFiltros" style="background-color: #9E846D;border-color:#9E846D">
            Aplicar
        </button>
    </li>
</div>
<style>
    .orderByMenu{
        display: grid;
        row-gap: 1rem;
    }
    .filtersWindow{
        height: 40dvh;
        overflow: auto;
        padding: 1rem;
        width: 40dvh;
    }
    .checker {
        display: flex;
        justify-content: space-between;
    }
</style>