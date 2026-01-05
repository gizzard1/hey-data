<!-- row -->


<div class="row">
        <div class="col-xl-2 col-xxl-2" style="width:5rem">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-intro-title">Empleados</h4>
                    <hr>
                        <div>
                            <label><a>Agenda</a></label>
                            <input type="checkbox" wire:click="regresarAgenda()">
                            <p></p>
                        </div>
                        @forelse ($empleados as $empleado)
                            <div>
                            <label  ><a>{{ $empleado->first_name }} {{ $empleado->last_name }}</a></label>
                            <input type="checkbox"  wire:click="mostrarCitas({{ $empleado->id }})" value="{{ $empleado->id }}" {{ $selectedEmpleadoId == $empleado->id ? 'checked' : '' }}>
                            <p></p>
                            </div>
                        @empty
                        <tr>
                            <td>No hay empleados</td>
                        </tr>
                        @endforelse
                </div>
            </div>
        </div>
        <div class="col-xl-10 col-xxl-9">
            <div class="card">
                <div class="card-body">
                    <div id="calendar" class="app-fullcalendar" style="font-size: smaller;"></div>
                </div>
            </div>
        </div>
    </div>
    
</div>
@include('livewire.calendar.jsCitas')