<div class="modal fade none-border" style="width: 30rem; margin-left:30%;text-align:justify"  id="modalEmpl" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#E2BBB4;color:white">
                <h5 class="modal-title" style="color:white">Agenda de Citas</h5>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <p>
                        ¡Ups! Parece que no has registrado empleados. Para poder usar la agenda de citas es necesario que agregues por lo menos un empleado.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" wire:click="redirectEmpleados(1)" class="btn btn-info ml-5" style="background-color:#9E846D;border-color:#9E846D">
                    ¡Hagámoslo!
                </button>
                <button type="button" class="btn btn-dark light" wire:click="redirectEmpleados(0)">Lo haré después</button>
            </div>
        </div>
    </div>
</div>