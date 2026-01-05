<script>
const canvas = document.getElementById('canvas');
const overlay = document.getElementById('overlay');
const ctx = canvas.getContext('2d');
const overlayCtx = overlay.getContext('2d');

const thresholdInput = document.getElementById('thresholdRange');
const thresholdValueDisplay = document.getElementById('thresholdValue');
const circleSizeInput = document.getElementById('circleSize');
const circleSizeDisplay = document.getElementById('circleSizeValue');

let originalImage = null;
let foundCoords = [];

const patternMap = {
    '100011100': 'A', '101010010': 'B', '001110001': 'C', '010010101': 'D',
    '010110001': 'E', '010011100': 'F', '010110001': 'G', '100011010': 'H',
    '001110010': 'I', '000110000': 'J', '010010000': 'K', '000011000': 'L',
    '000010010': 'M', '100010000': 'N', '001010000': 'O', '000010001': 'P',
    '010110010': 'Q', '010111000': 'R', '010011010': 'S', '000111010': 'T',
    '101010100': 'U', '101010001': 'V', '001010101': 'W', '100010101': 'X',
    '010111010': 'Y', '101010101': 'Z', '000010100': 'AA'
};

document.getElementById('upload').addEventListener('change', event => {
    
    const base64Data = localStorage.getItem("imageSrc");
    if (!base64Data) return;

    Livewire.emit('return64Image', base64Data);

});

window.addEventListener('sendProcessImagePath', event => {
    const imageUrl = event.detail.url;

    fetch(imageUrl)
        .then(res => res.blob())
        .then(blob => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.getElementById('canvas');
                const overlay = document.getElementById('overlay');
                const ctx = canvas.getContext('2d');

                canvas.width = img.width;
                canvas.height = img.height;
                overlay.width = img.width;
                overlay.height = img.height;

                ctx.drawImage(img, 0, 0);
                originalImage = ctx.getImageData(0, 0, canvas.width, canvas.height);
                processImage(parseInt(thresholdInput.value));
            };
            img.src = URL.createObjectURL(blob);
        });
});


thresholdInput.addEventListener('input', () => {
    const threshold = parseInt(thresholdInput.value);
    thresholdValueDisplay.textContent = threshold;
    if (originalImage) processImage(threshold);
});

circleSizeInput.addEventListener('input', () => {
    circleSizeDisplay.textContent = circleSizeInput.value;
    drawCircles();
});

function processImage(threshold,depth = 0) {
    const imageData = new ImageData(new Uint8ClampedArray(originalImage.data), originalImage.width, originalImage.height);
    const width = imageData.width;
    const height = imageData.height;
    ctx.putImageData(imageData, 0, 0);
    const pixels = imageData.data;
    const binaryMatrix = [];
    for (let y = 0; y < height; y++) {
        const row = [];
        for (let x = 0; x < width; x++) {
            const offset = (y * width + x) * 4;
            if (pixels[offset] > 128){
                gray = 1; // Umbral para blanco/negro
            }else{
                gray = 0; // Umbral para blanco/negro
            }
            row.push(gray);
        }
        binaryMatrix.push(row);
    }
    
    breakRec(binaryMatrix)
}
function breakRec(binaryMatrix)
{
    // // Detectar patrones en la imagen
    foundCoords = detectPatterns(binaryMatrix);
    getDistances(foundCoords);
}

function getDistances(patterns) {
    const distances = {};
    const maxDensity = Math.max(...patterns.map(p => p.density));
    const minDensity = Math.min(...patterns.map(p => p.density));
    const maxDensityDiff = maxDensity - minDensity || 1;

    for (let i = 0; i < patterns.length; i++) {
        for (let j = i + 1; j < patterns.length; j++) {
            const dx = patterns[j].x - patterns[i].x;
            const dy = patterns[j].y - patterns[i].y;
            const distance = Math.sqrt(dx * dx + dy * dy);

            const typePair = [patterns[i].char, patterns[j].char].sort().join('-');

            const d1 = patterns[i].density;
            const d2 = patterns[j].density;
            const densityDiff = d2 - d1;

            const directionFactor = densityDiff > 0
                ? 1 + Math.abs(densityDiff) / maxDensityDiff
                : 1 - Math.abs(densityDiff) / maxDensityDiff * 0.5;

            const weightedDistance = distance * directionFactor;

            if (!distances[typePair]) {
                distances[typePair] = { totalDistance: 0, count: 0 };
            }

            distances[typePair].totalDistance += weightedDistance;
            distances[typePair].count += 1;
        }
    }

    const accumulatedDistances = Object.entries(distances).map(([pair, data]) => ({
        pair,
        totalDistance: data.totalDistance,
        averageDistance: data.totalDistance / data.count,
        count: data.count
    }));

    const minDistance = Math.min(...accumulatedDistances.map(d => d.totalDistance));
    const maxDistance = Math.max(...accumulatedDistances.map(d => d.totalDistance));

    accumulatedDistances.forEach(d => {
        d.normalizedDistance = (d.totalDistance - minDistance) / (maxDistance - minDistance);
    });

    return accumulatedDistances;
}

function detectPatterns(binaryMatrix) {
    const patterns = [];
    const height = binaryMatrix.length;
    const width = binaryMatrix[0].length;

    for (let startX = 0; startX <= width - 15; startX += 3) {
        for (let startY = 0; startY <= height - 15; startY += 3) {
            const block = getSubMatrix(binaryMatrix, startX, startY, 15, 15);
            const finalBlock = reduceMatrix(block, 3);
            const onesCount = finalBlock.flat().reduce((a, b) => a + b, 0);

            // Verificar si el número de 1's coincide con los patrones conocidos
            if (onesCount > 1 && onesCount <= 5) {
                // Buscar patrones en la matriz resultante
                const flattened = finalBlock.flat().join('');
                if (isPatternMatch(flattened)) {
                    const color = getHeatColor(onesCount);
                    patterns.push({ 
                        x: startX, 
                        y: startY, 
                        char: isPatternMatch(flattened),
                        color,
                        quadrant: determineQuadrant(startX, startY, width, height),
                        density: onesCount
                    });
                }
            }
        }
    }

    return patterns;
}

function determineQuadrant(x, y, width, height) {
    const midX = width / 2;
    const midY = height / 2;

    if (x < midX && y < midY) return 'Q1'; // Superior izquierda
    if (x >= midX && y < midY) return 'Q2'; // Superior derecha
    if (x < midX && y >= midY) return 'Q3'; // Inferior izquierda
    return 'Q4'; // Inferior derecha
}

function getHeatColor(density) {
    // Mapeo manual entre 2 y 5
    switch (density) {
        case 2: return '#ffffb2'; // Amarillo claro
        case 3: return '#fecc5c'; // Amarillo
        case 4: return '#fd8d3c'; // Naranja
        case 5: return '#e31a1c'; // Rojo intenso
        default: return '#ffffff'; // Blanco o valor por defecto
    }
}

function reduceMatrix(matrix, size) {
    const originalSize = matrix.length; // Dimensión original de la matriz (asumimos cuadrada)
    const step = originalSize / size; // Tamaño del bloque (por ejemplo, 4 para 12 -> 3)
    const reducedMatrix = [];

    for (let y = 0; y < size; y++) {
        const row = [];
        for (let x = 0; x < size; x++) {
            // Extraer el bloque de la matriz original
            let onesCount = 0;
            let totalCount = 0;
            for (let subY = 0; subY < step; subY++) {
                for (let subX = 0; subX < step; subX++) {
                    const value = matrix[y * step + subY][x * step + subX];
                    onesCount += value; // Contar los `1`s en el bloque
                    totalCount++;
                }
            }

            // Decidir el valor predominante en el bloque
            const predominantValue = onesCount >= totalCount / 2 ? 1 : 0;
            row.push(predominantValue);
        }
        reducedMatrix.push(row);
    }

    return reducedMatrix;
}

function getSubMatrix(matrix, startX, startY, rows, cols) {
    const subMatrix = [];
    for (let y = 0; y < rows; y++) {
        const row = [];
        for (let x = 0; x < cols; x++) {
            row.push(matrix[startY + y][startX + x]);
        }
        subMatrix.push(row);
    }
    return subMatrix;
}

function isPatternMatch(flattenedMatrix) {
    const patternMap = {
        '100011100': 'A', '101010010': 'B', '001110001': 'C', '010010101': 'D',
        '010110001': 'E', '010011100': 'F', '010110001': 'G', '100011010': 'H',
        '001110010': 'I', '000110000': 'J', '010010000': 'K', '000011000': 'L',
        '000010010': 'M', '100010000': 'N', '001010000': 'O', '000010001': 'P',
        '010110010': 'Q', '010111000': 'R', '010011010': 'S', '000111010': 'T',
        '101010100': 'U', '101010001': 'V', '001010101': 'W', '100010101': 'X',
        '010111010': 'Y', '101010101': 'Z', '000010100': 'AA'
    };
    
    // Devuelve la letra asociada al patrón o null si no coincide
    return patternMap[flattenedMatrix] || null;
}

function drawCircles() {
    overlayCtx.clearRect(0, 0, overlay.width, overlay.height);
    const radius = parseInt(circleSizeInput.value);
    overlayCtx.lineWidth = 2;
    overlayCtx.font = 'bold 10px sans-serif';

    for (const { x, y, char, color } of foundCoords) {
        overlayCtx.beginPath();    
        overlayCtx.strokeStyle = color;
        overlayCtx.fillStyle = color;
        overlayCtx.arc(x, y, radius, 0, 2 * Math.PI);
        overlayCtx.stroke();
        overlayCtx.fillText(char, x - 4, y - radius - 2);
    }
}
</script>