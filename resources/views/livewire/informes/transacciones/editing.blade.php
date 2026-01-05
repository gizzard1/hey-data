<div>
    <div wire:ignore.self class="modal fade none-border" id="modalEditing" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="height: auto;max-height:35rem;overflow: auto;">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Transacción</h5>
                    <button type="button" class="close" data-dismiss="modal" wire:click="disableEditing"><span>x</span>
                    </button>
                </div>
                <div class="modal-body">
                @if($itemSelected!=null)
                    <div class="card-header">

                    @if($type == 'venta')
                    <h4 class="text-center">Venta #{{ $itemSelected->id}}</h4>
                    @elseif($type == 'cita')
                    <h4 class="text-center">Cita #{{ $itemSelected->id}}</h4>
                    @elseif($type == 'corte')
                    <h4 class="text-center">Corte de Caja #{{ $itemSelected->id}}</h4>
                    @endif

                    </div>
                    @if($type == 'venta' || $type == 'cita')
                        <div class="default-tab">
                            <ul class="nav nav-tabs" role="tablist" style="width:fit-content">
                                <li class="nav-item">
                                    <a class="nav-link {{ $pestaña == 1 ? 'active' : '' }}" name="pestaña-resumen" onclick="changeTo(1)"><i class="la la-cart-plus"></i> Resumen</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $pestaña == 2 ? 'active' : '' }}" name="pestaña-payment" onclick="changeTo(2)"><i class="la la-money-bill-wave"></i> Pagos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $pestaña == 3 ? 'active' : '' }}" name="pestaña-tips" onclick="changeTo(3)"><i class="la la-piggy-bank"></i> Propinas</a>
                                </li>
                                @if($type == 'cita')
                                    <li class="nav-item">
                                        <a class="nav-link {{ $pestaña == 4 ? 'active' : '' }}" name="pestaña-materials" onclick="changeTo(4)"><i class="la la-download"></i> Uso</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ $pestaña == 5 ? 'active' : '' }}" name="pestaña-files" onclick="changeTo(5)"><i class="la la-file"></i> Archivos</a>
                                    </li>
                                @endif
                            </ul>
                            <div div class="tab-content">
                                @if($pestaña == 1)
                                    <h4 class="text-center" style="margin-top: 1rem;">Resumen</h4>
                                    @if($type == 'venta')
                                        @include('livewire.informes.transacciones.editing.cartProduct')
                                    @elseif($type == 'cita')
                                        @include('livewire.informes.transacciones.editing.cartService')
                                        <!-- @if(count($itemSelected->details_product)>0)
                                            @include('livewire.informes.transacciones.editing.cartProduct')
                                        @endif -->
                                    @endif
                                @elseif($pestaña == 2)
                                    @include('livewire.informes.transacciones.editing.metodos-pago')
                                @elseif($pestaña == 3)
                                    @include('livewire.informes.transacciones.editing.propinas')
                                @elseif($pestaña == 4 && $type == 'cita')
                                    <livewire:material-uso :type="1"/>
                                @elseif($pestaña == 5 && $type == 'cita')
                                    <div class="p-4">
                                        @include('livewire.calendar.modal.loadFiles')
                                    </div>
                                    @include('livewire.informes.transacciones.editing.footerFiles')
                                @endif
                            </div>
                        </div>
                    @elseif($type == 'corte')
                        <h4 class="text-center" style="margin-top: 1rem;">Resumen</h4>
                        @include('livewire.informes.transacciones.editing.corte ')
                    @endif      

                    @if($type == 'venta' || $type == 'cita')
                    @else
                    @endif
                @endif
            </div>
            </div>
        </div>
    </div>
    @include('livewire.informes.transacciones.editing.listadoItems')
</div>