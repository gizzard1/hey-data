<script>
function thinning(imageData, width, height) {
     // Obtener valores de los controles deslizantes
    const lowerThreshold = parseInt(document.getElementById('lowerThreshold').value, 10);
    const upperThreshold = parseInt(document.getElementById('upperThreshold').value, 10);
    const iterations = parseInt(document.getElementById('iterations').value, 10);

    const pixels = imageData.data;
    const binaryImage = [];

    // Convertir la imagen a binaria (blanco y negro)
    for (let i = 0; i < pixels.length; i += 4) {
        const gray = pixels[i]; // Ya está en escala de grises
        binaryImage.push(gray === 255 ? 1 : 0);  // 1 para blanco, 0 para negro
    }

    function applyErosion(binaryImage, width, height) {
        const copy = [...binaryImage];
        for (let y = 1; y < height - 1; y++) {
            for (let x = 1; x < width - 1; x++) {
                const i = y * width + x;

                // Erosión: Si un pixel y sus vecinos no son blancos, lo hacemos negro
                const neighbors = [
                    binaryImage[i - width - 1], binaryImage[i - width], binaryImage[i - width + 1],
                    binaryImage[i - 1], binaryImage[i + 1],
                    binaryImage[i + width - 1], binaryImage[i + width], binaryImage[i + width + 1]
                ];

                const count = neighbors.filter(val => val === 1).length;
                if (count < lowerThreshold) {  // Cambiar umbral según se necesite
                    copy[i] = 0;  // Poner el pixel en negro si la vecindad no tiene suficientes blancos
                }
            }
        }
        return copy;
    }

    function applyDilation(binaryImage, width, height) {
        const copy = [...binaryImage];
        for (let y = 1; y < height - 1; y++) {
            for (let x = 1; x < width - 1; x++) {
                const i = y * width + x;

                // Dilatación: Si un pixel o sus vecinos son blancos, lo hacemos blanco
                const neighbors = [
                    binaryImage[i - width - 1], binaryImage[i - width], binaryImage[i - width + 1],
                    binaryImage[i - 1], binaryImage[i + 1],
                    binaryImage[i + width - 1], binaryImage[i + width], binaryImage[i + width + 1]
                ];

                const count = neighbors.filter(val => val === 1).length;
                if (count > upperThreshold) {  // Cambiar umbral según se necesite
                    copy[i] = 1;  // Poner el pixel en blanco si la vecindad tiene suficientes blancos
                }
            }
        }
        return copy;
    }

    // Aplicar un número de iteraciones de erosión y dilatación
    let result = [...binaryImage];
    for (let i = 0; i < iterations; i++) {  // Número de veces que quieres aplicar la erosión y dilatación
        result = applyErosion(result, width, height);
        result = applyDilation(result, width, height);
    }

    // Convertir la imagen binaria de nuevo a formato de imagen para dibujar en el canvas
    for (let i = 0; i < pixels.length; i += 4) {
        const value = result[i / 4] === 1 ? 255 : 0; // Convertimos de nuevo a blanco y negro
        pixels[i] = pixels[i + 1] = pixels[i + 2] = value;
    }

    return imageData;
}

// function enhanceFingerprintImage(imageSrc, callback) {
//     const img = new Image();
//     const canvas = document.createElement('canvas');
//     const ctx = canvas.getContext('2d');
//     const k = 1.5;

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

//         // Convertir a escala de grises utilizando la fórmula ponderada
//         for (let i = 0; i < pixels.length; i += 4) {
//             const r = pixels[i];
//             const g = pixels[i + 1];
//             const b = pixels[i + 2];
//             const gray = 0.2126 * r + 0.7152 * g + 0.0722 * b;
//             pixels[i] = pixels[i + 1] = pixels[i + 2] = gray;
//         }

//         // Paso 3: Aplicar un filtro de umbral binario
//         const threshold = 128;
//         for (let i = 0; i < pixels.length; i += 4) {
//             const gray = pixels[i];
//             const value = gray >= threshold ? 255 : 0;
//             pixels[i] = pixels[i + 1] = pixels[i + 2] = value;
//         }

//         // Paso 4: Obtener los datos de la imagen después de aplicar el umbral
//         const grayscaleData = [];
//         for (let i = 0; i < pixels.length; i += 4) {
//             const gray = pixels[i];
//             grayscaleData.push(gray);
//         }

//         // Convertir los datos a números complejos
//         const complexData = grayscaleToComplex(grayscaleData);

//         // Paso 5: Realizar la FFT en los datos de la imagen
//         const paddedWidth = nextPowerOfTwo(width);
//         const paddedHeight = nextPowerOfTwo(height);

//         // Asegurarse de que los datos con padding estén correctamente asignados
//         const paddedData = Array(paddedHeight).fill().map(() => Array(paddedWidth).fill({ real: 0, imag: 0 }));

//         // Copiar los datos originales en los datos con padding
//         for (let y = 0; y < height; y++) {
//             for (let x = 0; x < width; x++) {
//                 paddedData[y][x] = complexData[y * width + x];
//             }
//         }

//         // Aplicar la FFT sobre las filas
//         let transformedData = [];
//         for (let i = 0; i < paddedHeight; i++) {
//             const row = paddedData[i];
//             const rowTransformed = fft1d(row);

//             transformedData.push(rowTransformed); // Aplica FFT a cada fila
//         }

//         // Aplicar la FFT sobre las columnas
//         for (let i = 0; i < paddedWidth; i++) {
//             const column = transformedData.map(row => row[i]);
//             const transformedColumn = fft1d(column);

//             for (let j = 0; j < paddedHeight; j++) {
//                 transformedData[j][i] = transformedColumn[j];
//             }
//         }

//         // Paso 6: Modificar las magnitudes en el dominio de frecuencia
//         for (let i = 0; i < transformedData.length; i++) {
//             const current = transformedData[i];

//             if (current && current.real !== undefined && current.imag !== undefined) {
//                 const magnitude = Math.sqrt(Math.pow(current.real, 2) + Math.pow(current.imag, 2));
//                 const newMagnitude = Math.pow(magnitude, k);
//                 current.real = newMagnitude * (current.real / magnitude);
//                 current.imag = newMagnitude * (current.imag / magnitude);
//             } else {
//                 console.error(`Datos mal formados en el índice ${i}`);
//             }
//         }

//         // Paso 7: Devolver los datos transformados (o procesados) a la imagen
//         callback(transformedData);
//     };

//     img.src = imageSrc;
// }
function applyGaborFilter(imageSrc, callback) {
    const img = new Image();
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    img.onload = function () {
        const width = img.width;
        const height = img.height;
        canvas.width = width;
        canvas.height = height;
        
        // Dibujar la imagen en el canvas
        ctx.drawImage(img, 0, 0);
        
        // Obtener los píxeles de la imagen
        const imageData = ctx.getImageData(0, 0, width, height);
        const pixels = imageData.data;

        

        // Parámetros del filtro de Gabor
        const sigma = 3; // Ancho del filtro
        const theta = Math.PI / 4; // Orientación en radianes
        const lambda = 20; // Longitud de onda
        const gamma = 0.5; // Proporción de aspecto
        const psi = Math.PI / 2; // Fase

        // Crear el kernel de Gabor
        const kernelSize = 14; // Tamaño del kernel (ajusta para obtener diferentes efectos)
        const kernel = [];
        const halfSize = Math.floor(kernelSize / 2);
        
        for (let y = -halfSize; y <= halfSize; y++) {
            for (let x = -halfSize; x <= halfSize; x++) {
                const xPrime = x * Math.cos(theta) + y * Math.sin(theta);
                const yPrime = -x * Math.sin(theta) + y * Math.cos(theta);
                const value = Math.exp(-0.5 * (Math.pow(xPrime, 2) + Math.pow(yPrime, 2) * gamma * gamma) / (sigma * sigma)) *
                              Math.cos(2 * Math.PI * xPrime / lambda + psi);
                kernel.push(value);
            }
        }

        // Aplicar convolución con el filtro de Gabor
        const outputPixels = new Uint8ClampedArray(pixels);

        for (let y = halfSize; y < height - halfSize; y++) {
            for (let x = halfSize; x < width - halfSize; x++) {
                let sumR = 0, sumG = 0, sumB = 0;

                for (let ky = -halfSize; ky <= halfSize; ky++) {
                    for (let kx = -halfSize; kx <= halfSize; kx++) {
                        const pixelX = x + kx;
                        const pixelY = y + ky;
                        const offset = (pixelY * width + pixelX) * 4;
                        const kernelValue = kernel[(ky + halfSize) * kernelSize + (kx + halfSize)];
                        
                        sumR += pixels[offset] * kernelValue;
                        sumG += pixels[offset + 1] * kernelValue;
                        sumB += pixels[offset + 2] * kernelValue;
                    }
                }

                const outputOffset = (y * width + x) * 4;
                outputPixels[outputOffset] = sumR;
                outputPixels[outputOffset + 1] = sumG;
                outputPixels[outputOffset + 2] = sumB;
            }
        }

        // Colocar los píxeles resultantes en el canvas
        ctx.putImageData(new ImageData(outputPixels, width, height), 0, 0);
        callback(canvas.toDataURL());
    };

    img.src = imageSrc;
}

// Función para obtener la siguiente potencia de 2
function nextPowerOfTwo(n) {
    return Math.pow(2, Math.ceil(Math.log2(n)));
}

// Implementación simplificada de la FFT en 1D con manejo de números complejos
function fft1d(input) {
    const N = input.length;
    if (N <= 1) return input;

    const even = fft1d(input.filter((_, i) => i % 2 === 0)); // Pares
    const odd = fft1d(input.filter((_, i) => i % 2 !== 0)); // Impares

    const result = new Array(N);
    for (let i = 0; i < N / 2; i++) {
        const evenPart = even[i] || { real: 0, imag: 0 };
        const oddPart = odd[i] || { real: 0, imag: 0 };

        const exp = (-2 * Math.PI * i) / N;
        const twiddle = { 
            real: Math.cos(exp), 
            imag: Math.sin(exp) 
        };

        const oddTwiddled = {
            real: twiddle.real * oddPart.real - twiddle.imag * oddPart.imag,
            imag: twiddle.real * oddPart.imag + twiddle.imag * oddPart.real
        };

        result[i] = {
            real: evenPart.real + oddTwiddled.real,
            imag: evenPart.imag + oddTwiddled.imag
        };
        result[i + N / 2] = {
            real: evenPart.real - oddTwiddled.real,
            imag: evenPart.imag - oddTwiddled.imag
        };
        
        // Validar los datos resultantes
        if (!result[i] || result[i].real === undefined || result[i].imag === undefined) {
            console.error(`Datos mal formados en el índice ${i}`, result[i]);
        }
        if (!result[i + N / 2] || result[i + N / 2].real === undefined || result[i + N / 2].imag === undefined) {
            console.error(`Datos mal formados en el índice ${i + N / 2}`, result[i + N / 2]);
        }
    }

    return result;
}


function grayscaleToComplex(grayscaleData) {
    return grayscaleData.map(value => {
        if (value === undefined || value === null) {
            console.error("Valor no definido en los datos de escala de grises");
        }
        return { real: value, imag: 0 };
    });
}
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
//                 getPixel(x - 1, y - 1), getPixel(x + 1, y - 1), // Vecinos diagonales
//                 getPixel(x - 1, y + 1), getPixel(x + 1, y + 1)
//             ];
//             return neighbors.filter(value => value < 255).length;
//         }

//         function applyDilation(pixels, width, height) {
//             const newPixels = [...pixels]; // Copiar la imagen original

//             // Recorrer cada píxel de la imagen
//             for (let y = 1; y < height - 1; y++) {
//                 for (let x = 1; x < width - 1; x++) {
//                     const index = (y * width + x) * 4;
//                     const gray = pixels[index]; // Valor de gris del píxel

//                     // Si el píxel es negro (parte de la huella)
//                     if (gray === 0) {
//                         // Revisar los 8 vecinos del píxel
//                         for (let ny = -1; ny <= 1; ny++) {
//                             for (let nx = -1; nx <= 1; nx++) {
//                                 const neighborIndex = ((y + ny) * width + (x + nx)) * 4;
//                                 // Hacer que los píxeles vecinos también sean negros si son blancos
//                                 newPixels[neighborIndex] = 0;
//                                 newPixels[neighborIndex + 1] = 0;
//                                 newPixels[neighborIndex + 2] = 0;
//                             }
//                         }
//                     }
//                 }
//             }

//             // Reemplazar los píxeles originales con los nuevos después de la dilatación
//             for (let i = 0; i < pixels.length; i++) {
//                 pixels[i] = newPixels[i];
//             }
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
//                         const isBoundary = blackNeighbors == 1;

//                         // Verificar si es un píxel de conexión
//                         if (isBoundary && !isConnectionPixel(x, y, pixels, width, height)) {
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
//     function isConnectionPixel(x, y, pixels, width, height) {
//         // Función para obtener el valor de un píxel (0 = negro, 255 = blanco)
//         function getPixelValue(x, y) {
//             if (x < 0 || y < 0 || x >= width || y >= height) {
//                 return 255; // Considerar fuera del rango como blanco
//             }
//             const index = (y * width + x) * 4;
//             return pixels[index]; // Retorna el valor de gris
//         }

//         // Contar los vecinos negros (valor 0)
//         let blackNeighborCount = 0;
//         let connectionBroken = false;

//         // Definir los vecinos (8 direcciones)
//         const neighbors = [
//             [x - 1, y - 1], [x, y - 1], [x + 1, y - 1],
//             [x - 1, y], [x + 1, y], 
//             [x - 1, y + 1], [x, y + 1], [x + 1, y + 1]
//         ];

//         // Contar cuántos vecinos son negros (parte de la huella)
//         for (const [nx, ny] of neighbors) {
//             if (getPixelValue(nx, ny) === 0) {
//                 blackNeighborCount++;
//             }
//         }

//         // Si el píxel tiene 2 o más vecinos negros, puede ser considerado de conexión
//         if (blackNeighborCount >= 2) {
//             connectionBroken = true;
//         }

//         // Un píxel de conexión no puede ser eliminado si tiene 2 o más vecinos negros
//         return connectionBroken;
//     }



//     img.src = imageSrc;
// }


function enhanceFingerprintImage(imageSrc, callback) { 
    const img = new Image();
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    img.onload = function () {
        const width = img.width;
        const height = img.height;
        canvas.width = width;
        canvas.height = height;

        // Paso 1: Dibujar la imagen original en el canvas
        ctx.drawImage(img, 0, 0);

        // Paso 2: Convertir la imagen a escala de grises
        const imageData = ctx.getImageData(0, 0, width, height);
        const pixels = imageData.data;

        // Convertir a escala de grises
        for (let i = 0; i < pixels.length; i += 4) {
            const r = pixels[i];
            const g = pixels[i + 1];
            const b = pixels[i + 2];
            const gray = 0.2126 * r + 0.7152 * g + 0.0722 * b;
            pixels[i] = pixels[i + 1] = pixels[i + 2] = gray;
        }

        // Paso 3: Aplicar un filtro de umbral
        const threshold = 128;
        for (let i = 0; i < pixels.length; i += 4) {
            const gray = pixels[i];
            const value = gray >= threshold ? 255 : 0;
            pixels[i] = pixels[i + 1] = pixels[i + 2] = value;
        }

        const iterations = parseInt(document.getElementById('iterations').value, 10);
        const neighborhoodSize = iterations; // Tamaño de la vecindad, debe ser impar (por ejemplo, 3, 5, 7, etc.)
        const offset = Math.floor(neighborhoodSize / 2); // Desplazamiento para mantener la vecindad centrada

        let maxDensity = 0;
        const heightMap = Array.from({ length: height }, () => Array(width).fill(0));


        // Calcular densidades y encontrar la máxima
        for (let y = offset; y < height - offset; y++) {
            for (let x = offset; x < width - offset; x++) {
                const currentPixel = getPixel(pixels, x, y, width);
                if (currentPixel === 0) { // Si el píxel es negro
                    let count = 0;
                    for (let dy = -offset; dy <= offset; dy++) {
                        for (let dx = -offset; dx <= offset; dx++) {
                            if (getPixel(pixels, x + dx, y + dy, width) === 0) {
                                count++;
                            }
                        }
                    }
                    heightMap[y][x] = count;
                    if (count > maxDensity) maxDensity = count; // Actualiza el valor máximo de densidad
                }
            }
        }

        // Normalizar las alturas para que estén en un rango fijo, como de 0 a 255
        for (let y = offset; y < height - offset; y++) {
            for (let x = offset; x < width - offset; x++) {
                heightMap[y][x] = Math.floor((heightMap[y][x] / maxDensity) * 255);
            }
        }

        // Crear una línea principal basada en la mayor altura
        for (let y = 1; y < height - 1; y++) {
            for (let x = 1; x < width - 1; x++) {
                if (heightMap[y][x] > 0) {
                    const neighbors = [
                        heightMap[y + 1][x], heightMap[y - 1][x],
                        heightMap[y][x + 1], heightMap[y][x - 1]
                    ];
                    const maxNeighbor = Math.max(...neighbors);
                    if (heightMap[y][x] < maxNeighbor) {
                        setPixel(pixels, x, y, width, 255); // Convertir a blanco si no es la altura máxima
                    }
                }
            }
        }

        ctx.putImageData(imageData, 0, 0);
        callback(canvas.toDataURL());
    };

    img.src = imageSrc;
}

// Funciones auxiliares para manipular píxeles
// function getPixel(pixels, x, y, width) {
//     const index = (y * width + x) * 4;
//     return pixels[index];
// }

function setPixel(pixels, x, y, width, value) {
    const index = (y * width + x) * 4;
    pixels[index] = pixels[index + 1] = pixels[index + 2] = value;
    pixels[index + 3] = 255;
}

function processFingerprintPatterns(imageSrc, callback) {

    return new Promise((resolve, reject) => {
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
                    const gray = pixels[offset] > 128 ? 1 : 0; // Umbral para blanco/negro
                    row.push(gray);
                }
                binaryMatrix.push(row);
            }

            // Detectar patrones en la imagen
            const patterns = detectPatterns(binaryMatrix);

            const allDistances = []; // Usamos un arreglo para todas las distancias
            const distances = {}; // Usamos un objeto para las distancias acumuladas por tipo de patrón
            let totalDistance = 0;

            // Encontrar las distancias entre los patrones y almacenarlas en un arreglo
            for (let i = 0; i < patterns.length; i++) {
                for (let j = i + 1; j < patterns.length; j++) {
                    const dx = patterns[j].x - patterns[i].x;
                    const dy = patterns[j].y - patterns[i].y;
                    const distance = Math.sqrt((dx * dx) + (dy * dy));
                    
                    allDistances.push(distance); // Acumulamos todas las distancias en un arreglo
                    totalDistance += distance; // Para calcular la media después
                }
            }

            // Calcular la media (μ) de todas las distancias
            const mean = totalDistance / allDistances.length;

            // Calcular la desviación estándar (σ)
            const stdDev = Math.sqrt(allDistances.reduce((sum, dist) => sum + Math.pow(dist - mean, 2), 0) / allDistances.length);


            // Ahora normalizamos las distancias utilizando Z-score
            for (let i = 0; i < patterns.length; i++) {
                for (let j = i + 1; j < patterns.length; j++) {
                    const dx = patterns[j].x - patterns[i].x;
                    const dy = patterns[j].y - patterns[i].y;
                    const distance = Math.sqrt((dx * dx) + (dy * dy));

                    if (stdDev === 0) {
                        // Usar la distancia sin normalizar si no hay variación
                        normalizedDistance = distance;
                    } else {
                        // Calcular la distancia normalizada (Z-score)
                        normalizedDistance = (distance - mean) / stdDev;
                    }
                    
                    // Crear el par de patrones
                    const typePair = [patterns[i].pattern, patterns[j].pattern].sort().join('-');
                    
                    // Acumular la distancia normalizada
                    if (!distances[typePair]) {
                        distances[typePair] = {
                            totalDistance: 0,
                            count: 0
                        };
                    }
                    distances[typePair].totalDistance += normalizedDistance;
                    distances[typePair].count += 1;
                }
            }


            // Todos los posibles pares de letras (A-Z y AA)
            const allPairs = [];

            // Agregar los pares con 'AA'
            for (let i = 0; i < 26; i++) {
                const pair = String.fromCharCode(65 + i) + '-AA'; // Ejemplo: 'A-AA', 'B-AA', 'C-AA', ...
                allPairs.push(pair);
            }

            // Generar los demás pares A-Z
            for (let i = 0; i < 26; i++) {
                for (let j = i + 1; j < 26; j++) {
                    const pair = String.fromCharCode(65 + i) + '-' + String.fromCharCode(65 + j); // Ejemplo: 'A-B', 'A-C', etc.
                    allPairs.push(pair);
                }
            }
            // Generar un objeto con los pares y sus distancias acumuladas
            const accumulatedDistances = {};

            // Iterar sobre todos los pares y llenar el objeto con las distancias
            allPairs.forEach(pair => {
                const data = distances[pair] || { totalDistance: 0, count: 0 }; // Si no existe el par, lo llenamos con 0
                accumulatedDistances[pair] = data.totalDistance; // Usar el par como clave y la distancia como valor
            });

            callback(patterns);
            // Emitir los datos a Livewire
            Livewire.emit('returnPatterns', accumulatedDistances);
        };
        img.src = imageSrc;
    });
}

// function detectPatterns(binaryMatrix) {
//     const patterns = [];
//     const height = binaryMatrix.length;
//     const width = binaryMatrix[0].length;

//     // Recorrer bloques de 12x12
//     for (let startY = 0; startY <= height - 12; startY += 12) {
//         for (let startX = 0; startX <= width - 12; startX += 12) {
//             const block = getSubMatrix(binaryMatrix, startX, startY, 12, 12);

//             // Calcular densidad de píxeles negros
//             const pixelSum = block.flat().reduce((a, b) => a + b, 0);
//             const density = pixelSum / 144;

//             const finalBlock = reduceMatrix(block, 3);
            
//             // Contar la cantidad de 1's en la matriz final
//             const onesCount = finalBlock.flat().reduce((a, b) => a + b, 0);

//             // Verificar si el número de 1's coincide con los patrones conocidos
//             if (onesCount > 1 && onesCount <= 5) {
//                 // Buscar patrones en la matriz resultante
//                 const flattened = finalBlock.flat().join('');
//                 if (isPatternMatch(flattened)) {
//                     patterns.push({ x: startX, y: startY, pattern: isPatternMatch(flattened) });
//                 }
//             }
//         }
//     }

//     return patterns;
// }

function detectPatterns(binaryMatrix) {
    const patterns = [];
    const checkedPixels = new Set(); // Almacenar las coordenadas de los píxeles revisados
    const height = binaryMatrix.length;
    const width = binaryMatrix[0].length;

    // Recorrer cada píxel de la matriz
    for (let y = 0; y < height; y++) {
        for (let x = 0; x < width; x++) {
            const coordKey = `${x},${y}`; // Crear una clave única para el píxel

            // Verificar si ya se ha revisado este píxel
            if (checkedPixels.has(coordKey)) {
                continue; // Saltar al siguiente píxel si ya fue revisado
            }

            // Marcar el píxel como revisado
            checkedPixels.add(coordKey);
            

            // Procesar bloques centrados en el píxel actual (12x12 alrededor)
            const block = getSubMatrix(binaryMatrix, x, y, 12, 12);

            // Verificar si el bloque tiene suficiente espacio (si no sale de los límites)
            if (block.length === 12 && block[0].length === 12) {
                // Reducir la matriz y contar los 1's
                const finalBlock = reduceMatrix(block, 3);
                const onesCount = finalBlock.flat().reduce((a, b) => a + b, 0);

                // Verificar si el número de 1's coincide con los patrones conocidos
                if (onesCount > 1 && onesCount <= 5) {
                    // Buscar patrones en la matriz resultante
                    const flattened = finalBlock.flat().join('');
                    if (isPatternMatch(flattened)) {
                        patterns.push({ x, y, pattern: isPatternMatch(flattened) });
                    }
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

// function reduceMatrix(matrix, size) {
//     const reduced = [];
//     const rows = matrix.length;
//     const cols = matrix[0].length;
//     for (let y = 0; y <= rows - size; y += size) {
//         const row = [];
//         for (let x = 0; x <= cols - size; x += size) {
//             const subMatrix = getSubMatrix(matrix, x, y, size, size);
//             const sum = subMatrix.flat().reduce((a, b) => a + b, 0);
//             row.push(sum >= (size * size) / 2 ? 1 : 0);
//         }
//         reduced.push(row);
//     }
//     return reduced;
// }

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
</script>