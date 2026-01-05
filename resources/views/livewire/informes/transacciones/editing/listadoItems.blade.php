<div>
    <div wire:ignore.self class="modal fade none-border" id="modalItems" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="height: auto;max-height:35rem;overflow: auto;">
                <div class="modal-header">
                    <h5 class="modal-title">Buscar un {{ $type == 'venta' ? 'producto' : 'servicio' }}</h5>
                    <button type="button" class="close" data-dismiss="modal" wire:click="initializeQuery"><span>x</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="card-header">
                        <div class="input-group search-area d-xl-inline-flex d-none w-50" style="border-bottom:1px solid black;">
                            <input style="background-color: transparent;border-color:transparent;" wire:model="search" type="text" id="searchBox" class="form-control" autocomplete="off" placeholder="Escriba el nombre del {{ $type == 'venta' ? 'producto' : 'servicio' }}"> 
                            <div class="input-group-append">
                                <i style="background-color: white;" class="input-group-text"><i class="flaticon-381-search-2"></i></i>
                            </div>
                        </div>
                        <span class="float-right"><a class="details" data-dismiss="modal" wire:click="initializeQuery">Regresar</a></span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-responsive-md table-hover  text-center">
                            <thead>
                                    <tr class="text-center">
                                        <th style="background-color:transparent;color:#1d3557 !important">Nombre</th>
                                @if($itemType=='productos')
                                        <th style="background-color:transparent;color:#1d3557 !important">Sku</th>
                                        <th style="background-color:transparent;color:#1d3557 !important">Existencias</th>
                                @elseif($itemType=='servicios')
                                        <th style="background-color:transparent;color:#1d3557 !important">Duración</th>
                                @endif
                                        <th style="background-color:transparent;color:#1d3557 !important">Puntos</th>
                                        <th style="background-color:transparent;color:#1d3557 !important">Precio</th>
                                    </tr>
                            </thead>
                            <tbody>
                                @if($items != null)
                                @foreach($items as $item)
                                    <tr class="text-center" style="cursor: pointer;" wire:click="$emit('addNewProduct','{{$item->id}}')" data-dismiss="modal" wire:click="initializeQuery">
                                        <td>{{$item->name}}</td>
                                        @if($itemType=='productos')
                                            <td>{{$item->sku}}</td>
                                            <td>{{$item->stock_qty}}</td>
                                        @elseif($itemType=='servicios')
                                            <td>{{$item->duration}} min.</td>
                                        @endif
                                        <td> {{ number_format($item->reward_points,2,'.',',') }} </td>
                                        <td> ${{ number_format($item->disccount_price > 0 ? $item->disccount_price : $item->gross_price,2,'.',',') }} </td>
                                    </tr>

                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function closeModalItems() {
    $('#modalItems').modal('hide')
}
</script>