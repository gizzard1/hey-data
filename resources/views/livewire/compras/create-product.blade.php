<div wire:ignore.self class="modal fade none-border" id="modalCreateForm" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="height: 35rem; overflow: auto;">
            <div class="modal-header" style="background-color: white;">
                <h4>Producto Nuevo</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>x</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" id="product-name">
                                <label>Nombre</label>
                                <input wire:model.defer="product.name" type="text"
                                    class="form-control" placeholder="Nombre">
                                @error('product.name') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Sku</label>
                                <input wire:model.defer="product.sku" type="text" class="form-control"
                                    placeholder="Sku">
                                @error('product.sku') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Proveedor</label>
                                <select wire:model.defer='product.brand_id' class="form-control">
                                    <option value="">Seleccione una opción</option>
                                    @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}">{{ $marca->name }}</option>
                                    @endforeach
                                </select>
                                @error('product.brand_id') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            <div class="form-group" id="discount-product">
                                <label>Porcentaje de descuento</label>
                                <input wire:model="percent" type="number" class="form-control"
                                    placeholder="0.00%">
                            </div>
                            <div class="showmemore" style="display: none;" wire:ignore>
                                <div class="form-group" wire:ignore>
                                    <label>Categoría(s)</label>
                                <input wire:model="categoriesList" type="text" placeholder="Buscar categoría" autocomplete="off" id="tomCategoryP" class="form-control">

                                </div>
                                <div class="form-group">
                                    <label>Stock Mínimo</label>
                                    <input wire:model.defer="product.min_stock" type="number"
                                        class="form-control" placeholder="0">
                                    @error('product.min_stock') <span class="text-danger">*Corrige este campo* </span> @enderror
                                </div>
                                
                                @if(Auth::user()->role=='admin')
                                    <div class="form-group">
                                        <label>Existencias</label>
                                        <input wire:model.defer="product.stock_qty" type="number"
                                            class="form-control" placeholder="0">
                                        @error('product.stock_qty') <span class="text-danger">*Corrige este campo* </span> @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo de Unidad</label>
                                <select wire:model.defer='product.unit_type' class="form-control ">
                                    <option value="Unidad">Unidad</option>
                                    <option value="Mililitro">Mililitro</option>
                                    <option value="Ampolleta">Ampolleta</option>
                                    <option value="Artículo">Artículo</option>
                                    <option value="Onza">Onza</option>
                                    <option value="Gramo">Gramo</option>
                                    <option value="Envase">Envase</option>
                                </select>
                                @error('product.unit_type') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            <div class="form-group" id="product-price">
                                <label>Precio público</label>
                                <input wire:model.defer="product.gross_price" type="number" class="form-control"
                                    placeholder="0.00">
                                @error('product.gross_price') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Tipo</label>
                                <select wire:model.defer='product.type_product' class="form-control ">
                                    <option value="simple">Mercancía</option>
                                    <option value="variable">Uso</option>
                                </select>
                                @error('product.type_product') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            <div class="form-group" id="disccount-price">
                                <label>Precio descuento</label>
                                <input type="number" class="form-control" wire:model="product.disccount_price" placeholder="Precio descuento">
                            </div>
                            <div class="showmemore" style="display: none;" wire:ignore>
                                <div class="form-group">
                                    <label>Precio compra</label>
                                    <input wire:model.defer="product.cost" type="number" class="form-control"
                                        placeholder="0.00">
                                    @error('product.cost') <span class="text-danger">*Corrige este campo* </span> @enderror
                                </div>

                            <div class="form-group">
                                <label for="iva">IVA</label>
                                <select wire:model="product.iva" type="text" class="form-control" placeholder="IVA">
                                    <option value="0.16">16%</option>
                                    <option value="0.08">8%</option>
                                    <option value="0">Exento</option>
                                </select>
                                @error('product.iva') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            </div>
                        </div>
                        
                       <div class="col-md-12">
                            <div class="showmemore" style="display: none;" wire:ignore>
                                <div class="form-group">
                                    <label for="iva">Descripción</label>
                                    <textarea id="descripciónInput" wire:model.prevent="product.description" type="text" class="form-control" placeholder="Escribe una descripción (opcional)..." maxlength="200" style="resize: none;"></textarea>
                                    @error('product.description') <span class="text-danger">*Corrige este campo* </span> @enderror
                                </div>
                                
                                @if($pictures != null)
                                    <div class="row">
                                    @foreach ($pictures as $photo)
                                        <div class="col-6 col-sm-4">
                                            <img src="{{ $photo }}" class="img-fluid rounded" alt="Archivo pdf">
                                            <button type="button" class="btn btn-danger btn-sm float-right"  wire:click="removeFile('{{ $photo }}',0)">x</button>
                                            <button type="button" onclick="openPath('{{ asset($photo) }}')" class="btn btn-secondary btn-sm float-right"><i class="las la-eye"></i></button>
                                        </div>
                                    @endforeach
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label>Subir fotos</label>
                                    <input style="background-color: transparent  !important;border-color:transparent !important;" multiple type="file" class="form-control" wire:model="gallery" accept="image/x-png,image/jpeg">
                                    @error('gallery.*')
                                    <span style="color: red;">*Corrige este campo* </span>
                                    @enderror
                                </div>
                            </div>
                           
                            <div id="showmemorebutton" wire:ignore>
                                <a onclick="showmemore()">Ver más</a>
                            </div>
                           
                           
                           <div class="mt-5 d-flex " style="column-gap:1rem;justify-content:end">
                               <button id="cancel-editing" class="btn btn-sm float-left" data-dismiss="modal">Cancel</button>
                               <button id="save-button" onclick="next()" class="btn btn-sm btn-info float-right save" wire:click.prevent="Store">Guardar</button>
                           </div>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
   .ts-control  > input{
       color:black !important
   }
   .item {
       background-color: #278d46  !important;
   }
   
   .form-control {
        height: auto;
    }
</style>

<script>
    function showmemore()
    {
        var area = document.getElementsByClassName('showmemore');
        var button = document.getElementById('showmemorebutton');
        button.style.display = 'none';

        Array.from(area).forEach(element => {
            element.style.display = 'block';
        });
    }
</script>