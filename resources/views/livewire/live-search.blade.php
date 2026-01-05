
<div class="col-sm-12 col-md-8">
    <input style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" wire:model="query" @focus="open=true" @keydown.escape.window="open=false" type="text" id="searchBox" class="form-control form-control-lg" autocomplete="off">
    <label>Nombre</label>
</div>
<ul x-show="open" class="list-group position-absolute" style="width: 100%;z-index:99;">
    @foreach ($listado as $index => $item)
    <li wire:click="selectedItem({{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action {{ $index % 2 == 0 ? 'bg-light' : 'bg-dark' }}" style="font-weight:lighter; cursor:pointer; color:aliceblue;">{{ $item->name }}</li>
    @endforeach
</ul>