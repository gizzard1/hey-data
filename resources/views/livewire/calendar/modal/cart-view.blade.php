<div>
    <div class="card-body">
        <div class="table-responsive" style="height: auto !important;">

            <table id="cart-view" class="table table-striped table-responsive-sm">
                <thead>
                    <tr class="text-center">
                        <!-- <th width="96"><i class="las la-download"></i>Uso</th> -->
                        <th colspan="2">Horario</th>
                        <th width="280">Servicio</th>
                        <th width="260">Vendedor</th>
                        <th width="70">Color</th>
                        <th></th>
                    </tr>
                </thead>
                @if(isset($cartS)&&count($cartS)>0)
                <tbody style="height:1rem">
                    @foreach($cartS as $item)
                        @include('livewire.calendar.modal.cartServices')
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="text-right">
                        <td colspan="4" style="padding-right:4rem">Total: ${{ number_format($totalCart,2,'.',',') }} 
                        @if($totalCartBase>0&&$totalCart!=$totalCartBase)<small class="line-t">${{ number_format($totalCartBase,2,'.',',') }}</small>
                        @endif
                    </td>
                    </tr>
                    @if($pp_cart>0)
                    <tr class="text-right">
                        <td colspan="4" style="padding-right:4rem">Por pagar: ${{ number_format($pp_cart,2,'.',',') }}</td>
                    </tr>
                    @endif
                </tfoot>
                @endif
            </table>
            <div>
                <button id="clean-cart" onclick="initializeTutorial_3_1()" wire:click="cancelarCaptura" class="btn btn-dark btn-sm float-right" style="background-color: white;color:#60060F;border-color:#E2BBB4">Limpiar carrito</button>
            </div>
                <textarea id="descripciónInput" wire:model.prevent="description" type="text" class="form-control" placeholder="Escribe una descripción (opcional)..." maxlength="100" style="resize: none;margin-top:4rem"></textarea>

                <input type="file" class="form-control" id="input-file" wire:model="gallery" accept="image/x-png,image/jpeg,.pdf" multiple hidden>
                @include('livewire.calendar.modal.loadFiles')
        </div>
    </div>
</div>
<style>
    .table{
        max-width: none !important;
    }
</style>