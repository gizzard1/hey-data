@if(isset($cupon))
<div id="modalCuponesForm" class="modal fade" role="dialog">
<div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Crear Tarjeta de Regalo
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input wire:model.defer="cupon.password" type="text" class="form-control">
                            <label>Código secreto</label>
                            @error('password') <span class="text-danger">*Este campo es obligatorio </span> @enderror
                        </div>
                        
                        <div class="form-group" style="column-gap:1rem">
                            <input wire:model.defer="cupon.value_amount" type="number" placeholder="$0.00" class="form-control">
                            <label>Valor</label>
                            <svg style="margin-bottom:8px" data-toggle="popover" data-trigger="hover" data-content="Una vez que la tarjeta de regalo se agrega al carrito ya no se puede editar este valor." xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="12" height="12" stroke-width="2">
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                <path d="M12 9h.01"></path>
                                <path d="M11 12h1v4h1"></path>
                            </svg>
                            @error('value_amount') <span class="text-danger">*Este campo es obligatorio </span> @enderror
                        </div>
                        
                        <div class="form-check" id="form-check-expdate">
                            <input {{ $cupon->expires_at ? 'checked' : '' }} class="form-check-input" type="checkbox" id="expDate" onclick="changeExpirationDate(this)">
                            <label class="form-check-label" for="expDate">Tiene fecha de expiración</label>
                        </div>

                        <div hidden class="form-group mt-3" id="expirationDate">
                            <input wire:model.defer="cupon.expires_at" type="date" class="form-control" min="{{ \Carbon\Carbon::today()->toDateString() }}">
                            <label>Fecha de expiración</label>
                            @error('cupon.expires_at') <span class="text-danger">*Este campo es obligatorio </span> @enderror
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" wire:model.defer="cupon.acumulable" type="checkbox" id="acumulable">
                            <label class="form-check-label" for="acumulable">Se puede acumular</label>
                            <svg style="margin-bottom:10px" data-toggle="popover" data-trigger="hover" data-content="Marca la casilla si deseas que esta tarjeta de regalo sea acumulable con otras." xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="12" height="12" stroke-width="2">
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                <path d="M12 9h.01"></path>
                                <path d="M11 12h1v4h1"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-dismiss="modal">Cancelar</button>
                <button class="btn-sm btn float-right save" wire:click.prevent="addCupon('{{ $cupon->asignacion_venta_id }}')" style="color: white;">Agregar al carrito</button>
                <button wire:click.prevent="removeGC('{{ $cupon->asignacion_venta_id }}')" class="btn btn-sm"><i class="fa fa-trash fa-lg"></i></button>
            </div>
        </div>
    </div>
</div>
@endif