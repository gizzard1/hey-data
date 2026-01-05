<div wire:ignore.self id="modalActivateCard" class="modal fade" role="dialog">
<div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">{{isset($customer_card->tarjetaPuntos) ? 'Modificar' : 'Activar'}} tarjeta de puntos</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <img src="{{ asset('storage/TP.gif') }}" class="rounded" style="width:12rem;margin:auto;display:block;padding:1rem">
                            <input wire:keydown.enter="StoreCard" id='inputFocus' wire:model.defer="barcode" type="text" placeholder="Deslice o escanee la tarjeta..." class="form-control form-control-lg">
                            @error('barcode') <span class="text-danger">*Es posible que estés duplicando la tarjeta* </span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm" data-dismiss="modal">Cerrar</button>
                <button wire:click="StoreCard" class="btn btn-sm excel-button w-auto" style="background-color: #9E846D;color:white">Guardar sin registrar tarjeta</button>
                <button wire:click="StoreCard" class="btn btn-sm save w-auto" style="background-color: #9E846D;color:white">Guardar tarjeta</button>
                @if(isset($customer_card->tarjetaPuntos))
                    <button class="btn-sm btn-danger w-auto" onclick="confirmDeleteCard()">Eliminar</button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDeleteCard() {
        // Mostrar cuadro de diálogo de confirmación personalizado
        Swal.fire({
            title: '¿Seguro que desea cancelar este programa de recompensas?',
            text: 'Los puntos acumulados se perderán',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                showProcessing()
                // Si el usuario hace clic en "Aceptar", ejecutar el método de Livewire
                Livewire.emit('deleteCard'); // Llamar al método de Livewire
            }
        });
    }
</script>