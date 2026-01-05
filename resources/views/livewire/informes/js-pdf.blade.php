<script>
function transformPaymentData(data) {
  if (!Array.isArray(data)) {
    // Transformar el objeto con arrays en array de objetos
    return data.label.map((label, index) => ({
      label: label,
      percent: parseFloat(data.percent[index]) * 100, // Convertir a porcentaje
      color: data.color[index],
      amount: parseFloat(data.amount[index].replace(/,/g, '')) // Remover comas
    }));
  }
  return data;
}

document.addEventListener('DOMContentLoaded', function(){
    var vData = JSON.parse('<?php echo $dataSales; ?>')
    var parsedDataPeriod = JSON.parse('<?php echo $dataPeriodNormalizado; ?>')
    serviceData = JSON.parse('<?php echo $ranking_services; ?>')
    categoryData = JSON.parse('<?php echo $ranking_categories; ?>')

    vData = transformPaymentData(vData); // Transformar al formato correcto
    
    drawDonut('donutChart', vData);
    renderLegend('legend', vData);

    drawBarChart('barChart', parsedDataPeriod);

    setView('service'); // Inicialmente mostrar servicios
    renderTable(serviceData);

    showDatesByStatus('Pendiente');
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
}

function drawDonut(canvasId, data) {
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

    data.forEach(item => {
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

function renderLegend(containerId, data) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';

    let total = 0;

    data.forEach(item => {
    total += item.amount;
    container.innerHTML += `
        <div class="legend-item">
        <div class="legend-left">
            <span class="legend-color" style="background:${item.color}"></span>
            ${item.label}
        </div>
        <strong>$${item.amount.toLocaleString()}</strong>
        </div>
    `;
    });

    container.innerHTML += `<hr><strong>Total: $${total.toLocaleString()}</strong>`;
}
</script>