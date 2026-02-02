<div class="{{ isset($filtros) && count($filtros)>0 ? 'col-md-3' : 'd-none' }}" id="categoriesCard">
    @include('livewire.clientes.sidebar.filtros')
</div>


<div class=" {{ isset($filtros) && count($filtros)>0 ? 'col-md-9' : 'col-md-12' }}">
    <div class="card" style="height: auto">
        <div class="card-header">
            <div class="d-flex">
                <div class="separator"></div>
                <div class="mr-auto">
                    <h4 class="card-title mb-1">Clientes</h4>
                    <p class="fs-14 mb-0"> Listado Registrado</p>
                </div>
            </div>
            
            @include('livewire.clientes.header.botones')
        </div>
        
        <div class="card-body table-cust">
            <div class="table-responsive">
                <table class="table table-responsive-md table-hover  text-center">
                    <thead class="thead-primary">
                        <tr >
                            <th style="background-color:transparent;color:#9D1466 !important">
                                <input 
                                    wire:click="toggleSelectAll($event.target.checked)" 
                                    type="checkbox" 
                                    id="select-all">
                            </th>
                            <th style="background-color:transparent;color:#9D1466 !important">Nombre</th>
                            <th  class="ult-ver"style="background-color:transparent;color:#9D1466 !important">Email</th>
                            <th style="background-color:transparent;color:#9D1466 !important">Teléfono</th>
                            <th  class="ult-ver"style="background-color:transparent;color:#9D1466 !important">Añadido</th>
                            @if($by == 'visits')
                            <th  class="ult-ver"style="background-color:transparent;color:#9D1466 !important">Visitas</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody id="controls">
                        @forelse ($clientes as $item)
                        <tr wire:click="toggleItem('{{ $item['id'] }}')">
                            <td>
                                <input 
                                    type="checkbox" 
                                    id="checkbox-{{ $item['id'] }}" 
                                    value="{{ $item['id'] }}" 
                                    @checked(in_array($item['id'], $selectedItems)) 
                                    @click.stop>
                            </td>
                            <td class="text-left">
                                <a onclick="next()" wire:click.prevent="viewCust({{ $item->id ?? $item['id'] }})">
                                    {{ $item->first_name ?? $item['first_name'] }} {{ $item->last_name ?? $item['last_name'] }}
                                </a>
                            </td>
                            <td class="ult-ver"> <a style="color:#515457" href="mailto:{{ $item->email ?? $item['email'] }}">{{ $item->email ? $item['email'] : '-' }} </a></td>
                            <td> {{ $item->phone ? $item['phone'] : '-' }} </td>
                            <td class="ult-ver"> {{ $item->created_at ? $item['created_at'] : '-' }} </td>
                            @if($by == 'visits' || $item->citas_count || $item->compras_count)
                            <td class="ult-ver"> {{ ($item->citas_count ?? 0) + ($item->compras_count ?? 0) }} </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">No hay clientes</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            
            <div class="row">
                <div>
                    {{$clientes->links()}}
                </div>
                <div class="m-auto"><span class="float-right">Clientes encontrados: {{$records}}</span>
                    @if(count($selectedItems)>0)
                        <span class="float-right mr-2">Clientes seleccionados: {{count($selectedItems)}}</span>
                    @endif
                </div>
            </div>
        
        </div>
    </div>
    @include('livewire.clientes.modals.activateCard')
    @include('livewire.clientes.modals.mergeCust')
</div>

<div class="col-md-3" style="display:none" id="categoriesCardAux">
    @include('livewire.clientes.sidebar.filtros')
</div>


<style>
    .dropdown-item{
        color:black !important;
    }
    .swal2-html-container{
        color:#515457 !important;
    }
</style>


<script>
    function confirmDelete() {
        // Mostrar cuadro de diálogo de confirmación personalizado
        Swal.fire({
            title: '¿Seguro que desea eliminar al cliente?',
            text: 'Tome en cuenta que los datos del cliente no pueden ser recuperados, pero las visitas y compras del cliente se mantendrán almacenadas',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                showProcessing()
                // Si el usuario hace clic en "Aceptar", ejecutar el método de Livewire
                Livewire.emit('eliminar'); // Llamar al método de Livewire
            }
        });
    }
</script>