<script>
let chart
let flat
let indFlat
function closeModal()
{
    $('#modalClientesForm').modal('hide')
}

function update(data, options, override) {
  if(data) {
    this.data = data || {};
    this.data.labels = this.data.labels || [];
    this.data.series = this.data.series || [];
    // Event for data transformation that allows to manipulate the data before it gets rendered in the charts
    this.eventEmitter.emit('data', {
      type: 'update',
      data: this.data
    });
  }

  if(options) {
    this.options = Chartist.extend({}, override ? this.options : this.defaultOptions, options);

    // If chartist was not initialized yet, we just set the options and leave the rest to the initialization
    // Otherwise we re-create the optionsProvider at this point
    if(!this.initializeTimeoutId) {
      this.optionsProvider.removeMediaQueryListeners();
      this.optionsProvider = Chartist.optionsProvider(this.options, this.responsiveOptions, this.eventEmitter);
    }
  }

  // Only re-created the chart if it has been initialized yet
  if(!this.initializeTimeoutId) {
    this.createChart(this.optionsProvider.getCurrentOptions());
  }

  // Return a reference to the chart object to chain up calls
  return this;
}

function initializeChartist(data)
{
    var PSdata = {
        series: data,
    }
    var options = {
        donut: true,
        donutWidth: 50,
        startAngle:270,
        total:200,
        showLabel:true,
        width:500,
        height:500,

    }
    chart = new Chartist.Pie('.ct-chart-PS', PSdata, options)
}

document.addEventListener('livewire:load', function () {
        Livewire.emit('loadSearchBox', 4);

        Livewire.on('dateUpdated', function (newDate,newDateEnd) {
            // Actualizar el contenido donde se muestra la fecha
            document.getElementById('currentDate').innerText = newDate;
            if(newDateEnd!==''){
                document.getElementById('currentDateEnd').innerText = ' - ' + newDateEnd;
            }else{
                document.getElementById('currentDateEnd').innerText = '';
            }
        });


    

        Livewire.on('activateModal', function(){
            $('#modalClientesForm').modal('show')
        })
        
        Livewire.on('closeModalCust', function(){
            $('#modalClientesForm').modal('hide')
        })
        Livewire.on('closeMerge', function(){
            $('#modalMergeCust').modal('hide')
        })
        Livewire.on('abrir', function (value) {
            $('#filtro').dropdown('toggle')
        })
        Livewire.on('cargarFlat', function () {
            initializeFlat()
        })
    Livewire.on('refrescarChartist', data=>{
        // Inicializa el objeto de configuración del gráfico
        const chartData = {
            series: [data]
        };

        // Destruir el gráfico actual si existe
        chart.destroy();

        // initializeChartist($chartData)
        // Crear una nueva instancia de Chartist
        chart = new Chartist.Pie('.ct-chart-PS', chartData)

        // // Forzar actualización del gráfico
        chart.update(chartData);
    })
    Livewire.on('iniciarChartist',data=>{
        initializeChartist(data)
        initializeFlatPckr()
    })
    Livewire.on('loadFlat', function (){
        initializeFlatPckr()
    })
    Livewire.on('destroyFlat', function(){
        flat.destroy()
        indFlat.destroy()
    })
})

window.addEventListener('openModal', event => {   
    $('#modalForm').modal('show')
})
window.addEventListener('hideModal', event => {   
    $('#modalForm').modal('hide')
    Livewire.emit('categoriaAgregada'); // Llamar al método de Livewire
})
window.addEventListener('openTaxDataModal', event => {   
    $('#taxDataModal').modal('show')
})

function initializeTomSelect() {
var elTom = document.querySelector('#tomCategory');

    if (elTom.tomselect) {
        elTom.tomselect.destroy() // Destruye cualquier instancia previa para evitar conflictos
    }
    
var myurl ="{{ route('data.categoriesCustomer') }}"
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
     @this.set('listCategories',value)
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

function loadFlat(uid) {
    flatpickr(document.getElementsByClassName('flatpickr'), {
        inline: true,
        mode: "range",
        enableTime: false,
        dateFormat: 'Y-m-d',
        locale: {
            firstDateofWeek: 1,
            weekdays: {
                shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                longhand: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
            },
            months: {
                shorthand: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                longhand: [
                    "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre",
                ],
            }
        },
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                @this.emit('updateRange', uid, selectedDates);
            }
        },
        onReady: function(selectedDates, dateStr, instance) {
            // Detener la propagación de los clics dentro de Flatpickr
            instance.calendarContainer.addEventListener('click', function(event) {
                event.stopPropagation();
            });
        }
    });

}

function initializeFlatPckr(){
    
    flat = flatpickr('.flatpickr',{
        mode:"range",
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
        },
        onClose: function(selectedDates, dateStr, instance) {
            @this.emit('datesSelected', selectedDates);
        }
        })
        indFlat = flatpickr('.flatpickrInd',{
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
        },
        onChange: function(selectedDate, dateStr, instance) {
            @this.emit('dateSelected', selectedDate);
        }
    })
}

function initializeFlat(){
        document.querySelectorAll('.flatpickrRange').forEach(function(element) {
            flatpickr(element, {    
                mode: "range",
                enableTime: false,
                dateFormat: 'Y-m-d',
                locale: {
                    firstDayOfWeek: 1,
                    weekdays: {
                        shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                        longhand: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
                    },
                    months: {
                        shorthand: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                        longhand: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                    }
                },
                onClose: function(selectedDates, dateStr, instance) {
                    Livewire.emit('datesSelected', selectedDates);
                }
            });
        });
    }
    
    window.addEventListener('modal-delivery', event => {   
      $('#modalDelivery').modal('show')
      setTimeout(() => {
        document.getElementById('inputDeliveryName').focus()
      }, 1000);
   })
    window.addEventListener('modal-view-client', event => {   
      $('#modalViewClient').modal('show')
   })
    window.addEventListener('load-flat', event => {   
        flatpickr('.flatpickr',{
        mode:"range",
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
        },
        onClose: function(selectedDates, dateStr, instance) {
            @this.emit('datesSelected', selectedDates);
        }
        })
        flatpickr('.flatpickrInd',{
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
        },
        onChange: function(selectedDate, dateStr, instance) {
            @this.emit('dateSelected', selectedDate);
        }
    })
   })
    window.addEventListener('activateModal', event => {   
        initializeTomSelect();                 
        $('#modalClientesForm').modal('show')
   })
    window.addEventListener('activateCardCustomer', event => {   
      $('#modalActivateCard').modal('show')
      driverObj.moveNext();
    })
    window.addEventListener('editCardCustomer', event => {   
      $('#modalEditCardCustomer').modal('show')
    })
    window.addEventListener('closeAC', event => {   
      $('#modalActivateCard').modal('hide')
   })
    window.addEventListener('closeEC', event => {   
      $('#modalEditCardCustomer').modal('hide')
   })
    window.addEventListener('closeMerge', event => {   
      $('#modalMergeCust').modal('hide')
   })
    window.addEventListener('closeModalCust', event => {   
      $('#modalClientesForm').modal('hide')
   })
    window.addEventListener('closeRecordModal', event => {   
      $('#modalRecord').modal('hide');
    
      // Destruir la instancia de Quill para evitar problemas al abrir el modal nuevamente
      document.getElementById('record').remove();
   })
    window.addEventListener('initRecord', function (event) {  
        initQuill(false, 'recordReadOnly' + (event.detail.id ?? ''));
        setQuillContent(event.detail.content, 'recordReadOnly' + (event.detail.id ?? ''));
    })
    window.addEventListener('updateReadOnlyRecord', function (event) {  
        setQuillContent(event.detail.content, 'recordReadOnly');
    });

   
    let recorrido = @json(session('recorrido'));
    let driverObj
   
function help() {
    initializeTutorialPt3()
}

function next() {
    driverObj.moveNext();
}

function initializeTutorialPt3()
{
    const driver = window.driver.js.driver;
    driverObj = driver({
        nextBtnText: 'Siguiente',
        prevBtnText: 'Anterior',
        doneBtnText: 'Listo',
        showProgress: true,
        // allowClose: false,
        steps: [
            { element: '#create-cust', popover: { title: 'Crea un cliente', description: 'Empecemos por registrar a un cliente. Haz clic aquí' ,side: "right",align: 'start' } },
            { element: '#background', popover: { title: 'Formulario clientes', description: 'Este formulario almacena información relevante de tu cliente' ,side: "right",align: 'start' } },
            { element: '#cust-name', popover: { title: 'Formulario clientes', description: 'Asigna un nombre para tu primer cliente.' ,side: "right",align: 'start' } },
            { element: '#cust-noti', popover: { title: 'Formulario clientes', description: 'Si tu cliente está de acuerdo en que se le envíen mensajes automáticos y ofertas vía whatsapp activa estas casillas' ,side: "right",align: 'start' } },
            { element: '#save-info', popover: { title: 'Guarda la información', description: 'Para almacenar la información del cliente haz clic en este botón' ,side: "top",align: 'start' } },
            { element: '#background', popover: { title: 'Controles', description: '¡Felicidades! Añadiste tu primer cliente al sistema' ,side: "left",align: 'start' } },
            { element: '#controls', popover: { title: 'Controles', description: 'Haz clic en el nombre de un cliente' ,side: "left",align: 'start' } },
            { element: '#background', popover: { title: 'Información general', description: 'Esta ventana muestra un resumen del cliente' ,side: "right",align: 'start' } },
            { element: '#informe', popover: { title: 'Informe', description: 'Haz clic aquí' ,side: "right",align: 'start' } },
            { element: '#background', popover: { title: 'Informe', description: 'En esta ventana obtendremos un informe de los últimos movimientos del cliente' ,side: "right",align: 'start' } },
            { element: '#historial', popover: { title: 'Historial', description: 'Haz clic aquí' ,side: "right",align: 'start' } },
            { element: '#background', popover: { title: 'Informe', description: 'En esta ventana obtendremos el historial de visitas del cliente seleccionado' ,side: "right",align: 'start' } },
            
            { element: '#menu', popover: { title: '¡Continuemos!', description: 'Vamos a Proveedores',side: "right",align: 'start' } }, 
        ]
    });

    driverObj.drive();
}
   
document.addEventListener('DOMContentLoaded', function () {
    
    // Obtiene el dato de sesión de PHP y lo pasa a JavaScript

    if(recorrido) {   
        initializeTutorialPt3()
    }

    document.querySelector('.keep-open').addEventListener('click', function (event) {
        event.stopPropagation();
    })

})

function setQuillContent(content, id = 'record') {
    var dataParsed = content instanceof Object ? content : JSON.parse(content);
    
    const quillInstance = Quill.find(document.getElementById(id));
    if (quillInstance && content) {
        quillInstance.setContents(dataParsed);
    }

    if (!content && quillInstance) {
        quillInstance.setContents([]);
    }
}


function initQuill(enabled = true, id = 'record'){
    const quillInstance = Quill.find(document.getElementById(id));
    
    // Validar si existe una instancia de Quill antes de crear una nueva
    if (quillInstance) {
        return; // Si ya existe, no hacer nada
    }

    const toolbarOptions = id === 'record' ? ['bold', 'italic', 'underline', 'strike', { 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' },'clean'] : false;

    const quillContainer = document.getElementById(id);
    if (!quillContainer) {
        // Crear el contenedor si no existe
        const newContainer = document.createElement('div');
        newContainer.id = id;
        newContainer.classList.add('form-group');
        newContainer.setAttribute('wire:ignore', '');
        const classPath = id === 'record' ? '#modal-body-record .row .col-md-12' : '.card-body-record .row .col-md-6';
        document.querySelector(classPath).appendChild(newContainer);
    }
    
    var quill = new Quill('#' + id, {
        theme: 'snow',
        modules: {
            toolbar: toolbarOptions,
        },
        readOnly: !enabled,        
    });
}
function saveRecord() {
    var quill = Quill.find(document.getElementById('record'));
    var recordContent = quill.getContents();
    
    Livewire.emit('saveRecord', recordContent);
}

function changeTo(type){
        var pestañas = {
        1: document.getElementsByName('pestaña-all'),
        2: document.getElementsByName('pestaña-pending'),
        3: document.getElementsByName('pestaña-fin'),
        4: document.getElementsByName('pestaña-canc')
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

        Livewire.emit('windowCust', type)
    }

</script>