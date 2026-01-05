@if(Auth::user()->role=='admin')
<div class="card">
    <div class="card">
        <div class="card-header">
            <div class="d-flex">
                <div class="separator" style="background-color:#E2BBB4"></div>
                <div class="mr-auto mt-3">
                    <h4 class="card-title"><a wire:click="$emit('infoSelected','1')">Ajustes</a>/ <a wire:click="$emit('infoSelected','5')">Clientes</a> /Recompensas</h4>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <label>Recompensas por servicios:</label>
                        <div class="form-group d-flex" style="column-gap:1rem">
                            <div>
                                <select wire:model.defer='comision.type_comission_s' class="form-control">
                                    <option value="percent" {{ $comision->type_comission_s === 'percent' ? 'selected' : '' }}>%</option>
                                    <option value="qty" {{ $comision->type_comission_s === 'qty' ? 'selected' : '' }}>$</option>
                                </select>
                                <label>Tipo</label>
                            </div>
                            <div>
                                <input wire:model.defer="comision.qty_s" type="number" class="form-control">
                                <label>Cantidad</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">

                        <label>Recompensas por productos:</label>
                        <div class="form-group d-flex" style="column-gap:1rem">
                            <div>
                                <select wire:model.defer='comision.type_comission_p' class="form-control">
                                    <option value="percent" {{ $comision->type_comission_p === 'percent' ? 'selected' : '' }}>%</option>
                                    <option value="qty" {{ $comision->type_comission_p === 'qty' ? 'selected' : '' }}>$</option>
                                </select>
                                <label>Tipo</label>
                            </div>
                            <div>
                                <input wire:model.defer="comision.qty_p" type="number" class="form-control">
                                <label>Cantidad</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer" style="position: relative;" >
            <button  onclick="next()" class="btn btn-sm btn-info float-right save" wire:click.prevent="storeGeneral">Guardar</button>
            <button  onclick="next()" class="btn btn-sm btn-dark float-right" wire:click.prevent="cancel" style="background-color: transparent;color:#9D1466">Limpiar</button>
        </div>
        
        <div class="card-body table-prices" style="margin-top:-4rem">
            <div class="container">
                <div class="d-flex">
                    <div class="mr-auto">
                        <h4 class="card-title">Excepciones</h4>
                    </div>
                </div>
            </div>
                <div class="p-4" id="exceptions">
                <div class="table table-responsive" >
                    <div class="default-tab">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $pestaña == 1 ? 'active' : '' }}" name="pestaña-prod" onclick="changeTo(1)"><i class="la la-box mr-2"></i> Productos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $pestaña == 2 ? 'active' : '' }}" name="pestaña-serv" onclick="changeTo(2)"><i class="la la-calendar mr-2"></i> Servicios</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $pestaña == 3 ? 'active' : '' }}" name="pestaña-serv" onclick="changeTo(3)"><i class="la la-user mr-2"></i> Clientes</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        @if($pestaña == 1)
                            @include('livewire.ajustes.excepciones.productos')
                        @elseif($pestaña == 2)
                            @include('livewire.ajustes.excepciones.servicios')
                        @elseif($pestaña == 3)
                            @include('livewire.ajustes.excepciones.clientes')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('livewire.ajustes.excepciones.modal')
</div>
@else
@include('livewire.sinPermisos')
@endif