    <div class="card" style="background-color: white;" id="salonListado">
        <div class="card-header">
            <div class="d-flex">
                <div class="separator" style="background-color:#E2BBB4"></div>
                    <div class="mr-auto mt-3">
                        <h4 class="card-title"><a wire:click="$emit('infoSelected','1')">Ajustes</a>/ Datos del Salón</h4>
                    </div>
            </div>
        </div>
        <div class="card-body form-config" id="data-salon">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" id="salon-name">
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
                    
                    <div class="form-group d-flex" style="column-gap:1rem" id="salon-phone">
                        <div>
                            <select wire:model.defer="lada" style="appearance: none;" id="prefijos" name="prefijos" class="form-control" style="width:5rem">
                                <option value="+52">+52 - México</option>
                                <option value="+1">+1 - Estados Unidos, Canadá</option>
                                <option value="+44">+44 - Reino Unido</option>
                                <option value="+49">+49 - Alemania</option>
                                <option value="+33">+33 - Francia</option>
                                <option value="+34">+34 - España</option>
                                <option value="+39">+39 - Italia</option>
                                <option value="+81">+81 - Japón</option>
                                <option value="+61">+61 - Australia</option>
                                <option value="+86">+86 - China</option>
                                <option value="+91">+91 - India</option>
                                <option value="+55">+55 - Brasil</option>
                                <option value="+7">+7 - Rusia</option>
                                <option value="+82">+82 - Corea del Sur</option>
                                <option value="+66">+66 - Tailandia</option>
                            </select>
                            <label for="prefijos">Lada </label>
                        </div>
                        <div>
                            <input placeholder="Teléfono o celular" wire:model.defer="salon.phone" type="number" required class="form-control" id="phone" style="width:16rem">
                            @error('salon.phone') <span class="text-danger">*Favor de llenar este campo* </span> @enderror
                            <label for="phone">Teléfono *</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <input wire:model.defer="salon.email" type="text" class="form-control"
                            placeholder="Email">
                        @error('salon.email') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <label>Email</label>
                    </div>
                    <div class="form-group d-flex" style="column-gap:1rem" id="schedule">
                        <div>
                            <input wire:model.defer="salon.start" class="form-control" type="time" placeholder="<?php echo date('H:i'); ?>">
                            @error('salon.start') <span class="text-danger">*Corrige este campo* </span> @enderror
                            <label>Horario de apertura</label>
                        </div>
                        <div>
                            <input wire:model.defer="salon.end" class="form-control" type="time" placeholder="<?php echo date('H:i'); ?>">
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

                </div>
                <div class="col-md-6" id="redes">
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
                    <div class="form-group form-switch">
                        <input wire:model.defer="salon.simulador" type="checkbox" id="simulador">
                        @error('salon.youtube') <span class="text-danger">*Corrige este campo* </span> @enderror
                        <label for="simulador">Permitir simular fecha</label>
                    </div>
                    
                    <div class="form-group float-left">
                        <div wire:loading wire:target="photo">Cargando imagen...</div>
                        @if ($photo!=null)
                            <img src="{{ $photo }}" class="img-fluid rounded" alt="Img">
                            <button type="button" class="btn btn-danger btn-sm float-right"  wire:click="removeFile">x</button>
                            <button type="button" onclick="openPath('{{ asset($photo) }}')" class="btn btn-secondary btn-sm float-right"><i class="las la-eye"></i></button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-sm btn-info float-right save" wire:click="Store" style="background-color: #9E846D;border-color:#9E846D" onclick="next()" id="guardarSalon">Guardar cambios</button>
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
    
    //  flatpickr(document.getElementsByClassName('flatpickrHr'),{
    //     enableTime: true,
    //     noCalendar: true,
    //     dateFormat: "H:i",
    // })
    if(recorrido) {   
        initializeTutorialPt4()
    }
    })
    // document.addEventListener('livewire:load', function () {
    
    //     flatpickr(document.getElementsByClassName('flatpickrHr'),{
    //         enableTime: true,
    //         noCalendar: true,
    //         dateFormat: "H:i",
    //     })
    // })
    

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