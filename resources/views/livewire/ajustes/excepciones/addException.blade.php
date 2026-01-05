
<div class="card-header">
    <h4 style="color: #9D1466;">Agregar Excepciones</h4>
</div>
<div class="card-body" id="exception-form">
    <div class="row" x-data="{ open: false }" @click.away="open=false"> 
        <div class="col-sm-12 col-md-4">
            <input style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" wire:model="query" @focus="open=true" @keydown.escape.window="open=false" type="text" id="searchBox" class="form-control form-control-lg" autocomplete="off">
            <label>Nombre</label>
        </div>
        <ul x-show="open" class="list-group" style="width: 28%;position: absolute;transform: translate(3%, 16%);z-index: 99;">
            @foreach ($listado as $index => $item)
            <li wire:click="selectedItem({{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action" style="font-weight:lighter; cursor:pointer; color:#6E6E6E;">{{ $item->name }}</li>
            @endforeach
        </ul>
        <div class="col-sm-12 col-md-4">
            <select style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" wire:model='tipoExc' class="form-control  form-control-lg">
                <option value="percent">%</option>
                <option value="qty">$</option>
            </select>
            @error('tipoExc') <span class="text-danger">*Corrige este campo* </span> @enderror
            <label>Tipo</label>
        </div>
        <div class="col-sm-12 col-md-4">
            <input style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" wire:model="qty" type="text"
                class="form-control form-control-lg">
            @error('qty') <span class="text-danger">*Corrige este campo* </span> @enderror
            <label>Cantidad</label>
        </div>
    </div>
</div>
<div class="card-footer">
        <button class="btn btn-sm btn-dark float-left  mb-3" style="background-color: transparent;color:#9D1466" wire:click.prevent="cancel">Cancelar</button>
    <button onclick="next()" id="save-button-2" class="btn btn-sm btn-info float-right save  mb-3" wire:click.prevent="StoreException" style="background-color:#9D1466; border-color:#B59377">Guardar</button>
</div>