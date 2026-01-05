<div>
    <div class="card" >
        <div class="card-body p-1">
            <div class="table-responsive">

                <table class="table table-striped table-responsive-sm">
                    <thead>
                        <tr class="text-center">
                            <!-- <th width="96"><i class="las la-download"></i>Uso</th> -->
                            <th width="280">Servicio</th>
                            <th>Inicia</th>
                            <th>Termina</th>
                            <th width="260">Vendedor</th>
                            <th width="90">Descuento</th>
                            <th width="200">IVA</th>
                            <th width="200">Precio
                                <a data-toggle="popover" data-trigger="hover" data-content="Activa la casilla para elegir este precio como base para calcular la comisión de este servicio." style="
                                    color: #858585;
                                    font-size: smaller;">?</a>
                            </th>
                            <th width="200">Precio Unitario
                                <a data-toggle="popover" data-trigger="hover" data-content="Activa la casilla para elegir este precio como base para calcular la comisión de este servicio. Nota: este precio es actualizado cuando se modifica el descuento de este servicio." style="
                                    color: #858585;
                                    font-size: smaller;">?</a>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cartS as $item)
                            @include('livewire.calendar.cartServices')
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">AGREGA SERVICIOS</td>
                        </tr>
                    </tbody>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>