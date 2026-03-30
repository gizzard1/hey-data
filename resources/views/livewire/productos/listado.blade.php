
<div class="card-header">
    <div class="d-flex">
        <div class="separator"></div>
        <div class="mr-auto">
            <h4 class="card-title mb-1">Productos</h4>
            <p class="fs-14 mb-0"> Listado Registrado</p>
        </div>
    </div>
    
    @include('livewire.servicios.header.botones')
</div>

<div class="card-body table-prices">
    
    <div class="table-responsive" style="width:102%!important">
        <table class="table table-responsive-md table-hover  text-center" id="tblProducts">
            <thead class="thead-primary">
                <tr>
                    <th style="background-color:transparent;color:#9D1466 !important">
                        <input 
                            wire:click="toggleSelectAll($event.target.checked)" 
                            type="checkbox" 
                            id="select-all">
                    </th>
                    <th style="background-color:transparent;color:#9D1466 !important; width:3rem">Nombre</th>
                    @if($orderByMostOrLessSelled && ($orderByMostOrLessSelled==='asc' || $orderByMostOrLessSelled === 'desc'))
                        <th style="background-color:transparent;color:#9D1466 !important">Ventas</th>
                    @endif
                    <th style="background-color:transparent;color:#9D1466 !important">Precio público</th>
                    <th style="background-color:transparent;color:#9D1466 !important">Precio descuento</th>
                    <th style="background-color:transparent;color:#9D1466 !important">Costo</th>
                    <th style="background-color:transparent;color:#9D1466 !important">Stock</th>
                    <th style="background-color:transparent;color:#9D1466 !important" class="ult-ver">Stock mínimo</th>
                    <th style="background-color:transparent;color:#9D1466 !important">Categorías</th>
                    <th style="background-color:transparent;color:#9D1466 !important">Marca</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $item)
                <tr wire:click="toggleItem('{{ $item['id'] }}')">
                    <td>
                        <input 
                            type="checkbox" 
                            id="checkbox-{{ $item['id'] }}" 
                            value="{{ $item['id'] }}" 
                            @checked(in_array($item['id'], $selectedItems)) 
                            @click.stop>
                    </td>
                    <td class="text-left"><a wire:click.prevent="viewProduct({{ $item->id }})">{{$item->name}}</a>
                    </td>

                    @if(isset($item->asignaciones_sum_quantity))
                        <td>{{$item->asignaciones_sum_quantity }} </td>
                    @endif
                    <td>${{$item->gross_price }} </td>
                    <td>${{$item->disccount_price }} </td>
                    <td>${{$item->cost }} </td>
                    <td style="color:{{ $item->stock_qty<$item->min_stock ? 'red' : '' }}">{{$item->stock_qty }}</td>
                    <td class="ult-ver">{{$item->min_stock ? $item->min_stock : '-' }}</td>
                    <td>
                        <small> {{ $item->categorias->count() > 0 ? implode(", ", $item->categorias->pluck('name')->toArray()) : '-'}}</small>
                    </td>
                    <td>{{ $item->marca?->name }}</td>

                    <!-- <td>
                        <div class="dropdown position-static">
                            <button class="btn btn-info dropdown-toggle" style="background-color:#E2BBB4; border-color:#E2BBB4" type="button"
                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                Acciones
                            </button>
                            <div class="dropdown-menu dropdown-menu-right"
                                aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#"
                                    wire:click.prevent="viewProduct({{ $item->id }})"><i
                                        class="las la-eye la-2x"></i> Ver</a>
                                <a class="dropdown-item" href="#"
                                    wire:click.prevent="Edit({{ $item->id }})"><i
                                        class="las la-pen la-2x"></i> Editar</a>
                                        
                                <script>
                                    function confirmDelete(productId) {
                                        // Mostrar cuadro de diálogo de confirmación personalizado
                                        Swal.fire({
                                            title: '¿Seguro que desea eliminar este producto?',
                                            text: '',
                                            icon: 'warning',
                                            showCancelButton: true,
                                            confirmButtonColor: '#9A7D65',
                                            cancelButtonColor: '#962222',
                                            confirmButtonText: 'Aceptar',
                                            cancelButtonText: 'Cancelar',
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                showProcessing()
                                                // Si el usuario hace clic en "Aceptar", ejecutar el método de Livewire
                                                Livewire.emit('Delete', productId); // Llamar al método de Livewire
                                            }
                                        });
                                    }
                                </script>
                                <a class="dropdown-item" href="#"
                                    onclick="confirmDelete({{ $item->id }})"><i
                                        class="las la-trash-alt la-2x"></i> Eliminar</a>
                                <a class="dropdown-item" href="#"
                                    wire:click.prevent="Sync({{ $item->id }})"><i
                                        class="las la-sync la-2x"></i> Sincronizar</a>
                            </div>
                        </div>
                    </td> -->


                </tr>
                @empty
                <tr>
                    <td colspan="3">No hay productos</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

<div class="card-footer">
    <div class="row">
        <div>
            {{$productos->links()}}
        </div>
        <div class="m-auto">
            <span class="float-right">Productos encontrados: {{$records}}</span>
            @if(count($selectedItems)>0)
            <span class="float-right mr-2">Productos seleccionados: {{count($selectedItems)}}</span>
            @endif
        </div>
    </div>
</div>

<style>
.ts-control {
    padding: 0px !important;
    border-style: none;
    border-width: 0px !important;
    background-color: #0E0803
}
</style>