@if($totales)
<div>
    
    <div class="d-flex" style="justify-content:end;margin-right:3rem">
            
        <a type="button" id="date-time" wire:ignore.self wire:model="currentDateC" class="flatpickr" wire:change="dateSelected" onclick="pause()"><h5 class="fs-14 mb-0" style="justify-content: space-between;" >{{ $currentDate }} ({{ $this->minutes_qty }} min.)</h5></a>

        <a id="return" onclick="prevDouble()" wire:click="returnModal" style="text-decoration: underline;cursor:pointer;margin-left:3rem">Regresar</a>
    </div>
    <div class="card">
                
        <div class="card-header flex-wrap" style="row-gap:1rem">
            
            <div>
                <button id="clean-cart" wire:click="$set('ventaConstrained',1)" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Añadir venta</button>
            </div>
            <div>
                <button id="clean-cart" wire:click="clear" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Limpiar carrito</button>
            </div>
            <div>
                <button id="reprint" wire:click.prevent="reimpresion" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Reimprimir último ticket</button>
            </div>
            <div>
                <button onclick="CancelAllDate()" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Cancelar cita</button>
            </div>
            
            <div x-data="{ open: false }" @click.away="open = false">
                <div style="display:flex">
                    <!-- Input de búsqueda enlazado dinámicamente con wire:model -->
                     
                    <input wire:model="queryServices" @focus="open=true" @click="open = true" type="text" class="form-control" autocomplete="off" placeholder="Escriba el nombre del servicio" style="width: 30rem;"> 
                    
                    <!-- Icono de búsqueda -->
                    <div class="input-group-append">
                        <i style="background-color: white;" class="input-group-text">
                            <i class="flaticon-381-search-2"></i>
                        </i>
                    </div>
                </div>
                
                <!-- Desplegable de resultados -->
                <div>
                    <ul x-show="open" class="list-group float-right" style="width: 30rem; position: absolute; z-index:1; max-height: 300px; overflow-y: auto;">
                    @foreach ($servicios as $index => $item)
                        <li wire:click="$emit('addNewService', {{ $item->id }})" @click="open=false;" class="list-group-item list-group-item-action" style="cursor:pointer; color:#6E6E6E">{{ $item->name }} {{ $item->duration }} min. | ${{ number_format($item->gross_price, 2, '.', ',') }}</li>
                    @endforeach 
                    </ul>
                </div>
            </div>
        </div>

        

        
        @endif
        <div class="row" style="justify-content: center;">
            @if(isset($cartInfo)!==null)
                @if($totales)
                <div class="col-sm-12 col-md-12"> 
                    @include('livewire.cart-view-services')
                    @if($ventaConstrained)
                        <livewire:ventas :constrained="1"/>
                    @endif
                </div>
                <div class="col-sm-12 col-md-9">
                    @include('livewire.payment')
                    
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
                    
                </div>
                @endif
                @include('livewire.ventas.totales') 
            @endif
            @include('livewire.ventas.js')
        </div>
        @if($totales)
        
    </div>
</div>


@section('content')
    <livewire:search />
@endsection
@endif

<style>
    .swal2-textarea{
        background-color: transparent;
        color: black;
    }
</style>