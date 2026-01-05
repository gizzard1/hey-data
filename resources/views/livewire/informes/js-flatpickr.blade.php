

<script>

function initFlats()
{
    flatpickr(document.getElementsByClassName('flatpickr'),{
        mode:"range",
        enableTime: false,
        dateFormat: 'Y-m-d',
        locale: {
            firstDateofWeek:1,
            weekdays: {
                shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                longhand: ["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"],
            },    
            months: {
                shorthand: ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic",
                ],
                longhand: ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
            }
        },
        onClose: function(selectedDates, dateStr, instance) {
            if(selectedDates.length < 2) {
                // Si no se seleccionaron dos fechas, no hacer nada
                return;
            }
            @this.emit('datesSelected', selectedDates);
            showProcessing();
        }
    })
    flatpickr(document.getElementsByClassName('flatpickrInd'),{
        enableTime: false,
        dateFormat: 'Y-m-d',
        locale: {
            firstDateofWeek:1,
            weekdays: {
                shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                longhand: ["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado", ],
            },    
            months: {
                shorthand: ["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"],
                longhand: ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"],
            }
        },
        onChange: function(selectedDate, dateStr, instance) {
            @this.emit('dateSelected', selectedDate);
            showProcessing();
        }
    })
}
</script>