<script>
    document.addEventListener('livewire:load', function () {        
    var mainWrapper = document.getElementById('main-wrapper')
// aplicamos la clase al contenedor principal para compactar el menu lateral y tener espacio de trabajo
mainWrapper.classList.add('menu-toggle') 
//cerrar modal cash / payments
window.addEventListener('close-payment', event => {
    $('#modalPaymentServices').modal('hide')
})

})

function openPayment() {
    $('#modalPaymentServices').modal('show')
}
document.addEventListener('livewire:load', function () {
    
    Livewire.on('print',action =>{
            var tipo = action[0];
            var data = action[1];
            var ruta = "{{ route('reporte') }}";
            var variable = '/' + tipo + '/' + data;
            var url = ruta + variable;
            var back = "{{ route('calendar') }}";
        window.open(url,'_blank','width=500,height=500','toolbar=no');
    initizalizeTomReview()
        })
    });
    

function initizalizeTomReview(){
    miTom = document.querySelector('#tomReview')
    if(miTom.tomselect) return

    var myurlC = "{{ route('data.categoriesCustomer') }}";

    var control = new TomSelect(miTom,{
        persist: false,
        createOnBlur: true,
        create: true,
        
        createFilter: function(input) {
            input = input.toLowerCase();
            return !(input in this.options);
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
                });        
        },
        onChange: function(value) {
            @this.set('categoriesListNew', value);
            Livewire.emit('actualizarCalendar');
        },
        render: {
            option: function(item, escape) {
                return `<div class="py-2 d-flex">
                            <div>
                                <div class="mb-0">
                                    <span style="color:#9D1466;"> ${ escape(item.name) } </span>
                                </div>
                            </div>
                        </div>`;
            },
            item: function(item, escape) {
                return '<div>' + escape(item.name) + '</div>';
            }
        },
    });
}

</script>