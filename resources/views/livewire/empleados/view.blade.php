<div wire:ignore.self id="modalViewEmployee" class="modal fade" role="dialog">
<div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Información del empleado</h4>
            </div>
            <div class="modal-body">
                @if($empleado !=null)

                <div class="row">
                    <div class="col-md-12">
                        <!-- Mostrar la información del empleado -->
                        <h3><strong>Nombre:</strong> {{ $empleado->first_name }} {{ $empleado->last_name }}</h3>
                        <p><strong>Teléfono:</strong> {{ $empleado->phone_number ?? ' ' }}</p>
                        <p><strong>Fecha de nacimiento:</strong> {{ $empleado->birth_date ?? ' ' }}</p>
                        <p><strong>Email:</strong> {{ $empleado->email }}</p>
                    </div>
                </div>

                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>