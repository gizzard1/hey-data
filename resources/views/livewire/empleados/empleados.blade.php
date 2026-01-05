@if(Auth::user()->role !== 'estilista')

<div>
    <div class="row">
        <div class="col-md-4" id="categoriesCard">
            <div class="card">
                @include('livewire.empleados.form')
            </div>
        </div>
        <div class="col-md-8">
            <div class="card" id="list-employee">
                <div class="card-header ">
                    <div class="d-flex">
                        <div class="default-tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active"><i class="la la-user mr-2"></i> Empleados</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="ventas-w" href="{{ route(name: 'comisiones-empleados') }}"><i class="la la-chart-bar mr-2"></i> Comisiones</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route(name: 'propinas-empleados') }}"><i class="la la-piggy-bank mr-2"></i> Propinas</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body table-prices">
                    <div class="d-flex">
                        <table class="table table-responsive-md table-hover text-center">
                            <thead>
                                <tr >
                                    <td><strong>Nombre</strong></td>
                                    <td><strong>Teléfono</strong></td>
                                    <td><strong>Email</strong></td>
                                    <td><strong>Usuario</strong></td>
                                    <td><strong>Color predeterminado</strong> <a data-toggle="popover" data-trigger="hover" data-content="Cada empleado cuenta con un color por defecto para asignar a las futuras citas que se agenden." style="
                                        color: #858585;
                                        font-size: smaller;">?</a></td>
                                    <td></td>
                                    @if(Auth::user()->salon_id == 2)
                                        <td></td>
                                    @endif
                                </tr>
                            </thead>
                            <tbody id="table-body">
                            @forelse ($empleados as $item)
                                <tr style="background-color: {{ $item->visible ? '':'#81818166' }};" onclick="next()">
                                    <td class="text-left">
                                        <a wire:click="Edit('{{ $item->id }}')">
                                        <span>
                                            {{ $item->first_name }} {{ $item->last_name }} 
                                        </span></a>
                                    </td>
                                    <td> {{ $item->phone_number }} </td>
                                    <td> {{ $item->user?->email }} </td>
                                    <td> {{ $item->user?->name }} </td>
                                    <td>
                                        @include('livewire.empleados.dropdown-color')
                                    </td>
                                    <td>
                                    @if(!$item->visible)
                                    <a style="color:black"
                                        wire:click.prevent="visibilidadEmpleado({{ $item->id }},1)"><i
                                            class="las la-eye-slash la-2x"></i> </a></td>
                                    @else
                                    <a style="color:black"
                                        wire:click.prevent="visibilidadEmpleado({{ $item->id }},0)"><i
                                            class="las la-eye la-2x"></i> </a></td>
                                    @endif
                                    @if(Auth::user()->salon_id == 2)
                                    <td>
                                        <a style="color:black" onclick="Javascript:onStart()" data-toggle="modal" data-target="#modalFingerprint" wire:click.prevent="createFingerprint({{ $item->id }})"><i class="las la-fingerprint la-2x"></i> </a>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">No hay estilistas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            {{$empleados->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="display: none;" id="categoriesCardAux">
            <div class="card" >
                @include('livewire.empleados.form')
            </div>
        </div>
    </div>
</div>


@include('livewire.empleados.view')
@push('my-scripts')
@include('livewire.empleados.js')
@endpush


@else
@include('livewire.sinPermisos')
@endif


<style>
    a{
        color:#1d3557
    }
</style>