<div wire:ignore.self class="modal fade" id="modalCreateForm" data-keyboard="false">
   <div class="modal-dialog modal-dialog-centered modal-lg">
       <div class="modal-content" style="height: 35rem; overflow: auto;">
           <div class="modal-header">
               <h4>Datos del Servicio</h4>
               <button type="button" class="close" data-dismiss="modal">
                   <span>x</span>
               </button>
           </div>
           <div class="modal-body">
               <div class="container" style="margin-top:20px">
                   <div class="row">
                       <div class="col-md-6">
                            <div class="form-group" id="service-name">
                                <label>Nombre</label>
                                <input wire:model="service.name" type="text" class="form-control" placeholder="Nombre" autocomplete="nope">
                                @error('service.name') <span class="text-danger">*Corrige este campo* ({{ $message }}) </span> @enderror
                            </div>
                            <div class="form-group" id="duration">
                                <label>Duración</label>
                                <div class="d-flex">
                                        <input class="form-control" type="number" min="5" step="5" wire:model.defer='service.duration'>
                                        <label style="
                                            align-content: flex-end;
                                            margin-left: 10px;">min.</label>
                                </div>
                                
                                @error('service.duration') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                            <div id="input-disccount" class="form-group">
                                <label>Porcentaje de descuento</label>
                                <input min="0" style="background-color: transparent" wire:model.debounce.750ms="percent" type="number" class="form-control" placeholder="0.00%">
                            </div>
                            <div class="form-group" wire:ignore>
                                <label>Categorías</label>
                                <input wire:model="categoriesList" type="text" placeholder="Buscar categoría" autocomplete="off" id="tomCategoryS" class="form-control">
                            </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-group" id="service-price">
                               <label>Precio público</label>
                               <input style="background-color: transparent" wire:model.defer="service.gross_price"min="0"  type="number" class="form-control" placeholder="$0.00">
                               @error('service.gross_price') <span class="text-danger">*Corrige este campo* </span> @enderror
                           </div>
                           <div id="disccount-price" class="form-group">
                               <label>Precio descuento</label>
                               <input type="number"min="0"  class="form-control" wire:model="service.disccount_price" placeholder="Precio descuento">
                           </div>
                           <div class="form-group">
                               <label for="iva">IVA</label>
                               <select wire:model="service.iva" type="text" class="form-control" placeholder="IVA">
                                   <option value="0.16">16%</option>
                                   <option value="0.08">8%</option>
                                   <option value="0">Exento</option>
                               </select>
                               @error('service.iva') <span class="text-danger">*Corrige este campo* </span> @enderror
                           </div>
                            <div class="form-group">
                                <label>Proveedor</label>
                                <select wire:model.defer='service.brand_id' class="form-control">
                                    <option value="">Seleccione una opción</option>
                                    @foreach($marcas as $marca)
                                    <option value="{{ $marca->id }}">{{ $marca->name }}</option>
                                    @endforeach
                                </select>
                                @error('service.brand_id') <span class="text-danger">*Corrige este campo* </span> @enderror
                            </div>
                       </div>
                       <div class="col-md-12">
                            <div class="showmemore" style="display: none;" wire:ignore>
                                
                                <div class="form-group">
                                    <label for="iva">Descripción</label>
                                    <textarea id="descripciónInput" wire:model.prevent="service.description" type="text" class="form-control" placeholder="Escribe una descripción (opcional)..." maxlength="200" style="resize: none;"></textarea>
                                    @error('service.description') <span class="text-danger">*Corrige este campo* </span> @enderror
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
                               <button id="cancel-editing" class="btn btn-sm float-left" wire:click.prevent="cancelEdit" data-dismiss="modal">Cancel</button>
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
       background-color: #9D1466 !important;
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