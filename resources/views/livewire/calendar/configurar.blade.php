<div class="modal fade none-border" id="modalEmpl" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Disfruta tu experiencia</h5>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <p>
                        Agradecemos tu preferencia. Nos gustaría que aceptes un recorrido por la pataforma para que conozcas esta aplicación.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-sm" style="background-color: transparent;" wire:click="empezarRecorrido(false)">Lo haré después</button>
                <button type="button" data-dismiss="modal" class="btn btn-sm save" style="color:white"wire:click="empezarRecorrido(true)" class="btn btn-info ml-5">
                    ¡Hagámoslo!
                </button>
            </div>
        </div>
    </div>
</div>