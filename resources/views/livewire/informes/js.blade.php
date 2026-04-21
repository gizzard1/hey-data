@push('my-scripts')
<script>
    document.addEventListener('livewire:load', function () {
    Livewire.emit('loadSearchBox', 0);
        
    Livewire.on('refrescarCharts',data =>{
        var eData=JSON.parse(data.dataEmpleados);

        var parsedDataSales=JSON.parse(data.dataSales);
        parsedDataSales = transformPaymentData(parsedDataSales); // Transformar al formato correcto

        var parsedDataPeriod=JSON.parse(data.dataPeriodNormalizado);
        // parsedDataPeriod = transformPaymentData(parsedDataPeriod); // Transformar al formato correcto

        serviceData=JSON.parse(data.ranking_services);
        categoryData=JSON.parse(data.ranking_categories);
        var dataRanking = currentView === 'service' ? serviceData : categoryData;
        // var parsedDataExpenses=JSON.parse(data.dataExpenses)
        // removeData(mychart1)
        // removeData(mychart2)
        // addData(mychart1,parsedDataSales.label,parsedDataSales.qty)
        // addData(mychart2,parsedDataExpenses.label,parsedDataExpenses.qty)
        drawDonut('donutChart', parsedDataSales);
        renderLegend('legend', parsedDataSales);
        
        drawBarChart('barChart', parsedDataPeriod);

        renderTable(dataRanking);
        showDatesByStatus('Agendada');
        
        drawDonut('donutChartEmployees', eData, true);
        renderLegend('legendEmployees', eData, true);
        renderTableEmployees(eData);
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

function transformPaymentData(data) {
  if (!Array.isArray(data)) {
    // Transformar el objeto con arrays en array de objetos
    return data.label.map((label, index) => ({
      id: data.index ? data.index[index] : index + 1,
      label: label,
      percent: parseFloat(data.percent[index]) * 100, // Convertir a porcentaje
      color: data.color[index],
      amount: parseFloat(data.amount[index].replace(/,/g, '')) // Remover comas
    }));
  }
  return data;
}

// Estructura global para rastrear métodos de pago expandidos
const paymentMethodsState = {
    expandedSecondary: {}
};

const NUM_PRIMARY_METHODS = 5; // Los primeros 5 conceptos son primarios

function separatePaymentMethods(data, useAllData) {
  const primary = [];
  const secondary = [];
  let fourthItemForSecondary = null;

  // Filtrar items válidos y separarlos por posición
  const validItems = data.filter(item => item.label !== 'Total' && item.amount !== 0);

  validItems.forEach((item, index) => {
    if (item.id <= NUM_PRIMARY_METHODS || useAllData) {
      // Los primeros 5 son primarios
      primary.push(item);
      // El 4to item puede contener los secundarios
      if (item.id === NUM_PRIMARY_METHODS && !useAllData) {
        fourthItemForSecondary = item;
      }
    } else {
      // El resto son secundarios
      secondary.push(item);
    }
  });
  
  // Agrupar métodos secundarios bajo el 4to item (Otros)
  if (secondary.length > 0 && fourthItemForSecondary) {
    fourthItemForSecondary.secondary = secondary;
  }

  return primary;
}

document.addEventListener('DOMContentLoaded', function(){
    initFlats();
    
    var vData = JSON.parse('<?php echo $dataSales; ?>')
    var eData = JSON.parse('<?php echo $dataEmpleados; ?>')
    var parsedDataPeriod = JSON.parse('<?php echo $dataPeriodNormalizado; ?>')
    serviceData = JSON.parse('<?php echo $ranking_services; ?>')
    categoryData = JSON.parse('<?php echo $ranking_categories; ?>')

    vData = transformPaymentData(vData); // Transformar al formato correcto
    
    drawDonut('donutChart', vData);
    renderLegend('legend', vData);
    
    drawBarChart('barChart', parsedDataPeriod);
    
    setView('service'); // Inicialmente mostrar servicios
    renderTable(serviceData);
    showDatesByStatus('Agendada');

    drawDonut('donutChartEmployees', eData, true);
    renderLegend('legendEmployees', eData, true);
    renderTableEmployees(eData);
})

/* ==============================
    GRÁFICA DE BARRAS (CANVAS)
    ============================== */

function drawBarChart(canvasId, data) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0,0,canvas.width,canvas.height);

    const padding = 50;
    const chartHeight = canvas.height - padding * 2;
    const chartWidth = canvas.width - padding * 2;

    const maxValue = Math.max(...data.map(d => d.value));
    const steps = 5;
    const stepValue = Math.ceil(maxValue / steps / 1000) * 1000;
    const maxScale = stepValue * steps;

    /* EJE Y */
    ctx.strokeStyle = '#ddd';
    ctx.fillStyle = '#888';
    ctx.font = '12px Arial';

    for (let i = 0; i <= steps; i++) {
    const y = padding + chartHeight - (i / steps) * chartHeight;
    const value = i * stepValue;

    ctx.beginPath();
    ctx.moveTo(padding, y);
    ctx.lineTo(canvas.width - padding, y);
    ctx.stroke();

    ctx.textAlign = 'right';
    ctx.fillText(value.toLocaleString(), padding - 8, y + 4);
    }

    /* BARRAS */
    const barGap = 10;
    const barWidth = (chartWidth / data.length) - barGap;

    // Almacenar información de cada barra para detectar hover
    const bars = [];

    data.forEach((item, index) => {
    const barHeight = (item.value / maxScale) * chartHeight;
    const x = padding + index * (barWidth + barGap) + barGap / 2;
    const y = padding + chartHeight - barHeight;

    ctx.fillStyle = '#F0959C';
    ctx.fillRect(x, y, barWidth, barHeight);

    ctx.fillStyle = '#666';
    ctx.textAlign = 'center';
    ctx.fillText(item.label, x + barWidth / 2, canvas.height - 15);

    // Guardar información de la barra
    bars.push({
        x: x,
        y: y,
        width: barWidth,
        height: barHeight,
        label: item.label,
        value: item.value
    });
    });

    // Crear tooltip si no existe
    let tooltip = document.getElementById(canvasId + '-tooltip');
    if (!tooltip) {
        tooltip = document.createElement('div');
        tooltip.id = canvasId + '-tooltip';
        tooltip.style.position = 'absolute';
        tooltip.style.backgroundColor = '#333';
        tooltip.style.color = '#fff';
        tooltip.style.padding = '8px 12px';
        tooltip.style.borderRadius = '4px';
        tooltip.style.fontSize = '12px';
        tooltip.style.pointerEvents = 'none';
        tooltip.style.display = 'none';
        tooltip.style.zIndex = '1000';
        tooltip.style.whiteSpace = 'nowrap';
        document.body.appendChild(tooltip);
    }

    // Event listener para mouse move
    canvas.addEventListener('mousemove', function(event) {
        const rect = canvas.getBoundingClientRect();
        const mouseX = event.clientX - rect.left;
        const mouseY = event.clientY - rect.top;

        let hoveredBar = null;

        // Detectar si el mouse está sobre alguna barra
        for (let bar of bars) {
            if (mouseX >= bar.x && mouseX <= bar.x + bar.width &&
                mouseY >= bar.y && mouseY <= bar.y + bar.height) {
                hoveredBar = bar;
                break;
            }
        }

        if (hoveredBar) {
            canvas.style.cursor = 'pointer';
            tooltip.innerHTML = `${hoveredBar.label}<br>$${hoveredBar.value.toLocaleString()}`;
            tooltip.style.display = 'block';
            tooltip.style.left = (event.clientX + 10) + 'px';
            tooltip.style.top = (event.clientY - 30) + 'px';

            // Redibujar gráfica con barra iluminada
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Redibujar ejes
            ctx.strokeStyle = '#ddd';
            ctx.fillStyle = '#888';
            ctx.font = '12px Arial';

            for (let i = 0; i <= steps; i++) {
                const y = padding + chartHeight - (i / steps) * chartHeight;
                const value = i * stepValue;
                ctx.beginPath();
                ctx.moveTo(padding, y);
                ctx.lineTo(canvas.width - padding, y);
                ctx.stroke();
                ctx.textAlign = 'right';
                ctx.fillText(value.toLocaleString(), padding - 8, y + 4);
            }

            // Redibujar barras
            bars.forEach((bar, idx) => {
                if (bar === hoveredBar) {
                    ctx.fillStyle = '#FF6B7B'; // Color más brillante
                } else {
                    ctx.fillStyle = '#F0959C';
                }
                ctx.fillRect(bar.x, bar.y, bar.width, bar.height);

                ctx.fillStyle = '#666';
                ctx.textAlign = 'center';
                ctx.fillText(bar.label, bar.x + bar.width / 2, canvas.height - 15);
            });
        } else {
            canvas.style.cursor = 'default';
            tooltip.style.display = 'none';

            // Redibujar gráfica normal
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Redibujar ejes
            ctx.strokeStyle = '#ddd';
            ctx.fillStyle = '#888';
            ctx.font = '12px Arial';

            for (let i = 0; i <= steps; i++) {
                const y = padding + chartHeight - (i / steps) * chartHeight;
                const value = i * stepValue;
                ctx.beginPath();
                ctx.moveTo(padding, y);
                ctx.lineTo(canvas.width - padding, y);
                ctx.stroke();
                ctx.textAlign = 'right';
                ctx.fillText(value.toLocaleString(), padding - 8, y + 4);
            }

            // Redibujar barras
            bars.forEach((bar) => {
                ctx.fillStyle = '#F0959C';
                ctx.fillRect(bar.x, bar.y, bar.width, bar.height);

                ctx.fillStyle = '#666';
                ctx.textAlign = 'center';
                ctx.fillText(bar.label, bar.x + bar.width / 2, canvas.height - 15);
            });
        }
    });

    // Ocultar tooltip al salir del canvas
    canvas.addEventListener('mouseleave', function() {
        tooltip.style.display = 'none';
        canvas.style.cursor = 'default';
        
        // Redibujar gráfica normal
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Redibujar ejes
        ctx.strokeStyle = '#ddd';
        ctx.fillStyle = '#888';
        ctx.font = '12px Arial';

        for (let i = 0; i <= steps; i++) {
            const y = padding + chartHeight - (i / steps) * chartHeight;
            const value = i * stepValue;
            ctx.beginPath();
            ctx.moveTo(padding, y);
            ctx.lineTo(canvas.width - padding, y);
            ctx.stroke();
            ctx.textAlign = 'right';
            ctx.fillText(value.toLocaleString(), padding - 8, y + 4);
        }

        // Redibujar barras
        bars.forEach((bar) => {
            ctx.fillStyle = '#F0959C';
            ctx.fillRect(bar.x, bar.y, bar.width, bar.height);

            ctx.fillStyle = '#666';
            ctx.textAlign = 'center';
            ctx.fillText(bar.label, bar.x + bar.width / 2, canvas.height - 15);
        });
    });
}

function drawDonut(canvasId, data, useAllData=false) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0,0,canvas.width,canvas.height);
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = 90;
    const lineWidth = 35;

    let startAngle = -0.5 * Math.PI;

    // Almacenar información de cada slice para detectar hover
    const slices = [];
    
    // Separar métodos primarios de secundarios para la gráfica
    const processedData = separatePaymentMethods(data, useAllData);

    processedData.forEach(item => {
        if(item.label==='Total' || item.amount === 0) return; // Omitir 'total'
        const sliceAngle = (item.percent / 100) * (2 * Math.PI);
        ctx.beginPath();
        ctx.arc(centerX, centerY, radius, startAngle, startAngle + sliceAngle);
        ctx.strokeStyle = item.color;
        ctx.lineWidth = lineWidth;
        ctx.stroke();

        // Guardar información del slice
        slices.push({
            startAngle: startAngle,
            endAngle: startAngle + sliceAngle,
            label: item.label,
            percent: item.percent,
            amount: item.amount,
            color: item.color
        });

        startAngle += sliceAngle;
    });

    // Crear tooltip si no existe
    let tooltip = document.getElementById(canvasId + '-tooltip');
    if (!tooltip) {
        tooltip = document.createElement('div');
        tooltip.id = canvasId + '-tooltip';
        tooltip.style.position = 'absolute';
        tooltip.style.backgroundColor = '#333';
        tooltip.style.color = '#fff';
        tooltip.style.padding = '8px 12px';
        tooltip.style.borderRadius = '4px';
        tooltip.style.fontSize = '12px';
        tooltip.style.pointerEvents = 'none';
        tooltip.style.display = 'none';
        tooltip.style.zIndex = '1000';
        tooltip.style.whiteSpace = 'nowrap';
        document.body.appendChild(tooltip);
    }

    // Event listener para mouse move
    canvas.addEventListener('mousemove', function(event) {
        const rect = canvas.getBoundingClientRect();
        const mouseX = event.clientX - rect.left;
        const mouseY = event.clientY - rect.top;

        // Calcular ángulo del mouse
        const dx = mouseX - centerX;
        const dy = mouseY - centerY;
        const distance = Math.sqrt(dx * dx + dy * dy);
        let mouseAngle = Math.atan2(dy, dx);

        let hoveredSlice = null;

        // Detectar si el mouse está sobre algún slice
        if (distance >= radius - lineWidth / 2 && distance <= radius + lineWidth / 2) {
            for (let slice of slices) {
                if (angleInRange(mouseAngle, slice.startAngle, slice.endAngle)) {
                    hoveredSlice = slice;
                    break;
                }
            }
        }

        if (hoveredSlice) {
            canvas.style.cursor = 'pointer';
            tooltip.innerHTML = `${hoveredSlice.label}<br>$${hoveredSlice.amount.toLocaleString()}<br>${hoveredSlice.percent.toFixed(1)}%`;
            tooltip.style.display = 'block';
            tooltip.style.left = (event.clientX + 10) + 'px';
            tooltip.style.top = (event.clientY - 30) + 'px';

            // Redibujar gráfica con slice iluminado
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            let redrawStartAngle = -0.5 * Math.PI;
            slices.forEach((slice) => {
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius, slice.startAngle, slice.endAngle);
                
                if (slice === hoveredSlice) {
                    ctx.lineWidth = lineWidth + 5; // Más grueso cuando está en hover
                    ctx.strokeStyle = slice.color;
                    ctx.globalAlpha = 1;
                } else {
                    ctx.lineWidth = lineWidth;
                    ctx.strokeStyle = slice.color;
                    ctx.globalAlpha = 0.7; // Más transparente cuando no está en hover
                }
                ctx.stroke();
            });
            ctx.globalAlpha = 1;
        } else {
            canvas.style.cursor = 'default';
            tooltip.style.display = 'none';

            // Redibujar gráfica normal
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            slices.forEach((slice) => {
                ctx.beginPath();
                ctx.arc(centerX, centerY, radius, slice.startAngle, slice.endAngle);
                ctx.lineWidth = lineWidth;
                ctx.strokeStyle = slice.color;
                ctx.globalAlpha = 1;
                ctx.stroke();
            });
        }
    });

    // Ocultar tooltip al salir del canvas
    canvas.addEventListener('mouseleave', function() {
        tooltip.style.display = 'none';
        canvas.style.cursor = 'default';
        
        // Redibujar gráfica normal
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        slices.forEach((slice) => {
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, slice.startAngle, slice.endAngle);
            ctx.lineWidth = lineWidth;
            ctx.strokeStyle = slice.color;
            ctx.globalAlpha = 1;
            ctx.stroke();
        });
    });
}

function renderLegend(containerId, data, useAllData=false) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';

    // Filtrar items válidos
    const validItems = data.filter(item => item.label !== 'Total' && item.amount !== 0);
    
    let total = 0;
    const primary = [];
    const secondary = [];

    // Separar por posición
    validItems.forEach((item, index) => {
        if (item.id <= NUM_PRIMARY_METHODS || useAllData) {
            total += item.amount;
            primary.push(item);
        } else {
            secondary.push(item);
        }
    });

    // Renderizar items primarios
    primary.forEach((item, index) => {
        const isExpandable = item.id === NUM_PRIMARY_METHODS && secondary.length > 0 && !useAllData;
        const isExpanded = paymentMethodsState.expandedSecondary[containerId + '_' + item.label];
        
        container.innerHTML += `
            <div class="legend-item ${isExpandable ? 'expandable' : ''}" ${isExpandable ? `data-expandable="${containerId}_${item.label}"` : ''}>
                <div class="legend-left" ${isExpandable ? `style="cursor: pointer;"` : ''}>
                    ${isExpandable ? `<span class="expand-icon" style="display: inline-block; margin-right: 5px; transform: rotate(${isExpanded ? '90' : '0'}deg); transition: transform 0.2s;">▶</span>` : '<span style="display: inline-block; margin-right: 5px; width: 14px;"></span>'}
                    <span class="legend-color" style="background:${item.color}"></span>
                    ${item.label}
                </div>
                <strong>$${item.amount.toLocaleString()}</strong>
            </div>
        `;
        
        // Renderizar items secundarios (ocultos por defecto)
        if (isExpandable) {
            secondary.forEach(secItem => {
                container.innerHTML += `
                    <div class="legend-item legend-secondary" data-parent="${containerId}_${item.label}" style="display: ${isExpanded ? 'flex' : 'none'}; padding-left: 30px; font-size: 0.9em;">
                        <div class="legend-left">
                            <span class="legend-color" style="background:${secItem.color}"></span>
                            ${secItem.label}
                        </div>
                        <strong>$${secItem.amount.toLocaleString()}</strong>
                    </div>
                `;
            });
        }
    });

    container.innerHTML += `<hr><strong>Total: $${total.toLocaleString()}</strong>`;
    
    // Agregar event listeners para expandir/contraer
    container.querySelectorAll('.legend-item.expandable').forEach(item => {
        item.addEventListener('click', function() {
            const expandableId = this.getAttribute('data-expandable');
            paymentMethodsState.expandedSecondary[expandableId] = !paymentMethodsState.expandedSecondary[expandableId];
            
            // Actualizar visibilidad de secundarios
            const secondaryItems = container.querySelectorAll(`[data-parent="${expandableId}"]`);
            const isExpanded = paymentMethodsState.expandedSecondary[expandableId];
            secondaryItems.forEach(sec => {
                sec.style.display = isExpanded ? 'flex' : 'none';
            });
            
            // Rotar icono
            const icon = this.querySelector('.expand-icon');
            if (icon) {
                icon.style.transform = `rotate(${isExpanded ? '90' : '0'}deg)`;
            }
        });
    });
}


// Función auxiliar para verificar si un ángulo está dentro de un rango
function angleInRange(angle, startAngle, endAngle) {
    // Normalizar ángulos a rango -π a π
    const normalize = (a) => {
        while (a > Math.PI) a -= 2 * Math.PI;
        while (a < -Math.PI) a += 2 * Math.PI;
        return a;
    };

    angle = normalize(angle);
    startAngle = normalize(startAngle);
    endAngle = normalize(endAngle);

    if (startAngle <= endAngle) {
        return angle >= startAngle && angle <= endAngle;
    } else {
        return angle >= startAngle || angle <= endAngle;
    }
}
</script>

@endpush