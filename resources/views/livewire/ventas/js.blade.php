<script>
    
function initizalizeTomReview(){
    miTom = document.querySelector('#tomReview')
    if(miTom.tomselect) return

    var myurlC = "{{ route('data.categoriesCustomer') }}"

    var control = new TomSelect(miTom,{
        persist: false,
        createOnBlur: true,
        create: true,
        
        createFilter: function(input) {
            input = input.toLowerCase()
            return !(input in this.options)
        },
        
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],  
        load: function(query, callback) {        
            var urlC = myurlC + '?q=' + encodeURIComponent(query)
            fetch(urlC)
                .then(response => response.json())
                .then(json => {
                    callback(json)        
                }).catch(()=>{
                    callback()
                })        
        },
        onChange: function(value) {
            @this.set('categoriesListNew', value)
        },
        render: {
            option: function(item, escape) {
                return `<div class="py-2 d-flex">
                            <div>
                                <div class="mb-0">
                                    <span style="color:#9D1466"> ${ escape(item.name) } </span>
                                </div>
                            </div>
                        </div>`
            },
            item: function(item, escape) {
                return '<div>' + escape(item.name) + '</div>'
            }
        },
    })

}

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


window.addEventListener('abrirModalReseña', event => {
    $('#reviewModal').modal('show')
})

window.addEventListener('abrirModalCupon', event => {
    
    $('[data-toggle="popover"]').popover({
        html: true,
    });

    $('#modalCuponesForm').modal('show')
    const checkbox = document.getElementById('expDate');
    changeExpirationDate(checkbox)
})
window.addEventListener('cerrarModalCupon', event => {
    $('#modalCuponesForm').modal('hide')
})
window.addEventListener('reloadCheck', event => {
    reloadCheck();
})

window.addEventListener('close-review', event => {
    $('#reviewModal').modal('hide')
})
window.addEventListener('print_on', event => {
    const action = event.detail
    const tipo = action[0]
    const data = action[1]
    const ruta = "{{ route('reporte') }}"
    const variable = '/' + tipo + '/' + data
    const url = ruta + variable
    const width = 500
    const height = 500

    // Calcular la posición para centrar la ventana
    const left = (screen.width / 2) - (width / 2)
    const top = (screen.height / 2) - (height / 2)

    window.open(url, '_blank', `width=${width},height=${height},toolbar=no,scrollbars=yes,resizable=yes,left=${left},top=${top}`)
})

window.addEventListener('aperturar', event => {
    $('#modalConfirmApertura').modal('show')
})
window.addEventListener('aperturarOk', event => {
    $('#modalConfirmApertura').modal('hide')
})

document.addEventListener('livewire:load', function () {
    Livewire.on('totalUpdated', function (value) {
        // Actualizar el contenido donde se muestra el total
        document.getElementById('totalCart').innerText = '$' + value.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
    })
    Livewire.hook('message.processed', (el, component) => {            
        initizalizeTomReview()
    })
    
    Livewire.on('reset-tom-select', function() {
        tomSelectInstance.clearOptions() // Clear options
        tomSelectInstance.clear() // Clear the selected value
    })
    
    Livewire.on('redirect', url => {
        window.open(url, '_blank')
    })
    

    //cerrar modal cash / payments
    window.addEventListener('close-payment', event => {
        $('#modalPayment').modal('hide')
    })


})
</script>