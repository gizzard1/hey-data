@if($totales)
<div>
    
    <div class="d-flex" style="justify-content:end;margin-right:3rem">
            
        <a type="button" id="date-time" wire:ignore.self wire:model="currentDateC" class="flatpickr" wire:change="dateSelected" onclick="pause()"><h5 class="fs-14 mb-0" style="justify-content: space-between;" >{{ $currentDate }} ({{ $this->minutes_qty }} min.)</h5></a>

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
                <button id="reprint" wire:click.prevent="reimpresionTicket" class="btn btn-dark btn-sm btn-block " style="background-color: white;color:#60060F;border-color:#E2BBB4">Reimprimir ticket</button>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeFlatpickr();
})


function initializeFlatpickr() {
    flatpickr(document.getElementsByClassName('flatpickr'), {
        enableTime: false,
        dateFormat: 'Y-m-d',
        locale: {
            firstDateofWeek: 1,
            weekdays: {
                shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                longhand: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"]
            },
            months: {
                shorthand: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                longhand: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"]
            }
        }
    })
}

document.addEventListener('DOMContentLoaded', function() {
    let printWindow = null; // Variable para almacenar la ventana abierta

    Livewire.on('print_on',action =>{
        var tipo = action[0]
        var data = action[1]
        var ruta = "{{ route('reporte') }}"
        var variable = '/' + tipo + '/' + data
        var url = ruta + variable
        var back = "{{ route('agenda') }}"
        var width = 500
        var height = 500

        // Calcular la posición para centrar la ventana
        var left = (screen.width / 2) - (width / 2)
        var top = (screen.height / 2) - (height / 2)

        // Si la ventana ya está abierta, reutilizarla y actualizar la URL
        if (printWindow && !printWindow.closed) {
            printWindow.location.href = url;
            printWindow.focus();
        } else {
            printWindow = window.open(url, '_blank', `width=${width},height=${height},toolbar=no,scrollbars=yes,resizable=yes,left=${left},top=${top}`);
        }
    })
})

function CancelAllDate() {      
    Swal.fire({
    title: '¿SEGURO QUE DESEAS CANCELAR LA CITA?',
        input: "textarea",
        inputPlaceholder: "Motivo",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
    }).then((result) => {
    if (result.isConfirmed) {    
        // Captura el valor del textarea
        const motivo = result.value
        // Emite el evento Livewire junto con el valor
        Livewire.emit('cancelacion', motivo)
    }
    })
}

window.addEventListener('returnCustomersView', () => {
    window.location.href = '/'; // o usa una ruta diferente si lo necesitas
});
</script>