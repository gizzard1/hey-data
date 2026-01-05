<script>
// function thinning(imageData, width, height) {
//      // Obtener valores de los controles deslizantes
//     const lowerThreshold = parseInt(document.getElementById('lowerThreshold').value, 10);
//     const upperThreshold = parseInt(document.getElementById('upperThreshold').value, 10);
//     const iterations = parseInt(document.getElementById('iterations').value, 10);

//     const pixels = imageData.data;
//     const binaryImage = [];

//     // Convertir la imagen a binaria (blanco y negro)
//     for (let i = 0; i < pixels.length; i += 4) {
//         const gray = pixels[i]; // Ya está en escala de grises
//         binaryImage.push(gray === 255 ? 1 : 0);  // 1 para blanco, 0 para negro
//     }

//     function applyErosion(binaryImage, width, height) {
//         const copy = [...binaryImage];
//         for (let y = 1; y < height - 1; y++) {
//             for (let x = 1; x < width - 1; x++) {
//                 const i = y * width + x;

//                 // Erosión: Si un pixel y sus vecinos no son blancos, lo hacemos negro
//                 const neighbors = [
//                     binaryImage[i - width - 1], binaryImage[i - width], binaryImage[i - width + 1],
//                     binaryImage[i - 1], binaryImage[i + 1],
//                     binaryImage[i + width - 1], binaryImage[i + width], binaryImage[i + width + 1]
//                 ];

//                 const count = neighbors.filter(val => val === 1).length;
//                 if (count < lowerThreshold) {  // Cambiar umbral según se necesite
//                     copy[i] = 0;  // Poner el pixel en negro si la vecindad no tiene suficientes blancos
//                 }
//             }
//         }
//         return copy;
//     }

//     function applyDilation(binaryImage, width, height) {
//         const copy = [...binaryImage];
//         for (let y = 1; y < height - 1; y++) {
//             for (let x = 1; x < width - 1; x++) {
//                 const i = y * width + x;

//                 // Dilatación: Si un pixel o sus vecinos son blancos, lo hacemos blanco
//                 const neighbors = [
//                     binaryImage[i - width - 1], binaryImage[i - width], binaryImage[i - width + 1],
//                     binaryImage[i - 1], binaryImage[i + 1],
//                     binaryImage[i + width - 1], binaryImage[i + width], binaryImage[i + width + 1]
//                 ];

//                 const count = neighbors.filter(val => val === 1).length;
//                 if (count > upperThreshold) {  // Cambiar umbral según se necesite
//                     copy[i] = 1;  // Poner el pixel en blanco si la vecindad tiene suficientes blancos
//                 }
//             }
//         }
//         return copy;
//     }

//     // Aplicar un número de iteraciones de erosión y dilatación
//     let result = [...binaryImage];
//     for (let i = 0; i < iterations; i++) {  // Número de veces que quieres aplicar la erosión y dilatación
//         result = applyErosion(result, width, height);
//         result = applyDilation(result, width, height);
//     }

//     // Convertir la imagen binaria de nuevo a formato de imagen para dibujar en el canvas
//     for (let i = 0; i < pixels.length; i += 4) {
//         const value = result[i / 4] === 1 ? 255 : 0; // Convertimos de nuevo a blanco y negro
//         pixels[i] = pixels[i + 1] = pixels[i + 2] = value;
//     }

//     return imageData;
// }

// // function enhanceFingerprintImage(imageSrc, callback) {
// //     const img = new Image();
// //     const canvas = document.createElement('canvas');
// //     const ctx = canvas.getContext('2d');
// //     const k = 1.5;

// //     img.onload = function () {
// //         const width = img.width;
// //         const height = img.height;
// //         canvas.width = width;
// //         canvas.height = height;

// //         // Paso 1: Dibujar la imagen original en el canvas
// //         ctx.drawImage(img, 0, 0);

// //         // Paso 2: Convertir la imagen a escala de grises
// //         const imageData = ctx.getImageData(0, 0, width, height);
// //         const pixels = imageData.data;

// //         // Convertir a escala de grises utilizando la fórmula ponderada
// //         for (let i = 0; i < pixels.length; i += 4) {
// //             const r = pixels[i];
// //             const g = pixels[i + 1];
// //             const b = pixels[i + 2];
// //             const gray = 0.2126 * r + 0.7152 * g + 0.0722 * b;
// //             pixels[i] = pixels[i + 1] = pixels[i + 2] = gray;
// //         }

// //         // Paso 3: Aplicar un filtro de umbral binario
// //         const threshold = 128;
// //         for (let i = 0; i < pixels.length; i += 4) {
// //             const gray = pixels[i];
// //             const value = gray >= threshold ? 255 : 0;
// //             pixels[i] = pixels[i + 1] = pixels[i + 2] = value;
// //         }

// //         // Paso 4: Obtener los datos de la imagen después de aplicar el umbral
// //         const grayscaleData = [];
// //         for (let i = 0; i < pixels.length; i += 4) {
// //             const gray = pixels[i];
// //             grayscaleData.push(gray);
// //         }

// //         // Convertir los datos a números complejos
// //         const complexData = grayscaleToComplex(grayscaleData);

// //         // Paso 5: Realizar la FFT en los datos de la imagen
// //         const paddedWidth = nextPowerOfTwo(width);
// //         const paddedHeight = nextPowerOfTwo(height);

// //         // Asegurarse de que los datos con padding estén correctamente asignados
// //         const paddedData = Array(paddedHeight).fill().map(() => Array(paddedWidth).fill({ real: 0, imag: 0 }));

// //         // Copiar los datos originales en los datos con padding
// //         for (let y = 0; y < height; y++) {
// //             for (let x = 0; x < width; x++) {
// //                 paddedData[y][x] = complexData[y * width + x];
// //             }
// //         }

// //         // Aplicar la FFT sobre las filas
// //         let transformedData = [];
// //         for (let i = 0; i < paddedHeight; i++) {
// //             const row = paddedData[i];
// //             const rowTransformed = fft1d(row);

// //             transformedData.push(rowTransformed); // Aplica FFT a cada fila
// //         }

// //         // Aplicar la FFT sobre las columnas
// //         for (let i = 0; i < paddedWidth; i++) {
// //             const column = transformedData.map(row => row[i]);
// //             const transformedColumn = fft1d(column);

// //             for (let j = 0; j < paddedHeight; j++) {
// //                 transformedData[j][i] = transformedColumn[j];
// //             }
// //         }

// //         // Paso 6: Modificar las magnitudes en el dominio de frecuencia
// //         for (let i = 0; i < transformedData.length; i++) {
// //             const current = transformedData[i];

// //             if (current && current.real !== undefined && current.imag !== undefined) {
// //                 const magnitude = Math.sqrt(Math.pow(current.real, 2) + Math.pow(current.imag, 2));
// //                 const newMagnitude = Math.pow(magnitude, k);
// //                 current.real = newMagnitude * (current.real / magnitude);
// //                 current.imag = newMagnitude * (current.imag / magnitude);
// //             } else {
// //                 console.error(`Datos mal formados en el índice ${i}`);
// //             }
// //         }

// //         // Paso 7: Devolver los datos transformados (o procesados) a la imagen
// //         callback(transformedData);
// //     };

// //     img.src = imageSrc;
// // }
let precomputedKernel = null;

function createGaborKernel(sigma, theta, lambda, gamma, psi, kernelSize) {
    const kernel = [];
    const halfSize = Math.floor(kernelSize / 2);
    const cosTheta = Math.cos(theta);
    const sinTheta = Math.sin(theta);

    for (let y = -halfSize; y <= halfSize; y++) {
        for (let x = -halfSize; x <= halfSize; x++) {
            const xPrime = x * cosTheta + y * sinTheta;
            const yPrime = -x * sinTheta + y * cosTheta;
            const value = Math.exp(-0.5 * (Math.pow(xPrime, 2) + Math.pow(yPrime, 2) * gamma * gamma) / (sigma * sigma)) *
                          Math.cos(2 * Math.PI * xPrime / lambda + psi);
            kernel.push(value);
        }
    }

    return kernel;
}
function applyGaborFilter(imageSrc, callback) {
    const img = new Image();
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    img.onload = function () {
        const width = img.width;
        const height = img.height;
        canvas.width = width;
        canvas.height = height;

        ctx.drawImage(img, 0, 0);
        const imageData = ctx.getImageData(0, 0, width, height);
        const pixels = imageData.data;

        // Paso 1: Convertir la imagen a escala de grises
        const grayPixels = new Uint8ClampedArray(width * height);
        for (let i = 0; i < pixels.length; i += 4) {
            const r = pixels[i];
            const g = pixels[i + 1];
            const b = pixels[i + 2];
            grayPixels[i / 4] = 0.2126 * r + 0.7152 * g + 0.0722 * b; // Grises
        }

        // Paso 2: Crear el kernel de Gabor
        const sigma = parseFloat(document.getElementById('sigma').value);
        const theta = Math.PI / parseFloat(document.getElementById('radianes').value);
        const lambda = parseFloat(document.getElementById('lambda').value);
        const gamma = 0.5;
        const psi = Math.PI / 2;
        const kernelSize = parseInt(document.getElementById('kernelSize').value);
        const halfSize = Math.floor(kernelSize / 2);
        if (!precomputedKernel) {
            precomputedKernel = createGaborKernel(sigma, theta, lambda, gamma, psi, kernelSize);
        }
        const kernel = precomputedKernel;

        // Paso 3: Aplicar convolución
        const outputPixels = new Uint8ClampedArray(pixels.length);
        for (let y = halfSize; y < height - halfSize; y++) {
            for (let x = halfSize; x < width - halfSize; x++) {
                let sum = 0;
                for (let ky = -halfSize; ky <= halfSize; ky++) {
                    for (let kx = -halfSize; kx <= halfSize; kx++) {
                        const pixelX = x + kx;
                        const pixelY = y + ky;
                        const kernelValue = kernel[(ky + halfSize) * kernelSize + (kx + halfSize)];
                        sum += grayPixels[pixelY * width + pixelX] * kernelValue;
                    }
                }
                const offset = (y * width + x) * 4;
                const clamped = Math.min(255, Math.max(0, sum));
                outputPixels[offset] = outputPixels[offset + 1] = outputPixels[offset + 2] = clamped;
                outputPixels[offset + 3] = 255; // Alpha
            }
        }

        // Paso 4: Aplicar umbral
        const threshold = 128;
        for (let i = 0; i < outputPixels.length; i += 4) {
            const value = outputPixels[i] >= threshold ? 255 : 0;
            outputPixels[i] = outputPixels[i + 1] = outputPixels[i + 2] = value;
        }

        // Renderizar el resultado
        ctx.putImageData(new ImageData(outputPixels, width, height), 0, 0);
        callback(canvas.toDataURL());
    };

    img.src = imageSrc;
}

function processFingerprintPatterns(imageSrc, callback) {
    const img = new Image();
    img.onload = function () {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const width = img.width;
        const height = img.height;
        canvas.width = width;
        canvas.height = height;

        // Dibujar imagen y obtener píxeles
        ctx.drawImage(img, 0, 0);
        const imageData = ctx.getImageData(0, 0, width, height);
        const pixels = imageData.data;

        // Convertir la imagen a matriz de 0 y 1
        const binaryMatrix = [];
        for (let y = 0; y < height; y++) {
            const row = [];
            for (let x = 0; x < width; x++) {
                const offset = (y * width + x) * 4;
                const gray = pixels[offset] > 128 ? 0 : 1; // Umbral para blanco/negro
                row.push(gray);
            }
            binaryMatrix.push(row);
        }

        // Detectar patrones en la imagen
        const patterns = detectPatterns(binaryMatrix);

        // Calcular las distancias entre los puntos
        const distances = [];
        for (let i = 0; i < patterns.length; i++) {
            for (let j = i + 1; j < patterns.length; j++) {
                const dx = patterns[j].x - patterns[i].x;
                const dy = patterns[j].y - patterns[i].y;
                const distance = Math.sqrt((dx * dx) + (dy * dy));

                distances.push({
                    from: patterns[i],
                    to: patterns[j],
                    distance: distance,
                });
            }
        }
        // Emitir patrones encontrados al componente Livewire
        Livewire.emit('regresarPatrones', patterns,distances);

        callback(patterns);
    };
    img.src = imageSrc;
}

function detectPatterns(binaryMatrix) {
    const patterns = [];
    const height = binaryMatrix.length;
    const width = binaryMatrix[0].length;

    // Recorrer bloques de 12x12
    for (let startY = 0; startY <= height - 12; startY += 12) {
        for (let startX = 0; startX <= width - 12; startX += 12) {
            const block = getSubMatrix(binaryMatrix, startX, startY, 12, 12);

            // Calcular densidad de píxeles negros
            const pixelSum = block.flat().reduce((a, b) => a + b, 0);
            const density = pixelSum / 144;

            if (density < 5 / 9) {
                // Reducir a matrices de 2x2
                const reducedBlock = reduceMatrix(block, 2);
                const finalBlock = reduceMatrix(reducedBlock, 2);

                // Buscar patrones en la matriz resultante
                const flattened = finalBlock.flat().join('');
                if (isPatternMatch(flattened)) {
                    patterns.push({ x: startX, y: startY, pattern: flattened });
                }
            }
        }
    }

    return patterns;
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

function reduceMatrix(matrix, size) {
    const reduced = [];
    const rows = matrix.length;
    const cols = matrix[0].length;
    for (let y = 0; y <= rows - size; y += size) {
        const row = [];
        for (let x = 0; x <= cols - size; x += size) {
            const subMatrix = getSubMatrix(matrix, x, y, size, size);
            const sum = subMatrix.flat().reduce((a, b) => a + b, 0);
            row.push(sum >= (size * size) / 2 ? 1 : 0);
        }
        reduced.push(row);
    }
    return reduced;
}

function isPatternMatch(flattenedMatrix) {
    const patterns = [
        '100011100', '101010010', '001110001', '010010101',
        '010110001', '010011100', '100011010', '001110010',
        '000110000', '010010000', '000011000', '000010010',
        '100010000', '001010000', '000010001', '000010100',
        '010110010', '010111000', '010011010', '000111010',
        '101010100', '101010001', '001010101', '100010101',
        '010111010', '101010101'
    ];
    return patterns.includes(flattenedMatrix);
}
// // Función para obtener la siguiente potencia de 2
// function nextPowerOfTwo(n) {
//     return Math.pow(2, Math.ceil(Math.log2(n)));
// }

// // Implementación simplificada de la FFT en 1D con manejo de números complejos
// function fft1d(input) {
//     const N = input.length;
//     if (N <= 1) return input;

//     const even = fft1d(input.filter((_, i) => i % 2 === 0)); // Pares
//     const odd = fft1d(input.filter((_, i) => i % 2 !== 0)); // Impares

//     const result = new Array(N);
//     for (let i = 0; i < N / 2; i++) {
//         const evenPart = even[i] || { real: 0, imag: 0 };
//         const oddPart = odd[i] || { real: 0, imag: 0 };

//         const exp = (-2 * Math.PI * i) / N;
//         const twiddle = { 
//             real: Math.cos(exp), 
//             imag: Math.sin(exp) 
//         };

//         const oddTwiddled = {
//             real: twiddle.real * oddPart.real - twiddle.imag * oddPart.imag,
//             imag: twiddle.real * oddPart.imag + twiddle.imag * oddPart.real
//         };

//         result[i] = {
//             real: evenPart.real + oddTwiddled.real,
//             imag: evenPart.imag + oddTwiddled.imag
//         };
//         result[i + N / 2] = {
//             real: evenPart.real - oddTwiddled.real,
//             imag: evenPart.imag - oddTwiddled.imag
//         };
        
//         // Validar los datos resultantes
//         if (!result[i] || result[i].real === undefined || result[i].imag === undefined) {
//             console.error(`Datos mal formados en el índice ${i}`, result[i]);
//         }
//         if (!result[i + N / 2] || result[i + N / 2].real === undefined || result[i + N / 2].imag === undefined) {
//             console.error(`Datos mal formados en el índice ${i + N / 2}`, result[i + N / 2]);
//         }
//     }

//     return result;
// }


// function grayscaleToComplex(grayscaleData) {
//     return grayscaleData.map(value => {
//         if (value === undefined || value === null) {
//             console.error("Valor no definido en los datos de escala de grises");
//         }
//         return { real: value, imag: 0 };
//     });
// }

// function enhanceFingerprintImage(imageSrc, callback) {
//     const img = new Image();
//     const canvas = document.createElement('canvas');
//     const ctx = canvas.getContext('2d');

//     img.onload = function () {
//         const width = img.width;
//         const height = img.height;
//         canvas.width = width;
//         canvas.height = height;

//         // Paso 1: Dibujar la imagen original en el canvas
//         ctx.drawImage(img, 0, 0);

//         // Paso 2: Convertir la imagen a escala de grises
//         const imageData = ctx.getImageData(0, 0, width, height);
//         const pixels = imageData.data;

//         // Convertir a escala de grises
//         for (let i = 0; i < pixels.length; i += 4) {
//             const r = pixels[i];
//             const g = pixels[i + 1];
//             const b = pixels[i + 2];

//             const gray = 0.2126 * r + 0.7152 * g + 0.0722 * b;
//             pixels[i] = pixels[i + 1] = pixels[i + 2] = gray;
//         }

//         // Paso 3: Aplicar un filtro de umbral
//         const threshold = 220;
//         for (let i = 0; i < pixels.length; i += 4) {
//             const gray = pixels[i];
//             const value = gray >= threshold ? 255 : 0;
//             pixels[i] = pixels[i + 1] = pixels[i + 2] = value;
//         }

//         // Función para obtener el valor de un píxel (en escala de grises)
//         function getPixel(x, y) {
//             if (x < 0 || y < 0 || x >= width || y >= height) return 255; // Considerar fuera de límites como blanco
//             const index = (y * width + x) * 4;
//             return pixels[index];
//         }

//         // Función para establecer el valor de un píxel
//         function setPixel(x, y, value) {
//             const index = (y * width + x) * 4;
//             pixels[index] = pixels[index + 1] = pixels[index + 2] = value;
//         }

//         // Función para contar vecinos en un píxel (0 = negro)
//         function countBlackNeighbors(x, y) {
//             const neighbors = [
//                 getPixel(x - 1, y), getPixel(x + 1, y), // Vecinos horizontales
//                 getPixel(x, y - 1), getPixel(x, y + 1), // Vecinos verticales
//                 // getPixel(x - 1, y - 1), getPixel(x + 1, y - 1), // Vecinos diagonales
//                 // getPixel(x - 1, y + 1), getPixel(x + 1, y + 1)
//             ];
//             return neighbors.filter(value => value === 0).length;
//         }
//         function isConnectionPixel(x, y, pixels, width, height) {
//             // Función para obtener el valor de un píxel (0 = negro, 255 = blanco)
//             function getPixelValue(x, y) {
//                 if (x < 0 || y < 0 || x >= width || y >= height) {
//                     return 255; // Considerar fuera del rango como blanco
//                 }
//                 const index = (y * width + x) * 4;
//                 return pixels[index]; // Retorna el valor de gris
//             }

//             // Contar los vecinos negros (valor 0)
//             let blackNeighborCount = 0;
//             let connectionBroken = false;

//             // Definir los vecinos (4 direcciones)
//             const neighbors = [
//                 [x, y - 1], [x - 1, y], [x + 1, y], [x, y + 1]
//             ];

//             // Contar cuántos vecinos son negros (parte de la huella)
//             for (const [nx, ny] of neighbors) {
//                 if (getPixelValue(nx, ny) === 0) {
//                     blackNeighborCount++;
//                 }
//             }

//             // Si el píxel tiene 2 o más vecinos negros, puede ser considerado de conexión
//             if (blackNeighborCount >= 2) {
//                 connectionBroken = true;
//             }

//             // Un píxel de conexión no puede ser eliminado si tiene 2 o más vecinos negros
//             return connectionBroken;
//         }



//         // Paso 4-7: Procesar píxeles según las especificaciones
//         let changed;
//         do {
//             changed = false;

//             // Paso 4: Identificar y eliminar píxeles límite que no son de conexión
//             for (let y = 0; y < height; y++) {
//                 for (let x = 0; x < width; x++) {
//                     if (getPixel(x, y) === 0) { // Píxel negro
//                         const blackNeighbors = countBlackNeighbors(x, y);

//                         // Identificar si es límite (un solo vecino blanco)
//                         const isBoundary = blackNeighbors > 0 && blackNeighbors < 8;

//                         if (isBoundary && isConnectionPixel(x, y, pixels, width, height)) {
//                             setPixel(x, y, 255); // Eliminar píxel límite
//                             changed = true;
//                         }
//                     }
//                 }
//             }

//             // Paso 5: Identificar y eliminar píxeles internos con exactamente 3 vecinos
//             for (let y = 0; y < height; y++) {
//                 for (let x = 0; x < width; x++) {
//                     if (getPixel(x, y) === 0) { // Píxel negro
//                         const blackNeighbors = countBlackNeighbors(x, y);

//                         if (blackNeighbors === 3) {
//                             setPixel(x, y, 255); // Eliminar píxel interno
//                             changed = true;
//                         }
//                     }
//                 }
//             }

//             // Paso 6: Eliminar píxeles internos cuando no es posible eliminar píxeles límite
//             for (let y = 0; y < height; y++) {
//                 for (let x = 0; x < width; x++) {
//                     if (getPixel(x, y) === 0) { // Píxel negro
//                         const blackNeighbors = countBlackNeighbors(x, y);

//                         if (blackNeighbors > 3) {
//                             setPixel(x, y, 255); // Eliminar píxel interno
//                             changed = true;
//                         }
//                     }
//                 }
//             }

//             // Paso 7: Eliminar píxeles internos con únicamente 2 vecinos, asegurando que no sean de conexión
//             for (let y = 0; y < height; y++) {
//                 for (let x = 0; x < width; x++) {
//                     if (getPixel(x, y) === 0) { // Píxel negro
//                         const blackNeighbors = countBlackNeighbors(x, y);

//                         if (blackNeighbors === 2) {
//                             setPixel(x, y, 255); // Eliminar píxel interno
//                             changed = true;
//                         }
//                     }
//                 }
//             }

//         } while (changed);

//         // Paso 5: Aplicar dilatación para reconectar las líneas
//         applyDilation(pixels, width, height);
//         // Paso 8: Colocar la imagen procesada en el canvas
//         ctx.putImageData(imageData, 0, 0);

//         // Llamar al callback con la imagen procesada en Base64
//         callback(canvas.toDataURL());
//     };

//     img.src = imageSrc;
// }
// function applyDilation(pixels, width, height) {
//     const newPixels = [...pixels]; // Copiar la imagen original

//     // Recorrer cada píxel de la imagen
//     for (let y = 1; y < height - 1; y++) {
//         for (let x = 1; x < width - 1; x++) {
//             const index = (y * width + x) * 4;
//             const gray = pixels[index]; // Valor de gris del píxel

//             // Si el píxel es negro (parte de la huella)
//             if (gray === 0) {
//                 // Revisar los 8 vecinos del píxel
//                 for (let ny = -1; ny <= 1; ny++) {
//                     for (let nx = -1; nx <= 1; nx++) {
//                         const neighborIndex = ((y + ny) * width + (x + nx)) * 4;
//                         // Hacer que los píxeles vecinos también sean negros si son blancos
//                         newPixels[neighborIndex] = 0;
//                         newPixels[neighborIndex + 1] = 0;
//                         newPixels[neighborIndex + 2] = 0;
//                     }
//                 }
//             }
//         }
//     }

//     // Reemplazar los píxeles originales con los nuevos después de la dilatación
//     for (let i = 0; i < pixels.length; i++) {
//         pixels[i] = newPixels[i];
//     }
// }


// // Funciones auxiliares para manipular píxeles
// function getPixel(pixels, x, y, width) {
//     const index = (y * width + x) * 4;
//     return pixels[index];
// }

// function setPixel(pixels, x, y, width, value) {
//     const index = (y * width + x) * 4;
//     pixels[index] = pixels[index + 1] = pixels[index + 2] = value;
//     pixels[index + 3] = 255;
// }
</script>