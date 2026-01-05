@if(Auth::user()->role=='admin')
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-1" style="color: #1d3557;">
                    Ajustes
                </h4>
            </div>
            <div class="card-body">
                <li>
                    <ul class="tree">
                        <li>
                            <a wire:click="$set('infoSelected','1')"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-businessplan" width="32" height="32" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M16 6m-5 0a5 3 0 1 0 10 0a5 3 0 1 0 -10 0" />
                                <path d="M11 6v4c0 1.657 2.239 3 5 3s5 -1.343 5 -3v-4" />
                                <path d="M11 10v4c0 1.657 2.239 3 5 3s5 -1.343 5 -3v-4" />
                                <path d="M11 14v4c0 1.657 2.239 3 5 3s5 -1.343 5 -3v-4" />
                                <path d="M7 9h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
                                <path d="M5 15v1m0 -8v1" />
                                </svg> Comisiones
                            </a>
                        </li>
                    </ul>
                        
                    <ul class="tree" id="pagos">
                        <li>
                            <a onclick="next()" wire:click="$set('infoSelected','2')"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-wallet" width="32" height="32" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
                                <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4" />
                                </svg> Pagos
                            </a>
                        </li>
                    </ul>
                        
                    <ul class="tree" id="salon">
                        <li>
                            <a onclick="next()" wire:click="$set('infoSelected','4')"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home-edit" width="32" height="32" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2c.645 0 1.218 .305 1.584 .78" />
                                <path d="M20 11l-8 -8l-9 9h2v7a2 2 0 0 0 2 2h4" />
                                <path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42 -3.39z" />
                            </svg> Salón
                            </a>
                        </li>
                    </ul>
                    <ul class="tree">
                        <li>
                            <a wire:click="$set('infoSelected','5')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users-group" width="32" height="32" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" />
                                <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M17 10h2a2 2 0 0 1 2 2v1" />
                                <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M3 13v-1a2 2 0 0 1 2 -2h2" />
                                </svg> Clientes
                            </a>
                            <ul>
                                <li><a  wire:click="$set('infoSelected','5')">Procedencias</a></li>
                            </ul>
                            <ul>
                                <li><a  wire:click="$set('infoSelected','6')">Categorías</a></li>
                            </ul>
                            <ul>
                                <li><a  wire:click="$set('infoSelected','9')">Recompensas</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="tree">
                        <li>
                            <a wire:click="$set('infoSelected','7')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages" width="32" height="32" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M7 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" />
                                <path d="M2 13.5v5.5l5 3" />
                                <path d="M7 16.545l5 -3.03" />
                                <path d="M17 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" />
                                <path d="M12 19l5 3" />
                                <path d="M17 16.5l5 -3" />
                                <path d="M12 13.5v-5.5l-5 -3l5 -3l5 3v5.5" />
                                <path d="M7 5.03v5.455" />
                                <path d="M12 8l5 -3" />
                                </svg> Categorías
                            </a>
                            <ul>
                                <li><a  wire:click="$set('infoSelected','7')">Productos</a></li>
                            </ul>
                            <ul>
                                <li><a  wire:click="$set('infoSelected','8')">Servicios</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="tree">
                        <li>
                            <a wire:click="$set('infoSelected','7')">
                                <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="32"
                                height="32"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#000000"
                                stroke-width="1"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                >
                                <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2m4 -14h6m-6 4h6m-2 4h2" />
                                </svg> Gastos
                            </a>
                            <ul>
                                <li><a  wire:click="$set('infoSelected','10')">Categorías</a></li>
                            </ul>
                            <ul>
                                <li><a  wire:click="$set('infoSelected','12')">Tipos</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="tree">
                        <li>
                            <a wire:click="$set('infoSelected','11')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="32" height="32" stroke-width="1.5">
                                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"></path>
                                <path d="M16 3v4"></path>
                                <path d="M8 3v4"></path>
                                <path d="M4 11h16"></path>
                                <path d="M7 14h.013"></path>
                                <path d="M10.01 14h.005"></path>
                                <path d="M13.01 14h.005"></path>
                                <path d="M16.015 14h.005"></path>
                                <path d="M13.015 17h.005"></path>
                                <path d="M7.01 17h.005"></path>
                                <path d="M10.01 17h.005"></path>
                                </svg> Citas
                            </a>
                            <ul>
                                <li><a  wire:click="$set('infoSelected','11')">Etiquetas</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        @if($infoSelected==1)
            @include('livewire.ajustes.modulos.comisiones')
        @elseif($infoSelected==2)
            <livewire:pago />
        @elseif($infoSelected==3)
            @include('livewire.ajustes.modulos.recompensas')
        @elseif($infoSelected==4)
            <livewire:configuration />
        @elseif($infoSelected==5)
            <livewire:procedencias />
        @elseif($infoSelected==6)
            <livewire:categoria-clientes />
        @elseif($infoSelected==7)
            <livewire:categoria-productos />
        @elseif($infoSelected==8)
            <livewire:categoria-servicios />
        @elseif($infoSelected==9)
            <livewire:recompensas />
        @elseif($infoSelected==10)
            <livewire:categoria-gastos />
        @elseif($infoSelected==11)
            <livewire:etiquetas />
        @elseif($infoSelected==12)
            <livewire:tipo-gastos />
        @endif
    </div>
</div>
@else
@include('livewire.sinPermisos')
@endif

@include('livewire.ajustes.jsS')
@include('livewire.ajustes.jsP')


<script>
    
function openPath(path) {
    window.open(path, '_blank');
}
window.addEventListener('openModal', event => {   
    $('#modalForm').modal('show')
})
window.addEventListener('openModalTags', event => {   
    $('#modalFormTags').modal('show')
})
window.addEventListener('openCreateException', event => {   
    $('#modalException').modal('show')
})
window.addEventListener('hideCreateException', event => {   
    $('#modalException').modal('hide')
})
window.addEventListener('hideModal', event => {   
    $('#modalForm').modal('hide')
})
window.addEventListener('hideModalTags', event => {   
    $('#modalFormTags').modal('hide')
})
function confirmDelete(exceptionId,type_comission) {
    // Mostrar cuadro de diálogo de confirmación personalizado
    Swal.fire({
        title: '¿Seguro que desea eliminar esta excepción?',
        text: '',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            showProcessing()
            // Si el usuario hace clic en "Aceptar", ejecutar el método de Livewire
            Livewire.emit('Delete', exceptionId, type_comission); // Llamar al método de Livewire
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
if(recorrido) {   
        initializeTutorialPt9()
    }

})
    
document.addEventListener('livewire:load', function () {
    Livewire.emit('loadSearchBox', 0);

    var mainWrapper = document.getElementById('main-wrapper')
    mainWrapper.classList.add('menu-toggle')
    initializeTomSelect();
    initializeTomSelect2();
});


// Obtiene el dato de sesión de PHP y lo pasa a JavaScript
let recorrido = @json(session('recorrido'));
let driverObj
function help() {
    recorrido = true
    initializeTutorialPt9()
}

function next() {
    driverObj.moveNext();
}
function prev() {
    driverObj.movePrevious();
}

function initializeTutorialPt9()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Listo',
        showProgress: true,
        // allowClose: false,
        steps: [
            { popover: { title: 'Configuración', description: 'Vamos a revisar varias configuraciones antes de empezar' ,side: "top",align: 'start' } },  

            { element: '#user-data', popover: { title: 'Comisiones', description: 'Por default, tus estilistas tendrán 0% de comisiones. Haz clic en una fila' ,side: "top",align: 'end' } },
            { popover: { title: 'Configuración de comisiones', description: 'En esta sección podrás administrar los porcentajes que ganan tus estilistas' ,side: "bottom",align: 'start' } },
            { element: '#ind_comision', popover: { title: 'Porcentajes generales', description: 'En estos campos puedes configurar el porcentaje separado por productos y servicios' ,side: "bottom",align: 'start' } },
            { element: '#clean-button', popover: { title: 'Limpia los campos', description: 'Esto elimina los porcentajes que llenaste' ,side: "left",align: 'start' } }, 
            { element: '#save-button', popover: { title: 'Guardar', description: 'Botón que guarda la información y a partir de este momento se aplicarán los porcentajes agregados' ,side: "left",align: 'start' } },  
            { element: '#exceptions', popover: { title: 'Excepciones', description: 'Si lo deseas, puedes agregar comisiones por producto o por categoría' ,side: "top",align: 'start' } },
            { element: '#ind_comision_p', popover: { title: 'Excepciones por producto', description: 'Haz clic en este botón' ,side: "top",align: 'start' } },
            { element: '#background', popover: { title: 'Formulario de excepción', description: 'Estos campos van a definir una excepción para comisiones. Llénalos y usa el botón Guardar' ,side: "top",align: 'start' } }
        ]
    });

    driverObj.drive();
}

window.addEventListener('play', event => {   
    initializeTutorialPt9_2()
})
window.addEventListener('play_1', event => {   
    initializeTutorialPt9_3()
})
function initializeTutorialPt9_2()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Terminar',
        showProgress: true,
        // allowClose: false,
        steps: [
            { element: '#background', popover: { title: 'Excelente', description: 'Has agregado una excepción. Continúa con el recorrido, por favor' ,side: "bottom",align: 'start' } },
            { element: '#pagos', popover: { title: 'Pagos', description: 'Haz clic aquí' ,side: "bottom",align: 'start' } },
            { element: '#pagosListado', popover: { title: 'Pagos', description: 'En este listado se encuentran los métodos de pago que puedes usar en tu salón.' ,side: "bottom",align: 'start' } },
            { element: '#pagosAdd', popover: { title: 'Pagos', description: 'Este botón te permite crear un método personalizado.' ,side: "bottom",align: 'start' } },
            { element: '#background', popover: { title: 'Pagos', description: 'Solamente debes asignarle un nombre y guardar' ,side: "bottom",align: 'start' } },
        ]
    });

    driverObj.drive();
}
function initializeTutorialPt9_3()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Terminar',
        showProgress: true,
        // allowClose: false,
        steps: [
            { element: '#background', popover: { title: 'Excelente', description: 'Has agregado un método de pago. Continúa con el recorrido, por favor' ,side: "bottom",align: 'start' } },
            { element: '#salon', popover: { title: 'Datos del salón', description: 'Haz clic aquí' ,side: "bottom",align: 'start' } },
            { element: '#salonListado', popover: { title: 'Datos del salón', description: 'En estos campos podrás personalizar los datos de tu salón para mostrar información en los tickets que generes.' ,side: "bottom",align: 'start' } },
            { element: '#salon-name', popover: { title: 'Nombre del salón', description: '¿Cuál es el nombre de tu negocio?' ,side: "bottom",align: 'start' } },
            { element: '#salon-phone', popover: { title: 'Teléfono de contacto', description: '¿A qué número se pueden contactar tus clientes?' ,side: "bottom",align: 'start' } },
            { element: '#schedule', popover: { title: 'Horario de apertura y cierre', description: 'Esto modificara el rango de horas en tu agenda' ,side: "bottom",align: 'start' } },
            { element: '#logo', popover: { title: 'Logo', description: 'Si tu negocio tiene un logo, aquí súbelo. Se recomienda una imagen png con fondo transparente' ,side: "bottom",align: 'start' } },
            { element: '#redes', popover: { title: 'Redes sociales', description: 'Estas redes pueden aparecer en tus tickets y mensajes automáticos. Recuerda que las redes sociales son importantes para crear tu imagen como negocio' ,side: "bottom",align: 'start' } },
            { element: '#guardarSalon', popover: { title: 'Datos del salón', description: 'No olvides guardar los cambios' ,side: "bottom",align: 'start' } },
            { element: '#menu', popover: { title: '¡Continuemos!', description: 'Volvamos a Agenda',side: "top",align: 'start' } }, 
        ]
    });

    driverObj.drive();
}

</script>
    <style>
        

    .ts-wrapper{
        background-color: transparent  !important;
        border-color:transparent !important;
        border-bottom:1px solid black !important;
    }
    input style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;"[type=number]::-webkit-inner-spin-button, 
    input style="background-color: transparent;border-color:transparent;border-bottom:1px solid black;"[type=number]::-webkit-outer-spin-button { 
    -webkit-appearance: none; 
    margin: 0; 
    }

    ul.tree, ul.tree ul {
        list-style-type: none;
        padding-left: 1rem;
    }
    ul.tree ul {
        display: none; /* Ocultar sublistas por defecto */
        border-left: 1px solid #ccc;
        margin-left: 10px;
    }
    ul.tree li {
        margin: 5px 0;
        padding: 5px 0;
        position: relative;
    }
    ul.tree li:hover > ul {
        display: block; /* Mostrar sublistas al hacer hover sobre el padre */
    }
    ul.tree li:before {
        content: "";
        position: absolute;
        top: 10px;
        left: -12px;
        border-bottom: 1px solid #ccc;
        width: 10px;
        height: 10px;
    }
    ul.tree li:last-child:before {
        border-left: none;
    }

</style>