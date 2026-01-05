
<div>
    <div class="row" style="justify-content: center;">
        
        <div class="col-sm-12 col-md-12"> 
            <div>
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex">
                            <div class="separator"></div>
                            <div class="mr-auto">
                                <h4 class="card-title mb-1">Historial de uso</h4>
                                <p class="fs-14 mb-0"> Listado Registrado</p>
                            </div>
                        </div>
                        
                        <div>
                            <button wire:click="$emit('changeWindow','3')" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Añadir uso</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-responsive-md table-hover text-center">
                                <thead class="thead-primary">
                                    <tr>
                                        <th style="background-color:transparent;color:#9D1466 !important">Producto</th>
                                        <th style="background-color:transparent;color:#9D1466 !important">Empleado</th>
                                        <th style="background-color:transparent;color:#9D1466 !important">Cliente</th>
                                        <th style="background-color:transparent;color:#9D1466 !important">Añadido</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($usos as $item)
                                    <tr>
                                        <td class="text-left">
                                            <a wire:click="viewDetails('{{ $item }}')">
                                                @foreach ($item as $uso)
                                                    {{ $uso->producto->name }}@if (! $loop->last) | @endif
                                                @endforeach
                                            </a>
                                        </td>
                                        <td> <a style="color:#515457">{{ $item[0]->empleado ? $item[0]->empleado->first_name . ' ' . $item[0]->empleado->last_name : '-' }}</a></td>
                                        <td> {{ $item[0]->customer ? $item[0]->customer->first_name . ' ' . $item[0]->customer->last_name : '-' }}</td>
                                        <td> {{ $item[0]->created_at }} </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5">No hay usos</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="m-auto">
                                <span class="float-right">Usos encontrados: {{count($usos)}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($itemSelected)
        @include('livewire.uso.modal-detail')
    @endif
</div>
                    
<script>
    
window.addEventListener('viewDetailTransaccion', event => {   
    $('#modalDetailTransaccion').modal('show')
})
</script>