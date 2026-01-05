<script>
    function initializeTomSelect2() {

var elTom = document.querySelector('#tomCategory');
if (elTom.tomselect) return


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
    return `<div class="py-2 d-flex">
        <div>
            <div class="mb-0"> 
                <span style="color: #6E6E6E"> ${ escape(item.name) }</span>
            </div>
        </div>
    </div>`;
    },      
    },
    })
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