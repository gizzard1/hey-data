<div>
    <div wire:ignore.self class="modal fade none-border" id="modalDetailTransaccion" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content" style="height: auto;max-height:35rem;overflow: auto;">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Transacción</h5>
                    <button type="button" class="close" data-dismiss="modal" wire:click="recuperarInfo"><span>x</span>
                    </button>
                </div>
                <div class="modal-body">
                @if($itemSelected!=null || count($itemSelected)>0)
                    @if($type=='apertura')
                        @include('livewire.informes.transacciones.apertura')
                        @if($itemSelected->caja_corte_id!=null)
                        <hr>
                            <span class="float-right"><a class="details" wire:click.defer="viewDetails({{ $itemSelected->caja_corte_id }},'cortes')">Ver corte</a></span>
                        @endif
                    @elseif($type =='corte')
                        @include('livewire.informes.transacciones.corte')
                    @elseif($type == 'venta')
                        @include('livewire.informes.transacciones.movimiento')
                    @elseif($type == 'cita')
                        @include('livewire.informes.transacciones.cita')
                    @elseif($type == 'uso')
                        @include('livewire.informes.transacciones.uso')
                    @elseif($type == 'entrada')
                        @include('livewire.informes.transacciones.entrada')
                    @endif    
                @endif    
                
            </div>
            </div>
        </div>
    </div>
    @include('livewire.informes.transacciones.editing')
</div>
<script>
function closeModalDetail() {
    $('#modalDetailTransaccion').modal('hide')
}
</script>