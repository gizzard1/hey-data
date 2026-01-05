
<!-- 
    <div class="col-sm-12 col-md-5" x-data="{ open: false }"@click.away="open=false"> 
        <div class="card">
            <div class="card-header">
                <span class="h3" style="margin:auto">Agregar Fórmula</span>
            </div>
            <div >
                <div class="input-group search-area d-xl-inline-flex d-none ml-4" style="border-bottom:1px solid black;width:90%">
                    <input style="background-color: transparent;border-color:transparent;" wire:model="query" @focus="open=true" @keydown.escape.window="open=false" type="text" id="searchBox" class="form-control form-control-lg" autocomplete="off" placeholder="Escriba el nombre del producto"> 
                    <div class="input-group-append">
                        <i style="background-color: white;" class="input-group-text"><i class="flaticon-381-search-2"></i></i>
                    </div>
                </div>
            </div>
            <div>
                <ul x-show="open" class="list-group position-relative"  style="width: 100%;z-index:99">
                @foreach ($productos as $index => $item)
                    <li wire:click="addProductFromCard({{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action" style="font-weight:lighter; cursor:pointer; color:#6E6E6E">{{ $item->name }}</li>
                @endforeach 
                </ul>
            </div>
            <div class="card-body p-1">
                <div class="table-responsive">

                    <table class="table table-striped table-responsive-sm">
                        <thead>
                            <tr class="text-center">
                                <th width="280">Material</th>
                                <th width="100">Unidad</th>
                                <th width="90">Cantidad</th>
                                <th width="90">Strock</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cartInfo as $item)
                            <tr class="text-center">

                                <td class="text-justify" >{{ $item['name'] }}
                                </td>
                                <td>{{ $item['unit_type'] }}</td>
                                <td>
                                    <input
                                        wire:keydown.enter.prevent="$emit('updateQty', '{{ $item['id'] }}', $event.target.value)"
                                        class="form-control form-control-sm text-center" type="numeric" style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;" 
                                        value=" {{ $item['qty'] }}" >

                                </td>
                                <td>{{ $item['stock'] }}</td>
                                <td>
                                    <button wire:click.prevent="$emit('removeItem', '{{ $item['id'] }}' )"
                                        class="btn tp-btn btn-xxs btn-danger "><i class="fa fa-trash fa-lg"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">BUSCA O ESCANEA UN PRODUCTO</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> -->