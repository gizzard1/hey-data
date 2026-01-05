<script>
document.addEventListener('livewire:load', function () {
    Livewire.emit('loadSearchBox', 0);
    Livewire.on('recorrido',function(){
        help()
    })
})

window.addEventListener('createFingerprint', event => {   
    $('#modalFingerprint').modal('show')
        
})
window.addEventListener('closeFingerprint', event => {   
        $('#modalFingerprint').modal('hide')
        onStop()
})
    
window.addEventListener('modal-view-employee', event => {   
        $('#modalViewEmployee').modal('show')
})
    
   
function help() {
    initializeTutorialPt2()
}
   
let driverObj

function next() {
    driverObj.moveNext();
}

   function initializeTutorialPt2()
   {
        const driver = window.driver.js.driver;
        driverObj = driver({
            nextBtnText: 'Siguiente',
            prevBtnText: 'Anterior',
            doneBtnText: 'Listo',
            showProgress: true,
            // allowClose: false,
            steps: [
                { element: '#basic-data', popover: { title: 'Crea un estilista', description: 'Llena los campos básicos de tu estilista para poder visualizar en tu agenda. (El campo requerido es únicamente el nombre)' ,side: "right",align: 'start' } },
                { element: '#user-data', popover: { title: 'Crea su usuario', description: 'Si deseas que tu estilista tenga acceso al sistema, crea su perfil en esta sección. (Todos los campos son requeridos en caso en que desees crear el perfil).' ,side: "right",align: 'start' } },
                { element: '#save-info', popover: { title: 'Guarda la información', description: 'Para almacenar la información del estilista haz clic en este botón' ,side: "top",align: 'start' } },
                { element: '#list-employee', popover: { title: 'Consulta tus estilistas', description: 'En este listado se muestran los estilistas que has agregado.' ,side: "left",align: 'start' } },
                { element: '#table-body', popover: { title: 'Edita un estilista', description: 'Haz clic en alguna fila' ,side: "left",align: 'start' } }, 
                { element: '#data-employee', popover: { title: 'Edita un estilista', description: 'A continuación se carga la información del empleado en esta sección y se podrá editar libremente' ,side: "left",align: 'start' } }, 
                { element: '#cancel-editing', popover: { title: 'Cancelar operación', description: 'Si lo deseas, puedes cancelar esta edición para evitar que se modifique algún dato' ,side: "top",align: 'start' } },
                
                { element: '#menu', popover: { title: '¡Continuemos!', description: 'Vamos a Inventarios',side: "right",align: 'start' } }, 
            ]
        });

        driverObj.drive();
   }
   

   document.addEventListener('DOMContentLoaded', function(){
        // Obtiene el dato de sesión de PHP y lo pasa a JavaScript
        var recorrido = @json(session('recorrido'));

        if(recorrido) {   
            initializeTutorialPt2()
        }

        flatpickr(document.getElementsByClassName('flatpickr'),{
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: {
                firstDateofWeek:1,
                weekdays: {
                    shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                    longhand: [
                    "Domingo",
                    "Lunes",
                    "Martes",
                    "Miércoles",
                    "Jueves",
                    "Viernes",
                    "Sábado",
                    ],
                },    
                months: {
                    shorthand: [
                    "Ene",
                    "Feb",
                    "Mar",
                    "Abr",
                    "May",
                    "Jun",
                    "Jul",
                    "Ago",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dic",
                    ],
                    longhand: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre",
                    ],
                }
            }
        })
   })
