
<div>
    <div>
    @include('livewire.ventas.modals.confirmApertura')
    @include('livewire.ventas.reseña')
    @if($isAdmin && $agregarEmpleados!=null)
    @include('livewire.calendar.configurar')
    @endif
    @if($action==1)
        @include('livewire.calendar.resource-hour-grid')
    @elseif($action==2)
        @include('livewire.calendar.form')
    @endif
    </div>
</div>
@include('livewire.calendar.jsCitas')

@include('livewire.calendar.modal.form')
