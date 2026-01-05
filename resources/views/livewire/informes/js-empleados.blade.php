@push('my-scripts')
    
<script>
var mychart1;
$(document).ready(function(){
    var eData = JSON.parse('<?php echo $dataDurationDates; ?>')
    
    const ctv = document.getElementById('chart-durations').getContext('2d');

    const config ={
        options:{
            responsive: true,
            maintainAspectRatio: true, 
        }      
    }
    mychart1 = new Chart(ctv,{
        type:'pie',
        data:{
            labels:eData.label,
            datasets:[{
                data:eData.qty,
                backgroundColor:[
                    '#6f42c1', '#e83e8c', '#e63946', '#1d3557', '#278d46',
                    '#3A82EF', '#FFAB2D','#42B6C1','#C19542'
                ],
            }],
            hoverOffset: 4
        },
        options:{
            responsive: true,
            maintainAspectRatio: true, 
        }  
    });    
})

function addData(chart, labels,newData) {
    for (let i = 0; i < labels.length; i++) {
        chart.data.labels.push(labels[i]);
        chart.data.datasets.forEach((dataset) => {
            dataset.data.push(newData[i]);
        });
    }
    chart.update();
}
    
function removeData(chart) {
    // Eliminar todos los labels
    while (chart.data.labels.length > 0) {
        chart.data.labels.pop();
    }
    
    // Eliminar todos los datos de cada dataset
    chart.data.datasets.forEach((dataset) => {
        while (dataset.data.length > 0) {
            dataset.data.pop();
        }
    });
    
    chart.update();
}
document.addEventListener('livewire:load', function () {
    
    Livewire.on('reloadCharts',data =>{
        var parsedDataDurationDates=JSON.parse(data.dataDurationDates)
        removeData(mychart1)
        addData(mychart1,parsedDataDurationDates.label,parsedDataDurationDates.qty)
    })

    Livewire.on('dateUpdated-empleados', function (newDate,newDateEnd) {
        // Actualizar el contenido donde se muestra la fecha
        document.getElementById('currentDate').innerText = newDate;
        if(newDateEnd!==''){
            document.getElementById('currentDateEnd').innerText = ' - ' + newDateEnd;
        }else{
            document.getElementById('currentDateEnd').innerText = '';
        }
    });
    
    Livewire.on('print', function () {
        var card = document.getElementById('reporte-global')
        var chart = document.getElementById('chart')
        
        card.style.transform = 'translate(-10%, -8%)'
        card.style.zIndex = '1000'
        card.style.fontSize = 'smaller'
        card.style.border = 'none'
        card.style.width = '58rem'
        chart.style.marginTop = '40rem'

        window.print(); 

        card.style.border = '1px solid rgba(0, 0, 0, .125);'
        card.style.fontSize = 'medium'
        card.style.transform = 'translate(0%, 0%)'
        card.style.zIndex = '1'
        card.style.width = 'auto'
        chart.style.marginTop = '0'
    });
})

document.addEventListener('DOMContentLoaded', function(){
    
        flatpickr(document.getElementsByClassName('flatpickr'),{
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
                if (selectedDates.length === 2) {
                    @this.emit('datesSelected', selectedDates);
                }
            }
        })
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
   })
</script>

@endpush