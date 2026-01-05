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

    // Convertir base64 a Blob
    fetch(base64Data)
        .then(res => res.blob())
        .then(blob => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.getElementById('canvas');
                const overlay = document.getElementById('overlay'); // si usas uno
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
const TOLERANCE = 0.01;

function processImage(threshold,depth = 0) {
    const imageData = new ImageData(new Uint8ClampedArray(originalImage.data), originalImage.width, originalImage.height);
    const data = imageData.data;

    for (let i = 0; i < data.length; i += 4) {
        const gray = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
        const bw = gray < threshold ? 0 : 255;
        data[i] = data[i + 1] = data[i + 2] = bw;
    }

    // Ruido
    const width = imageData.width;
    const height = imageData.height;
    const denoised = new Uint8ClampedArray(data);

    for (let y = 1; y < height - 1; y++) {
        for (let x = 1; x < width - 1; x++) {
            const i = (y * width + x) * 4;
            const neighbors = [
                ((y - 1) * width + x) * 4,
                ((y + 1) * width + x) * 4,
                (y * width + (x - 1)) * 4,
                (y * width + (x + 1)) * 4,
            ];
            const blackNeighbors = neighbors.filter(n => data[n] === 0).length;
            if (data[i] === 0 && blackNeighbors < 2) {
                denoised[i] = denoised[i + 1] = denoised[i + 2] = 255;
            }
        }
    }

    imageData.data.set(denoised);
    ctx.putImageData(imageData, 0, 0);
    let black = 0;
    let white = 0;

    // Convertir la imagen a matriz de 0 y 1
    const pixels = imageData.data;
    const binaryMatrix = [];
    for (let y = 0; y < height; y++) {
        const row = [];
        for (let x = 0; x < width; x++) {
            const offset = (y * width + x) * 4;
            if (pixels[offset] > 128){
                gray = 1; // Umbral para blanco/negro
                black++;
            }else{
                gray = 0; // Umbral para blanco/negro
                white++;
            }
            row.push(gray);
        }
        binaryMatrix.push(row);
    }
    let whitePixelsAverage = black/white;

    if (Math.abs(whitePixelsAverage - 4.18) < TOLERANCE) {
        depth = 50;
    }
    
    if(depth <= 50){
        if (whitePixelsAverage < 4.18){
            processImage(threshold-1,depth+1)
        }else if (whitePixelsAverage > 4.18) {
            processImage(threshold+1,depth+1)
        }
    }else{
        breakRec(binaryMatrix)
    }
}
function breakRec(binaryMatrix)
{
    // // Detectar patrones en la imagen
    foundCoords = detectPatterns(binaryMatrix);
    drawCircles();
    getDistances(foundCoords);
}

function getDistances(patterns)
{
    // Calcular las distancias entre los puntos y acumularlas por tipo
    const distances = {};
    for (let i = 0; i < patterns.length; i++) {
        for (let j = i + 1; j < patterns.length; j++) {
            const dx = patterns[j].x - patterns[i].x;
            const dy = patterns[j].y - patterns[i].y;
            const distance = Math.sqrt((dx * dx) + (dy * dy));

            const typePair = [patterns[i].char, patterns[j].char].sort().join('-');

            if (!distances[typePair]) {
                distances[typePair] = {
                    totalDistance: 0,
                    count: 0
                };
            }

            distances[typePair].totalDistance += distance;
            distances[typePair].count += 1;
        }
    }

    // Convertir a un arreglo para analizar las distancias acumuladas
    const accumulatedDistances = Object.entries(distances).map(([pair, data]) => ({
        pair,
        totalDistance: data.totalDistance,
        averageDistance: data.totalDistance / data.count,
        count: data.count
    }));

    // Encontrar los valores mínimos y máximos de distancia
    const minDistance = Math.min(...accumulatedDistances.map(d => d.totalDistance));
    const maxDistance = Math.max(...accumulatedDistances.map(d => d.totalDistance));

    // Normalizar las distancias
    accumulatedDistances.forEach(d => {
        d.normalizedDistance = (d.totalDistance - minDistance) / (maxDistance - minDistance);
    });
    Livewire.emit('returnPatterns', accumulatedDistances);

}

function detectPatterns(binaryMatrix) {
    const patterns = [];
    const height = binaryMatrix.length;
    const width = binaryMatrix[0].length;

    // Recorrer bloques de 12x12
    for (let startY = 0; startY <= height - 12; startY += 12) {
        for (let startX = 0; startX <= width - 12; startX += 12) {
            const block = getSubMatrix(binaryMatrix, startX, startY, 12, 12);
            const finalBlock = reduceMatrix(block, 3);
            const onesCount = finalBlock.flat().reduce((a, b) => a + b, 0);

            // Verificar si el número de 1's coincide con los patrones conocidos
            if (onesCount > 1 && onesCount <= 5) {
                // Buscar patrones en la matriz resultante
                const flattened = finalBlock.flat().join('');
                if (isPatternMatch(flattened)) {
                    const color = getHeatColor(onesCount);
                    patterns.push({ x: startX, y: startY, char: isPatternMatch(flattened),color });
                }
            }
        }
    }

    return patterns;
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