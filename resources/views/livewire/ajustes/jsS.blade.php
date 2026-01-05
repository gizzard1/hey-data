<script>
    

function changeTo(type){
var pestañas = document.getElementsByName('pestaña-serv')
var pestañas_p = document.getElementsByName('pestaña-prod')
    if(type==1){
        pestañas.forEach(function(pestaña) {
            pestaña.classList.remove('active');
        });
        pestañas_p.forEach(function(pestaña) {
            pestaña.classList.add('active');
        });  
    }else{
        pestañas.forEach(function(pestaña) {
            pestaña.classList.add('active');
        });
        pestañas_p.forEach(function(pestaña) {
            pestaña.classList.remove('active');
        });
    }
    Livewire.emit('changeWindow', type)
}

function initializeTomSelect() {
    var elTom = document.querySelector('#tomCategoryS');
    if (!elTom) return;


if(!elTom.TomSelect){
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
        @this.set('categoriesList',value);
    },
    render: {
    option: function(item, escape) {
    return `<div >
        <div >
            <div >
                               
                <span style="color:#B59377">${ escape(item.name) }</span>
            </div>
        </div>
    </div>`;
    },      
    },
    })
}
}


</script>
<style>
    .ts-control {
            padding: 0px !important;
            border-style: none;
            border-width: 0px !important;
            background-color: transparent !important; 
        }
        .ts-wrapper.multi .ts-control > div {
            background-color: #6E6E6E;
        }
        .ts-control > input {
            
            color: black !important;
        }
</style>