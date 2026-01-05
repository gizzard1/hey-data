@if(Auth::user()->role !== 'estilista')


<div>
    <div class="row">
        @if($action == 1)
            @include('livewire.clientes.listado')
        @elseif($action == 2)
            @include('livewire.clientes.info')
        @endif
    </div>

    @include('livewire.clientes.categories')
    @include('livewire.clientes.modal')
    @include('livewire.clientes.modals.activateCard')
    @include('livewire.clientes.js')
    <livewire:categoria-clientes :configuration="0"/>
</div>
<style>
    .page-item:hover .page-link:hover {
        background-color: #9D1466 !important;
    }
</style>


@else
@include('livewire.sinPermisos')
@endif