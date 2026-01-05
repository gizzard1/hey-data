<div>
    <div class="row">
        <div class="col-md-4">
            <div class="card" style="background-color: white;">
                <div class="card-header">
                    <h4 style="color:#9D1466">Editar Datos del Salón</h4>
                </div>
                <div class="card-body form-config" id="data-salon">

                    <div class="form-group">
                        <input wire:model.defer="salon.name" type="text"
                            class="form-control" placeholder="Nombre" autocomplete="nope">
                        @error('salon.name') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <label>Nombre</label>
                    </div>
                    <div class="form-group">
                        <input wire:model.defer="salon.rfc"  type="rfc"
                            class="form-control" placeholder="RFC">
                        @error('salon.rfc') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <label>RFC</label>
                    </div>
                    <div class="form-group">
                        <input wire:model.defer="salon.phone" type="text"
                            class="form-control" placeholder="Teléfono" autocomplete="nope">
                        @error('salon.phone') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <label>Teléfono</label>
                    </div>
                    <div class="form-group">
                        <input wire:model.defer="salon.email" type="text" class="form-control"
                            placeholder="Email">
                        @error('salon.email') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <label>Email</label>
                    </div>
                    <div class="form-group text-center" id="schedule">
                        <div style="display: inline-block;width:15rem">
                            
                            <input wire:model.defer="salon.start" class="text-center form-control flatpickr" placeholder="<?php echo date('H:i'); ?>">
                            @error('salon.start') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Horario de apertura</label>

                        </div>
                        
                        <div style="display: inline-block;width:15rem">

                            <input wire:model.defer="salon.end" class="text-center form-control flatpickr" placeholder="<?php echo date('H:i'); ?>">
                            @error('salon.end') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Horario de cierre</label>

                        </div>
                    </div>
                    <div id="logo" class="form-group">
                        <div >
                            <input style="background-color: transparent  !important;border-color:transparent !important;"  type="file" class="form-control" wire:model="photo" accept="image/x-png,image/jpeg">
                            <label>Logo</label>
                            @error('photo.*')
                            <span style="color: red;">*Corrige este campo* </span>
                            @enderror
                        </div>
                        
                    </div>

                    <div class="mt-2">
                        <div wire:loading wire:target="photo">Cargando imagen...</div>
                        @if (!empty($photo))
                        <div class="row">
                            <div class="mb-2">
                                <div class="media">
                                    <img src="{{ $photo->temporaryUrl() }}" class="img-fluid rounded" alt="img">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                        <a style="cursor: pointer;color:#B59377" id="showMoreButton" class="">Ver más</a>
                        <div id="moreContainer"style="display: none;">
                    <div class="form-group">
                        <input wire:model.defer="salon.webPage" type="text" class="form-control"
                            placeholder="Página Web">
                        @error('salon.webPage') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <label>Página Web</label>
                    </div>
                    <div class="form-group">
                        <input wire:model.defer="salon.instagram" type="text" class="form-control"
                            placeholder="Instagram">
                        @error('salon.instagram') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <label>Instagram</label>
                    </div>
                    <div class="form-group">
                        <input wire:model.defer="salon.facebook" type="text" class="form-control"
                            placeholder="Facebook">
                        @error('salon.facebook') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <label>Facebook</label>
                    </div>
                    <div class="form-group">
                        <input wire:model.defer="salon.tiktok" type="text" class="form-control"
                            placeholder="Tiktok">
                        @error('salon.tiktok') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <label>Tiktok</label>
                    </div>
                    <div class="form-group">
                        <input wire:model.defer="salon.youtube" type="text" class="form-control"
                            placeholder="Youtube">
                        @error('salon.youtube') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <label>Youtube</label>
                    </div>
                    
                            <a style="cursor: pointer;color:#B59377" class="" id="hideButton">Ver menos</a>
                        </div>
                        
                    
                    <script>
                        // Obtén referencias a los elementos HTML
                        const showMoreButton = document.getElementById('showMoreButton');
                        const moreContainer = document.getElementById('moreContainer');
                        const hideButton = document.getElementById('hideButton');

                        // Maneja el clic en el botón "Ver más"
                        showMoreButton.addEventListener('click', () => {
                            // Muestra el contenedor del campo de fecha
                            moreContainer.style.display = 'block';
                            // Oculta el botón "Ver más"
                            showMoreButton.style.display = 'none';
                        });
                        hideButton.addEventListener('click', () => {
                            // Muestra el contenedor del campo de fecha
                            moreContainer.style.display = 'none';
                            // Oculta el botón "Ver más"
                            showMoreButton.style.display = 'block';
                        });
                    </script>
                    
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-dark light float-left hidden"
                        wire:click="cancelEdit">Cancelar</button>
                    <button id="save-button" class="btn btn-sm btn-info float-right save" wire:click="Store" style="background-color: #9E846D;border-color:#9E846D" onclick="next()">Guardar</button>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card p-4">
                <div class="card-body text-center">
                    <div>
                        @if(isset($salon->picture))
                        <img src="{{ asset($salon->picture) }}" class="img-fluid rounded"
                            alt="{{ $salon->name }}">
                        @endif
                    </div>
                    <br>
                    <h4>
                    {{ $salon->name ?? 'Configura un nombre para tu salón y un horario para mostrar en tu agenda' }}</h4>
                    <br>
                    {{ 'Teléfono: ' . $salon->phone ?? '' }}
                    <br>
                    {{ 'Correo electrónico: ' . $salon->email ?? '' }}
                    <br>
                    {{ 'Página Web: ' . $salon->webPage ?? '' }}
                    <br>
                    {{ 'Instagram: ' . $salon->instagram ?? '' }}
                    <br>
                    {{ 'Tiktok: ' . $salon->tiktok ?? '' }}
                    <br>
                    {{ 'Facebook: ' . $salon->facebook ?? '' }}
                    <br>
                    {{ 'Canal de Youtube: ' . $salon->youtube ?? '' }}
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .img-fluid{
        width: 20rem;
    }
</style>
<script>
    
let driverObj
let recorrido = @json(session('recorrido'));
   document.addEventListener('DOMContentLoaded', function(){
    
     flatpickr(document.getElementsByClassName('flatpickr'),{
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
    })
    if(recorrido) {   
        initializeTutorialPt4()
    }
    })
    

function next() {
    driverObj.moveNext();
}

function initializeTutorialPt4()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Listo',
        showProgress: true,
        // allowClose: false,
        steps: [
            { element: '#data-salon', popover: { title: 'Datos del salón', description: 'Llena estos campos de tu salón. Dale tu toque ;)' ,side: "right",align: 'start' } },  
            { element: '#schedule', popover: { title: 'Horario de atención', description: 'Este horario hará que tu agenda tome como referencia las horas que trabaja tu salón' ,side: "left",align: 'start' } },  
            { element: '#logo', popover: { title: 'Agrega un logotipo', description: 'Para personalizar tu aplicación. Se recomienda usar una imagen png sin fondo' ,side: "left",align: 'start' } },  

            { element: '#save-button', popover: { title: 'Guardar', description: 'Guarda la información' ,side: "left",align: 'start' } }, 
            { element: '#ham', popover: { title: '¡Continuemos!', description: 'Despliega el menú principal' ,side: "right",align: 'start' } },
            { element: '#menu', popover: { title: 'Menú', description: 'Vamos a Citas',side: "right",align: 'start' } }, 
        ]
    });

    driverObj.drive();
}
    
</script>