@if(Auth::user()->role !== 'estilista')

<div>
    <div class="row">
        <div class="col-md-4" >
            <div class="card"style="border-width: 2px;" id="basic-data">

                <div class="card-header">
                    <h4 style="margin: auto;">{{ $editing ? 'Editar categoría' : 'Crear categoría' }}</h4>
                </div>

                <div class="card-body">

                    <div class="form-group">
                        <input wire:model.defer="categoria_producto.name" id='inputFocus' type="text"
                            class="form-control" placeholder="Categoría" style="background-color: white; color:black;">
                            <label >Nombre</label>
                        @error('categoria_producto.name') <span class="text-danger">*Corrige este campo* </span> @enderror
                    </div>

                    <!-- <div class="input-group" >
                        <label class="custom-file-label" style="background-color: white;">Image</label>
                        <div class="custom-file" >
                            <input wire:model="upload" type="file" class="custom-file-input" style=" cursor:pointer;"
                                accept="image/x-png,image/jpeg,image/jpg">
                        </div>
                        @error('categoria_producto.image') <span class="text-danger">*Corrige este campo* </span> @enderror
                    </div>

                    <!-- picture preview -->
                    <!-- @if( $upload!=null )
                    <div class="form-group mt-2">
                        <img class="img-fluid rounded" src="{{ $upload->temporaryUrl() }}" width="200" style="display:flex;margin: auto;padding-top:40px">
                        <h6 class="text-muted" style="text-align: center;">New Pic</h6>
                    </div>
                    @elseif($categoria_producto->id !=null)
                    <div class="form-group mt-2">
                        <img class="img-fluid rounded" src="{{ $savedImg }}" width="200" style="display:flex;margin: auto;padding-top:40px">
                        <h6 class="text-muted" style="text-align: center;">Current Pic</h6>
                    </div>
                    @endif -->


                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-dark light float-left hidden {{$editing ? 'd-block' : 'd-none' }}"
                        wire:click="cancelEdit" id="cancel-editing" onclick="next()">
                        Cancelar
                    </button>
                    <button id="save-info" onclick="next()" class="btn btn-sm btn-info float-right save" wire:click="Store" style="background-color: #9E846D;border-color:#9E846D">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card"style="background-color: white;">
                <div class="card-header ">
                    <div class="d-flex">
                        <div class="separator" style="background-color:#6E6E6E"></div>
                        <div class="mr-auto">
                            <h4 >Categorias de Productos</h4>
                            <p class="fs-14 mb-0"> Listado Registrado</p>
                        </div>
                    </div>
                </div>
                <div class="card-body" >
                    <div class="table-responsive" >
                        <table id="list-cat" class="table table-responsive-md table-hover  text-center">
                            <thead class="thead-primary" style="max-width: 400px;">
                                <tr>
                                    <!-- <th width="5%" class="text-center"style="background-color:transparent;color:#9D1466 !important">Imagen</th> -->
                                    <th width="50%"style="background-color:transparent;color:#9D1466 !important">Nombre</th>
                                    <th width="70%" style="background-color:transparent;color:#9D1466 !important"></th>
                                </tr>
                            </thead>
                            <tbody id="table-body">
                                @forelse ($categoria_productos as $item)
                                <tr onclick="next()" style="max-width: 400px;" wire:click="Edit({{ $item->id }})">
                                    <!-- <td class="text-center" >
                                        <img class="img-fluid rounded" src="{{ $item->picture }}" alt="pic" width="40">
                                    </td> -->
                                    <td>
                                        <div>{{$item->name }}</div>
                                        <!-- @if($item->platform_id != null)
                                        <small>Woocommerce: {{$item->platform_id}}</small>
                                        @endif -->
                                    </td>
                                    <td >
                                        <script>
                                            function confirmDelete(categoryId) {
                                                // Mostrar cuadro de diálogo de confirmación personalizado
                                                Swal.fire({
                                                    title: '¿Seguro que desea eliminar esta categoría?',
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
                                                        Livewire.emit('Delete', categoryId); // Llamar al método de Livewire
                                                    }
                                                });
                                            }
                                        </script>
                                        </button>

                                        @if(!$item->productos()->exists())
                                        <a onclick="confirmDelete({{ $item->id }})"><i style="color:#9D1466" class="fa fa-trash fa-lg"></i>
                                        </a>
                                        
                                        @endif

<!--                                         

                                        @if($item->platform_id == null)
                                        <button title="sincronizar" class="btn tp-btn btn-xs btn-light" wire:click.prevent="Sync({{ $item->id }})">
                                            
                                            <i class="las la-sync-alt la-2x"></i>
                                        </button>
                                        @endif -->

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3">No hay categorías</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            {{$categoria_productos->links()}}
                        </div>
                    </div>
                </div>
            </div>
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
            initializeTutorialPt5()
        }

    })
    
    let driverObj

    function next() {
        driverObj.moveNext();
    }

   function initializeTutorialPt5()
   {
        const driver = window.driver.js.driver;
        driverObj = driver({
            nextBtnText: 'Siguiente',
            prevBtnText: 'Anterior',
            doneBtnText: 'Listo',
            showProgress: true,
            // allowClose: false,
            steps: [
                { element: '#basic-data', popover: { title: 'Crea una categoría', description: 'Agrega nombre a tu primer categoría' ,side: "right",align: 'start' } },
                { element: '#save-info', popover: { title: 'Guarda la información', description: 'Para almacenar la información de la categoría haz clic en este botón' ,side: "top",align: 'start' } },
                { element: '#list-cat', popover: { title: 'Consulta tus categorías', description: 'En este listado se muestran las categorías que has agregado.' ,side: "left",align: 'start' } },
                { element: '#table-body', popover: { title: 'Edita un categoría', description: 'Haz clic en alguna fila' ,side: "left",align: 'start' } }, 
                { element: '#basic-data', popover: { title: 'Edita un categoría', description: 'A continuación se carga la información de la categoría en esta sección y se podrá editar libremente' ,side: "left",align: 'start' } }, 
                { element: '#cancel-editing', popover: { title: 'Cancelar operación', description: 'Si lo deseas, puedes cancelar esta edición para evitar que se modifique algún dato' ,side: "top",align: 'start' } },
                
                { element: '#ham', popover: { title: '¡Continuemos!', description: 'Despliega el menú principal' ,side: "right",align: 'start' } },
                { element: '#menu', popover: { title: 'Menú', description: 'Vamos a Inventarios > Productos',side: "right",align: 'start' } }, 
            ]
        });

        driverObj.drive();
   }
   
    function help() {
        initializeTutorialPt5()
    }
    </script>
</div>
@else
@include('livewire.sinPermisos')
@endif