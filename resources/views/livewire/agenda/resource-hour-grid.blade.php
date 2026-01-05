<div>
<div class="card" style="font-size:small;min-height:2rem;">
    <div style="width: 100%; margin: 0; padding: 0;">
<div style="padding: 1rem;">

<div style="transform: translate(0%,-7%);">

<div class="row">
    <div style="display: inline-flex;cursor:pointer;" class="flatpickrInd" >
        <h5 id="currentDate" class="center mt-2 ml-4 mr-4 " style="color:#9D1466;justify-content: space-between;transform: translate(10px, 10px);" >{{ $currentDate }}</h5>
        <h5 id="currentDate" class="center mt-2 ml-4 mr-4 " style="color:#9D1466;justify-content: space-between;transform: translate(10px, 10px);" >@if($is_interval) <div>- {{ $currentDateEnd }}</div>@endif</h5>
    </div>
</div>


<div>
    <button class="button-style wider float-right" style="transform: translate(-110%, -179%)" wire:click="loadFecha()">Hoy</button>                
</div>      

</div>
</div>
    <div class="table-responsive">
        <table class="table-scroll table tick-borders" style="min-width: -webkit-fill-available">
            <thead style="width: 100%;display:table-header">
                <tr class="text-center" >
                    <th width="70"></th>
                    @foreach($empleados as $empleado)
                    <th style="font-weight:300;height:auto;cursor:default" class="casilla1 sticky-top">{{ $empleado->first_name }} {{ $empleado->last_name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody style="width: auto;">
                @foreach($horas as $hora)
                <tr  data-hour="{{ $hora }}">
                    <td width="70" class="float-left sticky-left">{{ $hora }}</td>
                    @foreach($empleados as $empleado)
                    <td wire:ignore.self class="casilla trigger" wire:click="crearCita('{{ $hora }}','{{ $empleado->id }}')" id="casilla" value="('{{ $hora }}', '{{ $empleado->id }}')" >
                            @foreach($citas[$empleado->id] as $cita)
                            @if(isset($cita['start']))
                                <!-- Acceder a la propiedad start del array -->
                                @if(Carbon\Carbon::parse($cita['start'])->format('H:i') == $hora)
                                <div class="evento draggable" wire:click="viewDetails({{ $cita->date->id }})" value="{{ $cita->id }}" draggable="true">
                                    <div class="event-header" style="background-color:{{ ($cita->date->status === 'Pagada' ? '#D4E9D6' : ($cita->date->status === 'Agendada' ? '#E2BBB4' : ($cita->date->status === 'Cancelada' ? '#6E6E6E' : '#868E96'))
                                    ) }};color: {{ $cita->date->status === 'Cancelada' ? 'white' : 'inherit' }}">
                                        {{ Carbon\Carbon::parse($cita['start'])->format('H:i') }}-{{ Carbon\Carbon::parse($cita['end'])->format('H:i') }}
                                    </div>    
                                    @php
                                        if($cita['duration']<30){
                                            $height=0;
                                        }else{
                                            $height = ($cita['duration']/15)-2;
                                        }
                                    @endphp
                                
                                    <div class="description" style="height:{{ $height }}">
                                        <div>
                                            {{ $cita->date->customer->first_name }}
                                        </div>
                                        <div>
                                            {{ $cita->title }}
                                        </div>
                                    </div>
                                <div class="ventana-emergente"><div>{{ Carbon\Carbon::parse($cita['start'])->format('H:i') }}-{{Carbon\Carbon::parse($cita['end'])->format('H:i') }}</div>
                                        @if($cita->date->status==='Cancelada')
                                        <div>Motivo de Cancelación: {{ $cita->date->motivoCancelacion }}</div>
                                        @endif
                                        <div>{{ $cita->date->customer->first_name }} {{ $cita->date->customer->last_name }}</div>
                                        <div>Servicio: {{ $cita->title}}</div>
                                        <div>Descripción del cliente: 
                                        @foreach($cita->date->customer->categorias as $categoria)
                                        {{ $categoria->name }} ~
                                        @endforeach
                                        <div>Precio de la cita: ${{ $cita->date->total }}</div>
                                        
                                </div>
                                </div>    
                                @endif
                            @endif
                        @endforeach
                                
                        </td>
                    @endforeach
                </tr>
                @php
                if (!function_exists('renderTimeSlotRow')) {
                    function renderTimeSlotRow($displayHora, $empleados, $citas) {
                        $output = '<tr>';
                        $output .= '<td width="70"></td>';
                        foreach ($empleados as $empleado) {
                            $output .= '<td wire:ignore.self class="casilla trigger" id="casilla" value="(\'' . $displayHora . '\', \'' . $empleado->id . '\')" wire:click="crearCita(\'' . $displayHora . '\', \'' . $empleado->id . '\')">';
                            foreach ($citas[$empleado->id] as $cita) {
                                if (isset($cita['start']) && Carbon\Carbon::parse($cita['start'])->format('H:i') == $displayHora) {
                                    $output .= '<div class="evento draggable" wire:click="setAsignacion(\'' . $cita->id . '\')" value="' . $cita->id . '" draggable="true" >';
                                    $output .= '<div class="event-header" style="background-color:' . ( $cita->date->status === 'Pagada' ? '#D4E9D6' : ($cita->date->status === 'Agendada' ? '#E2BBB4' : ($cita->date->status === 'Cancelada' ? '#6E6E6E' : '#6E6E6E'))
                                    ) . ';color:' . ( $cita->date->status === 'Cancelada' ? 'white' : 'inherit' ) .';">';
                                    $output .= Carbon\Carbon::parse($cita['start'])->format('H:i') . '-' . Carbon\Carbon::parse($cita['end'])->format('H:i');
                                    $output .= '</div>';
                                    $output .= '<div class="description" style="height:' . ($cita['duration']/15)-2 . 'rem">';
                                    $output .= '<div>' . $cita->date->customer->first_name . '</div>';
                                    $output .= '<div>' . $cita->title . '</div>';
                                    $output .= '</div>';
                                    $output .= '<div class="ventana-emergente">
                                        <div>' . Carbon\Carbon::parse($cita['start'])->format('H:i') . '-' . Carbon\Carbon::parse($cita['end'])->format('H:i') . '</div>
                                        <div>Cliente: ' . $cita->date->customer->first_name . ' ' . $cita->date->customer->last_name . '</div>';
                                        if($cita->date->status === 'Cancelada'){
                                            $output .= '<div>Motivo de Cancelación: ' . $cita->date->motivoCancelacion . '</div>';
                                        }
                                        $output .= '<div>Servicio: ' . $cita->title . '</div>
                                                    <div>Descripción del cliente:</div>';

                                        foreach ($cita->date->customer->categorias as $categoria) {
                                            $output .= $categoria->name . ' ~ ';
                                        }

                                        $output .= '<div>Precio de la cita: $' . $cita->date->total . '</div>
                                                    </div>
                                                    </div>';
                                }
                            }
                            $output .= '</td>';
                        }
                        $output .= '</tr>';
                        return $output;
                    }
                }
                @endphp

                {!! renderTimeSlotRow(str_replace(':00', ':15', $hora), $empleados, $citas) !!}
                {!! renderTimeSlotRow(str_replace(':00', ':30', $hora), $empleados, $citas) !!}
                {!! renderTimeSlotRow(str_replace(':00', ':45', $hora), $empleados, $citas) !!}
                @endforeach
            </tbody>

        </table>
    </div>
    </div>
</div>
</div>

@include('livewire.agenda.js')
<style>
/* Estilo eventos */
.trigger {
    cursor: pointer;
    z-index:1000;
}

.ventana-emergente {
    position: relative;
    top: 50%; /* Coloca la ventana-emergente justo debajo del trigger */
    left: 110%;
    border: 1px solid #BFADA1;
    width: auto; /* Ancho completo */
    height: auto; /* Ancho completo */
    background-color: #fff; /* Color de fondo */
    transform: translate(-110%, -50%); /* Desplaza un poco a la izquierda cuando se muestra */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Sombra suave */
    z-index: 2500; /* Asegúrate de que la ventana-emergente esté sobre otros elementos */
    opacity: 0;
}
.trigger:hover .ventana-emergente {
    opacity: 1; /* La ventana-emergente se muestra al pasar el mouse por encima del contenedor */
    left: 110%;
}
.evento{
    width: inherit;
    position: absolute;
    display: grid;
    padding: 0;
    text-align: center;
    margin: 0;
    cursor: pointer;
    overflow: hidden;
    transform: translate(8%, -3%);
}
.event-header{
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    height: 1.5;
    z-index: 1000;
    color:#9D1466;
    border-color: #BFADA1;
    border-width: 1px;
    border-style: solid;
}
.description{
    background-color: #f5f5f5;
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
    z-index: 1000;
    overflow: hidden;
    border-color: #BFADA1;
    border-width: 1px;
    border-style: solid;
}
/* formato tabla */
.table-responsive {
    height:30rem;
    margin: auto;
    padding: 0;
    margin-bottom: 2rem;
    transform: translate(0%,-3%);
}
.table-responsive tbody{
    height: 35rem;
    overflow-y: auto;
}
.table-responsive tbody td,
.table-responsive thead > tr > th{
    border-bottom-width: 0;
    height: 3px;
    margin:0;
    padding: 0;
    font-size:small;
}
.casilla1 , .casilla{
    height: 1.5rem;
    min-width: 12rem;
    text-overflow: ellipsis;
}
.casilla1{
    height: 5px;
}
.casilla:hover {
    background-color: #f5f5f5; /* Color de fondo un poco más claro que el original */
    cursor:pointer;
}
.table {
    border-bottom: 2px solid #f5f5f5; /* Borde azul para la parte inferior de la tabla */
    max-width: min-content;
}
tbody tr[data-hour]:not([data-hour=""]) {
    border-top: 4px solid #f5f5f5; /* Borde azul más grueso */
}
/* Estilos generales para los botones */
.button-style {
    margin-top:13px;
    width: 2rem;
    height: 2rem;
    border-radius: 4px;
    background-color: transparent;
    border: 1px solid #ccc;
    cursor: pointer; /* Cambia el cursor al pasar sobre el botón */
    transition: background-color 0.3s ease; /* Transición suave para el cambio de color */
}

/* Estilo al pasar el cursor sobre el botón */
.button-style:hover {
    background-color:#E2BBB4;
    color:#f1f1f1;
}

.button-style:active{
    border-color: transparent;
}

/* Estilos específicos para algunos botones */
.button-style.wider {
    width: 3rem;
}

.button-style.widest {
    width: 6rem;
}
.nextEmpl:hover{
    background-color:#E2BBB4 !important;
    color:#f1f1f1;
}
.sticky-left {
    position: sticky;
    left: 0;
    z-index: 2;
    background-color: white;
}
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 1;
    background-color: white;
}
</style>

@push('my-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        initializeFlatpckr();
        initializeDraggFunction()
})

document.addEventListener('livewire:load', function () {
    Livewire.on('recargarFlat', function () {
        initializeDraggFunction()
    })
})

function initializeFlatpckr(){
    
    flatpickr(document.getElementsByClassName('flatpickrInd'),{
        enableTime: false,
        dateFormat: 'Y-m-d',
        locale: {
            firstDateofWeek:1,
            weekdays: {
                shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                longhand: [
                "Domingo",
                "Lunes",
                "Martes",
                "Miércoles",
                "Jueves",
                "Viernes",
                "Sábado",
                ],
            },    
            months: {
                shorthand: [
                "Ene",
                "Feb",
                "Mar",
                "Abr",
                "May",
                "Jun",
                "Jul",
                "Ago",
                "Sep",
                "Oct",
                "Nov",
                "Dic",
                ],
                longhand: [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre",
                ],
            }
        },
        onChange: function(selectedDate, dateStr, instance) {
            @this.emit('dateSelected', selectedDate);
        }
    })
}
function initializeDraggFunction()
{
    eventData = null;
    let elementosArrastrables = document.querySelectorAll('.draggable');
    
    elementosArrastrables.forEach(function(elemento) {
        elemento.removeEventListener('dragstart', comenzarArrastre);
    });
    
    elementosArrastrables.forEach(function(elemento) {
        elemento.addEventListener('dragstart', function(event) {
            eventData = comenzarArrastre(event);
        });
    });

    let contenedorCasilla = document.querySelectorAll('.casilla')
    contenedorCasilla.forEach(function(elemento) {
        elemento.addEventListener('dragover', permitirSoltar);
        elemento.addEventListener('drop',function(event){
            soltarElemento(event,eventData)
        })
    });
}

function comenzarArrastre(event) {
    // Obtener el elemento con la clase ventana-emergente
    var ventanaEmergente = document.querySelector('.ventana-emergente');

    // Aplicar la propiedad visibility: hidden
    ventanaEmergente.style.opacity = 0;

    var eventData = event.target.getAttribute('value')
    return eventData

}

function soltarElemento(event,eventData) {
    let casillaData = event.target.getAttribute('value');
    console.log(casillaData)
    Livewire.emit('setCitaDragged', casillaData,eventData);
}

function permitirSoltar(event) {
    event.preventDefault();
    event.target.style.background = "#EFE3D3"

    event.target.addEventListener('dragleave', function() {
        event.target.style.background = ""; // Cambiar al color original al salir del área
    });
}
</script>
@endpush
