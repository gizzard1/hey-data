<div class="card">
    <div class="card-header">
        <span class="h3" style="color:#60060F;margin:auto">Caja</span>
    </div>
    <div class="card-body p-3" >
        <div class="form-group mt-5">
            <button wire:click="reimpresion()" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Reimprimir último ticket</button>
        </div>
        @if($caja)
        <div class="form-group mt-5">
            <button wire:click="corteCaja()" class="btn btn-dark btn-sm btn-block " style="background-color:#430007">Cerrar caja</button>
        </div>
        @else
        <div class="form-group mt-5">
            <button wire:click="apertura()" class="btn btn-sm btn-block" style="background-color: #9E846D;color:white;">Aperturar caja</button>
        </div>
        @endif
    </div>
</div>