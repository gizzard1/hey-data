<script>
    document.addEventListener('livewire:load', function () {
      
        initizalizeTomReview()                 
          
    
    });

    document.addEventListener('DOMContentLoaded', function() {

        initializeFlatpickr();
    });

    function initializeTomSelect(elTom) {


    var myurl ="{{ route('data.customers') }}"

        new TomSelect(elTom, {
        maxItems: 1,
        valueField: 'id',
        labelField: 'first_name',
        searchField: ['first_name','last_name','phone'],  
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
            Livewire.emit('customerId', value)
        },
        render: {
        option: function(item, escape) {
        return `<div class="py-2 d-flex">
            <div>
                <div class="mb-0">
                    <span style="color:#B59377;"> ${ escape(item.first_name) } ${ escape(item.last_name) } | ${ escape(item.phone) }</span>
                </div>
            </div>
        </div>`;
        },      
        },
        }) 

    }
    

	
	function CancelDate() {          
		Swal.fire({
		title: 'LA INFORMACIÓN AGREGADA SERÁ PERDIDA, ¿DESEA CONTINUAR?',
		text: "",
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: '#d33',
		confirmButtonText: 'Aceptar'
		}).then((result) => {
		if (result.value) {    
			showProcessing(); 
			Livewire.emit('cancelarCaptura')
		}
		})
	}
	function CancelAllDate() {      
        Swal.fire({
		title: '¿SEGURO QUE DESEAS CANCELAR LA CITA?',
            input: "textarea",
            inputPlaceholder: "Motivo",
            inputAttributes: {
                "aria-label": "Type your message here"
            },
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
		if (result.isConfirmed) {    
            // Captura el valor del textarea
            const motivo = result.value;
            // Emite el evento Livewire junto con el valor
            Livewire.emit('cancelarCita', motivo);
		}
		})
	}

	function openCliente() {
    $('#modalCliente').modal('show')

    initializeTomSelect(document.querySelector('#tomCustomer'))

	setTimeout(() => {
		document.getElementById('tomCustomer-ts-control').focus()
	}, 1000);

	}
    
	//cerrar modal de agregar cliente
	window.addEventListener('close-cliente', event => {
		$('#modalCliente').modal('hide')
	})

</script>
