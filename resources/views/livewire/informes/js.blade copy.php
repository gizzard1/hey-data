@push('my-scripts')
<script>
var mychart1, mychart2, mychart3;
$(document).ready(function(){
        var vData = JSON.parse('<?php echo $dataSales; ?>')
        // var gData = JSON.parse('<?php echo $dataExpenses; ?>')
        const ctv = document.getElementById('chart-ventas').getContext('2d');
        // const ctg = document.getElementById('chart-gastos').getContext('2d');

        const config ={
            options:{
                responsive: true,
                maintainAspectRatio: false, 
                layout: {
                    padding: 20
                }
            }      
        }
        mychart1 = new Chart(ctv,{
            type:'pie',
            data:{
                labels:vData.label,
                datasets:[{
                    data:vData.qty,
                    backgroundColor:[
                        '#6f42c1', '#e83e8c', '#e63946', '#1d3557', '#278d46',
                        '#3A82EF', '#FFAB2D'
                    ],
                }],
                hoverOffset: 0
            },
        });
        // mychart2 = new Chart(ctg,{
        //     type:'pie',
        //     data:{
        //         labels:gData.label,
        //         datasets:[{
        //             data:gData.qty,
        //             backgroundColor:[
        //                 '#BFD9C7', '#CED9D3', '#9ED1DC', '#C6D9BD', '#D8D9D3',
        //                 '#ADCDD5', '#CFD9D5', '#B2D9C9', '#CDD9C6', '#E2D9C4',
        //                 '#AFD9C9', '#CDD9BC', '#E1D9D7', '#BAD9CA', '#D5D9C8',
        //                 '#B5D9D2', '#CCD9C6', '#EFD9EF', '#CDD9D9', '#F6D9EB'
        //             ],
        //         }],
        //         hoverOffset: 0
        //     },
        // });
    
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
    Livewire.on('refrescarCharts',data =>{
        var parsedDataSales=JSON.parse(data.dataSales)
        // var parsedDataExpenses=JSON.parse(data.dataExpenses)
        removeData(mychart1)
        // removeData(mychart2)
        addData(mychart1,parsedDataSales.label,parsedDataSales.qty)
        // addData(mychart2,parsedDataExpenses.label,parsedDataExpenses.qty)
    })
    Livewire.on('dateUpdated', function (newDate,newDateEnd,newDataEmpleados) {
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
        var table = document.getElementById('last-table')
        var datosCita = document.getElementById('datos-cita')
        var datosPeriodo = document.getElementById('datos-periodo')
        var tablaMetodos = document.getElementById('tabla-metodos')
        var tablaGastos = document.getElementById('table-gastos')
        var chartGastos = document.getElementById('chart-gastos-marco')
        
        card.style.transform = 'translate(-10%, -8%)'
        card.style.zIndex = '1000'
        card.style.fontSize = 'smaller'
        card.style.border = 'none'
        tablaMetodos.style.display = 'block'
        tablaMetodos.style.textAlign = 'center'
        tablaGastos.style.marginTop = '2rem'
        tablaGastos.style.textAlign = 'center'
        datosCita.style.marginTop = '2rem'
        datosPeriodo.style.marginTop = '2rem'
        datosPeriodo.style.textAlign = 'center'
        chartGastos.style.marginTop = '2rem'
        
        window.print(); 

        card.style.border = '1px solid rgba(0, 0, 0, .125);'
        card.style.fontSize = 'medium'
        tablaMetodos.style.display = 'none'
        datosCita.style.marginTop = '10rem'
        datosPeriodo.style.marginTop = '10rem'
        card.style.transform = 'translate(0%, 0%)'
        card.style.zIndex = '1'
        tablaGastos.style.marginTop = '10rem'
        tablaGastos.style.textAlign = 'left'
        Livewire.emit('loadCharts')
    });
})

document.addEventListener('DOMContentLoaded', function(){
    initFlats();
})
</script>

@endpush