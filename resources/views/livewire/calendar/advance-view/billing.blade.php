
<div class="card-footer billing-footer">
    <div class="d-flex" style="justify-content:space-between;align-items:baseline">
        <div wire:ignore>
            <label for="billingDate">Requiere factura </label>
            <input type="checkbox" id="billingDate" name="billingDate" wire:model="billRequired">
        </div>
        @if(isset($customerId) && $customer!==null && $billRequired)
            <div>
                <label for="billedDate">Marcar como facturado </label>
                <input type="checkbox" id="billedDate" name="billedDate" wire:model="billed">
            </div>
        @endif
        <div style="display: {{ $billRequired ? '' : 'none' }};">
            <select name="usos" id="usoscfdi" class="form-control" wire:model.defer="usoCfdi">
                <option value="">Seleccione una opción</option>
                @foreach ($usos as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
    @error('usoCfdi') <span class="text-danger">*Seleccione un uso de cfdi</span> @enderror
    @if(isset($customerId) && $customer!==null)
        <div class="mt-3" style="display: {{ $billRequired ? '' : 'none' }};">
            <a id="tax_data_modal" wire:click="editTaxData">{{ $customer && count($customer->datosFacturacion) > 0  ? 'Consultar' : 'Agregar' }} datos fiscales</a>
        </div>
    @endif
    <livewire:clientes :action="3"/>
</div>