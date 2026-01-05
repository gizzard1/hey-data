<div>
    <div class="row">
        <div class="col-md-4"id="categoriesCard">
            @include('livewire.marcas.form')
        </div>
        <div class="col-md-8">
            <div class="card" id="list-prov">
                <div class="card-header ">
                    <div class="d-flex">
                        <div class="default-tab">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('gastos') }}"><i class="la la-box mr-2"></i> Gastos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active"><i class="la la-store mr-2"></i> Proveedores</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="float-right">
                    </div>
                </div>
                <div class="card-body" id="table-body">
                    <div class="table-responsive">
                        <table class="table table-responsive-md table-hover">
                            <thead class="thead-primary">
                                <tr>
                                    <th style="background-color:transparent;color:#60060F !important" >Nombre</th>
                                    <th style="background-color:transparent;color:#60060F !important" >Teléfono</th>
                                    <th class="ult-ver" style="background-color:transparent;color:#60060F !important" >Email</th>
                                    <th class="ult-ver" style="background-color:transparent;color:#60060F !important" >Contacto</th>
                                    <th style="background-color:transparent;color:#60060F !important" ></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($marcas as $item)
                                <tr onclick="next()">
                                    <td>
                                        <a wire:click="Edit('{{ $item->id }}')"><span>{{$item->name }}</span></a>
                                    </td>
                                    <td>
                                        <div>{{$item->phone_number }}</div>
                                    </td>
                                    <td class="ult-ver">
                                        <div>{{$item->email }}</div>
                                    </td>
                                    <td class="ult-ver">
                                        <div>{{$item->contact_name }}</div>
                                    </td>
                                    <td>
                                        <script>
                                            function confirmDelete(marcaId) {
                                                // Mostrar cuadro de diálogo de confirmación personalizado
                                                Swal.fire({
                                                    title: '¿Seguro que desea eliminar este proveedor?',
                                                    text: '',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#3085d6',
                                                    cancelButtonColor: '#d33',
                                                    confirmButtonText: 'Aceptar',
                                                    cancelButtonText: 'Cancelar'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        showProcessing()
                                                        // Si el usuario hace clic en "Aceptar", ejecutar el método de Livewire
                                                        Livewire.emit('Delete', marcaId); // Llamar al método de Livewire
                                                    }
                                                });
                                            }
                                        </script>
                                        <a onclick="confirmDelete('{{ $item->id }}')"><i style="color:#60060F" class="fa fa-trash fa-lg"></i>
                                        </a>
                                        


                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3">No hay marcas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            {{$marcas->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4"style="display: none;" id="categoriesCardAux">
            @include('livewire.marcas.form')
        </div>
    </div>
<script>
        document.addEventListener('stop-loader', (event) => {
                SlickLoader.disable()
    }) 

    document.addEventListener('DOMContentLoaded', function(){
    
    // Obtiene el dato de sesión de PHP y lo pasa a JavaScript
    var recorrido = @json(session('recorrido'));

    if(recorrido) {   
        initializeTutorialPt4()
    }

})
    
    let driverObj

    function next() {
        driverObj.moveNext();
    }

   function initializeTutorialPt4()
   {
        const driver = window.driver.js.driver;
        driverObj = driver({
            nextBtnText: 'Siguiente',
            prevBtnText: 'Anterior',
            doneBtnText: 'Listo',
            showProgress: true,
            // allowClose: false,
            steps: [
                { element: '#basic-data', popover: { title: 'Crea un proveedor', description: 'Llena los campos básicos de tu proveedor. (El campo requerido es únicamente el nombre)' ,side: "right",align: 'start' } },
                { element: '#save-info', popover: { title: 'Guarda la información', description: 'Para almacenar la información del proveedor haz clic en este botón' ,side: "top",align: 'start' } },
                { element: '#list-prov', popover: { title: 'Consulta tus proveedores', description: 'En este listado se muestran los proveedores que has agregado.' ,side: "left",align: 'start' } },
                { element: '#table-body', popover: { title: 'Edita un proveedor', description: 'Haz clic en alguna fila' ,side: "left",align: 'start' } }, 
                { element: '#basic-data', popover: { title: 'Edita un proveedor', description: 'A continuación se carga la información del proveedor en esta sección y se podrá editar libremente' ,side: "left",align: 'start' } }, 
                { element: '#cancel-editing', popover: { title: 'Cancelar operación', description: 'Si lo deseas, puedes cancelar esta edición para evitar que se modifique algún dato' ,side: "top",align: 'start' } },
                
                { element: '#menu', popover: { title: '¡Continuemos!', description: 'Vamos a Empleados',side: "right",align: 'start' } }, 
            ]
        });

        driverObj.drive();
   }

   function help() {
        initializeTutorialPt4()
    }
    </script>

</div>

<style>
    a{
        color:#1d3557
    }
</style>