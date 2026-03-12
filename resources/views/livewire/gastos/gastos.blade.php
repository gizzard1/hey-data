@if(Auth::user()->role!=='estilista')
    <div class="row">
        <div class="col-md-4" id="categoriesCard">
            @include('livewire.gastos.form')
        </div>
        <div class="col-md-8" >
            <div class="card">
            <div class="card-header ">
                <div class="d-flex">
                    <div class="default-tab">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active"><i class="la la-box mr-2"></i> Gastos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="ventas-w" href="{{ route(name: 'marcas') }}"><i class="la la-store mr-2"></i> Proveedores</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div>
                    <div style="display: inline-flex;cursor:pointer;" class="flatpickrInd" >
                        <div id="currentDate">
                        {{ $currentDate }}
                        </div>
                        <div id="currentDateEnd">
                        @if($is_interval) <div>- {{ $currentDateEnd }}</div>@endif
                        </div>

                    </div>
                        <div style="width:fit-content;display:inline">
                            <i type="button" class="las la-calendar dropdown-toggle" data-toggle="dropdown"></i>
                            <div class="dropdown-menu">
                                <a style="cursor: pointer;" class="dropdown-item dropright flatpickr" data-toggle="dropdown">Elegir periodo</a>
                                <a style="cursor: pointer;" class="dropdown-item" wire:click="returnToday">Hoy</a>
                                <a style="cursor: pointer;" class="dropdown-item" wire:click="returnYesterday">Ayer</a>
                                <a style="cursor: pointer;" class="dropdown-item" wire:click="setWeek">Semanal</a>
                                <a style="cursor: pointer;" class="dropdown-item" wire:click="setMonth">Mensual</a>
                                <a style="cursor: pointer;" class="dropdown-item" wire:click="setYear">Anual</a>
                            </div>
                        </div>
                </div>
                
                @if(Auth::user()->role=='admin')
                <div class="botones-exportar d-flex" style="column-gap: 2dvh">
                    <button wire:click="generateExcel" class="btn excel-button input-group-text">Exportar Informe</button>   
                    <button class="btn save input-group-text" style="color:white" data-toggle="modal" data-target="#modalImportFromPDF">Importar datos</button>   
                    <!-- <button class="button-style" wire:click="generatePdf" style="border-width: 0;color:red"><i class="las la-file-pdf la-2x"></i></button>
                    <button class="button-style" wire:click="generateExcel" style="border-width: 0;color:#68d100"><i class="las la-file-excel la-2x"></i></button> -->
                </div>
                @endif
            </div>
                
                <div class="card-body table-prices">
                    <div class="table-responsive">
                        <table class="table table-responsive-md table-hover  text-left">
                            <thead class="thead-primary">
                                <tr >
                                    <th style="background-color:transparent;color:#1d3557 !important">Descripción</th>
                                    <th style="background-color:transparent;color:#1d3557 !important">Tipo de gasto</th>
                                    <th style="background-color:transparent;color:#1d3557 !important">Monto</th>
                                    <th style="background-color:transparent;color:#1d3557 !important">Clasif. fiscal</th>
                                    <th style="background-color:transparent;color:#1d3557 !important">Fecha</th>
                                    <th style="background-color:transparent;color:#1d3557 !important"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($gastos as $item)
                                <tr style="background-color:{{ $item->status =='vigente' ? '#D4E9D6' : ($item->status == 'cancelado' ? '#ff8b8b' : ($item->status == 'default' ?? ''))}}!important" wire:click="Edit({{ $item->id }})">
                                    <td><a wire:click="Edit({{ $item->id }})">{{ $item->note }}</a></td>
                                    <td> {{ $item->tipo?->name ?? 'N/A' }}</td>
                                    <td> ${{ $item->total }} </td>
                                    <td> {{ $item->type == "Acreditable" ? 'Deducible' : 'No deducible' }} </td>
                                    <td> {{ date_format(new DateTime($item->date),'d-m-Y') }} </td>

                                    <td>
                                        <button style="background-color:transparent;border-color:transparent" onclick="confirmDelete({{ $item->id }})" style="border-style:none"><i style="color:#1d3557;cursor:pointer;" class="fa fa-trash fa-lg"></i>
                                        </button>
                                        @if($item->marca_id!=null && $item->folio_fiscal!=null && Auth::user()->salon->rfc!=null)
                                            <button style="background-color:transparent;border-color:transparent" wire:click="verificarFactura({{ $item->id }})" style="border-style:none"><i style="color:#1d3557;cursor:pointer;" class="fa fa-search fa-lg"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">No hay gastos</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            {{$gastos->links()}}
                        </div>
                        <div class="col-md-6">
                            <span class="float-right">Total: ${{ number_format($total_bruto,2,'.',',') }}</span> 
                            <span>Gastos totales: {{ number_format($movs,0,'.',',') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="display: none;" id="categoriesCardAux">
            @include('livewire.gastos.form')
        </div>
        <livewire:marcas :action="0" />
    </div>
    @include('livewire.gastos.payment')
    @include('livewire.gastos.import-from-pdf')
@else
@include('livewire.sinPermisos')
@endif

<script>
        
    function changeTo(type){
        var pestañas = {
        1: document.getElementsByName('pestaña-prod'),
        2: document.getElementsByName('pestaña-ven'),
        3: document.getElementsByName('pestaña-uso'),
        4: document.getElementsByName('pestaña-ent')
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

    function openPath(path) {
        window.open(path, '_blank');
    }
        
    function confirmDelete(gastoId) {
    // Mostrar cuadro de diálogo de confirmación personalizado
    Swal.fire({
        title: '¿Seguro que desea eliminar este gasto?',
        text: '',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            showProcessing()
            // Si el usuario hace clic en "Aceptar", ejecutar el método de Livewire
            Livewire.emit('DeleteExpense', gastoId); // Llamar al método de Livewire
        }
    });
    }
    document.addEventListener('livewire:load', function () {
        Livewire.emit('loadSearchBox', 0);
        Livewire.on('dateUpdated-gastos', function (newDate,newDateEnd,newDataEmpleados) {
            // Actualizar el contenido donde se muestra la fecha
            document.getElementById('currentDate').innerText = newDate;
            if(newDateEnd!==''){
                document.getElementById('currentDateEnd').innerText = ' - ' + newDateEnd;
            }else{
                document.getElementById('currentDateEnd').innerText = '';
            }
        });
        Livewire.on('print', function () {
            var form = document.getElementById('form')
            
            form.style.visibility = 'hidden'
            
            window.print(); 

            form.style.visibility = 'visible'

        });
    })

   document.addEventListener('DOMContentLoaded', function(){
            flatpickr(document.getElementsByClassName('flatpickr2'),{
            enableTime: false,
            dateFormat: 'Y-m-d',
            locale: {
                firstDateofWeek:1,
                weekdays: {
                    shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                    longhand: [
                    "Domingo",
                    "Lunes",
                    "Martes",
                    "Miércoles",
                    "Jueves",
                    "Viernes",
                    "Sábado",
                    ],
                },    
                months: {
                    shorthand: [
                    "Ene",
                    "Feb",
                    "Mar",
                    "Abr",
                    "May",
                    "Jun",
                    "Jul",
                    "Ago",
                    "Sep",
                    "Oct",
                    "Nov",
                    "Dic",
                    ],
                    longhand: [
                    "Enero",
                    "Febrero",
                    "Marzo",
                    "Abril",
                    "Mayo",
                    "Junio",
                    "Julio",
                    "Agosto",
                    "Septiembre",
                    "Octubre",
                    "Noviembre",
                    "Diciembre",
                    ],
                }
            }
        })
      
    flatpickr(document.getElementsByClassName('flatpickr'),{
        mode:"range",
        enableTime: false,
        dateFormat: 'Y-m-d',
        locale: {
            firstDateofWeek:1,
            weekdays: {
                shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                longhand: [
                "Domingo",
                "Lunes",
                "Martes",
                "Miércoles",
                "Jueves",
                "Viernes",
                "Sábado",
                ],
            },    
            months: {
                shorthand: [
                "Ene",
                "Feb",
                "Mar",
                "Abr",
                "May",
                "Jun",
                "Jul",
                "Ago",
                "Sep",
                "Oct",
                "Nov",
                "Dic",
                ],
                longhand: [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre",
                ],
            }
        },
        onClose: function(selectedDates, dateStr, instance) {
            @this.emit('datesSelected', selectedDates);
        }
    })
    flatpickr(document.getElementsByClassName('flatpickrInd'),{
        enableTime: false,
        dateFormat: 'Y-m-d',
        locale: {
            firstDateofWeek:1,
            weekdays: {
                shorthand: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                longhand: [
                "Domingo",
                "Lunes",
                "Martes",
                "Miércoles",
                "Jueves",
                "Viernes",
                "Sábado",
                ],
            },    
            months: {
                shorthand: [
                "Ene",
                "Feb",
                "Mar",
                "Abr",
                "May",
                "Jun",
                "Jul",
                "Ago",
                "Sep",
                "Oct",
                "Nov",
                "Dic",
                ],
                longhand: [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre",
                ],
            }
        },
        onChange: function(selectedDate, dateStr, instance) {
            @this.emit('dateSelected', selectedDate);
        }
    })  
   })

    window.addEventListener('closeImportModal', () => {
        $('#modalImportFromPDF').modal('hide');
        document.getElementById('importFile').value = ''; // Limpiar el input
    });
    </script>
</div>

<style>
    a{
        color:#1d3557
    }
</style>