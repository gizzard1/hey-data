@if(Auth::user()->role !== 'estilista')

<div>
    <div class="row" id="cardTable">
        <div class="col-md-3" style="height: fit-content;" id="categoriesCard">
            @include('livewire.servicios.sidebar.categorias')  
        </div>
        <div class="col-sm-9">
            <div class="card h-auto">
                <div class="card-header">
                    <div class="d-flex">
                        <div class="separator" style="background-color:#6E6E6E"></div>
                        <div class="mr-auto">
                            <h4 class="card-title mb-1">Servicios</h4>
                            <p class="fs-14 mb-0"> Listado Registrado</p>
                        </div>
                    </div>
                    
                    @include('livewire.servicios.header.botones')
                </div>
                <div class="card-body table-cust">
                    <div class="table-responsive">
                        <table class="table table-responsive-md table-hover text-center" id="tblProducts">
                            <thead class="thead-primary">
                                <tr>
                                    <th style="background-color:transparent;color:#9D1466 !important">
                                        <input 
                                            wire:click="toggleSelectAll($event.target.checked)" 
                                            type="checkbox" 
                                            id="select-all">
                                    </th>
                                    <th class="text-left" style="background-color:transparent;color:#9D1466 !important">Nombre</th>
                                    @if($orderByMostOrLessSelled)
                                        <th style="background-color:transparent;color:#9D1466 !important">Ventas</th>
                                    @endif
                                    <th style="background-color:transparent;color:#9D1466 !important">Precio venta</th>
                                    <th style="background-color:transparent;color:#9D1466 !important">Precio descuento</th>
                                    <th style="background-color:transparent;color:#9D1466 !important" class="ult-ver">Categorías</th>
                                    <th style="background-color:transparent;color:#9D1466 !important">Duración</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($servicios as $item)
                                <tr wire:click="toggleItem('{{ $item['id'] }}')">
                                    <td>
                                        <input 
                                            type="checkbox" 
                                            id="checkbox-{{ $item['id'] }}" 
                                            value="{{ $item['id'] }}" 
                                            @checked(in_array($item['id'], $selectedItems)) 
                                            @click.stop>
                                    </td>
                                    <td class="text-left"><a wire:click.prevent="viewService({{ $item->id }})">{{$item->name}}</a>
                                    </td>
                                    @if(isset($item->asignaciones_sum_quantity))
                                    <td>{{$item->asignaciones_sum_quantity }} </td>
                                    @endif
                                    <td>${{$item->gross_price }} </td>
                                    <td>${{$item->disccount_price }} </td>
                                    <td class="ult-ver">
                                        <small> {{$item->categorias->count() ? implode(", ", $item->categorias->pluck('name')->toArray()) : '-'}}</small>
                                    </td>
                                    <td>{{ $item->duration ? $item->duration . ' min.' : 'Sin duración' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3">No hay servicios</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            {{$servicios->links()}}
                        </div>
                        <div class="col-md-6 m-auto">
                            <span class="float-right">Servicios encontrados: {{$records}}</span>
                            @if(count($selectedItems)>0)
                            <span class="float-right mr-2">Servicios seleccionados: {{count($selectedItems)}}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3" style="height: fit-content;display:none" id="categoriesCardAux">
            @include('livewire.servicios.sidebar.categorias')  
        </div>
    </div>


    
    {{-- card form --}}
    @include('livewire.servicios.modals.form')
    @include('livewire.servicios.modals.changeReward')
    @include('livewire.servicios.view')
    @include('livewire.clientes.categories')
    <livewire:categoria-servicios :configuration="0"/>
    
    @push('my-scripts')
    @include('livewire.servicios.js')
    @endpush

    <style>
        .ts-control {
            padding: 0px !important;
            border-style: none;
            border-width: 0px !important;
            background-color: #0E0803
        }
    </style>
</div>


@else
@include('livewire.sinPermisos')
@endif