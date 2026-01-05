<div wire:ignore.self class="modal fade" id="serviciosFormModalLong" data-keyboard="false">
   <div class="modal-dialog modal-dialog-centered modal-lg">
       <div class="modal-content" style="height: 35rem; overflow: auto;">
           <div class="modal-header" style="background-color: #E2BBB4; color: white;">
               <h4 style="color: white;">Datos del Servicio</h4>
               <button type="button" class="close" data-dismiss="modal">
                   <span style="color: white;">x</span>
               </button>
           </div>
           <div class="modal-body">
               <div class="container" style="margin-top:20px">
                   <div class="row">
                       <div class="col-md-6">
                           <div class="form-group">
                               <label>Nombre</label>
                               <input wire:model="service.name" type="text" class="form-control" placeholder="Nombre" autocomplete="nope">
                               @error('service.name') <span class="text-danger">*Corrige este campo* </span> @enderror
                           </div>
                           <div class="form-group" id="duration">
                               <label>Duración</label>
                               <select style="background-color: transparent" wire:model.defer='service.duration' class="form-control ">
                                   <option value="15">15 minutos</option>
                                   <option value="30">30 minutos</option>
                                   <option value="45">45 minutos</option>
                                   <option value="60">1:00 hora</option>
                                   <option value="75">1:15 hora</option>
                                   <option value="90">1:30 horas</option>
                                   <option value="105">1:45 horas</option>
                                   <option value="120">2:00 horas</option>
                                   <option value="135">2:15 horas</option>
                                   <option value="150">2:30 horas</option>
                                   <option value="165">2:45 horas</option>
                                   <option value="180">3:00 horas</option>
                                   <option value="195">3:15 horas</option>
                                   <option value="210">3:30 horas</option>
                                   <option value="225">3:45 horas</option>
                                   <option value="240">4:00 horas</option>
                               </select>
                               @error('service.duration') <span class="text-danger">*Corrige este campo* </span> @enderror
                           </div>
                           <div id="input-disccount" class="form-group">
                               <label>Porcentaje de descuento</label>
                               <input style="background-color: transparent" wire:model.debounce.750ms="percent" type="number" class="form-control" placeholder="0.00%">
                           </div>
                           <div class="form-group" wire:ignore>
                               <label>Categorías</label>
                               <input wire:model="categoriesList" type="text" placeholder="Buscar categoría" autocomplete="off" id="tomCategoryS" class="form-control">
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-group">
                               <label>Precio público</label>
                               <input style="background-color: transparent" wire:model.defer="service.gross_price" type="number" class="form-control" placeholder="$0.00">
                               @error('service.gross_price') <span class="text-danger">*Corrige este campo* </span> @enderror
                           </div>
                           <div class="form-group">
                               <label>Puntos Generados</label>
                               <input wire:model.defer="service.reward_points" type="number" class="form-control" placeholder="0.00">
                               @error('service.reward_points') <span class="text-danger">*Corrige este campo* </span> @enderror
                           </div>
                           <div id="disccount-price" class="form-group">
                               <label>Precio descuento</label>
                               <input type="number" class="form-control" wire:model="service.disccount_price" placeholder="Precio descuento">
                               <button id="cancelDisccount" onclick="next()" type="button" class="close" wire:click="calculate">×</button>
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
                       </div>
                       <div class="col-md-12">
                           <div class="form-group">
                               <label for="iva">Descripción</label>
                               <textarea id="descripciónInput" wire:model.prevent="description" type="text" class="form-control" placeholder="Escribe una descripción (opcional)..." maxlength="200" style="resize: none;"></textarea>
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
                           
                           <div class="mt-5 d-flex " style="column-gap:1rem;justify-content:end">
                               <button id="cancel-editing" class="btn btn-sm btn-dark float-left" wire:click.prevent="cancelEdit" data-dismiss="modal">Cancel</button>
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
</style>