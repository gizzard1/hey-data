
<div class="row mt-2">
    <div class="col-sm-12 col-md-6 mb-3 mt-3">
        <input style="background-color: transparent  !important;border-color:transparent !important;"  type="file" class="form-control" wire:model="gallery" accept="image/x-png,image/jpeg"" multiple id="formFileMultiple">
        @error('gallery.*')
        <span style="color: red;">*Corrige este campo* </span>
        @enderror
    </div>
    
</div>

<input type="file" class="form-control" id="input-file" wire:model="gallery" accept="image/x-png,image/jpeg,.pdf" multiple id="inputImg" hidden>

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
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 right-0"  wire:click="removeFile('{{ $photo->getFilename() }}')">x</button>
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