<script>
function toggleMethods(id)
{
    const elements = document.querySelectorAll('.methods' + id);
    elements.forEach(element => {
        element.classList.toggle('toggled');
    });
}
function changeBadgeText(element, state) {
    const hoverText = element.getAttribute('data-hover-text');
    const originalText = element.getAttribute('data-original-text');

    if (state === 'hover' && element.textContent !== hoverText) {
        element.textContent = hoverText;
    } else if (state === 'original' && element.textContent !== originalText) {
        element.textContent = originalText;
    }
}    
function openPath(path) {
    window.open(path, '_blank');
}
function closeModals() {
    $('#modalEditing').modal('hide')
    $('#modalDetailTransaccion').modal('hide')
}
document.addEventListener('livewire:load', function () {
    
    Livewire.on('closeAll', event => {   
        closeModals()
    })

})

window.addEventListener('viewDetailTransaccion', event => {   
    $('#modalDetailTransaccion').modal('show')
})
window.addEventListener('closeAll', event => {   
    $('#modalEditing').modal('hide')
    $('#modalDetailTransaccion').modal('hide')
})
window.addEventListener('closeAll', event => {   
    closeModals()
})
window.addEventListener('hideDetailTransaccion', event => {   
    $('#modalDetailTransaccion').modal('hide')
})
function changeTo(type){
    var pestañas = {
        1: document.getElementsByName('pestaña-resumen'),
        2: document.getElementsByName('pestaña-payment'),
        3: document.getElementsByName('pestaña-tips'),
        4: document.getElementsByName('pestaña-materials'),
        5: document.getElementsByName('pestaña-files')
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

document.addEventListener('livewire:load', function () {
Livewire.emit('loadSearchBox', 0);
Livewire.on('dateUpdated-movimientos', function (newDate,newDateEnd,newDataEmpleados) {
    // Actualizar el contenido donde se muestra la fecha
    document.getElementById('currentDate').innerText = newDate;
    if(newDateEnd!==''){
        document.getElementById('currentDateEnd').innerText = ' - ' + newDateEnd;
    }else{
        document.getElementById('currentDateEnd').innerText = '';
    }
});
Livewire.on('print', function () {
    var desaparecerColumna = document.getElementById('desaparecerColumna')
    var fecha = document.getElementById('fecha')
    var filtro = document.getElementById('filtro')
    var filtros = document.getElementById('filtros')
    
    desaparecerColumna.style.opacity = '0'
    fecha.style.width = '300'
    filtro.style.visibility = 'hidden'
    filtros.style.display = 'none'
    window.print(); 
    
    desaparecerColumna.style.opacity = '1'
    fecha.style.width = '152'
    filtro.style.visibility = 'visible'
    filtros.style.display = 'block'
})
})


function initializeDraggFile() {
    var holder = document.getElementById('holder');

    // Manejo de arrastrar y soltar archivos
    holder.ondragover = function () {
        this.className = 'hover';
        return false;
    };

    holder.ondrop = function (e) {
        e.preventDefault();
        var files = e.dataTransfer.files; // Asignar los archivos arrastrados al input
        
        const fileInputElement = document.getElementById('input-file');
        fileInputElement.files = e.dataTransfer.files; // Asignar los archivos arrastrados al input
        fileInputElement.dispatchEvent(new Event('change')); // Disparar el evento 'change' para Livewire
    };

    // Manejo de clic para seleccionar archivos
    holder.onclick = function (e) {
        const fileInputElement = document.getElementById('input-file');
        fileInputElement.click(); // Abrimos el selector de archivos

        // Manejar el evento de selección de archivos
        // fileInputElement.onchange = function () {
        //     var files = fileInputElement.files; // Obtén los archivos seleccionados del input
        //     workFiles(files); // Pasamos los archivos a la función
        // };
    };
}

document.addEventListener('DOMContentLoaded', function(){
    initFlats();    
})
</script>