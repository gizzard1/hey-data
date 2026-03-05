<div class="d-flex">
    @if($method['paymentMethod'] == "5")
        <input type="button" wire:click.prevent="removeRewardMethods" value="x" class="remove-tag p-1">
        <select class="service-title employees-title employee-selector" disabled>
            <option value="">Puntos recompensa</option>
        </select>
        <input type="text" class="form-control qty qty-payment" value="${{ number_format($method['amount'],2,'.',',') }}" disabled>
    @else
        <input type="button" wire:click.prevent="$emit('removeItem', '{{ $method['uid'] }}' , 'method' )" value="x" class="remove-tag p-1">
        <select class="service-title employees-title employee-selector" wire:change="$emit('cambioDataMethods','{{ $method['uid'] }}',$event.target.value,3,'metodos')" {{ $method['paymentMethod'] == 99999 ? 'disabled' : '' }}>
            <option style="text-align: center;" value="1" {{ $method['paymentMethod'] == "1" ? 'selected' : '' }}>Efectivo</option>
            <option style="text-align: center;" value="2" {{ $method['paymentMethod'] == "2" ? 'selected' : '' }}>Tarjeta</option>
            <option style="text-align: center;" value="3" {{ $method['paymentMethod'] == "3" ? 'selected' : '' }}>MSI</option>
            <option style="text-align: center;" value="99999" {{ $method['paymentMethod'] == "99999" ? 'selected' : '' }}>Gift Card</option>
            @if($method['paymentMethod']=='4')
                <option style="text-align: center;" value="4" {{ $method['paymentMethod'] == "4" ? 'selected' : '' }}>Descuento</option>
            @endif
            @foreach($metodosSalon as $metodoSalon)
                <option style="text-align: center;" value="{{ $metodoSalon['id'] }}" {{ $method['paymentMethod'] == $metodoSalon['id'] ? 'selected' : '' }}>{{ $metodoSalon['Payment_method'] }}</option>
            @endforeach
        </select>
        
        @if($method['paymentMethod'] == '4')
            <!-- Select para elegir porcentaje o moneda, con flecha oculta -->
            <select class="form-control no-arrow bg-white qty discount" wire:change="$emit('cambioDataMethods','{{ $method['uid'] }}',$event.target.value,6,'metodos')">
                <option value="Porcentaje" {{ $method['tipo'] == "Porcentaje" ? 'selected' : '' }}>%</option>
                <option value="Cantidad" {{ $method['tipo'] == "Cantidad" ? 'selected' : '' }}>$</option>
            </select>
            <input  class="form-control qty qty-payment" value="{{ $method['amount'] }}" wire:change="$emit('cambioDataMethods','{{ $method['uid'] }}',$event.target.value,1,'metodos')">
        @else
            <!-- Select para elegir porcentaje o moneda, con flecha oculta -->
            <select class="form-control no-arrow bg-white qty discount" disabled>
                <option value="Cantidad" selected>$</option>
            </select>
            <input {{ $method['paymentMethod'] == 99999 ? 'disabled' : '' }} class="form-control qty qty-payment" value="{{ $method['amount'] }}" wire:change="$emit('cambioDataMethods','{{ $method['uid'] }}',$event.target.value,1,'metodos')">
        @endif
        <input {{ $method['paymentMethod'] == 99999 ? 'disabled' : '' }} placeholder="Referencia (opcional)" class="form-control qty reference-field" type="text" value="{{ $method['reference'] ?? $method['reference'] }}" wire:change="$emit('cambioDataMethods','{{ $method['uid'] }}',$event.target.value,2,'metodos')" >
    @endif
</div>