@if(Auth::user()->role !== 'estilista')

@if($action)
    @include('livewire.marcas.main')
@else
    @include('livewire.marcas.modal-brand')
@endif



@else
@include('livewire.sinPermisos')
@endif