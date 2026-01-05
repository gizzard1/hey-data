<script>
// Función para cargar y procesar la imagen base64
function loadAndProcessImage(base64Image) {
    return new Promise((resolve) => {
        const img = new Image();
        img.src = base64Image; // Asigna la imagen base64 a la propiedad src

        img.onload = () => {
            const scaleFactor = 0.5; // Cambia este valor para ajustar la calidad (0.5 es 50% del tamaño original)
            
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");

            // Ajustar el tamaño del canvas a la imagen redimensionada
            canvas.width = img.width * scaleFactor;
            canvas.height = img.height * scaleFactor;
            
            // Dibujar la imagen redimensionada en el canvas
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            
            // Obtener datos de la imagen en forma de array de píxeles
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height).data;

            // Extraer puntos blancos (donde la imagen está binarizada)
            const points = [];
            for (let i = 0; i < imageData.length; i += 4) {
                const r = imageData[i]; // Valor del canal rojo
                const x = (i / 4) % canvas.width; // Ajustar a la nueva ancho del canvas
                const y = Math.floor((i / 4) / canvas.width); // Ajustar a la nueva alto del canvas
                // Si es blanco, agregamos el punto
                if (r === 255) {
                    points.push([x, y]);
                }
            }

            // Devuelve los puntos que representan la huella
            resolve(points);
        };

        img.onerror = () => {
            console.error("Error al cargar la imagen");
            resolve([]); // Devuelve un array vacío en caso de error
        };
    });
}

function euclideanDistance(p1, p2) {
    return Math.sqrt(Math.pow(p1[0] - p2[0], 2) + Math.pow(p1[1] - p2[1], 2));
}

function hausdorffDistance(setA, setB) {
    let maxDistanceAB = 0;
    for (let a of setA) {
        let minDistance = Infinity;
        for (let b of setB) {
            const distance = euclideanDistance(a, b);
            minDistance = Math.min(minDistance, distance);
        }
        maxDistanceAB = Math.max(maxDistanceAB, minDistance);
    }

    let maxDistanceBA = 0;
    for (let b of setB) {
        let minDistance = Infinity;
        for (let a of setA) {
            const distance = euclideanDistance(b, a);
            minDistance = Math.min(minDistance, distance);
        }
        maxDistanceBA = Math.max(maxDistanceBA, minDistance);
    }

    // La distancia de Hausdorff es el máximo entre estas dos distancias
    return Math.max(maxDistanceAB, maxDistanceBA);
}

window.addEventListener('compareFingerprints', async function(event) {
    const fingerprintsImages = event.detail.fingerprints; // Huellas dactilares disponibles
    const fingerprintA = event.detail.imageData;  // Datos de la huella a comparar

    // Procesar la huella a comparar
    const setB = await loadAndProcessImage(fingerprintA);
    const loadPromises = fingerprintsImages.map(base64Image => loadAndProcessImage(base64Image));

    try {
        const processedImages = await Promise.all(loadPromises);
        let matchFound = false;

        processedImages.forEach((setA, index) => {
            const distance = hausdorffDistance(setA, setB); // Comparar con setB
            console.log(`Distancia de Hausdorff entre huella ${index}:`, distance);
            const THRESHOLD = 10; // Establecer umbral para la comparación

            if (distance < THRESHOLD) {
                console.log(`Las huellas dactilares coinciden con la huella ${index}.`);
                matchFound = true; // Si hay coincidencia, actualizar el estado
            } else {
                console.log(`Las huellas dactilares no coinciden con la huella ${index}.`);
            }
        });

        // Muestra un mensaje si no hay coincidencias
        if (!matchFound) {
            console.log("No se encontraron coincidencias. Intente de nuevo");
        }
    } catch (error) {
        console.error("Error procesando las huellas:", error);
        console.log("Error procesando las huellas. Intente de nuevo.");
    }
});

</script>