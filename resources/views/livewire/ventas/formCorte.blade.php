<div class="card-header">
    <span class="h3" style="color:#60060F;margin:auto">Corte de Caja</span>
</div>
<div class="card-body">
    <div>
        <span class="h2"><b style="color:#60060F">TOTAL:</b></span>
        <span class="float-right h1">${{ number_format($totalCorteReal ?? 0, 2, '.', ',') }}</span>
    </div>
    <hr>
    <div class="form-group">
        <input wire:change.prevent="changeCash('incomes',$event.target.value)" type="number"
            class="form-control" placeholder="Efectivo" autocomplete="nope">
        @error('totalCashReal') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Efectivo</label>
    </div>
    <div class="form-group">
        <input wire:change.prevent="changeCash('card',$event.target.value)" type="number"
            class="form-control" placeholder="Tarjeta" autocomplete="nope">
        @error('totalCashReal') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Tarjeta</label>
    </div>
    
    @foreach($tarjetaCorteReal as $terminal => $qty)
        <div class="form-group">
            <input wire:change.prevent="changeQtyTerminal( '{{$terminal}}',$event.target.value)" type="number" class="form-control"
                placeholder="{{ $terminal }}">
            <label>{{ $terminal }}</label>
        </div>
    @endforeach

    <div class="form-group">
        <input wire:change.prevent="changeCash('msi',$event.target.value)" type="number" class="form-control"
            placeholder="Msi">
        @error('totalNFreal') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Msi</label>
    </div>

    <div>
        <span class="h2"><b style="color:#60060F">CAJA CHICA</b></span>
    </div>
    <hr>
    <div class="form-group">
        <input wire:model.defer="caja_chica_real" type="number"
            class="form-control" placeholder="Caja chica" autocomplete="nope">
        @error('caja_chica_real') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Caja chica</label>
    </div>

    <div>
        <span class="h2"><b style="color:#60060F">PROPINAS:</b></span>
        <span class="float-right h1" >${{ number_format($totalTipsReal ?? 0, 2, '.', ',') }}</span>
    </div>
    <hr>
    <div class="form-group">
        <input wire:change.prevent="changeCash('propinas',$event.target.value)" type="number"
            class="form-control" placeholder="Efectivo" autocomplete="nope">
        @error('propinasMsiReal') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Efectivo</label>
    </div>
    <div class="form-group">
        <input wire:change.prevent="changeCash('cardPropinas',$event.target.value)" type="number"
            class="form-control" placeholder="Tarjeta" autocomplete="nope">
        @error('propinasCardReal') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Tarjeta</label>
    </div>
    
    @foreach($tarjetaCortePropinaReal as $terminal => $qty)
        <div class="form-group">
            <input wire:change.prevent="changeQtyTerminalPropina( '{{$terminal}}',$event.target.value)"  type="number" class="form-control"
                placeholder="{{ $terminal }}">
            <label>{{ $terminal }}</label>
        </div>
    @endforeach

    <div class="form-group">
        <input wire:change.prevent="changeCash('msiPropinas',$event.target.value)" type="number" class="form-control"
            placeholder="Msi">
        @error('propinasMsiReal') <span class="text-danger">*Corrige este campo* </span> @enderror
        <label>Msi</label>
    </div>
</div>