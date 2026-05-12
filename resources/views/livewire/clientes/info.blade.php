<div class="col-md-12">
    @if($infoSelected==1)
        @include('livewire.clientes.cust-data')
        @include('livewire.clientes.modals.record')
    @endif
</div>


<style>
    .dropdown-item{
        color:black !important;
    }
    .swal2-html-container{
        color:#515457 !important;
    }
</style>


<script>
    function confirmDelete(clienteId) {
        // Mostrar cuadro de diálogo de confirmación personalizado
        Swal.fire({
            title: '¿Seguro que desea eliminar al cliente?',
            text: 'Tome en cuenta que los datos del cliente no pueden ser recuperados, pero las visitas y compras del cliente se mantendrán almacenadas',
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
</script>