<script>
function mostrarUsosCfdi() {
    const checked = document.getElementById('billingDate').checked;
    const divs = document.querySelectorAll('.usosCfdiSelector');
    divs.forEach(div => {
        div.style.display = checked ? 'block' : 'none';
    });
}

window.addEventListener('reloadForm', event => {
	})
function initTomSelect() {
    var elTom = document.querySelector('#tomServices')
    if (elTom.tomselect) {
        elTom.tomselect.destroy() // Destruye cualquier instancia previa para evitar conflictos
    }
    var myurl = "{{ route('data.services') }}"

    var control = new TomSelect(elTom, {
        labelField: 'name',
        valueField: 'id',
        searchField: ['name', 'duration', 'gross_price'],
        load: function(query, callback) {
            if(recorrido) {   
                pause()
            }
            var url = myurl + '?q=' + encodeURIComponent(query)
            fetch(url)
                .then(response => response.json())
                .then(json => {
                    callback(json)
                })
                .catch(() => {
                    callback()
                })
        },
        onChange: function(value) {
            // Emitir el evento Livewire
            Livewire.emit('addNewService', value)

            // Usar addItem para mantener el estado del input
            control.addItem(value)
            // Resetear el input manualmente
            control.setTextboxValue('')        

        },
        render: {
            option: function(item, escape) {
                return `
                    <div class="py-2 d-flex">
                        <div>
                            <div class="mb-0">
                                <span style="color:#B59377">
                                    ${escape(item.name)} ${escape(item.duration)} min. | ${escape(item.gross_price)}
                                </span>
                            </div>
                        </div>
                    </div>
                `
            },
        },
    })

}

function closeModal()
{
    $('#modalClientesForm').modal('hide')
}

    // Re-inicializa TomSelect después de cada actualización de Livewire
    document.addEventListener('livewire:update', function () {
        initializePopper()
    })

document.addEventListener('livewire:load', function () {
    Livewire.emit('loadSearchBox', 0);
    
    Livewire.on('reloadToms', event => {
    })

    Livewire.on('activateModal', function(){
        $('#modalClientesForm').modal('show')
    })
    Livewire.on('closeModalForm', function(){
        closeModal()
    })

    Livewire.on('closeForm', function(){
        closeForm()
    })


    Livewire.on('dateUpdated-movimientos', function (newDate,newDateEnd,newDataEmpleados) {
        // Actualizar el contenido donde se muestra la fecha
        document.getElementById('currentDate').innerText = newDate
        document.getElementById('currentDateModal').innerText = newDate
        if(newDateEnd!==''){
            document.getElementById('currentDateEnd').innerText = ' - ' + newDateEnd
        }else{
            document.getElementById('currentDateEnd').innerText = ''
        }
    })   

    Livewire.on('abrirForm',function(){
        abrirForm()
    })       
    Livewire.on('loadFlatForm',function(){
            
        flatpickr(document.getElementsByClassName('flatpickrForm'), {
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
            },
            onChange: function(selectedDate, dateStr, instance) {
                @this.emit('changeDate', selectedDate)
            }
        })
    })

    Livewire.on('reloadFlat',function(){
        initializeFlatpickr()
    })
    
})
    
let driverObj
let recorrido = @json(session('recorrido'));


document.addEventListener('DOMContentLoaded', function() {
    let printWindow = null; // Variable para almacenar la ventana abierta
        // Obtiene el dato de sesión de PHP y lo pasa a JavaScript
    if(recorrido) {   
        initializeTutorial_1()
    }
    initializeFlatpickr()
    initializeDraggFile()
    
    window.addEventListener("cerrarReview", () => {
        $('#reviewModal').modal('hide');
    });
    
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

function initializeTutorial() {
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Listo',
        showProgress: true,
        // allowClose: false,
        steps: [
            { element: '#user-profile', popover: { title: 'Usuario', description: 'En este menú puedes modificar tu perfil, salir de sesión y tus empleados podrán consultar sus comisiones desde su propio usuario' ,side: "right",align: 'start' } },
            // { element: '#flatResource', popover: { title: 'Abrir el calendario', description: 'Para ubicar la fecha en que desees visualizar las citas',side: "right",align: 'start' } },
            { element: '#menu', popover: { title: 'Menú', description: 'En esta zona podrás dirigirte a los diferentes módulos del sistema. Haz clic en Clientes',side: "right",align: 'start' } },
            // { element: '#flatResource', popover: { title: 'Abrir el calendario', description: 'Para ubicar la fecha en que desees visualizar las citas',side: "right",align: 'start' } },
            // { element: '#agenda', popover: { title: 'Agenda', description: 'Haz clic en cualquier casilla para generar una cita',side: "top",align: 'start' } },
        ]
    });

    driverObj.drive();
    
    
}
function next() {
    driverObj.moveNext();
}

function prevDouble(){
    driverObj.movePrevious();
    driverObj.movePrevious();
}

function pause()
{
    driverObj.destroy();
}

function play()
{
    initializeTutorial_2()
}
function play_1()
{
    initializeTutorial_3()
}
function play_3()
{
    if(recorrido){
        initializeTutorial_3_1()
    }
}

function initializeTutorial_2()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Terminar',
        showProgress: true,
        // allowClose: false,
        steps: [
            { element: '#cart-view', popover: { title: 'Servicios agregados', description: 'En esta tabla encontrarás los servicios que hayas agregado. Puedes modificar el estilista que realizará el servicio y el horario en que se atenderá' ,side: "bottom",align: 'start' } },
            { element: '#clientes-input', popover: { title: 'Clientes', description: 'Busca o crea un nuevo cliente' ,side: "bottom",align: 'start' } },
        ]
    });

    driverObj.drive();
}
function initializeTutorial_3_1()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Terminar',
        showProgress: true,
        // allowClose: false,
        steps: [
            { element: '#buscar-serv', popover: { title: 'Vuelve a buscar un servicio', description: 'Busca un servicio en este apartado' ,side: "bottom",align: 'start' } },
        ]
    });

    driverObj.drive();
}
function initializeTutorial_3()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Terminar',
        showProgress: true,
        // allowClose: false,
        steps: [
            { element: '#clean-cart', popover: { title: 'Limpiar carrito', description: 'Con este botón puedes borrar la información agregada.' ,side: "bottom",align: 'start' } },
            { element: '#remember', popover: { title: 'Recordar cita', description: 'Si se marca esta casilla, al cliente le llegará un mensaje de recordatorio a su whatsapp del cual podrás obtener una respuesta de confirmación y contar con su presencia' ,side: "bottom",align: 'start' } },
            { element: '#agendar', popover: { title: 'Agendar', description: 'Haz clic aquí para agendar la cita' ,side: "bottom",align: 'start' } },
            { element: '#background', popover: { title: '¡Felicidades!', description: 'Agendaste tu primera cita. ' ,side: "bottom",align: 'start' } },
            { element: '#calendar-master', popover: { title: 'Eventos', description: 'Haz clic en el evento que se genero' ,side: "bottom",align: 'start' } },
            { element: '#background', popover: { title: 'Eventos', description: 'Se ha cargado la cita que acabamos de crear. Podemos modificar los mismos datos. Haremos algo distinto' ,side: "bottom",align: 'start' } },
            { element: '#showAdvanced', popover: { title: 'Eventos', description: 'Haz clic en vista avanzada.' ,side: "right",align: 'start' } },
            { element: '#background', popover: { title: 'Vista avanzada', description: 'Hemos pasado a una vista similar a la de ventas. Donde podemos cobrar la cita o algún movimiento pendiente relacionado a las citas.' ,side: "top",align: 'start' } },
            { popover: { title: 'Ahora depende de ti', description: 'El resto tiene similitud con las ventas.' ,side: "bottom",align: 'start' } },
            { element: '#user-profile', popover: { title: 'Ayuda', description: 'Si quieres reiniciar el recorrido, haz clic aquí y luego en el botón de ayuda.' ,side: "right",align: 'start' } },
             
        ]
    });

    driverObj.drive();
}

function initializeTutorial_34()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Terminar',
        showProgress: true,
        // allowClose: false,
        steps: [
            { element: '#clean-cart', popover: { title: 'Agrega un descuento', description: 'Ingresa una cantidad a descontar sobre el total si lo deseas y presiona enter' ,side: "bottom",align: 'start' } },
            { element: '#set-method', popover: { title: 'Agrega una forma de pago', description: 'Haz clic en este botón' ,side: "bottom",align: 'start' } },
            { element: '#set-propina', popover: { title: 'Agrega una propina', description: 'Haz clic en este botón' ,side: "bottom",align: 'start' } },
            { element: '#methods', popover: { title: 'Formas de pago', description: 'En esta tabla se pueden visualizar las formas de pago agregadas' ,side: "bottom",align: 'start' } },
            { element: '#propinas', popover: { title: 'Propinas', description: 'En esta tabla se pueden visualizar las propinas agregadas' ,side: "bottom",align: 'start' } },
            { element: '#guardar', popover: { title: 'Guardar', description: 'Para guardar esta venta presiona este botón. No te preocupes si la cantidad a pagar no se completó. El sistema guardará esa información' ,side: "bottom",align: 'start' } },
            { popover: { title: '¡Felicidades!', description: 'Cerraste tu primer venta' ,side: "bottom",align: 'start' } },
            { element: '#pendientes', popover: { title: 'Pendientes', description: 'Los movimientos que no se paguen en su totalidad se almacenan en este apartado. Haz clic' ,side: "bottom",align: 'start' } },
            { element: '#background', popover: { title: 'Pendientes', description: 'Si haces clic en Cobrar, la información se carga de nuevo para terminar la venta' ,side: "bottom",align: 'start' } },
            { element: '#uso', popover: { title: 'Materiales de uso', description: 'Haz clic aquí' ,side: "bottom",align: 'start' } },
            { element: '#background', popover: { title: 'Materiales de uso', description: 'Este apartado te permite dar salida a productos que uses en el día y que no necesariamente estés cobrando al cliente. (No es requerido el campo cliente y consumido por)' ,side: "bottom",align: 'start' } },

            { element: '#ham', popover: { title: '¡Continuemos!', description: 'Despliega el menú principal' ,side: "right",align: 'start' } },
            { element: '#menu', popover: { title: 'Menú', description: 'Vamos a Categorías > Servicios',side: "right",align: 'start' } }, 
        ]
    });

    driverObj.drive();
}

function help() {
    recorrido = true
    Livewire.emit('recorrido')
    initializeTutorial_1()
}

function initializeTutorial_1()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Terminar',
        showProgress: true,
        // allowClose: false,
        steps: [
            { popover: { title: '¡Bienvenido de vuelta!', description: 'Aquí es donde iniciamos. Se trata de la agenda de citas. Uno de los módulos más importantes.' ,side: "bottom",align: 'start' } },
            { element: '#controls', popover: { title: 'Fecha', description: 'Con estos controles podrás filtrar la fecha en que deseas ver la agenda.' ,side: "bottom",align: 'start' } },
            { element: '#hoy', popover: { title: 'Botón Hoy', description: 'Con este botón podrás volver a la fecha actual con un sólo clic' ,side: "bottom",align: 'start' } },
            { element: '#empleados', popover: { title: 'Estilistas', description: 'Aquí se muestran tus estilistas activos, si quieres dejar de ver o volver a ver la agenda de un estilista, debes presionar su nombre' ,side: "bottom",align: 'start' } },
            { element: '#agenda', popover: { title: 'Agenda', description: 'Aquí podrás visualizar tus citas una vez que se generen. Haz clic en una casilla debajo del nombre de tu estilista' ,side: "bottom",align: 'start' } },
            { popover: { title: 'Vista rápida', description: 'Esta ventana nos permite crear citas de manera rápida.' ,side: "bottom",align: 'start' } },
            { element: '#buscar-serv', popover: { title: 'Busca un servicio', description: 'Busca un servicio en este apartado' ,side: "bottom",align: 'start' } },
        ]
    });

    driverObj.drive();
}



    function initializeTomSelect(elTomCust) {

    elTomCust = document.querySelector('#tomCustomer')

    if (elTomCust.tomselect) {
            elTomCust.tomselect.destroy() // Destruye cualquier instancia previa para evitar conflictos
        }

    var myurl ="{{ route('data.customers') }}"

        new TomSelect(elTomCust, {
        maxItems: 1,
        valueField: 'id',
        labelField: 'first_name',
        searchField: ['first_name','last_name','phone'],  
        load: function(query, callback) {        
        if(recorrido) {   
            pause()
        }
        var url = myurl + '?q=' + encodeURIComponent(query)
        fetch(url)
        .then(response => response.json())
        .then(json => {
        callback(json)        
        }).catch(()=>{
        callback()
        })        
        },
        onChange: function(value) {         
            Livewire.emit('customerId', value)
        },
        render: {
        option: function(item, escape) {
        return `<div class="py-2 d-flex">
            <div>
                <div class="mb-0">
                    <span style="color:#B59377"> ${ escape(item.first_name) } ${ escape(item.last_name !== null ? item.last_name : '') } | ${ escape(item.phone !== null ? item.phone : 'Sin teléfono') }</span>
                </div>
            </div>
        </div>`
        },      
        },
        }) 

    }
    

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
	
	function CancelDate(type = 'cita') {         
        console.log(type) 
		Swal.fire({
		title: 'ESTA CITA SE ELIMINARÁ PERMANENTEMENTE, ¿DESEA CONTINUAR?',
		text: "",
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Aceptar'
		}).then((result) => {
		if (result.value) {    
            $('#modalCitasForm').modal('hide')
			Livewire.emit('deleteMov',type)
		}
		})
	}

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

	function abrirForm() {
        $('#modalCitasForm').modal('show')


        if(recorrido){
            next()
        }

    }
        
    
	//cerrar modal de agregar cliente
	window.addEventListener('close-form', event => {
		$('#modalCitasForm').modal('hide')
	})
	window.addEventListener('play', event => {
        play()
    })

	window.addEventListener('next', event => {
        next()
    })
	window.addEventListener('play_1', event => {
        play_1()
    })
	window.addEventListener('comenzar_recorrido', event => {
        initializeTutorial()
	})

	window.addEventListener('abrirReview', event => {
		$('#reviewModal').modal('show')
	})
	window.addEventListener('recorrido', event => {
		$('#modalEmpl').modal('show')
	})
    
    window.addEventListener('returnCustomersView', () => {
        window.location.href = '/'; // o usa una ruta diferente si lo necesitas
    });
    function openCustReview()
    {
        event.preventDefault()
        var url = "{{ route('encuesta') }}"
        var width = 500
        var height = 500
        // Verificar si la ventana ya está abierta
        var openedWindows = window.open(url, '_blank', `width=${width},height=${height},toolbar=no`);
    }

    
    function changeTo(type){
        var pestañas = {
        1: document.getElementsByName('pestaña-prod'),
        2: document.getElementsByName('pestaña-ven'),
        3: document.getElementsByName('pestaña-uso'),
        4: document.getElementsByName('pestaña-ent')
    };

    Object.keys(pestañas).forEach(function(key) {
        pestañas[key].forEach(function(pestaña) {
            if (key == type) {
                pestaña.classList.add('active');
            } else {
                pestaña.classList.remove('active');
            }
        });
    });

        Livewire.emit('changeWindow', type)
    }

window.addEventListener('aperturar', event => {
    $('#modalConfirmApertura').modal('show')
})
window.addEventListener('aperturarOk', event => {
    $('#modalConfirmApertura').modal('hide')
})
function disableTags()
{
    tags = document.getElementById('tags');
    tagsButton = document.getElementById('tagsButton');

    tags.setAttribute("hidden"); 
    tagsButton.removeAttribute("hidden"); 
}

function addTags()
{
    tags = document.getElementById('tags');
    tagsButton = document.getElementById('tagsButton');

    tags.removeAttribute("hidden"); 
    tagsButton.setAttribute("hidden"); 

    initializeTomTags()
}
function initializeTomTags()
{
    elTom = document.querySelector('#tomTags')

    if (elTom.tomselect) {
        elTom.tomselect.destroy() // Destruye cualquier instancia previa para evitar conflictos
    }

    var myurl ="{{ route('data.etiquetas') }}"

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
            @this.set('listTags',value)
        },
        render: {
            option: function(item, escape) {
                return `<div >
                    <div >
                        <div >                
                            <span style="color:${item.color}"> ${ escape(item.name) }</span>
                        </div>
                    </div>
                </div>`;
            },    
        },
    }) 
}
</script>