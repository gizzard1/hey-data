@if(Auth::user()->role!=='estilista')
<div >
    <div class="row" >
        <div class="col-sm-12">
            <div class="card" id="reporte-global">
                <div class="card-header ">
                    <div class="d-flex">
                        <div class="default-tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active"><i class="la la-box mr-2"></i> Histórico</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="ventas-w" href="{{ route('informe-movimientos') }}"><i class="la la-store mr-2"></i> Transacciones</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('informe-caja') }}"><i class="la la-cash-register mr-2"></i> Caja</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        @include('livewire.informes.header.botonesFecha')
                    </div>
                    
                    {{-- @if(Auth::user()->role=='admin')
                    <div class="botones-exportar" style="background-color: #278d46;border-radius:5px">
                        <button wire:click="generateExcel" class="btn-sm excel-button input-group-text">Exportar Informe</button>
                        <button class="button-style" wire:click="generatePdf" style="border-width: 0;color:red"><i class="las la-file-pdf la-2x"></i></button>
                        <button class="button-style" wire:click="generateExcel" style="border-width: 0;color:white"><i class="las la-file-excel la-2x"></i></button>
                    </div>
                    @endif --}}
                </div>
                <div class="card-body cuerpo-informe informe" style="margin-left: 2rem;margin-right:2rem;margin-top:2rem;">
                    @include('livewire.informes.informe-contenido')
                </div>
            </div>
        </div>
    </div>
    @include('livewire.informes.js')
    @include('livewire.informes.js-flatpickr')
</div>

<style>
    @media print{
        p{
            widows:3;
        }
    }
    a{
        color:#1d3557
    }
</style>
@else
@include('livewire.sinPermisos')
@endif
