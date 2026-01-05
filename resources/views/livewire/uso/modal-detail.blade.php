<div>
    <div wire:ignore.self class="modal fade none-border" id="modalDetailTransaccion" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content" style="height: auto;max-height:35rem;overflow: auto;">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Transacción</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>x</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('livewire.informes.transacciones.uso')
            </div>
            </div>
        </div>
    </div>
</div>
<script>
function closeModalDetail() {
    $('#modalDetailTransaccion').modal('hide')
}
</script>