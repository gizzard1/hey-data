<script>
    
function toggleMethods(id)
{
    const elements = document.querySelectorAll('.methods' + id);
    elements.forEach(element => {
        element.classList.toggle('toggled');
    });
}
function changeTo(type){
    var pestañas = {
        1: document.getElementsByName('pestaña-servicios'),
        2: document.getElementsByName('pestaña-productos'),
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

document.addEventListener('DOMContentLoaded', function(){
    initFlats();
})
</script>

@include('livewire.informes.js-flatpickr')
