<div id="calendar-master">
    <div class="card" style="font-size: small; min-height: 2rem;overflow:hidden">
        <div class="row">
            <div class="col-12" style="height: fit-content;">
                <div style="width: 100%; margin: 0; padding: 0;">
                    <div style="padding: 1rem;">
                        <div style="transform: translate(0%,-7%);">
                            <div class="row-1" id="controls">
                                <button class="button-style mr-0" wire:click="prevDay()" title="Ajusta la agenda al día anterior"><</button>
                                <button class="button-style ml-0" wire:click="nextDay()" title="Ajusta la agenda al día siguiente">></button>      
                                <button id="hoy" class="button-style wider float-right" wire:click="loadFecha()" title="Usa este botón para ajustar la fecha de agenda al día {{ $currentDate }}">Hoy</button>                  
                                
                                <div id="empleados" class="d-flex employee-tags">
                                    @if($empleados!=null)    
                                        @foreach($empleados as $empleado)
                                            <div class="tag" title="{{ $empleado->first_name }} {{ $empleado->last_name }}">
                                            @php
                                                $nameParts = explode(' ', trim($empleado->first_name));
                                                $initials = count($nameParts) > 1
                                                    ? substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1)
                                                    : substr($nameParts[0], 0, 1); // Solo la inicial del primer nombre si no hay segundo nombre
                                            @endphp

                                            <input class="tag-name remove-tag {{ $empleado->is_active ? '' : 'line-t' }}" 
                                                style="font-weight: 300;font-size:medium" 
                                                type="button" 
                                                wire:click="$emit('filterEmployee', '{{ $empleado->id }}' )" 
                                                value="{{ $initials }}{{ $empleado->last_name ? '' . substr($empleado->last_name, 0, 1) : '' }}">
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                
                                <div wire:model="currentDateC" id="flatResource" class="flatpickrInd flatpickrArea" wire:change.prevent="dateSelected">
                                    <h5 class="center mt-2 ml-4 mr-4 float-right " id="flatCalendar" style="justify-content: space-between;transform: translate(10px, 10px);" >{{ $currentDate }}</h5>
                                </div>

                                <div class="m-auto">
                                    <a href="{{ route('informe-movimientos',1) }}" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4" title="Accede al módulo de transacciones">Mostrar historial</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- AGENDA STARTS -->

                    <div style="display: none;">

                    <!-- Cargar eventos -->
                    @foreach($empleados as $empleado)
                        @if($empleado->is_active==1)
                            @if(isset($citas))
                                @foreach($citas[$empleado->id] as $cita)
                                    @if(isset($cita['start']))
                                        <div class="evento draggable" draggable="true" data-id="{{ $cita->id }}" data-start="{{ $cita['start']->format('H:i') }}" data-duration="{{$cita['duration']}}" data-type-event="cita" wire:click="setAsignacion({{ $cita->id }})" value="{{ $cita->id }}" id="evento-{{ $cita->id }}" data-type_date="{{ $cita->type_date ?? 0 }}" data-employee="{{ $empleado->id }}" data-toggle="popover" data-trigger="hover" data-content="
                                        
                                        <div class='icons-data-popover'>
                                            <svg
                                            xmlns='http://www.w3.org/2000/svg'
                                            width='32'
                                            height='32'
                                            viewBox='0 0 24 24'
                                            fill='none'
                                            stroke='#000000'
                                            stroke-width='1'
                                            stroke-linecap='round'
                                            stroke-linejoin='round'
                                            >
                                            <path d='M12 13m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0' />
                                            <path d='M12 10l0 3l2 0' />
                                            <path d='M7 4l-2.75 2' />
                                            <path d='M17 4l2.75 2' />
                                            </svg>
                                            {{ Carbon\Carbon::parse($cita['start'])->format('H:i') }}-{{ Carbon\Carbon::parse($cita['end'])->format('H:i') }} hrs.
                                        </div>
                                        <hr>
                                        <div class='icons-data-popover'>
                                            <svg
                                            xmlns='http://www.w3.org/2000/svg'
                                            width='32'
                                            height='32'
                                            viewBox='0 0 24 24'
                                            fill='none'
                                            stroke='#000000'
                                            stroke-width='1'
                                            stroke-linecap='round'
                                            stroke-linejoin='round'
                                            >
                                            <path d='M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0' />
                                            <path d='M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2' />
                                            </svg>

                                            {{ isset($cita->date->customer) ? $cita->date->customer->first_name : 'Cliente eliminado' }} {{ isset($cita->date->customer) ? $cita->date->customer->last_name : '' }}
                                        </div>
                                        <br>
                                        {!! $cita->categorias_cliente ? $cita->categorias_cliente . '<br>' : '' !!}
                                        <hr>
                                        <div class='icons-data-popover'>
                                            <svg
                                            xmlns='http://www.w3.org/2000/svg'
                                            width='32'
                                            height='32'
                                            viewBox='0 0 24 24'
                                            fill='none'
                                            stroke='#000000'
                                            stroke-width='1'
                                            stroke-linecap='round'
                                            stroke-linejoin='round'
                                            >
                                            <path d='M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2' />
                                            <path d='M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z' />
                                            <path d='M9 14h.01' />
                                            <path d='M9 17h.01' />
                                            <path d='M12 16l1 1l3 -3' />
                                            </svg>
                                            {!! $cita->title !!}
                                        </div>
                                        <br>
                                        {!! $cita->date->description ? "<hr>
                                        <div class='icons-data-popover'>
                                        <svg
                                        xmlns='http://www.w3.org/2000/svg'
                                        width='32'
                                        height='32'
                                        viewBox='0 0 24 24'
                                        fill='none'
                                        stroke='#000000'
                                        stroke-width='1'
                                        stroke-linecap='round'
                                        stroke-linejoin='round'
                                        >
                                        <path d='M13 20l7 -7' />
                                        <path d='M13 20v-6a1 1 0 0 1 1 -1h6v-7a2 2 0 0 0 -2 -2h-12a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7' />
                                        </svg>" . $cita->date->description . '</div>' : '' !!}
                                        <hr>
                                        
                                        @foreach ($cita->tags as $tag)
                                            <span class='badge' style='background-color: {{ $tag->color }}; color: white;'>
                                                {{ $tag->name }}
                                            </span><br>
                                        @endforeach
                                            <span class='float-right'>
                                                Total: ${{ $cita->date->total }}
                                            </span><br>">
                                            <div class="horario-evento" id="header-{{$cita->id}}" style="background-color:{{ $cita->date->status == 'Pagada' ? '#63686C' : ($cita->date->status=='Cancelada' ? '#e63946':$cita->color) }};">{{ Carbon\Carbon::parse($cita['start'])->format('H:i') }} - {{ Carbon\Carbon::parse($cita['end'])->format('H:i') }}</div>
                                            <div class="description detalles">
                                                <div>
                                                    @if($cita->date->status==='Cancelada')
                                                        <strong style="color:#e63946;">Cita cancelada</strong>
                                                        <span>{{ $cita->date->motivoCancelacion }}</span>
                                                        <br>
                                                    @endif
                                                    <span class="customer-name">
                                                        {{ isset($cita->date->customer) ? $cita->date->customer->first_name : 'Cliente eliminado' }}
                                                    </span>
                                                </div>
                                                <div class="tags">
                                                    @foreach($cita->tags as $tag)
                                                        <div class='badge bg-{{ $tag->color }} text-white' style="background-color: {{ $tag->color }};">{{ $tag->name }}</div>
                                                    @endforeach
                                                </div>
                                                @if($cita->date->description!==null)
                                                <div>
                                                    {{ $cita->date->description }}
                                                </div>
                                                @endif
                                                <div>
                                                    {!! $cita->title !!}
                                                </div>
                                            </div>
                                            <div class="cambia-tamanio"></div>
                                        </div>
                                        @include('livewire.calendar.dropdown.color')
                                    @endif
                                @endforeach
                            @endif
                            @if(isset($bloqueos))
                                @foreach($bloqueos[$empleado->id] as $cita)
                                    @if(isset($cita['start']))
                                        <div class="evento draggable" draggable="true" data-id="{{ $cita->id }}" data-start="{{ $cita['start']->format('H:i') }}" data-duration="{{$cita['duration']}}" data-type-event="bloqueo" wire:click="setBlockId({{ $cita->id }})" value="{{ $cita->id }}" id="evento-{{ $cita->id }}" data-employee="{{ $empleado->id }}" data-toggle="popover" data-trigger="hover" data-content="
                                        <div class='icons-data-popover'>
                                            <svg
                                            xmlns='http://www.w3.org/2000/svg'
                                            width='32'
                                            height='32'
                                            viewBox='0 0 24 24'
                                            fill='none'
                                            stroke='#000000'
                                            stroke-width='1'
                                            stroke-linecap='round'
                                            stroke-linejoin='round'
                                            >
                                            <path d='M12 13m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0' />
                                            <path d='M12 10l0 3l2 0' />
                                            <path d='M7 4l-2.75 2' />
                                            <path d='M17 4l2.75 2' />
                                            </svg>
                                            {{ Carbon\Carbon::parse($cita['start'])->format('H:i') }}-{{ Carbon\Carbon::parse($cita['end'])->format('H:i') }}
                                        </div>
                                        <hr>
                                        <div class='icons-data-popover'>
                                            <svg
                                            xmlns='http://www.w3.org/2000/svg'
                                            width='32'
                                            height='32'
                                            viewBox='0 0 24 24'
                                            fill='none'
                                            stroke='#000000'
                                            stroke-width='1'
                                            stroke-linecap='round'
                                            stroke-linejoin='round'
                                            >
                                            <path d='M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z' />
                                            <path d='M8 11m0 1a1 1 0 0 1 1 -1h6a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-6a1 1 0 0 1 -1 -1z' />
                                            <path d='M10 11v-2a2 2 0 1 1 4 0v2' />
                                            </svg>
                                            {!! $cita->title != '' ? $cita->title : 'Sin motivo' !!}
                                        </div>">
                                            <div class="horario-evento" id="header-{{$cita->id}}" style="background-color:{{ $cita->color }};">{{ Carbon\Carbon::parse($cita['start'])->format('H:i') }} - {{ Carbon\Carbon::parse($cita['end'])->format('H:i') }}</div>
                                            <div class="description detalles">
                                                <div>
                                                    Bloqueo: {!! $cita->title != '' ? $cita->title : 'Sin motivo' !!}
                                                </div>
                                            </div>
                                            <div class="cambia-tamanio"></div>
                                        </div>
                                        @include('livewire.calendar.dropdown.color-block')
                                    @endif
                                @endforeach
                            @endif
                        @endif
                    @endforeach
                    </div>
                     
                    <!-- Cargar vista del calendario -->
                    <div class="vista-empleados-tiempos">
                        <div class="calendario">
                            <div class="tabla-calendario"  id="agenda">
                                <div class="vista table-responsive">
                                    <table>
                                        <thead style="display:table-header">
                                            <tr class="text-center" >
                                                <th class="sticky-left"></th>
                                                @if($empleados!=null)
                                                    @foreach($empleados as $empleado)
                                                        @if($empleado->is_active)
                                                            <th class="sticky-top" style="font-weight:300;font-size:medium;height:auto;cursor:default">{{ $empleado->first_name }} {{ $empleado->last_name }}</th>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($times as $hora)
                                                <tr style="background-color:{{ in_array($hora,$salonTimes) ? '' : '#E9EAEC' }};border-top:{{ \Carbon\Carbon::parse($hora)->format('i') != '00' ? '' : '2px solid #e5e5e5' }}">
                                                    <td class="sticky-left cell-hour">{{ \Carbon\Carbon::parse($hora)->format('i') != '00' ? '' : $hora}}</td>
                                                    @if($empleados!=null)
                                                        @foreach($empleados as $empleado)
                                                            @if($empleado->is_active)
                                                                <td class="casilla trigger cell  {{ in_array($hora, $salonTimes) ? 'hora-disponible' : '' }}" title="Crea una cita a las {{ $hora  }} con {{ $empleado->first_name }} {{ $empleado->last_name }}" value="('{{ $hora }}', '{{ $empleado->id }}')" data-hour="{{ $hora }}" data-employee="{{ $empleado->id }}"  onclick="openOptionsMenu('option{{$empleado->id.$hora}}')" id="casilla">
                                                                    <div id="option{{$empleado->id.$hora}}" class="options-menu">
                                                                        <a wire:click="crearCita('{{ $hora }}','{{ $empleado->id }}')">Crear cita</a>
                                                                        <a wire:click="crearBloqueo('{{ $hora }}','{{ $empleado->id }}')">Bloquear horario</a>
                                                                    </div>
                                                                </td>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AGENDA ENDS -->

                </div>
            
            </div>
        </div>
    </div>
</div>
@include('livewire.calendar.modal.blockMenuForm')

@include('livewire.calendar.js')
@include('livewire.calendar.resource-styles')

@push('my-scripts')
<script>
function abrirBlockMenuForm() {
    $('#modalBlockForm').modal('show')
}
window.addEventListener('abrirBlockMenuForm', event => {
    abrirBlockMenuForm()
});
function cerrarBlockMenuForm() {
    $('#modalBlockForm').modal('hide')
}
window.addEventListener('cerrarBlockMenuForm', event => {
    cerrarBlockMenuForm()
});
function closeAllOptionsMenus() {
    const allOptionsMenus = document.querySelectorAll('.options-menu');
    allOptionsMenus.forEach(menu => {
        menu.classList.remove('options-menu-wrapped');
        menu.style.display = 'none';
    });
}

function openOptionsMenu(id) {
    // Cierra todos los demás menús
    closeAllOptionsMenus();

    // Abre/cierra el menú actual
    const optionsMenu = document.getElementById(id);

    if (optionsMenu.classList.contains('options-menu-wrapped')) {
        // Si ya está abierto ciérralo
        optionsMenu.classList.remove('options-menu-wrapped');
        optionsMenu.style.display = 'none';
    } else {
        // Si está cerrado ábrelo
        optionsMenu.classList.add('options-menu-wrapped');
        optionsMenu.style.display = 'flex';
    }

}
function toggleDropdown(event, trigger) {
    event.preventDefault(); // Previene el comportamiento predeterminado
    event.stopPropagation(); // Detiene la propagación del evento

    // Encuentra el menú dropdown asociado
    const dropdownMenu = trigger.nextElementSibling; // Selecciona el siguiente elemento hermano (el menú)
    
    if (dropdownMenu.classList.contains('show')) {
        dropdownMenu.classList.remove('show');
    } else {
        dropdownMenu.classList.add('show');
    }
}

let isResizing = false;
let isDragging = false;
let eventoActual;
document.addEventListener('DOMContentLoaded', function(){

    $('#modalEmpl').modal('show')
    initializeFlatpckr();
    initializePopper()
    initializeRightClickMenu()
    
    detectOverlaps()
    initializeDraggFunction();
    positionEvents();
    initializeResizer();

    destroyPopover();
    horarioDisponible();
})

document.addEventListener('keydown', function(event) {
    // Comprobar si el elemento activo es un input o textarea
    if (document.activeElement.tagName === 'INPUT' || document.activeElement.tagName === 'TEXTAREA') {
        return; // No hacer nada si se está escribiendo en un input
    }
    destroyPopover()
    switch(event.key) {

        // Detectar atajo, por ejemplo: Ctrl + K
        case 'n':
            event.preventDefault();
            Livewire.emit('teclaC');  // Emitir evento de Livewire
            break;
        case "ArrowLeft":
            event.preventDefault();
            Livewire.emit('teclaLeft');  // Emitir evento de Livewire
            break;
        case "ArrowRight":
            event.preventDefault();
            Livewire.emit('teclaRight');  // Emitir evento de Livewire
            break;
        case "ArrowUp":
            event.preventDefault();
            Livewire.emit('teclaUp');  // Emitir evento de Livewire
            break;
        case "ArrowDown":
            event.preventDefault();
            Livewire.emit('teclaDown');  // Emitir evento de Livewire
            break;
        case 't':
            event.preventDefault();
            Livewire.emit('teclaT');  // Emitir evento de Livewire
            break;
        case 'Escape':
            document.getElementById('closeNewDate').click();
            break;
        case 'f':
            document.getElementById('flatCalendar').click();
            break;
        case '1':
            changeTo(1)
            break;
        case '2':
            changeTo(2)
            break;
        case '3':
            changeTo(5)
            break;
        case '4':
            changeTo(6)
            break;
    }
    destroyPopover()
});

//cerrar modal de agregar cliente
window.addEventListener('close-popover', event => {
    destroyPopover()
})

function destroyPopoverClass()
{
    destroyPopover()
}

function destroyPopover()
{
    var popoverElements = document.querySelectorAll('.popover');
    popoverElements.forEach(function(element) {
        element.remove();
    });
}
function initializeRightClickMenu()
{
    const eventos = document.querySelectorAll('.evento');

    eventos.forEach(evento => {
      evento.addEventListener('contextmenu', function (e) {
        e.preventDefault(); // Prevenir el menú contextual por defecto

        // Ocultar cualquier menú abierto previamente
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
        destroyPopover()
        // Mostrar el menú correspondiente al área clicada
        const menuId = evento.dataset.id;
        const menu = document.getElementById('menu'+menuId);

        if (menu) {
            // Mostrar el menú
            menu.classList.add('show');
        }
      });
    });
    
}

function updateBackgroundColor(citaId, colorValue) {
    // Encontrar el contenedor .event-header asociado con este id
    const eventHeader = document.getElementById(`header-${citaId}`);
    
    // Si encontramos el contenedor, actualizamos el color de fondo
    if (eventHeader) {
        eventHeader.style.backgroundColor = colorValue;
    }
}

document.addEventListener('livewire:load', function () {
    Livewire.on('recargarFlat', function () {
        initializeRightClickMenu()
        initializeFlatpckr()
        
        detectOverlaps()
        initializeDraggFunction();
        positionEvents();
        initializeResizer();

        destroyPopover();
        horarioDisponible();
    })
    Livewire.on('reloadDragg',function(){
        initializeRightClickMenu()
        initializePopper()
        
        detectOverlaps()
        initializeDraggFunction();
        positionEvents();
        initializeResizer();

        destroyPopover();
        horarioDisponible();
    })
})



function initializePopper(){
    $('[data-toggle="popover"]').popover({
        html: true,
        sanitize:false,
        fallbackPlacements: ['left', 'right'],
    });
}

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
    })
}

function positionEvents() {
    const eventos = document.querySelectorAll('.evento');

    // Función para redondear una hora al cuarto de hora más cercano
    function roundToQuarterHour(time) {
        const [hour, minute] = time.split(':').map(Number); // Divide en horas y minutos
        const roundedMinute = Math.round(minute / 15) * 15; // Redondea al múltiplo de 15 más cercano

        // Ajustar las horas si el redondeo supera los 60 minutos
        const finalHour = roundedMinute === 60 ? hour + 1 : hour;
        const finalMinute = roundedMinute === 60 ? 0 : roundedMinute;

        // Asegurar el formato HH:mm
        const paddedHour = String(finalHour).padStart(2, '0');
        const paddedMinute = String(finalMinute).padStart(2, '0');
        return `${paddedHour}:${paddedMinute}`;
    }

    eventos.forEach(evento => {
      const start = evento.dataset.start; 
      const duration = evento.dataset.duration; 
      const employee = evento.dataset.employee;

    // Redondear la hora al cuarto de hora más cercano
    const roundedStart = roundToQuarterHour(start);

      // Buscar la celda de inicio basada en data-hour y data-employee
      const matchingCell = document.querySelector(
        `.cell[data-hour="${roundedStart}"][data-employee="${employee}"]`
      );

      if (matchingCell) {
        // Posicionar el evento dentro de la celda
        matchingCell.appendChild(evento);
        
        // Mostrar el menú correspondiente al área clicada
        const menuId = evento.dataset.id;
        const dropdown = document.getElementById('menu'+menuId);

        matchingCell.appendChild(dropdown);

        // Ajustar altura y top relativo a la celda
        const height = (duration/15)*16;
        if(duration<27){
          evento.style.display = 'flex';
        }else{
          evento.style.display = 'inline-block';
        }
        evento.style.height = `${height}px`;
        evento.style.position = 'absolute'; // Importante para permitir posicionamiento relativo
        evento.style.width = '90%'; // Para que ocupe todo el ancho de la celda
      } else {
        console.warn(`No se encontró celda para el evento: ${start} - ${employee}`);
      }
    });

    detectOverlaps();
  }
  function initializeResizer() {
    const eventos = document.querySelectorAll('.evento');
  
    eventos.forEach(evento => {
      const resizer = evento.querySelector('.cambia-tamanio');
  
      let initialY = 0; // Posición inicial del mouse
      let initialHeight = 0; // Altura inicial del evento
      let isResizing = false; // Marcar si estamos redimensionando en este evento
  
      resizer.addEventListener('mousedown', initDrag, false);
  
      function initDrag(e) {
        e.preventDefault();
        isResizing = true; // Marcar como redimensionando
        evento.setAttribute('draggable', false);
        initialY = e.clientY;
        document.body.style.userSelect = 'none';
        initialHeight = parseInt(document.defaultView.getComputedStyle(evento).height, 10);
        document.documentElement.addEventListener('mousemove', doDrag, false);
        document.documentElement.addEventListener('mouseup', stopDrag, false);
      }
  
      function doDrag(e) {
        if (!isResizing) return;
  
        // Calcula el cambio en la posición del mouse
        const deltaY = e.clientY - initialY;
  
        // Calcula el número de bloques de 18px (15 minutos) según el delta
        const blockHeight = 18; // Cada bloque equivale a 15 minutos
        const newHeight = initialHeight + Math.round(deltaY / blockHeight) * blockHeight;
  
        // Asegura un tamaño mínimo y máximo
        if (newHeight >= blockHeight) { // Por ejemplo, 15 minutos mínimo
          evento.style.height = `${newHeight}px`;
  
          // Actualiza dinámicamente la duración en base al nuevo tamaño
          const duration = Math.round(newHeight / blockHeight) * 15; // Duración en minutos
          evento.setAttribute('data-duration', duration);
  
          // Opcional: Actualiza el texto con el nuevo rango horario
          const start = evento.getAttribute('data-start');
          const end = calculateEndTime(start, duration); // Función auxiliar para calcular el fin
          evento.querySelector('.horario-evento').textContent = `${start} - ${end}`;
          positionEvents();
        }
      }
  
      function stopDrag() {
        if (isResizing) {
          isResizing = false;
          evento.setAttribute('draggable', true); // Reactiva el atributo draggable
          document.body.style.userSelect = ''; 
            console.log(evento.dataset.type_date);
          Livewire.emit('updateDuration', evento.dataset.duration,evento.dataset.id,evento.dataset.typeEvent,evento.dataset.type_date);
        }
  
        // Elimina los listeners para evitar que el redimensionamiento de un evento afecte a otros
        document.documentElement.removeEventListener('mousemove', doDrag, false);
        document.documentElement.removeEventListener('mouseup', stopDrag, false);
      }
    });
  }
  
  
// Función auxiliar para calcular el horario de fin
function calculateEndTime(start, duration) {
    const [hours, minutes] = start.split(':').map(Number);
    const totalMinutes = hours * 60 + minutes + duration;

    const endHours = Math.floor(totalMinutes / 60);
    const endMinutes = totalMinutes % 60;

    // Retorna el horario en formato "HH:MM"
    return `${endHours.toString().padStart(2, '0')}:${endMinutes.toString().padStart(2, '0')}`;
}

function initializeDraggFunction() {
    let eventData = null;
    let eventBox = null;
    let elementosArrastrables = document.querySelectorAll('.draggable');
    let contenedorCasilla = document.querySelectorAll('.cell');

    elementosArrastrables.forEach(function(elemento) {
        // Eliminar cualquier evento previo para evitar duplicados
        elemento.removeEventListener('dragstart', comenzarArrastreHandler);
        elemento.addEventListener('dragstart', comenzarArrastreHandler);
    });

    contenedorCasilla.forEach(function(elemento) {
        // Eliminar cualquier evento previo para evitar duplicados
        elemento.removeEventListener('dragover', permitirSoltar);
        elemento.removeEventListener('drop', soltarElementoHandler);

        elemento.addEventListener('dragover', permitirSoltar);
        elemento.addEventListener('drop', soltarElementoHandler);
    });
    
    // Listener global para el evento 'dragover' en todo el documento
    document.addEventListener('dragover', function(event) {
        event.preventDefault(); // Necesario para permitir que el drop funcione en cualquier lugar
    });
    
    // Listener global para 'drop' en cualquier parte del documento
    document.addEventListener('drop', function(event) {
        // Prevenir el comportamiento por defecto
        event.preventDefault();
        event.stopPropagation();

        // Si el target no es una casilla ni un hijo de una casilla, manejar el drop fuera de una casilla
        if (!event.target.classList.contains('cell')) {

            // Livewire.emit('cancelarCaptura')

            // Aquí puedes agregar cualquier lógica adicional para manejar el drop fuera de una casilla
        }
    });
}

function comenzarArrastreHandler(event) {
    destroyPopover()
    if (isResizing) {
        event.preventDefault(); // Evita que el evento dragstart continúe si estás redimensionando
        return;
    }
    isDragging = true;
    eventData = comenzarArrastre(event);
    eventBox = event.target;
}


function soltarElementoHandler(event) {
    let targetCell = event.target.closest('.cell');
    if (!targetCell) return; // Asegúrate de que sea una celda válida

    soltarElemento(event, eventData);
    isDragging = false;
}

function comenzarArrastre(event) {
    eventData = {
        startTime: event.target.getAttribute('data-start'),
        duration: event.target.getAttribute('data-duration'),
        employee: event.target.getAttribute('data-employee'),
        type: event.target.getAttribute('data-type-event'),
        type_date: event.target.getAttribute('data-type_date'),
        id: event.target.getAttribute('data-id'),
    };

    return eventData;
}

function soltarElemento(event,eventData) {
    let casillaData = event.target.getAttribute('value');
    let targetCell = event.target.closest('.cell');

    if (targetCell) {
        targetCell.classList.remove('drag-over');
    }

    
    Livewire.emit('setCitaDragged', casillaData, eventData);
}

function permitirSoltar(event) {
    event.preventDefault();
    
    let targetCell = event.target.closest('.cell');
    if (targetCell) {
        targetCell.classList.add('drag-over');
        
        eventBox.querySelector('.horario-evento').textContent = `${event.target.dataset.hour}`;
    }
    // Listener para restaurar el estado original al salir del área
    event.target.addEventListener('dragleave', function handleDragLeave() {
        let targetCell = event.target.closest('.cell');
        if (targetCell) {
            targetCell.classList.remove('drag-over');
        }
    });
}

function detectOverlaps() {
    const appointments = Array.from(document.querySelectorAll('.evento'));

    // Limpiar clases y estilos previos (incluye propiedades !important inline)
    appointments.forEach(app => {
        app.classList.remove('overlap', 'left');
        app.style.removeProperty('width');
        app.style.removeProperty('left');
        app.style.removeProperty('z-index');
    });

    // Agrupar por empleado
    const byEmployee = {};
    appointments.forEach(app => {
        const emp = app.dataset.employee || 'no-emp';
        if (!byEmployee[emp]) byEmployee[emp] = [];
        byEmployee[emp].push(app);
    });

    // Convierte "HH:mm" o "HH:mm:ss" a segundos totales
    const timeToSeconds = (timeStr) => {
        if (!timeStr) return NaN;
        const parts = String(timeStr).trim().split(':').map(p => parseInt(p, 10) || 0);
        const h = parts[0] || 0;
        const m = parts[1] || 0;
        const s = parts[2] || 0;
        return h * 3600 + m * 60 + s;
    };

    // Convierte a minutos (redondeando al minuto más cercano)
    const toMinutes = (timeStr) => Math.round(timeToSeconds(timeStr) / 60);

    const durMinutes = d => {
        if (d === undefined || d === null) return 0;
        const f = parseFloat(String(d).trim());
        if (Number.isNaN(f)) return 0;
        return Math.round(f);
    };

    const endFor = app => {
        const startMin = toMinutes(app.dataset.start);
        const duration = durMinutes(app.dataset.duration);
        return startMin + duration;
    };

    // Procesar por empleado
    Object.values(byEmployee).forEach(list => {
        // Ordenar por inicio
        list.sort((a, b) => {
            const sa = toMinutes(a.dataset.start) || 0;
            const sb = toMinutes(b.dataset.start) || 0;
            return sa - sb;
        });

        let group = [];
        let currentEnd = -1;

        const flushGroup = () => {
            if (group.length <= 1) {
                const a = group[0];
                a.style.setProperty('width', '90%', 'important');

                group = [];
                return;
            }

            if (group.length === 2) {
                const a = group[0], b = group[1];
                const da = durMinutes(a.dataset.duration), db = durMinutes(b.dataset.duration);
                const shorter = da <= db ? a : b;

                // Aplicar estilos inline con prioridad para sobreescribir CSS (.evento.overlap !important)
                a.classList.add('overlap');

                a.style.setProperty('width', '90%', 'important');
                a.style.setProperty('left', '0%', 'important');
                a.style.setProperty('z-index', '100', 'important');

                b.style.setProperty('width', '45%', 'important');
                b.style.setProperty('left', '45%', 'important');
                b.style.setProperty('z-index', '101', 'important');

                // La más corta recibe también 'left' (la clase no afectará el left porque inline !important tiene precedencia)
                shorter.classList.add('left');
            } else {
                // Más de dos citas -> repartir ancho y espacio equidistante
                const n = group.length;
                const widthPercent = 90 / n;
                group.forEach((evt, idx) => {
                    evt.classList.remove('overlap', 'left');
                    evt.style.setProperty('width', `${widthPercent}%`, 'important');
                    evt.style.setProperty('left', `${idx * widthPercent}%`, 'important');
                    evt.style.setProperty('z-index', `${100 + idx}`, 'important');
                });
            }

            group = [];
        };

        // Construir grupos por solapamiento (start < currentEnd => solapa; start === currentEnd NO solapa)
        for (let i = 0; i < list.length; i++) {
            const app = list[i];
            const start = toMinutes(app.dataset.start);
            const end = endFor(app);

            if (Number.isNaN(start) || Number.isNaN(end)) {
                continue;
            }

            if (group.length === 0) {
                group.push(app);
                currentEnd = end;
            } else {
                // Si empieza estrictamente antes del final actual => solapamiento
                // start === currentEnd se considera adyacente (NO solapa)
                if (start < currentEnd) {
                    group.push(app);
                    currentEnd = Math.max(currentEnd, end);
                } else {
                    // No solapan -> procesar grupo previo y empezar nuevo
                    flushGroup();
                    group.push(app);
                    currentEnd = end;
                }
            }
        }

        // Procesar último grupo restante
        flushGroup();
    });
}

function parseTime(start, end) {
  const toMinutes = (time) => {
      const [hours, minutes] = time.split(':').map(Number);
      return hours * 60 + minutes;
  };

  const startMinutes = toMinutes(start);
  const endMinutes = toMinutes(end);

  // Validar que el tiempo de fin no sea menor que el tiempo de inicio
  if (endMinutes < startMinutes) {
      console.error(`Error: El tiempo de fin (${end}) no puede ser menor que el de inicio (${start}).`);
  }

  return [startMinutes, endMinutes];
}

function horarioDisponible(){
    // Selecciona el tbody con scroll
    const tbody = document.querySelector('.table-responsive');

    // Busca la primera celda disponible
    const firstAvailableCell = tbody.querySelector('.hora-disponible');

    if (tbody && firstAvailableCell) {
        // Calcula la posición de la celda disponible relativa al tbody
        const offsetTop = firstAvailableCell.offsetTop - tbody.offsetTop;

        // Ajusta el scroll interno del tbody, sumando un margen adicional (por ejemplo, el tamaño de 6 celdas)
        const cellHeight = firstAvailableCell.offsetHeight; // Altura de una celda
        tbody.scrollTop = offsetTop - (-5.5 * cellHeight); // Ajusta hacia arriba
    }

}
</script>
@endpush
