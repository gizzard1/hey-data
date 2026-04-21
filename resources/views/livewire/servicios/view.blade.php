@if($serviceSelected !=null)
<div id="modalViewService" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Información del servicio</h4>
            </div>
            <div class="modal-body">
                @if($serviceSelected !=null)

                <div class="row">
                    <div class="col">
                        <!-- Mostrar la información del servicio -->
                        <h3 class="text-center">{{ $serviceSelected->name }}</h3>
                        <p class="text-center">{{ $serviceSelected->description ?? 'Sin descripción' }}</p>
                        @if(count($serviceSelected->files) > 0)
                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    @foreach($serviceSelected->photos as $index => $file)
                                        <li data-target="#carouselExampleIndicators" data-slide-to="{{ $index }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                                    @endforeach
                                </ol>
                                <div class="carousel-inner">
                                    @foreach($serviceSelected->photos as $index => $file)
                                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                            <img class="d-block w-100" style="max-height: 14rem;object-fit: contain;" src="{{ asset($file) }}" alt="Slide {{ $index + 1 }}">
                                        </div>
                                    @endforeach
                                </div>
                                <a class="carousel-control-prev" href="#carouselExampleIndicators" data-slide="prev">
                                    <span class="carousel-control-prev-icon"></span> 
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#carouselExampleIndicators" data-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        @endif


                        <p><strong>Duración:</strong> {{ ucfirst($serviceSelected->duration) }} minutos</p>
                        <p><strong>Precio bruto:</strong> ${{ $serviceSelected->gross_price }}</p>
                        <p><strong>Precio descuento:</strong> ${{ $serviceSelected->disccount_price }}</p>
                        <p><strong>IVA:</strong> {{ floatval($serviceSelected->iva ?? 0)*100 }}% </p>
                        <p><strong>Categoría:</strong> {{implode(", ", $serviceSelected->categorias->pluck('name')->toArray())}}</p>
                        <p><strong>Proveedor:</strong> {{ $serviceSelected->marca ? $serviceSelected->marca->name : 'Sin proveedor' }}</p>
                    </div>
                </div>

                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-sm" data-dismiss="modal" wire:click.prevent="Edit('{{ $serviceSelected->id }}')" data-toggle="modal" data-target="#modalCreateForm">Editar</button>
            </div>
        </div>
    </div>
</div>
@endif