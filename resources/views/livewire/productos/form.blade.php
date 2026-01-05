<div id="cardForm" style="background-color: white; display: {{ $action !=1 ? 'block' : 'none' }}">
    <div class="row">
        <div class="col-sm-12">
            <div class="card" id="form-product">
                <div class="card-header ">
                    <h4 class="card-title mb-1">{{ $action == 2 ? 'Crear Producto' : 'Editar Producto' }}</h4>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-sm-12 col-md-4">
                            
                            <input style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"  wire:model.defer="product.name" type="text"
                                class="form-control form-control" placeholder="Nombre">
                            @error('product.name') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Nombre</label>
                        
                        </div>
                        <div class="col-sm-12 col-md-4">
                            
                            <input style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"  wire:model.defer="product.sku" type="text" class="form-control form-control"
                                placeholder="Sku">
                            @error('product.sku') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Sku</label>
                        
                        </div>
                        <div class="col-sm-12 col-md-4">
                            
                            <input style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"  wire:model.defer="product.intern_sku" type="text" class="form-control form-control"
                                placeholder="Sku interno">
                            @error('product.intern_sku') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Sku interno</label>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-12 col-md-4">
                            <input style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"  wire:model.defer="product.description" type="text"
                                class="form-control form-control" placeholder="Descripción">
                            @error('product.description') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Descripción</label>
                        </div>
                        <div class="col-sm-12 col-md-4" wire:ignore style="background-color:white;" id="optional-step">
                            <input style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"   wire:model="categoriesList" type="text" class="form-control form-control" 
                                placeholder="Buscar categoría" autocomplete="off" id="tomCategory" style="background-color:white;">
                            <label>Categoría(s)</label>
                        </div>
                        <div class="col-sm-12 col-md-4" id="optional-step">
                            <select style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;" wire:model.defer='product.brand_id' class="form-control  form-control">
                                @foreach($marcas as $marca)
                                <option value="{{ $marca->id }}">{{ $marca->name }}</option>
                                @endforeach
                            </select>
                            @error('product.brand_id') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Proveedor</label>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-12 col-md-4">
                            <select style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;" wire:model.defer='product.type_product' class="form-control  form-control">
                                <option value="simple">Mercancía</option>
                                <option value="variable">Uso</option>
                            </select>
                            @error('product.type_product') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Tipo</label>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <select style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;" wire:model.defer='product.status' class="form-control  form-control">
                                <option value="publish">Publicado</option>
                                <option value="pending">Pendiente</option>
                                <option value="draft">Basura</option>
                            </select>
                            @error('product.status') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Estatus tienda en línea</label>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <select style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;" wire:model.defer='product.visibility' class="form-control  form-control">
                                <option value="visible">Visible</option>
                                <option value="hide">Oculto</option>
                            </select>
                            @error('product.visibility') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Visibilidad</label>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-12 col-md-4">
                            <input style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"  wire:model.defer="product.gross_price" type="number" class="form-control form-control"
                                placeholder="0.00">
                            @error('product.gross_price') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Precio público</label>
                        </div>
                        <div class="col-sm-12 col-md-2" id="input-disccount" class="step-input">
                            <input style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"  wire:model="percent" type="number" class="form-control form-control"
                                placeholder="0.00%">
                            <label>Porcentaje de descuento</label>
                        </div>
                        <div class="col-sm-12 col-md-2" id="disccount-price">
                            <input style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"  type="number" class="form-control form-control"
                            value="{{ $finalD ? number_format($finalD,2,'.',',') : number_format($this->product->disccount_price,2,'.',',') }}"" placeholder="$0.00">
                            <label>Precio descuento</label><button onclick="next()" id="cancelDisccount" type="button" class="close" wire:click="calculateP">×</button>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <select style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;" wire:model.defer='product.unit_type' class="form-control  form-control">
                                <option value="Unidad">Unidad</option>
                                <option value="Mililitro">Mililitro</option>
                                <option value="Ampolleta">Ampolleta</option>
                                <option value="Artículo">Artículo</option>
                                <option value="Onza">Onza</option>
                                <option value="Gramo">Gramo</option>
                                <option value="Envase">Envase</option>
                            </select>
                            @error('product.unit_type') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Tipo de Unidad</label>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <input style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"  wire:model.defer="product.cost" type="number" class="form-control form-control"
                                placeholder="0.00">
                            @error('product.cost') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Precio compra</label>
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <input style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"  wire:model.defer="product.reward_points" type="number" class="form-control form-control"
                                placeholder="0.00">
                            @error('product.reward_points') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Puntos Generados</label>
                        </div>
                        @if(Auth::user()->role=='admin')
                        <div class="col-sm-12 col-md-2" id="stock_qty">
                            <input style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"  wire:model.defer="product.stock_qty" type="number"
                                class="form-control form-control" placeholder="0">
                            @error('product.stock_qty') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Existencias</label>
                        </div>
                        @endif
                        <div class="col-sm-12 col-md-2">
                            <input style="background-color: transparent  !important;border-color:transparent !important;border-bottom:1px solid black !important;"  wire:model.defer="product.min_stock" type="number"
                                class="form-control form-control" placeholder="0">
                            @error('product.min_stock') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Stock Mínimo</label>
                        </div>
                    </div>


                    <div class="row mt-2">
                        <div class="col-sm-12 col-md-6 mb-3 mt-3">
                            <input style="background-color: transparent  !important;border-color:transparent !important;"  type="file" class="form-control" wire:model="gallery" accept="image/x-png,image/jpeg"" multiple id="formFileMultiple">
                            @error('gallery.*')
                            <span style="color: red;">*Corrige este campo* </span>
                            @enderror
                        </div>
                        
                    </div>

                    <div class="mt-2">
                        <div wire:loading wire:target="gallery">Cargando imágenes...</div>
                        @if (!empty($gallery))
                        <div class="row">
                            @foreach ($gallery as $photo)
                            <div class="col-6 col-sm-4 col-md-3 col-lg-2 mb-3">
                                <div class="media">
                                    <img src="{{ $photo->temporaryUrl() }}" class="img-fluid rounded" alt="img">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>


                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-dark float-left" wire:click.prevent="cancelEdit" id="cancel-editing">Cancel</button>
                    <button class="btn btn-sm btn-info float-right save" wire:click.prevent="Store" style="background-color:#B59377; border-color:#B59377" id="save-button" onclick="next()">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .ts-wrapper{
            background-color: transparent  !important;
            border-color:transparent !important;
            border-bottom:1px solid black !important;
        }
        /* estilos tom select */
        .ts-control {
            padding: 0px !important;
            border-style: none;
            border-width: 0px !important;
            background: white !important;
            font-size: 18px;
            cursor: text !important;
            height: 26px;
            color: #6C757D;
        }

        .ts-control input  {
            color: #6E6E6E !important;
            font-size: 1rem !important;
            cursor: text !important;
        }

        .ts-wrapper.multi .ts-control>div {
            font-size: 1rem !important;
            color: white !important;
            background-color: #6E6E6E;
        }

        .search-area .input -group-append .input -group-text i {
            font-size: 16px !important;
            background-color: #6E6E6E;
        }
        
        .ts-wrapper.multi .ts-control > div {
            cursor: none !important;
            background: #6E6E6E !important;
        }
        input [type=number]::-webkit-inner-spin-button, 
        input [type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
        }
    
    </style>
</div>
