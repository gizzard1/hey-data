
<input type="file" class="form-control" id="input-file" wire:model="gallery" accept="image/x-png,image/jpeg,.pdf" multiple hidden>

@error('gallery.*')
<span style="color: red;">{{ $message }}</span>
@enderror


<div wire:loading wire:target="gallery">Cargando imágenes...</div>
@if ($gallery!=null)
<div class="row">
    @foreach ($gallery as $photo)
        <div class="col-6 col-sm-4">
            <div class="media">
                @if (in_array($photo->getMimeType(), ['image/jpeg', 'image/png']))
                    <img src="{{ $photo->temporaryUrl() }}" class="img-fluid rounded" alt="img">
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 right-0"  wire:click="removeFile('{{ $photo->getFilename() }}',1)">x</button>
                @else
                    <p>{{ $photo->getClientOriginalName() }}</p>
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 right-0"  wire:click="removeFile('{{ $photo->getFilename() }}',1)">x</button>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endif
@if($pictures != null)
<div class="row">
    @foreach ($pictures as $photo)
        <div class="col-6 col-sm-4">
            <img src="{{ $photo }}" class="img-fluid rounded" alt="Archivo pdf">
            <button type="button" class="btn btn-danger btn-sm float-right"  wire:click="removeFile('{{ $photo }}',0)">x</button>
            <button type="button" onclick="openPath('{{ asset($photo) }}')" class="btn btn-secondary btn-sm float-right"><i class="las la-eye"></i></button>
        </div>
    @endforeach
</div>
@endif

<div style="display:grid" id="holder" class="holder_default {{ $uploadFiles ? 'visible' : 'hidden' }}">
    <a>Arrastra y suelta tus archivos aquí</a>
</div>
<style>
    
    .holder_default {
        margin-top: 1rem;
        height:6rem; 
        border: 3px dashed #ccc;
        text-align: center;
        cursor:pointer;
        max-height: 8rem;
    }

    #holder.hover { 
        margin-top: 1rem;
        height:6rem; 
        border: 3px dashed #0c0 !important; 
        text-align: center;
        cursor:pointer;
        max-height: 8rem;
    }

    .hidden {
        visibility: hidden;   
        height: 0;
        text-align: center;
    }
    .hidden-file{
        visibility: hidden;   
        max-height: 8rem !important;
        text-align: center;
    }

    .visible {
        visibility: visible;
        height: 8rem;
        text-align: center;
    }
</style>

<script>
function initializeDraggFile() {
    var holder = document.getElementById('holder');

    // Manejo de arrastrar y soltar archivos
    holder.ondragover = function () {
        this.className = 'hover';
        return false;
    };

    holder.ondrop = function (e) {
        e.preventDefault();
        var files = e.dataTransfer.files; // Asignar los archivos arrastrados al input
        
        const fileInputElement = document.getElementById('input-file');
        fileInputElement.files = e.dataTransfer.files; // Asignar los archivos arrastrados al input
        fileInputElement.dispatchEvent(new Event('change')); // Disparar el evento 'change' para Livewire
    };

    // Manejo de clic para seleccionar archivos
    holder.onclick = function (e) {
        const fileInputElement = document.getElementById('input-file');
        fileInputElement.click(); // Abrimos el selector de archivos

        // Manejar el evento de selección de archivos
        // fileInputElement.onchange = function () {
        //     var files = fileInputElement.files; // Obtén los archivos seleccionados del input
        //     workFiles(files); // Pasamos los archivos a la función
        // };
    };
}

function workFiles(files) {
    var previewContainer = document.getElementById('preview_container');
    
    previewContainer.innerHTML = ''; // Limpiar el contenedor de previsualización

    // Recorremos todos los archivos subidos    
    Array.from(files).forEach(function (file) {
        var reader = new FileReader();
        
        // Comprobamos si es una imagen o un PDF
        if (file.type.startsWith('image/')) {
            reader.onload = function (event) {
                Livewire.emit('filesDroped', event.target.result)
            };
        } else if (file.type === 'application/pdf') {
            var fileURL = URL.createObjectURL(file);
            Livewire.emit('filesDroped', fileURL)
        }
    });
}

function openPath(path) {
    window.open(path, '_blank');
}
</script>