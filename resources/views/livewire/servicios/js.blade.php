<script>
document.addEventListener('DOMContentLoaded', function () {
    initializeTomSelect()                 

})
    
function confirmDelete() {
    // Mostrar cuadro de diálogo de confirmación personalizado
    Swal.fire({
        title: '¿Seguro que desea eliminar los servicios seleccionados?',
        text: 'Tome en cuenta que si los servicios son eliminados, no se podrá recuperar esta información, pero seguirá presente en los movimientos hechos',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            showProcessing()
            // Si el usuario hace clic en "Aceptar", ejecutar el método de Livewire
            Livewire.emit('eliminar'); // Llamar al método de Livewire
        }
    });
}
document.addEventListener('livewire:load', function () {      
    Livewire.emit('loadSearchBox', 2);
    
    // Obtiene el dato de sesión de PHP y lo pasa a JavaScript
    var recorrido = @json(session('recorrido'));

    if(recorrido) {   
        initializeTutorialPt8()
    }  

})

window.addEventListener('view-service', event => {   
        $('#modalViewService').modal('show')
})

window.addEventListener('hideModal', event => {   
    $('#modalForm').modal('hide')
    Livewire.emit('categoriaAgregada'); // Llamar al método de Livewire
})

window.addEventListener('closeCreate', event => {   
    $('#modalCreateForm').modal('hide')
})
window.addEventListener('openCreate', event => {   
    $('#modalCreateForm').modal('show')
})
window.addEventListener('hideModalRewardForm', event => {   
    $('#modalRewardForm').modal('hide')
})

function openPath(path) {
    window.open(path, '_blank');
}

window.addEventListener('next', event => {   
    next()
})
let driverObj

function next() {
    driverObj.moveNext();
    event.preventDefault()

}
function pause()
{
    driverObj.destroy();
}
function help() {
    initializeTutorialPt8()
}

function initializeTutorialPt8()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Terminar',
        showProgress: true,
        // allowClose: false,
        steps: [
            { element: '#crateCategory', popover: { title: 'Crea una categoría de servicio', description: 'Haz clic en Crear Categoría para desplegar el formulario' ,side: "right",align: 'start' } },
            { element: '#background', popover: { title: 'Crea una categoría de servicio', description: 'Dale nombre a tu primer categoría' ,side: "right",align: 'start' } },
        ]
    });

    driverObj.drive();
}
function play()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Terminar',
        showProgress: true,
        // allowClose: false,
        steps: [
            { element: '#background', popover: { title: '¡Excelente!', description: 'Recuerda que las categorías te ayudarán a separar tus servicios cada que lo requieras' ,side: "bottom",align: 'start' } },
            { element: '#createService', popover: { title: 'Crea un servicio', description: 'Haz clic en este botón para abrir el formulario de servicios' ,side: "right",align: 'start' } },
            { element: '#background', popover: { title: 'Llena el formulario', description: 'Los campos requeridos son nombre y precio público.' ,side: "bottom",align: 'start' } },
            { element: '#service-name', popover: { title: 'Llena el formulario', description: 'Vamos a darle un nombre al servicio' ,side: "bottom",align: 'start' } },
            { element: '#service-price', popover: { title: 'Llena el formulario', description: 'Ahora asignemos el precio con el que lo ofreces' ,side: "bottom",align: 'start' } },
            { element: '#duration', popover: { title: 'Agrega una duración predeterminada', description: 'No te preocupes si no es precisa, eso se puede ajustar durante la creación de una cita' ,side: "top",align: 'start' } },
            { element: '#input-disccount', popover: { title: 'Calcula un descuento', description: 'En este campo puedes calcular un descuento sobre la cantidad que agregues como precio. Escribe una cantidad y presiona enter.' ,side: "top",align: 'start' } },
            { element: '#disccount-price', popover: { title: 'Precio descuento', description: 'En este campo se muestra la cantidad calculada. Si cambias de opinión, puedes borrar este campo' ,side: "left",align: 'start' } },
            { element: '#save-button', popover: { title: 'Guarda la información', description: 'Con este botón guardaremos la información' ,side: "left",align: 'start' } }, 
            { element: '#background', popover: { title: '¡Felicidades!', description: 'Ahora tienes un servicio para agendar citas.' ,side: "bottom",align: 'start' } },

            { element: '#ajustes', popover: { title: '¡Continuemos!', description: 'Vamos al último módulo Ajustes',side: "top",align: 'start' } }, 
        ]
    });

    driverObj.drive();
}



function initializeTomSelect() {

    var elTom = document.querySelector('#tomCategoryS');

var myurl ="{{ route('data.categoriesS') }}"
        new TomSelect(elTom, {
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],  
        load: function(query, callback) {        
        var url = myurl + '?q=' + encodeURIComponent(query)
        fetch(url)
        .then(response => response.json())
        .then(json => {
        callback(json)        
        }).catch(()=>{
        callback()
        });        
        },
        onChange: function(value) {
         @this.set('categoriesList',value)
        },
        render: {
        option: function(item, escape) {
        return `<div >
            <div >
                <div >                
                    <span style="color:#B59377"> ${ escape(item.name) }</span>
                </div>
            </div>
        </div>`;
        },      
        },
        })
    }

</script>

