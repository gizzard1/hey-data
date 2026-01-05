<div class="card">
    <div class="card-header ">
        <div class="d-flex">
            <div class="separator" style="background-color:#E2BBB4"></div>
            <div class="mr-auto mt-3">
                <h4 class="card-title"><a wire:click="$set('infoSelected','1')">Ajustes</a>/ Recompensas</h4>
            </div>
        </div>
    </div>
    <div class="card-body" id="recompensas">
            <div class="mr-auto">
                <h4 class="card-title">Recompensas en Servicios</h4>
            </div>
            
        <hr>
        <div class="row">
            <div class="col-sm-12 col-md-3  " style="margin: auto;" wire:ignore>
                <label style="text-align: center;">Seleccionar todo</label>
                <input type="checkbox" style="align-self: center;" wire:model.defer="recompensaS.to_all" value="1">
            </div>
            <div class="col-sm-12 col-md-4" wire:ignore style="background-color:white !important;">
                <input  style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"  wire:model.defer="categoriesList" type="text" id="tomCategoryS" placeholder="{{ $categoriesList ? '': 'Buscar categoría' }}" autocomplete="off" class="form-control form-control-lg" style="background-color:white !important;">
                <label style="background-color:white !important;">Categorías de servicios</label>
            </div>
            <div class="col-sm-12 col-md-4">
                <input style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" wire:model.defer="recompensaS.percent" type="number" class="form-control form-control-lg"
                    placeholder="0.00%">
                <label>Porcentaje de puntos</label>
            </div>
        </div>
    </div>
    <div class="card-footer" id="save-apply">
        @if($this->recompensaS->id)
        <button class="btn btn-sm btn-dark float-right ml-3" wire:click.prevent="ApplyChangeS" style="background-color:black; border-color:black">Aplicar</button>
        @endif
        <button class="btn btn-sm btn-dark float-right" wire:click.prevent="cancelRS" style="background-color: transparent;color:#9D1466">Cancelar</button>
        <button class="btn btn-sm btn-info float-right save mr-3" wire:click.prevent="StoreService" style="background-color:#9D1466">Guardar</button>
    </div>
    <div class="card-body mt-2">
            <div class="mr-auto">
                <h4 class="card-title">Recompensas en Productos</h4>
            </div>
        <hr>
        <div class="row">
            <div class="col-sm-12 col-md-3  " style="margin: auto;" wire:ignore>
                <label style="text-align: center;">Seleccionar todo</label>
                <input type="checkbox" style="align-self: center;" wire:model.defer="recompensaP.to_all" value="1">
            </div>
            <div class="col-sm-12 col-md-4" wire:ignore style="background-color:white !important;">
                <input  style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"  wire:model.defer="categoriesListP" type="text" id="tomCategory" placeholder="Buscar categoría" autocomplete="off" class="form-control form-control-lg" style="background-color:white !important;">
                <label style="background-color:white !important;">Categorías de productos</label>
            </div>
            <div class="col-sm-12 col-md-4">
                <input style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" wire:model.defer="recompensaP.percent" type="number" class="form-control form-control-lg"
                    placeholder="0.00%">
                <label>Porcentaje de puntos</label>
            </div>
        </div>
    </div>
    <div class="card-footer">
        @if($this->recompensaP->id)
        <button class="btn btn-sm btn-dark float-right ml-3" wire:click.prevent="ApplyChangeP" style="background-color:black; border-color:black">Aplicar</button>
        @endif
        <button class="btn btn-sm btn-dark float-right" wire:click.prevent="cancelRP" style="background-color: transparent;color:#9D1466">Cancelar</button>
        <button class="btn btn-sm btn-info float-right save mr-3" wire:click.prevent="StoreProduct" style="background-color:#9D1466">Guardar</button>
    </div>
</div>