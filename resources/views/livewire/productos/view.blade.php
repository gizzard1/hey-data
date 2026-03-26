@if($productSelected !=null)
<div id="modalViewProduct" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Información del producto</h4>
            </div>
            <div class="modal-body">

                <div class="col">
                    <div >
                        <!-- Mostrar la información del producto -->
                        <h3 class="text-center">{{ $productSelected->name }}</h3>
                        <p class="text-center" style="word-wrap: break-word;">{{ $productSelected->description ?? 'Sin descripción' }}</p>
                        @if(count($productSelected->files) > 0)
                            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                                <ol class="carousel-indicators">
                                    @foreach($productSelected->photos as $index => $file)
                                        <li data-target="#carouselExampleIndicators" data-slide-to="{{ $index }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                                    @endforeach
                                </ol>
                                <div class="carousel-inner">
                                    @foreach($productSelected->photos as $index => $file)
                                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                            <img class="d-block w-100" style="max-height: 14rem;" src="{{ asset($file) }}" alt="Slide {{ $index + 1 }}">
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
                        <p><strong>SKU:</strong> {{ $productSelected->sku }}</p>
                        <p><strong>Existencias:</strong> {{ $productSelected->stock_qty }}</p>
                        <p><strong>Precio bruto:</strong> ${{ $productSelected->gross_price }}</p>
                        <p><strong>Precio descuento:</strong> ${{ $productSelected->disccount_price }}</p>
                        <p><strong>Precio compra:</strong> ${{ $productSelected->cost }}</p>
                        <p><strong>Tipo:</strong> {{ ucfirst($productSelected->type_product) }}</p>
                        <p><strong>Marca:</strong> {{ $productSelected->marca ? $productSelected->marca->name : 'Sin marca' }}</p>
                        <p><strong>Stock mínimo:</strong> {{ $productSelected->min_stock }}</p>
                        <p><strong>Categoría(s):</strong> {{implode(", ", $productSelected->categorias->pluck('name')->toArray())}}</p>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-sm" data-dismiss="modal" wire:click.prevent="Edit('{{ $productSelected->id }}')" data-toggle="modal" data-target="#modalCreateForm">Editar</button>
            </div>
        </div>
    </div>
</div>
@endif