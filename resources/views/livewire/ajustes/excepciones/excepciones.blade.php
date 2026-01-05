@if(Auth::user()->role=='admin')
<div id="cardForm" class="card">
        <div class="card-header">
            <div class="d-flex">
                <div class="separator" style="background-color:#E2BBB4"></div>
                <div class="mr-auto mt-3">
                @if(isset($this->empleado))
                    <h4 class="card-title"><a wire:click="set(1)">Ajustes</a>/ <a wire:click="set(1)">Recompensas</a>/ Comisiones para {{ $this->empleado->first_name }} {{ $this->empleado->last_name }}</h4>
                @endif
                </div>
            </div>
        </div>
        @if(isset($this->empleado))
            <div class="card-body">
                <div class="container">
                    <div id="ind_comision" class="row">
                        <div class="col-md-6">
                            <label>Comisiones por servicios:</label>
                            <div class="form-group d-flex" style="column-gap:1rem">
                                <div>
                                    <select wire:model='comisionGen.type_comission_s' class="form-control">
                                        <option value="percent" {{ $comisionGen->type_comission_s === 'percent' ? 'selected' : '' }}>%</option>
                                        <option value="qty" {{ $comisionGen->type_comission_s === 'qty' ? 'selected' : '' }}>$</option>
                                    </select>
                                    <label>Tipo</label>
                                </div>
                                <div>
                                    <input wire:model="comisionGen.qty_s" type="number" class="form-control">
                                    <label>Cantidad</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <label>Comisiones por productos:</label>
                            <div class="form-group d-flex" style="column-gap:1rem">
                                <div>
                                    <select wire:model='comisionGen.type_comission_p' class="form-control">
                                        <option value="percent" {{ $comisionGen->type_comission_p === 'percent' ? 'selected' : '' }}>%</option>
                                        <option value="qty" {{ $comisionGen->type_comission_p === 'qty' ? 'selected' : '' }}>$</option>
                                    </select>
                                    <label>Tipo</label>
                                </div>
                                <div>
                                    <input wire:model="comisionGen.qty_p" type="number" class="form-control">
                                    <label>Cantidad</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer" style="position: relative;" id="exception-buttons">
                <button id="save-button" onclick="next()" class="btn btn-sm btn-info float-right save" wire:click.prevent="StoreGeneralException">Guardar</button>
                <button id="clean-button" onclick="next()" class="btn btn-sm btn-dark float-right" wire:click.prevent="cancelGen" style="background-color: transparent;color:#9D1466">Limpiar</button>
            </div>
            <div class="card-body table-prices" style="height: 19rem;">
                <div class="container">
                    <div class="d-flex">
                        <div class="mr-auto">
                            <h4 class="card-title">Excepciones</h4>
                        </div>
                    </div>
                </div>
                    <div class="p-4" id="exceptions">
                    <div class="table table-responsive table-hover">
                        <div class="default-tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link {{ $pestaña == 2 ? 'active' : '' }}" name="pestaña-serv" onclick="changeTo(2)"><i class="la la-calendar mr-2"></i> Servicios</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $pestaña == 1 ? 'active' : '' }}" name="pestaña-prod" onclick="changeTo(1)"><i class="la la-box mr-2"></i> Productos</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            @if($pestaña == 1)
                                @include('livewire.ajustes.excepciones.productos')
                            @elseif($pestaña == 2)
                                @include('livewire.ajustes.excepciones.servicios')
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @include('livewire.ajustes.excepciones.modal')
</div>
@else
@include('livewire.sinPermisos')
@endif
@include('livewire.ajustes.jsS')
<style>
    .cust{
        background-color: #9D1466;
        color: aliceblue !important ;
        cursor:pointer;
    }
    a:hover{
        color:#9D1466;
    }
    a{
        color:#9D1466;
    }
    .nav-link{
        cursor:pointer;
    }
</style>
