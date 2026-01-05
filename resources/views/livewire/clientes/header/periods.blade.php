<div class="d-flex flatpickrInd">
    <a style="display: contents;" class="">
        @if($allTimes)
        <div>Histórico</div>
        @else
        <div id="currentDate">
        {{ $currentDate }}
        </div>
        <div id="currentDateEnd">
        @if($is_interval) <div> - {{ $currentDateEnd }}</div>@endif
        </div>
        @endif
    </a>
</div>
<div style="width:fit-content;display:inline">
    <i type="button" class="las la-calendar dropdown-toggle" data-toggle="dropdown"></i>
    <div class="dropdown-menu">
        <a class="dropdown-item flatpickr" data-toggle="dropdown">Elegir periodo</a>
        <a style="cursor: pointer;" class="dropdown-item" wire:click="returnToday">Hoy</a>
        <a style="cursor: pointer;" class="dropdown-item" wire:click="returnYesterday">Ayer</a>
        <a style="cursor: pointer;" class="dropdown-item" wire:click="setWeek">Semanal</a>
        <a style="cursor: pointer;" class="dropdown-item" wire:click="setMonth">Mensual</a>
        <a style="cursor: pointer;" class="dropdown-item" wire:click="setYear">Anual</a>
        <a style="cursor: pointer;" class="dropdown-item" wire:click="setAllTimes">Histórico</a>
    </div>
</div>
<script>
   
document.addEventListener('DOMContentLoaded', function () {
    initializeFlatPckr()
})


function initializeFlatPckr(){
    flat = flatpickr('.flatpickr',{
        mode:"range",
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
        onClose: function(selectedDates, dateStr, instance) {
            if(selectedDates.length < 2) {
                // Si no se seleccionaron dos fechas, no hacer nada
                return;
            }
            @this.emit('datesSelected', selectedDates);
        }
        })
        indFlat = flatpickr('.flatpickrInd',{
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

</script>