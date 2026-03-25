<div class="d-flex">
    {{-- Botón de eliminar --}}
    <input type="button" wire:click.prevent="$emit('removeItem', '{{ $propina['uid'] }}' , 'propina' )" value="x" class="remove-tag p-1">
    <!-- Campos para elegir porcentaje y cantidad sobre total para calcular la propina -->
    <input
        min="0" max="100"
        onchange="calculateTipAmount(this.value,'{{ $propina['uid'] }}', '{{ $totalCart }}')"
        class="form-control text-center qty percent-qty-tip"
        type="number"
        value="{{ ($propina['amount'] > 0 ? $propina['amount'] : 0) / ($totalCart > 0 ? $totalCart : 1) * 100 }}">
    <select class="form-control no-arrow bg-white qty discount percent-tip" disabled>
        <option value="Porcentaje" checked>%</option>
    </select>
    {{-- Select para elegir método de pago recibida --}}
    <select name="payment" class="service-title hide-text employees-title employee-selector" wire:change="$emit('cambioDataMethods','{{ $propina['uid'] }}',$event.target.value,3,'propinas')">
        <option style="text-align: center;" value="1" {{ $propina['paymentMethod'] == "1" ? 'selected' : '' }}>Efectivo</option>
        <option style="text-align: center;" value="2" {{ $propina['paymentMethod'] == "2" ? 'selected' : '' }}>Tarjeta</option>
        <option style="text-align: center;" value="3" {{ $propina['paymentMethod'] == "3" ? 'selected' : '' }}>MSI</option>
        @foreach($metodosSalon as $metodoSalon)
            <option style="text-align: center;" value="{{ $metodoSalon['id'] }}" {{ $propina['paymentMethod'] == $metodoSalon['id'] ? 'selected' : '' }}>{{ $metodoSalon['Payment_method'] }}</option>
        @endforeach
    </select>
    <input type="text" class="form-control qty qty-payment-2"  value="${{ $propina['amount'] }}" wire:change="$emit('cambioDataMethods','{{ $propina['uid'] }}',$event.target.value,1,'propinas')">
    <!-- Select para elegir empleado que percibe el ingreso -->
    <select wire:change="$emit('cambioDataMethods','{{ $propina['uid'] }}',$event.target.value,4,'propinas')" class="service-title hide-text employees-title employee-selector employee-selector-2">
        @if(!isset($propina['empleado']))
            <option value="">Seleccione un empleado</option>
        @endif
        @foreach($empleados as $empleado)
            <option style="text-align: center;" value="{{ $empleado->id }}" {{ $propina['empleado'] == $empleado->id ? 'selected' : '' }}>{{ $empleado->first_name }}</option>
        @endforeach
    </select>
</div>
<script>
    window.calculateTipAmount = function(percent, uid, total) {
        total = parseFloat(total);
        let amount = (percent / 100) * total;

        if (window.Livewire) {
            window.Livewire.emit('cambioDataMethods', uid, amount, 1, 'propinas');
        } else if (window.livewire) {
            window.livewire.emit('cambioDataMethods', uid, amount, 1, 'propinas');
        } else {
            console.error('Livewire no está definido');
        }
    }
</script>