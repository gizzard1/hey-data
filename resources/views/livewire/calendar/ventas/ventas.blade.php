@if(Auth::user()->role !== 'estilista')
@include('livewire.calendar.ventas.cart-view')
@else
@include('livewire.sinPermisos')
@endif