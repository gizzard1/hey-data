<div wire:ignore.self id="modalImportFromPDF" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Contenido del modal-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Adjuntar archivo pdf</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <p>Solicita una consulta de CFDI desde el portal del SAT y pega el archivo aquí para volcar los datos al sistema</p>
                            <input id="importFile" type="file" class="form-control" accept="application/pdf" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
</script>

<script>
  document.getElementById('importFile').addEventListener('change', function (ev) {
    const file = ev.target.files[0];
    const reader = new FileReader();

    reader.onload = function (ev) {
      const typedarray = new Uint8Array(ev.target.result);

      pdfjsLib.getDocument(typedarray).promise.then(async function (pdf) {
        const numPages = pdf.numPages;

        const textosPorPagina = await Promise.all(
          Array.from({ length: numPages }, async (_, index) => {
            const page = await pdf.getPage(index + 1);
            const content = await page.getTextContent();
            return content.items.map(item => item.str).join('\n');
          })
        );

        // Unimos el texto de todas las páginas
        const textoCompleto = textosPorPagina.join('\n');

        // Procesar el texto para extraer el JSON
        const lines = textoCompleto
          .split('\n')
          .map(line => line.trim())
          .filter(line => line.length > 0);

        const datos = {};
        
        const resultados = [];
        let actual = {};

        for (let i = 0; i < lines.length; i++) {
            const line = lines[i];
            if (line.includes('Folio')) {
                actual.folio = lines[i + 1];
            } else if (line.includes('RFC Emisor')) {
                actual.rfc_emisor = lines[i + 1];
            } else if (line.includes('RFC Receptor')) {
                actual.rfc_receptor = lines[i + 1];
            } else if (line.includes('Total')) {
                const valor = lines[i + 1].replace(/[$,]/g, '');
                actual.total = parseFloat(valor);
            }else if (line.includes('Fecha Emisión')) {
                actual.fecha_emision = lines[i + 1];
            }else if (line.includes('Estado del Comprobante')) {
                actual.status = lines[i + 1];
                
                if (Object.keys(actual).length > 0) {
                    resultados.push({ ...actual });
                    actual = {}; // reiniciar para el siguiente bloque
                }
            } 
        }

        // Si al final quedó uno sin cerrar
        if (Object.keys(actual).length > 0) {
        resultados.push(actual);
        }
        Livewire.emit('importFromPdf', resultados);
      });
    };

    reader.readAsArrayBuffer(file);
  });
</script>