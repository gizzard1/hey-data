<script>



let driverObj
let recorrido = @json(session('recorrido'));

function closeModal()
{
    $('#modalClientesForm').modal('hide')
}    

document.addEventListener('DOMContentLoaded', function () {
    
    // Obtiene el dato de sesión de PHP y lo pasa a JavaScript
    var recorrido = @json(session('recorrido'));

    if(recorrido) {   
        initializeTutorialPt6()
    }      
    
})

function changeExpirationDate(checkbox)
{
    const expirationDate = document.getElementById('expirationDate');
    const expDate = document.getElementById('form-check-expdate');

    if(checkbox.checked){
        expirationDate.removeAttribute("hidden"); 
        expDate.setAttribute("hidden"); 
    }else{
        expirationDate.setAttribute("hidden"); 
    }
}


function reloadCheck()
{
    const expDate = document.getElementById('form-check-expdate');
    expDate.removeAttribute("hidden");
}


document.addEventListener('livewire:load', function () {    
    
Livewire.emit('loadSearchBox', 1);

Livewire.on('createModalForm',function(){
    initializeTomSelect()
    $('#modalProductForm').modal('show')
})
Livewire.on('closeCreateModalForm',function(){
    $('#modalProductForm').modal('hide')
})
Livewire.on('openBrandModal',function(){
    $('#modalBrandForm').modal('show')
})
Livewire.on('closeModalBrand',function(){
    $('#modalBrandForm').modal('hide')
})
            

})

window.addEventListener('hideModal', event => {   
    $('#modalForm').modal('hide')
    Livewire.emit('categoriaAgregada'); // Llamar al método de Livewire
})
window.addEventListener('createProduct', event => {   
    initializeTomSelect()                 
})
window.addEventListener('closeCreateModalForm', event => {   
    $('#modalCreateForm').modal('hide')
})


function next() {
    driverObj.moveNext();
    event.preventDefault()

}

function pause()
{
    driverObj.destroy();
}

function play()
{    
    if(recorrido) {   
        initializeTutorialPt6_1()
    }
}
function play_1()
{
    if(recorrido) {   
        initializeTutorialPt6_2()
    }
}
function confirmDelete() {
    // Mostrar cuadro de diálogo de confirmación personalizado
    Swal.fire({
        title: '¿Seguro que desea eliminar los productos seleccionados?',
        text: 'Tome en cuenta que si los productos son eliminados, no se podrá recuperar esta información, pero seguirá presente en los movimientos hechos',
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

function openPath(path) {
    window.open(path, '_blank');
}

window.addEventListener('hideModalRewardForm', event => {   
    $('#modalRewardForm').modal('hide')
})

function initializeTutorialPt6_1()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Terminar',
        showProgress: true,
        // allowClose: false,
        steps: [
            { element: '#cart-products', popover: { title: 'Carrito de compras', description: 'Los productos que agregues se verán refrejados en esta sección y puedes modificar los campos: vendedor, piezas, descuento, iva y precio' ,side: "bottom",align: 'start' } },
            { element: '#cart-employee', popover: { title: 'Selecciona un empleado', description: 'En este campo se despliegan los empleados que has agregado. Busca el que añadimos recientemente' ,side: "bottom",align: 'start' } },
            { element: '#add-cust', popover: { title: 'Busca o agrega un cliente', description: 'Busca un cliente en el campo. Si no diste de alta un cliente, aún puedes hacerlo en el botón Añadir Cliente Nuevo.' ,side: "bottom",align: 'start' } },
        ]
    });

    driverObj.drive();
}
function initializeTutorialPt6_2()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Terminar',
        showProgress: true,
        // allowClose: false,
        steps: [
            { element: '#global-disccount', popover: { title: 'Agrega un descuento', description: 'Ingresa una cantidad a descontar sobre el total si lo deseas y presiona enter' ,side: "bottom",align: 'start' } },
            { element: '#set-method', popover: { title: 'Agrega una forma de pago', description: 'Haz clic en este botón' ,side: "bottom",align: 'start' } },
            { element: '#set-propina', popover: { title: 'Agrega una propina', description: 'Haz clic en este botón' ,side: "bottom",align: 'start' } },
            { element: '#methods', popover: { title: 'Formas de pago', description: 'En esta tabla se pueden visualizar las formas de pago agregadas' ,side: "bottom",align: 'start' } },
            { element: '#propinas', popover: { title: 'Propinas', description: 'En esta tabla se pueden visualizar las propinas agregadas' ,side: "bottom",align: 'start' } },
            { element: '#guardar', popover: { title: 'Guardar', description: 'Para guardar esta venta presiona este botón. No te preocupes si la cantidad a pagar no se completó. El sistema guardará esa información' ,side: "bottom",align: 'start' } },
            { element: '#aperturarCaja', popover: { title: 'Apertura de caja', description: 'Al ser tu primer venta del día y en el sistema, aparecerá esta ventana. Apertura para continuar' ,side: "bottom",align: 'start' } },
            { popover: { title: '¡Felicidades!', description: 'Cerraste tu primer venta' ,side: "bottom",align: 'start' } },
            { element: '#reviewModal', popover: { title: 'Reseña', description: 'Puedes calificar a tus clientes y darles etiquetas si lo deseas. Esto te dará referencias para el futuro' ,side: "bottom",align: 'start' } },
            { element: '#pendientes', popover: { title: 'Pendientes', description: 'Los movimientos que no se paguen en su totalidad se almacenan en este apartado. Haz clic' ,side: "bottom",align: 'start' } },
            { element: '#cobrar', popover: { title: 'Pendientes', description: 'Si haces clic en Cobrar, la información se carga de nuevo para terminar la venta' ,side: "bottom",align: 'start' } },
            { element: '#uso', popover: { title: 'Materiales de uso', description: 'Haz clic aquí' ,side: "bottom",align: 'start' } },
            { element: '#background', popover: { title: 'Materiales de uso', description: 'Este apartado te permite dar salida a productos que uses en el día y que no necesariamente estés cobrando al cliente. (No es requerido el campo cliente y consumido por)' ,side: "bottom",align: 'start' } },

            { element: '#menu', popover: { title: '¡Continuemos!', description: 'Vamos a Servicios',side: "right",align: 'start' } }, 
        ]
    });

    driverObj.drive();
}

function help() {
    recorrido=true
    initializeTutorialPt6()
}

function initializeTutorialPt6()
   {
        const driver = window.driver.js.driver;
        driverObj = driver({
            nextBtnText: 'Siguiente',
            prevBtnText: 'Anterior',
            doneBtnText: 'Terminar',
            showProgress: true,
            // allowClose: false,
            steps: [
                { element: '#createProduct', popover: { title: 'Crea un producto', description: 'Haz clic en este botón para abrir el formulario de productos' ,side: "left",align: 'start' } },
                { element: '#background', popover: { title: 'Llena el formulario', description: 'En este registro podrás almacenar los productos que usas en tu negocio' ,side: "bottom",align: 'start' } },
                { element: '#product-name', popover: { title: 'Llena el formulario', description: 'Vamos a darle un nombre' ,side: "bottom",align: 'start' } },
                { element: '#product-price', popover: { title: 'Llena el formulario', description: 'Ahora asignemos el precio con el que lo vendes' ,side: "bottom",align: 'start' } },
                { element: '#discount-product', popover: { title: 'Calcula un descuento', description: 'En este campo puedes calcular un porcentaje de descuento sobre la cantidad que agregues como precio. Escribe una cantidad al azar' ,side: "top",align: 'start' } },
                { element: '#disccount-price', popover: { title: 'Precio descuento', description: 'En este campo se muestra la cantidad calculada. Puedes borrar el campo si no quieres que se aplique el descuento' ,side: "left",align: 'start' } },
                { element: '#cancel-editing', popover: { title: 'Cancelar', description: 'Este botón cancela la operación. Continúa el recorrido' ,side: "left",align: 'start' } }, 
                { element: '#save-button', popover: { title: 'Guarda la información', description: 'Para guardar el producto debes hacer clic en este botón' ,side: "left",align: 'start' } }, 

                { element: '#background', popover: { title: 'Productos', description: 'Ahora tienes productos para venta en tu listado.' ,side: "bottom",align: 'start' } },
                { element: '#ventas-w', popover: { title: 'Agrega una venta', description: 'Haz clic en esta pestaña' ,side: "bottom",align: 'start' } },
                { element: '#background', popover: { title: 'Módulo Ventas', description: 'En este módulo se crean los movimientos que te generan ingresos, es decir, las ventas' ,side: "bottom",align: 'start' } },
                { element: '#searchInput', popover: { title: 'Busca productos', description: 'Si cuentas con lector de barras, posiciona el cursor en esta barra y escanea productos. De lo contrario continúa el recorrido' ,side: "bottom",align: 'start' } },
                { element: '#buscador-prod', popover: { title: 'Busca productos', description: 'En esta zona podrás buscarlos por nombre' ,side: "bottom",align: 'start' } },
                { element: '#cart-products', popover: { title: 'Busca productos', description: 'Los productos que agregues se verán refrejados en esta sección y puedes modificar los campos: vendedor, piezas, descuento, iva y precio' ,side: "bottom",align: 'start' } },
                { element: '#add-cust', popover: { title: 'Busca o agrega un cliente', description: 'Busca un cliente en el campo. Si no diste de alta un cliente, aún puedes hacerlo en el botón Añadir Cliente Nuevo.' ,side: "bottom",align: 'start' } },
                { element: '#add-cust', popover: { title: 'Activa recompensas', description: 'Puedes ofrecer al cliente activar su programa de puntos para que empiece a generar a partir de esta venta.' ,side: "bottom",align: 'start' } },
            ]
        });

        driverObj.drive();
   }

window.addEventListener('view-product', event => {   
        $('#modalViewProduct').modal('show')
})
window.addEventListener('close-review', event => {   
        $('#reviewModal').modal('hide')
})
window.addEventListener('next', event => {   
    next()
})
window.addEventListener('play_1', event => {
    play_1()   
})



function initializeTomSelect() {

    var elTom = document.querySelector('#tomCategoryP');

var myurl ="{{ route('data.categories') }}"
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
                    <span style="color:#1d3557 "> ${ escape(item.name) }</span>
                </div>
            </div>
        </div>`;
        },      
        },
        })
    }

</script>