<div class="card">
    <div class="card">
        <div class="card-header">
            <div class="d-flex">
                <div class="separator" style="background-color:#E2BBB4"></div>
                <div class="mr-auto mt-3">
                    <h4 class="card-title"><a wire:click="$emit('infoSelected','1')">Ajustes</a>/ <a wire:click="$emit('infoSelected','5')">Clientes</a> /Recompensas</h4>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <label>Recompensas por servicios:</label>
                        <div class="form-group d-flex" style="column-gap:1rem">
                            <div>
                                <select wire:model='comisionGen.type_comission_s' class="form-control">
                                    <option value="percent" {{ $comisionGen->type_comission_s === 'percent' ? 'selected' : '' }}>%</option>
                                    <option value="qty" {{ $comisionGen->type_comission_s === 'qty' ? 'selected' : '' }}>$</option>
                                </select>
                                <label>Tipo</label>
                            </div>
                            <div>
                                <input wire:model="comisionGen.qty_s" type="number" class="form-control">
                                <label>Cantidad</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">

                        <label>Recompensas por productos:</label>
                        <div class="form-group d-flex" style="column-gap:1rem">
                            <div>
                                <select wire:model='comisionGen.type_comission_p' class="form-control">
                                    <option value="percent" {{ $comisionGen->type_comission_p === 'percent' ? 'selected' : '' }}>%</option>
                                    <option value="qty" {{ $comisionGen->type_comission_p === 'qty' ? 'selected' : '' }}>$</option>
                                </select>
                                <label>Tipo</label>
                            </div>
                            <div>
                                <input wire:model="comisionGen.qty_p" type="number" class="form-control">
                                <label>Cantidad</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer" style="position: relative;" >
            <button  onclick="next()" class="btn btn-sm btn-info float-right save" wire:click.prevent="StoreGeneralException">Guardar</button>
            <button  onclick="next()" class="btn btn-sm btn-dark float-right" wire:click.prevent="cancelGen" style="background-color: transparent;color:#9D1466">Limpiar</button>
        </div>
    </div>
</div>