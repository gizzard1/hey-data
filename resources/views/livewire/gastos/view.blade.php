<div wire:ignore.self id="modalViewClient" class="modal fade" role="dialog">
<div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Información del cliente</h4>
            </div>
            <div class="modal-body">
                @if($customerSelected !=null)

                <div class="row">
                    <div class="col-md-12">
                        <!-- Mostrar la información del servicio -->
                        <h3>{{ $customerSelected->first_name }} {{ $customerSelected->last_name }}</h3>
                        <p><strong>Teléfono:</strong> {{ $customerSelected->phone ?? ' ' }}</p>
                        <p><strong>Fecha de nacimiento:</strong> {{ $customerSelected->birth_date ?? ' ' }}</p>
                        <p><strong>Email:</strong> {{ ($customerSelected->email) }}</p>
                        <p><strong>Descripción:</strong> {{ $customerSelected->description }}</p>
                        <p><strong>Categoría:</strong> {{ $customerSelected->categoria ? $customerSelected->categoria->name : 'Sin categoría' }}</p>
                        <p><strong>Mensajes personalizados <input type="checkbox" disabled @if($customerSelected->want_custom_messages) checked @endif> </strong> </p>
                        <p><strong>Ofertas </strong> <input type="checkbox" disabled @if($customerSelected->want_offers) checked @endif></p>
                        @if($customerSelected->tarjetaPuntos)
                        <p><strong>Puntos acumulados:</strong> {{ $customerSelected->tarjetaPuntos->balance }}</p>
                        @endif
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
<style>
    strong{
        color:#9D1466;
    }
    .btn:hover{
        background-color: #430007;
    }
</style>