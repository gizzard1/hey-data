<?php

namespace App\Http\Livewire;

use App\Models\Asignacion_servicio;
use App\Models\Asignacion_venta;
use App\Models\bloqueo;
use App\Models\caja_apertura;
use App\Models\calificacion_empleado_cliente;
use App\Models\categoria_cliente;
use App\Models\cita;
use App\Models\cliente;
use App\Models\coupon;
use App\Models\Empleado;
use App\Models\etiquetas_cita;
use App\Models\File;
use App\Models\log;
use App\Models\Material;
use App\Models\metodo_pago_servicio;
use App\Models\producto;
use App\Models\Propina;
use App\Models\Salon;
use App\Models\servicio;
use App\Models\walog;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Http\Controllers\DataResourceGrid as DRG;
use App\Http\Controllers\DataSales as DS;

class Agenda extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $currentDate, $currentDateEnd, $is_interval = false;
    public $start, $currentDateC, $end, $currentDateCEnd;
    public $end_date, $end_date_DB, $start_date, $start_date_DB;
    protected $paginationTheme = 'bootstrap';
    private $citas;
    public $bloqueos;
    private $salon_id;

    public $max, $min, $recorrido = false;

    public $balance = 0;
    public $type, $itemSelected = null, $terminales = [], $terminales_propina = [], $total_real = 0, $total_propinas_real = 0, $total_propinas = 0;
    public $empleados;

    public Collection $cartP, $cartS, $methods, $propinas, $terminales_real, $terminales_propina_real, $cartPendingMethods, $cartPendingPropinas;
    public $disccount = 0;

    public $totalCart = 0, $totalCartBase = 0, $itemsCart = 0, $taxCart = 0, $subtotalCart = 0, $generated_points = 0, $total_disccount = 0, $totalMethods = 0, $global_disccount = 0, $recibido = 0, $propinasRecibidas = 0;
    public $search, $items, $itemType;
    private caja_apertura $apertura;

    public $description = '';
    public $mensajesRespaldados = [];
    public $selectedEmpleadoId = null, $remember = 0, $customerId, $listCategories, $isAdmin, $horas, $minutes_qty = 0, $customer, $agregarEmpleados;
    public $asignacion_id, $action = 1, $pestaña = 1, $queryServices, $servicios = [], $query, $categoriasTag = [];
    public $show, $totales = true;
    public $pp_cart = 0;


    public $categoriesList, $categoriesListNew;
    public $cliente, $listCategoriesIds;
    public $calificacion = 0;
    public $cash, $infoDate = [], $reference, $paymentMethod, $tips, $rest, $indexTotal = 0, $cita, $metodoProp, $qtyProp, $referenceProp;

    public Collection $cartPS;
    public $clientes = [], $disccount_form = false;
    public $metodosSalon = [], $uploadFiles = 0;
    public $gallery = [], $pictures = [], $respaldoFiles;
    public $ventaConstrained = 0;
    public $queryTag;
    public $type_disccount = '%';
    public $cajaChica = 0;
    public $listTags = null, $searchPassword = null;
    public $billRequired = 0, $usoCfdi = null, $billed = false;
    public $vista;
    public $type_mov = "cita";
    public $usos = [
        "G01" => "G01 | Adquisición de mercancías",
        "G02" => "G02 | Devoluciones, descuento o bonificaciones",
        "G03" => "G03 | Gastos en general",
        "I01" => "I01 | Construcciones",
        "I02" => "I02 | Mobiliario y equipo de oficina para inversiones",
        "I03" => "I03 | Equipo de transporte",
        "I04" => "I04 | Equipo de cómputo y accesorios",
        "I05" => "I05 | Dados, troqueles, moldes, matrices y herramental",
        "I06" => "I06 | Comunicaciones telefónicas",
        "I07" => "I07 | Comunicaciones satelitales",
        "I08" => "I08 | Otra máquina y equipo",
        "D01" => "D01 | Honorarios médicos, dentales y hospitalarios",
        "D02" => "D02 | Gastos médicos por incapacidad o discapacidad",
        "D03" => "D03 | Gastos funerales",
        "D04" => "D04 | Donativos",
        "D05" => "D05 | Intereses reales pagados por créditos hipotecarios",
        "D06" => "D06 | Aportaciones voluntarias al SAR",
        "D07" => "D07 | Primas de seguros de gastos médicos",
        "D08" => "D08 | Gastos de transportación escolar obligatoria",
        "D09" => "D09 | Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones.",
        "D10" => "D10 | Pagos por servicios educativos (colegiaturas)",
        "S01" => "S01 | Sin efectos fiscales",
        "CP01" => "CP01 | Pagos",
        "CN01" => "CN01 | Nomina",
    ];
    protected $rules =    [
        'searchPassword' => "required|min:1|max:255",
    ];
    public function removeImage($index)
    {
        array_splice($this->gallery, $index, 1);
    }
    public function removeFile($filename, $fromGallery)
    {
        try {
            if ($fromGallery) {
                // Filtrar el arreglo para eliminar el archivo con el nombre coincidente
                $this->gallery = array_filter($this->gallery, function ($file) use ($filename) {
                    return $file->getFilename() !== $filename;
                });
            } else {
                // Filtrar la colección para eliminar el archivo con la ruta coincidente
                $this->pictures = $this->pictures->filter(function ($picture) use ($filename) {
                    return $picture !== $filename;
                });
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 115459Agenda"]);
        }
    }
    public function filesDroped($files)
    {
        $this->gallery[] = $files;
    }
    public function mount($action = null, $pestaña = null, $cita_id = null)
    {
        $this->action = $action ?? 1;
        $this->pestaña = $pestaña ?? 1;
        if (session()->has('cartPV')) {
            $this->clearSession(['cartPV']);
        }

        $this->getMetodosSalon();
        $this->agregarEmpleados = $this->contarEmpleados();
        $this->isAdmin = Auth::user()->role == 'admin' || Auth::user()->role == 'recepcionista';
        $this->loadHoras();
        $this->loadFecha();
        $this->initializeCollections();
        if (!session()->has('recorrido') && session()->has('recorrido') != 'terminado') {
            $this->empezarRecorrido(false);
        }
        if (session()->has('recorrido')) {
            $this->clearSession(['recorrido']);
        }

        $this->emit('reloadFlat');

        if ($cita_id) {
            $this->vista = 'livewire.calendar.edit'; // otra vista por defecto

            $this->changeWindow($pestaña, $cita_id);
        } else {
            $this->vista = 'livewire.calendar.calendar'; // otra vista por defecto
        }
    }
    private function getMetodosSalon()
    {
        foreach (Auth::user()->salon->metodosPago as $metodosSalon) {
            $this->metodosSalon[] = $metodosSalon;
        }
    }

    function save()
    {
        try {
            session()->put('cartS', $this->cartS);
            session()->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 38967Agenda"]);
        }
    }


    private function clearSession(array $keys)
    {
        $session = session();

        foreach ($keys as $key) {
            if ($session->has($key)) {
                $session->forget($key);
            }
        }
        session()->save();
    }

    public function empezarRecorrido($respuesta)
    {
        session()->put('recorrido', $respuesta);
        session()->save();
        if ($respuesta) {
            $this->dispatchBrowserEvent('comenzar_recorrido');
        }
    }
    public function filterEmployee($empleado_id)
    {
        try {
            $empleado = empleado::where('id', $empleado_id)->where('visible', 1)->first();
            $empleado->is_active = !$empleado->is_active;
            $empleado->save();
            if (!$empleado->is_active) {
                redirect('/');
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 13459Agenda"]);
        }
    }

    public function updatedQueryServices()
    {
        try {

            $this->servicios = servicio::where('salon_id', Auth::user()->salon->id)
                ->where('visibility', 'visible')
                ->where('name', '!=', 'Servicio eliminado')
                ->where(function ($q) {
                    $q->where('name', 'like', "%{$this->queryServices}%")
                        ->orWhere('description', 'like', "%{$this->queryServices}%");
                })
                ->orderBy('name', 'asc')
                ->get();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097Agenda"]);
        }
    }
    public function updatedQueryTag()
    {
        try {

            $this->categoriasTag = categoria_cliente::where('salon_id', Auth::user()->salon->id)
                ->where(function ($q) {
                    $q->where('name', 'like', "%{$this->queryTag}%");
                })
                ->orderBy('name', 'asc')
                ->get();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097Agenda"]);
        }
    }
    public function updatedQuery()
    {
        try {
            $q = $this->query;

            $this->clientes = cliente::where(function ($query) {
                $words = preg_split('/\s+/', trim($this->query));

                foreach ($words as $word) {
                    $query->where(function ($q) use ($word) {
                        $q->where('first_name', 'like', "%{$word}%")
                            ->orWhere('last_name', 'like', "%{$word}%")
                            ->orWhere(DB::raw("CONCAT_WS(' ', TRIM(first_name), TRIM(last_name))"), 'like', "%{$word}%")
                            ->orWhere('email', 'like', "%{$word}%")
                            ->orWhere('phone', 'like', "%{$word}%")
                            ->orWhereRaw("SOUNDEX(first_name) = SOUNDEX(?)", [$word])
                            ->orWhereRaw("SOUNDEX(last_name) = SOUNDEX(?)", [$word]);
                    });
                }
            })
                ->where('salon_id', Auth::user()->salon->id)
                ->orderByRaw("
                CASE
                    WHEN first_name LIKE '{$q}%' THEN 1
                    WHEN last_name LIKE '{$q}%' THEN 2
                    WHEN phone LIKE '{$q}%' THEN 3
                    WHEN first_name LIKE '%{$q}%' THEN 4
                    WHEN last_name LIKE '%{$q}%' THEN 5
                    WHEN phone LIKE '%{$q}%' THEN 6
                    ELSE 7
                END
            ")
                ->get();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097Agenda"]);
        }
    }
    public function clear()
    {
        $this->initializeCollections();
        $this->clearSession(['cartS', 'cartMaterials', 'cartPV', 'rfcSelected']);
        $this->clearCliente();
        $this->itemSelected = null;
        $this->description = '';
        $this->pictures = null;
        $this->gallery = null;
        $this->asignacion_id = null;
        $this->loadCartTotales();
        $this->emit('clear-cart-pv');

        $this->billRequired = 0;
        $this->usoCfdi = null;
        $this->billed = false;

        $this->queryServices = '';
        $this->query = '';
    }
    public function cancelarCaptura()
    {
        $this->clear();
        $this->minutes_qty = 0;
        $this->action = 1;
        $this->asignacion_id = null;
        $this->calificacion = null;
        $this->pictures = null;
        $this->gallery = null;
        $this->listTags = null;
        $this->initializeCollections();
        $this->clearCliente();
        $this->loadData();
        $this->emit('reloadFlat');
    }
    private function loadHoras()
    {
        try {
            $horaDesconcatenada = explode(":", Auth::user()->salon->start);
            $horaFinDesconcatenada = explode(":", Auth::user()->salon->end);

            $inicio = intval($horaDesconcatenada[0]);
            $fin = intval($horaFinDesconcatenada[0]);

            for ($hora = $inicio; $hora <= $fin; $hora++) {
                $this->horas[] = sprintf('%02d:00', $hora);
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 35417Agenda"]);
        }
    }
    protected $listeners = [
        'refresh' => '$refresh',
        'datesSelected' => 'setDatesFromPeriod',
        'filesDroped',
        'prevDay',
        'dateSelected' => 'setDate',
        'viewDetails',
        'cancelacion',
        'updateDuration',
        'StoreReview',
        'editar',
        'updateQty',
        'changeWindow',
        'deleteMov',
        'aperturaCaja',
        'continueStoring',
        'updateIva',
        'deleteItem' => 'removeItem',
        'updatePercentage',
        'loadItems',
        'addNewService',
        'cambioData',
        'cambioDataMethods',
        'changeTerminalQty',
        'changeQty',
        'StoreCorte',
        'changeStartDuration',
        'changeEndDuration',
        'changeEmpleado' => 'updateEmpleado',
        'customerId' => 'setCustomerId',
        'newCust',
        'enviarCliente' => 'recibirClienteNuevo',
        'reimpresion',
        'cancelarCaptura',
        'changeDate',
        'setCitaDragged',
        'clear-cart' => 'cancelarCaptura',
        'enviarMethods' => 'recibirMethods',
        'enviarGlobal',
        'enviarPropinas' => 'recibirPropinas',
        'changeTotalCP',
        'actualizarCalificacion',
        'storeDate',
        'selectAssigment',
        'crearCita',
        'crearCitaCel',
        'setAsignacion',
        'removeItem',
        'setCustomerId',
        'Store',
        'setMethod',
        'setTip' => 'newPropina',
        'newPropina',
        'setReward',
        'newMethod',
        'filterEmployee',
        'recorrido',
        'productAdded',
        'rfcSelected',
        'updateBaseComision',
        // Acciones con teclas
        'teclaC',
        'teclaLeft',
        'teclaRight',
        'teclaUp',
        'teclaDown',
        'teclaT',
        'teclaESC'
    ];
    public function productAdded()
    {
        $this->loadCartTotales();
    }
    public function teclaC()
    {
        $start = substr(Auth::user()->salon->start, 0, 5);  // Resultado: '08:00'
        $this->crearCita($start, null);
    }
    public function teclaLeft()
    {
        $this->prevDay();
    }
    public function teclaRight()
    {
        $this->nextDay();
    }
    public function teclaDown()
    {
        $this->prevMonth();
    }
    public function teclaUp()
    {
        $this->nextMonth();
    }
    public function teclaT()
    {
        $this->loadFecha();
    }
    public function teclaESC()
    {
        $this->cancelarCaptura();
    }

    public function updateDuration($newDuration, $cita, $tipo = 'cita', $agrupadas)
    {
        try {
            if ($tipo == 'cita') {
                $detail = asignacion_servicio::with('date.details')->find($cita);
                if ($detail == null) {
                    preg_match("/'([^']+)'/", $cita, $matches);
                    // El contenido extraído estará en $matches[1]
                    $cita = $matches[1];
                    // El contenido extraído estará en $matches[1]
                    $cita = $matches[1];

                    $detail = asignacion_servicio::with('date.details')->find($cita);
                }
                $citas_continuas = $this->identificarCitasContinuas($detail, $agrupadas);
                $duration_per_service = round($newDuration / ($citas_continuas->count() > 0 ? $citas_continuas->count() : 1), 0, PHP_ROUND_HALF_DOWN);
                $new_start = Carbon::parse($detail->start);
                foreach ($citas_continuas as $cita) {
                    $cita->duration = $duration_per_service;
                    $cita->start = $new_start;
                    $cita->save();
                    $new_start->addMinutes($duration_per_service);
                }
                $detail->duration = $duration_per_service;
                $detail->save();
                $date = $detail->date;
                $data = $this->calculateStartEndDate($date);
                $date->start = $data['start'];
                $date->end = $data['end'];
                $date->save();
            } elseif ($tipo == 'bloqueo') {
                $detail = bloqueo::find($cita);
                $newEnd = Carbon::parse($detail->start)->addMinutes($newDuration);
                $detail->end = $newEnd;
                $detail->save();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 525Agenda"]);
        }
    }
    private function identificarCitasContinuas($detail, $agrupadas)
    {
        // Ordenamos los detalles por hora de inicio
        $details_sorted = $detail->date->details
            ->sortBy(fn($d) => [$d->empleado_id, $d->start])
            ->values();
        // Obtenemos el índice del detalle actual
        $index = $details_sorted->search(fn($d) => $d->id === $detail->id);

        if ($index === false) {
            return collect(); // Por seguridad, si no se encuentra
        }

        // Tomamos desde el actual hasta los siguientes $agrupadas elementos
        $remaining = $details_sorted->slice($index, intval($agrupadas + $index))->values();

        return $remaining;
    }
    public function recorrido()
    {
        session()->put('recorrido', true);
        session()->save();
    }
    public function enviarGlobal($total)
    {
        $this->total_disccount += $total;
    }
    public function eliminarCategoria($categoriaName)
    {
        try {
            $categories = $this->listCategories;
            // Buscar el índice del ID de la categoría en la lista
            $index = array_search($categoriaName, $this->listCategories);

            // Si se encuentra el ID, eliminarlo de la lista
            if ($index !== null) {
                unset($categories[$index]);
                $this->categoriesList = array_values($categories);
            }

            // Recargar la lista de categorías completas desde la base de datos
            $this->listCategories = categoria_cliente::whereIn('name', $this->categoriesList)->pluck('name')->toArray();
            $this->listCategoriesIds = categoria_cliente::whereIn('name', $this->categoriesList)->pluck('id')->toArray();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1701352Agenda"]);
        }
    }
    public function actualizarCalificacion($puntaje)
    {
        $this->calificacion = $puntaje;
    }
    public function reseñaClienteDate($customer_id)
    {
        try {
            $this->customer = cliente::with('categorias')->find($customer_id);
            $categoriesList = $this->customer->categorias->pluck('name')->toArray();
            $this->listCategories = $categoriesList;
            $this->listCategoriesIds = $this->customer->categorias->pluck('id')->toArray();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1651350Agenda"]);
        }
    }
    public function createTag()
    {
        if ($this->queryTag != null) {
            //guardar categoría
            $newCat =  new categoria_cliente;
            $newCat->name = $this->queryTag;
            $newCat->salon_id = Auth::user()->salon_id;
            $newCat->save();
            $this->listCategories[] = $this->queryTag;
            $this->listCategoriesIds[] = $newCat->id;
            $this->emit('refresh');
        }
    }
    public function addTag($tagId, $name)
    {
        try {
            // Verificamos si el tagId ya está en listCategoriesIds
            if (!in_array($tagId, $this->listCategoriesIds)) {
                $this->listCategories[] = $name;
                $this->listCategoriesIds[] = $tagId;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1651350Agenda"]);
        }
    }
    public function StoreReview()
    {
        try {
            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }

            $listCategories = $this->listCategoriesIds;

            $this->customer = session('cust');

            $listCategories !== null ? $this->customer->categorias()->sync($listCategories) : $this->customer->categorias()->detach();

            calificacion_empleado_cliente::create([
                'puntaje' => $this->calificacion,
                'calificado' => 'cliente',
                'cliente_id' => $this->customer?->id,
                'user_id' => Auth::user()->id
            ]);
            $this->cancelarCaptura();
            $this->dispatchBrowserEvent('cerrarReview');
            $this->clearSession(['cust']);
            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1662351Agenda"]);
        }
    }
    public function reimpresion()
    {
        $cita = Cita::where('status', 'Pagada')->where('salon_id', Auth::user()->salon_id)->latest('id')->first();
        $this->imprimirTicket($cita);
    }
    public function reimpresionTicket()
    {
        $this->imprimirTicket($this->itemSelected);
    }
    private function imprimirTicket($date)
    {
        try {
            if ($date) {
                $this->emit('print_on', ['ticket_servicio', $date->id]);
                $this->emit('refresh');
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 18959citas"]);
        }
    }
    public function showAdvanced()
    {
        try {
            $this->methods = new Collection;
            $this->propinas = new Collection;
            $this->colectMethods();
            $this->enviarFechas();
            $this->dispatchBrowserEvent('close-form');
            $this->emit('reloadFlat');
            $this->action = 2;
            $this->loadCartTotales();
            $this->totalPropinas();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 18959citas"]);
        }
    }

    public function colectMethods()
    {
        $this->loadCart(0);
    }
    public function returnModal()
    {
        $this->emit('abrirForm');
        $this->action = 1;
    }
    public function changeDate($date)
    {
        try {
            $horario = explode(":", $this->start_date);
            $date = Carbon::parse($date[0]);

            // Extract the time components from currentDateC
            $hour = $horario[0];
            $minute = $horario[1];

            // Combine the date and time
            $new_date = $date->locale('es')->setTime($hour, $minute, '00');


            $this->start_date_DB = $new_date->format('Y-m-d H:i:s');
            $this->start_date = $this->start_date_DB;
            $this->currentDate = $new_date->locale('es')->isoFormat('dddd, D MMMM YYYY');

            $this->loadData();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2034Agenda"]);
        }
    }

    public function newCust()
    {
        $this->emit('activateModalForm');
    }
    public function recibirClienteNuevo($cust_id)
    {
        $this->setCustomerId($cust_id);
        $this->emit('closeModalForm');
    }

    public function render()
    {
        try {
            $this->loadEmpleados();
            $salonTimes = $this->loadSalonTimes();
            $times = $this->loadTimes();
            $agregarEmpleados = $this->contarEmpleados();
            $isAdmin = Auth::user()->role == 'admin' || Auth::user()->role == 'recepcionista';
            if ($isAdmin) {
                return view($this->vista, ['agregarEmpleados' => $agregarEmpleados, 'citas' => $this->useDate(), 'propinas' => $this->propinas, 'type' => $this->type, 'itemSelected' => $this->itemSelected, 'methods' => $this->methods, 'totalCart' => $this->totalCart, 'taxCart' => $this->taxCart, 'subtotalCart' => $this->subtotalCart, 'generated_points' => $this->generated_points, 'items' => $this->items, 'times' => $times, 'salonTimes' => $salonTimes, 'pp_cart' => $this->pp_cart, 'categoriasCliente' => $this->listCategories, 'total_disccount' => $this->total_disccount, 'restante' => $this->rest, 'isAdmin' => $isAdmin]);
            } else {
                return view('livewire.calendar.resource-hour-grid-employees', ['citas' => $this->useDate(), 'propinas' => $this->propinas, 'times' => $salonTimes]);
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 88354Agenda"]);
        }
    }

    public function aperturaCaja()
    {
        $this->aperturarCaja();
        $this->dispatchBrowserEvent('noty', ['msg' => 'CAJA APERTURADA CON ÉXITO']);
    }
    private function aperturarCaja()
    {
        try {
            $apertura = new caja_apertura;
            $apertura->caja_chica = $this->cajaChica;
            $apertura->user_id = Auth()->user()->id;
            $apertura->save();
            $this->dispatchBrowserEvent('aperturarOk');
            $this->storeDate(1);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 11456Agenda"]);
        }
    }

    private function verificarApertura()
    {
        try {
            $salon_id = Auth::user()->salon_id;
            $apertura = caja_apertura::whereHas('user', function ($query) use ($salon_id) {
                $query->where('salon_id', $salon_id);
            })
                ->latest('id')
                ->first();
            if ($apertura != null && $apertura->caja_corte_id == null) {
                return true;
            } else {
                $corte = $apertura->corteCaja;
                $totalCashReal = $corte->total_cash_real;
                $efectivoCorte = $corte->total_cash;
                $propinasEfectivo = $corte->propinas_efectivo;
                $gastos = $corte->gastos;
                $this->cajaChica = $totalCashReal - $efectivoCorte - $propinasEfectivo + $gastos;
                return false;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 9655Agenda"]);
        }
    }

    private function contarEmpleados()
    {
        try {
            $empleados = Empleado::where('is_active', true)
                ->where('visible', 1)
                ->where('salon_id', Auth::user()->salon->id)
                ->get();
            return count($empleados) == 0 ? true : false;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1240255Agenda"]);
        }
    }
    public function cancelacion($motivo)
    {
        try {
            if ($this->asignacion_id === null && $this->itemSelected === null) {
                $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Refresque la pantalla e intente nuevamente."]);
                return;
            }

            $asignacion = Asignacion_servicio::with('date.details')->find($this->asignacion_id);
            $this->itemSelected = $asignacion->date;
            $this->itemSelected->status = 'Cancelada';
            $this->itemSelected->motivoCancelacion = $motivo;
            $this->itemSelected->save();
            $this->cancelarStock();
            $this->dispatchBrowserEvent('noty', ['msg' => "SOLICITUD PROCESADA CON ÉXITO"]);
            $this->clear();

            if ($this->vista === 'livewire.calendar.edit') {
                $this->dispatchBrowserEvent('returnCustomersView');
            }
            $this->cancelarCaptura();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 49728Agenda"]);
        }
    }
    private function clearCliente()
    {
        $this->ventaConstrained = 0;
        $this->customer = null;
        $this->customerId = null;
        $this->removeRewardMethods();
    }
    private function inMethods($key = null)
    {
        try {
            $mymethods = $this->methods;

            $cont = $mymethods->where($key ?? 'paymentMethod', $this->paymentMethod)->count();

            return  $cont > 0 ? true : false;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 281214Payment"]);
        }
    }
    public function removeRewardMethods()
    {
        $this->paymentMethod = '5';
        if ($this->inMethods()) {
            $puntos = $this->methods->where('paymentMethod', $this->paymentMethod)->first();
            $this->removeItem($puntos['uid'], 'method');
        }
    }
    private function subReward()
    {
        try {
            $this->paymentMethod = '5';
            if ($this->inMethods()) {
                $puntos = $this->methods->where('paymentMethod', $this->paymentMethod)->first();
                if (count($this->methods) < 1 && $this->rest < 0) {
                    $this->cambioDataMethods($puntos['uid'], $this->totalCart, 1, 'metodos');
                }
            } else {
                return;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 22314Agenda"]);
        }
    }
    private function validateCustFields($cust)
    {
        try {
            $response = $cust->first_name !== null && $cust->last_name !== null && $cust->email !== null &&
                $cust->postcode !== null && $cust->birth_date !== null && $cust->phone !== null &&
                $cust->sexo !== null && $cust->procedencia_id !== null;
            return $response;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 22314Agenda"]);
        }
    }
    public function setReward()
    {
        try {
            $cust = cliente::find($this->customerId);

            if (!$cust) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'CLIENTE NO ENCONTRADO']);
                return;
            }

            if (!$cust) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'SELECCIONE UN CLIENTE']);
                return;
            }

            if (!$this->validateCustFields($cust)) {
                $this->emit('activateModalForm', $cust->id);
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'FAVOR DE COMPLETAR INFORMACIÓN DEL CLIENTE']);
                return;
            }

            if (!isset($cust->tarjetaPuntos)) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'EL CLIENTE NO TIENE UNA TARJETA DE PUNTOS ACTIVADA']);
                return;

                if ($cust->tarjetaPuntos->balance < 0)
                    $this->dispatchBrowserEvent('noty-error', ['msg' => 'SIN SALDO EN TARJETA']);
                return;
            }

            if ($this->inMethods()) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'YA NO SE PUEDEN AGREGAR PUNTOS O PUNTOS INSUFICIENTES']);
                return;
            }

            $puntos = $cust->tarjetaPuntos->balance;

            if ($puntos > $this->rest) {
                $excedente = $cust->tarjetaPuntos->balance - $this->rest;
                $puntos = $cust->tarjetaPuntos->balance - $excedente;
            }

            $this->recibido = $puntos;
            $this->addMethod($puntos, '', '5', $this->methods);
            $this->totalMethods();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 101207Agenda"]);
        }
    }
    public function unsetCustomer()
    {
        $this->clearCliente();
    }
    public function dateSelected()
    {
        $this->loadDateByAgenda();
    }
    private function loadDateByAgenda()
    {
        try {
            $this->currentDateC = Carbon::parse($this->currentDateC);
            $this->currentDate = $this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->citas = $this->useDate();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1240255Agenda"]);
        }
    }

    public static function loadSalonTimes($salon_id = null)
    {
        try {
            $salon = $salon_id ? Salon::select('start', 'end')->where('id', $salon_id)->first() : Auth::user()->salon;
            $horas = [];
            $horaDesconcatenada = explode(":", $salon->start);
            $horaFinDesconcatenada = explode(":", $salon->end);

            $inicioHoras = intval($horaDesconcatenada[0]);
            $inicioMinutos = intval($horaDesconcatenada[1]);
            $finHoras = intval($horaFinDesconcatenada[0]);
            $finMinutos = intval($horaFinDesconcatenada[1]);

            $currentTime = new DateTime();
            $currentTime->setTime($inicioHoras, $inicioMinutos);

            $endTime = new DateTime();
            $endTime->setTime($finHoras, $finMinutos + 45);

            while ($currentTime <= $endTime) {
                $horas[] = $currentTime->format('H:i');
                $currentTime->modify('+15 minutes');
            }
            return $horas;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            self::dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 223234Agenda"]);
        }
    }
    private function loadTimes()
    {
        try {
            $horas = [];
            $horaDesconcatenada = explode(":", Auth::user()->salon->start);
            $horaFinDesconcatenada = explode(":", Auth::user()->salon->end);

            $inicioHoras = intval($horaDesconcatenada[0]);
            $inicioMinutos = intval($horaDesconcatenada[1]);
            $finHoras = intval($horaFinDesconcatenada[0]);
            $finMinutos = intval($horaFinDesconcatenada[1]);

            // Ajustar inicio 4 horas antes
            $currentTime = new DateTime();
            $currentTime->setTime($inicioHoras, $inicioMinutos);
            $currentTime->modify('-4 hours');

            // Ajustar fin 4 horas después
            $endTime = new DateTime();
            $endTime->setTime($finHoras, $finMinutos);
            $endTime->modify('+4 hours');

            // Generar el rango de horas
            while ($currentTime <= $endTime) {
                $horas[] = $currentTime->format('H:i');
                $currentTime->modify('+15 minutes');
            }

            return $horas;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 223234Agenda"]);
        }
    }
    private function loadEmpleados()
    {
        try {
            $this->empleados = Empleado::where('salon_id', Auth::user()->salon_id)->when(Auth::user()->role === 'estilista', function ($query) {
                $query->where('id', Auth::user()->empleado->id);
            })->where('visible', 1)->get();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 95355Agenda"]);
        }
    }
    public function loadFecha()
    {
        try {
            $this->currentDate = Carbon::now()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start = Carbon::now()->toDateString();
            $this->currentDateC = Carbon::now();
            $this->currentDateEnd = '';
            $this->end = '';
            $this->currentDateCEnd = '';
            $this->citas = $this->useDate();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 103356Agenda"]);
        }
    }

    public function setDatesFromPeriod($selectedDates)
    {
        try {
            if (count($selectedDates) >= 2) {
                // Actualizar las fechas según la lógica que necesites
                $currentDateC = Carbon::parse($selectedDates[0]);
                $currentDateCEnd = Carbon::parse($selectedDates[1]);


                $this->currentDateC = Carbon::parse($currentDateC);
                $this->currentDateCEnd = Carbon::parse($currentDateCEnd);
                $this->is_interval = true;
                $this->currentDate = $this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
                $this->start = $this->currentDateC->toDateString();
                $this->currentDateEnd = $this->currentDateCEnd->locale('es')->isoFormat('dddd, D MMMM YYYY');
                $this->end = $this->currentDateCEnd->toDateString();
                $this->loadDatesWithNewPeriod();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 118357Agenda"]);
        }
    }
    public function setDate($selectedDate)
    {
        try {
            // Actualizar las fechas según la lógica que necesites
            $currentDateC = Carbon::parse($selectedDate[0]);
            $this->currentDateC = Carbon::parse($currentDateC);
            $this->is_interval = false;
            $this->currentDate = $this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start = $this->currentDateC->toDateString();
            $this->currentDateEnd = '';
            $this->end = '';
            $this->currentDateCEnd = '';
            $this->loadDatesWithNewPeriod();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 141358Agenda"]);
        }
    }

    #Función que establece un día anterior 
    public function prevDay()
    {
        try {
            $this->is_interval = false;
            $this->currentDateC = $this->currentDateC->subDay();
            $this->start = $this->currentDateC->toDateString();
            $this->currentDate = $this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->citas = $this->useDate();
            $this->loadDatesWithNewPeriod();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 161359Agenda"]);
        }
    }
    public function prevMonth()
    {
        try {
            $this->is_interval = false;
            $this->currentDateC = $this->currentDateC->subMonth();
            $this->start = $this->currentDateC->toDateString();
            $this->currentDate = $this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->citas = $this->useDate();
            $this->loadDatesWithNewPeriod();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 161359Agenda"]);
        }
    }
    public function nextDay()
    {
        try {
            $this->addDay();
            $this->citas = $this->useDate();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 37719Agenda"]);
        }
    }
    public function nextMonth()
    {
        try {
            $this->addMonth();
            $this->citas = $this->useDate();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 37719Agenda"]);
        }
    }
    private function addMonth()
    {
        try {
            $this->currentDate = Carbon::parse($this->currentDateC);
            $this->currentDateC = $this->currentDateC->addMonth();
            $this->currentDate = $this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 39521Agenda"]);
        }
    }
    private function addDay()
    {
        try {
            $this->currentDate = Carbon::parse($this->currentDateC);
            $this->currentDateC = $this->currentDateC->addDay();
            $this->currentDate = $this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 39521Agenda"]);
        }
    }
    #Función que retorna la fecha actual
    public function returnToday()
    {
        $this->is_interval = false;
        $this->citas = $this->useDate();
        $this->loadDatesWithNewPeriod();
    }
    #Función que retorna información de "ayer"
    public function returnYesterday()
    {
        $this->citas = $this->useDate();
        $this->prevDay();
    }

    public function setWeek()
    {
        try {
            $this->is_interval = true;
            $this->currentDate = Carbon::now()->startOfWeek()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start = Carbon::now()->startOfWeek()->toDateString();
            $this->currentDateC = Carbon::now()->startOfWeek();
            $this->currentDateEnd = Carbon::now()->endOfWeek()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->end = Carbon::now()->endOfWeek()->toDateString();
            $this->currentDateCEnd = Carbon::now()->endOfWeek();
            $this->loadDatesWithNewPeriod();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 188360Agenda"]);
        }
    }
    public function setMonth()
    {
        try {
            $this->is_interval = true;
            $this->currentDate = Carbon::now()->startOfMonth()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start = Carbon::now()->startOfMonth()->toDateString();
            $this->currentDateC = Carbon::now()->startOfMonth();
            $this->currentDateEnd = Carbon::now()->endOfMonth()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $end = Carbon::now()->endOfMonth()->addDay();
            $this->end = $end->toDateString();
            $this->currentDateCEnd = Carbon::now()->endOfMonth();
            $this->loadDatesWithNewPeriod();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 204361Agenda"]);
        }
    }
    public function setYear()
    {
        try {
            $this->is_interval = true;
            $this->currentDate = Carbon::now()->startOfYear()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start = Carbon::now()->startOfYear()->toDateString();
            $this->currentDateC = Carbon::now()->startOfYear();
            $this->currentDateEnd = Carbon::now()->endOfYear()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->end = Carbon::now()->endOfYear()->toDateString();
            $this->currentDateCEnd = Carbon::now()->endOfYear();
            $this->loadDatesWithNewPeriod();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 221362Agenda"]);
        }
    }
    private function useDate()
    {
        try {
            $citas = [];
            $bloqueos = [];
            $currentDate = $this->currentDateC->format('Y-m-d'); // Obtiene la fecha del día de $currentDateC
            $salon_id = Auth::user()->salon_id;

            foreach ($this->empleados as $empleado) {
                if ($empleado->is_active) {

                    $citaPorEmpleado = Asignacion_servicio::with('date.customer.categorias', 'date.etiquetas')
                        ->whereHas('empleado', function ($query) use ($salon_id) {
                            $query->where('salon_id', $salon_id);
                        })
                        ->where('empleado_id', $empleado->id)
                        ->whereDate('start', $currentDate) // Filtra por la fecha del día de $currentDateC
                        ->orderBy('start')
                        ->get();

                    // Agrupación de horarios continuos
                    $agrupadas = [];
                    $temp = null;

                    foreach ($citaPorEmpleado as $cita) {
                        $cita->tags = $cita->date->etiquetas;

                        $cita->start = Carbon::parse($cita->start); // Asegura que $cita->start sea un objeto Carbon
                        $cita->end = (Carbon::parse($cita->start))->addMinutes($cita->duration);

                        $servicio = servicio::find($cita->selected_service);

                        if (isset($cita->date->customer)) {
                            $cita->categorias_cliente = implode("~ ", $cita->date->customer->categorias->pluck('name')->toArray());
                        }

                        // Si es la primera cita, inicializamos $temp
                        if ($temp === null) {
                            $temp = clone $cita;
                            $temp->title = '· ' . ($servicio != null ? $servicio->name : 'Servicio desconocido');
                            $temp->type_date += 1;
                        } elseif ($temp->end->equalTo($cita->start) && $temp->cita_id == $cita->cita_id) {
                            // Si es continua, extendemos la duración y unimos títulos
                            $temp->end = $cita->end;
                            $temp->duration += $cita->duration;
                            $temp->type_date += 1;
                            $temp->title .= "<br>" . '· ' . ($servicio != null ? $servicio->name : 'Servicio desconocido');
                        } else {
                            // Si hay una interrupción, guardamos el evento agrupado y comenzamos uno nuevo
                            $agrupadas[] = $temp;
                            $temp = clone $cita;
                            $temp->type_date += 1;
                            $temp->title = '· ' . ($servicio != null ? $servicio->name : 'Servicio desconocido');
                        }
                    }

                    // Guarda el último evento agrupado
                    if ($temp !== null) {
                        $agrupadas[] = $temp;
                    }

                    $citas[$empleado->id] = $agrupadas;

                    $bloqueosAgrupados = [];

                    $bloqueosPorEmpleado = bloqueo::whereHas('empleado', function ($query) use ($salon_id) {
                        $query->where('salon_id', $salon_id);
                    })
                        ->where('empleado_id', $empleado->id)
                        ->whereDate('start', $currentDate) // Filtra por la fecha del día de $currentDateC
                        ->orderBy('start')
                        ->get();


                    foreach ($bloqueosPorEmpleado as $bloqueo) {
                        $bloqueo->start = Carbon::parse($bloqueo->start); // Asegura que $bloqueo->start sea un objeto Carbon
                        $bloqueo->end = Carbon::parse($bloqueo->end);
                        $bloqueo->duration = $bloqueo->start->diffInMinutes($bloqueo->end);
                        $bloqueo->title = $bloqueo->description;
                        $bloqueosAgrupados[] = $bloqueo;
                    }

                    $bloqueos[$empleado->id] = $bloqueosAgrupados;
                }
            }
            $this->bloqueos = $bloqueos;

            $this->emit('reloadDragg');
            $this->dispatchBrowserEvent('close-popover');
            return $citas;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' => "Código de error: 235363Agenda"]);
        }
    }


    #función que actualiza las gráficas con la nueva fecha
    private function loadDatesWithNewPeriod()
    {
        try {
            $this->emit('dateUpdated-movimientos', $this->currentDate, $this->currentDateEnd);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 343365Agenda"]);
        }
    }
    public function setBlockId($id)
    {
        try {
            $this->asignacion_id = $id;
            $item = bloqueo::find($id);
            $minutes_qty = carbon::parse($item->end)->diffInMinutes(carbon::parse($item->start));
            $service = servicio::find(100100);
            $service->duration = $minutes_qty;
            $this->loadStartEndDateCarbon(carbon::parse($item->start)->format('H:i'));
            $this->AddItem('servicio', null, $service, 1, 0, 0, $item->empleado_id);
            $this->changeColorEvent($this->cartS[0]['id'], $item->color);
            $this->description = $item->description;
            $this->itemSelected = $item;
            $this->itemSelected->status = 'Bloqueada';
            $this->dispatchBrowserEvent('abrirBlockMenuForm');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 412343Agenda"]);
        }
    }
    public function setAsignacion($id)
    {
        $this->asignacion_id = $id;
        $this->emit('loadFlatForm');
        $this->abrirFormulario();
    }
    public function crearCita($start_date, $empleadoId)
    {
        try {
            $this->ventaConstrained = 0;
            $this->selectedEmpleadoId = $empleadoId;
            $this->loadStartEndDateCarbon($start_date);
            $this->abrirFormulario();
            $this->dispatchBrowserEvent('close-popover');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 4233Agenda"]);
        }
    }
    public function crearBloqueo($start_date, $empleadoId)
    {
        try {
            $this->dispatchBrowserEvent('abrirBlockMenuForm');
            $this->selectedEmpleadoId = $empleadoId;
            $this->loadStartEndDateCarbon($start_date);
            $this->dispatchBrowserEvent('close-popover');
            $this->addNewService(100100);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41523Agenda"]);
        }
    }
    public function crearCitaCel($start_date, $empleadoId)
    {
        try {
            $this->selectedEmpleadoId = $empleadoId;
            $this->loadStartEndDateCarbon($start_date);
            $this->dispatchBrowserEvent('close-popover');
            $this->showAdvanced();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 4343Agenda"]);
        }
    }
    private function abrirFormulario()
    {
        $this->emit('abrirForm');
        if (isset($this->asignacion_id) || isset($this->itemSelected)) {
            $this->loadCitaByStartEmpleado();
        }
        $this->loadData();
        $this->emit('loadFlatForm');
    }
    private function loadStartEndDateCarbon($start_date)
    {
        try {
            $this->start_date = $start_date;
            $this->end_date = $start_date;
            // Extraer año, mes y día de $currentDateC
            $year = $this->currentDateC->year;
            $month = $this->currentDateC->month;
            $day = $this->currentDateC->day;

            // Combina fecha y hora
            $combinedDateTime = Carbon::createFromFormat('Y-m-d H:i:s', "$year-$month-$day $start_date:00")->format('Y-m-d H:i:s');
            $this->start_date_DB = $combinedDateTime;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2232134Agenda"]);
        }
    }
    private function loadCitaByStartEmpleado()
    {
        try {
            $asignacion = Asignacion_servicio::with('date.details.materiales.producto', 'date.etiquetas', 'date.metodosPago', 'date.propinas', 'date.abonos')->find($this->asignacion_id);
            $this->loadCita($asignacion);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 48627Agenda"]);
        }
    }
    private function updateDates()
    {
        $this->end_date = Carbon::parse($this->start_date);
        $this->end_date->addMinutes(intval($this->minutes_qty));
        $this->end_date_DB = Carbon::parse($this->end_date)->format('Y-m-d H:i:s');
        $this->end_date = Carbon::parse($this->end_date)->format('H:i');
    }
    private function loadCita($asignacion)
    {
        try {
            if (session()->has('cartS')) {
                $this->cartS = new Collection;
            }
            if (isset($asignacion->date)) {
                $this->itemSelected = $asignacion->date;
                $this->pictures = $this->itemSelected->photos;
                $this->description = $this->itemSelected->description;
                $this->listTags = implode(", ", $this->itemSelected->etiquetas->pluck('id')->toArray());
                $this->loadStartEndDateCarbon(Carbon::parse($this->itemSelected->start)->format('H:i'));
                $this->end_date = Carbon::parse($this->itemSelected->end)->format('H:i');
                $this->setCustomerId($asignacion->date->customer_id, false);
                $this->remember = $asignacion->date->remember;

                $this->billRequired = $asignacion->date->billing;
                $this->billed = $this->billRequired === 2 ? true : false;
                $this->usoCfdi = $asignacion->date->billing_description;
                $this->rfcSelected($asignacion->date->tax_data_id);

                $this->loadCart();
                $this->loadCartTotales();
                if ($this->itemSelected->status == 'Pagada' || $this->itemSelected->status == 'Cancelada') {
                    $this->totalMethods();
                    $this->totalPropinas();
                }
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 222134Agenda"]);
        }
    }
    public function setCustomerId($customer, $recorrido = true)
    {
        try {
            if ($customer != null) {
                $this->customer = cliente::with('tarjetaPuntos', 'datosFacturacion')->find($customer);
                $this->customerId = $customer;
                if (session('recorrido') && $recorrido) {
                    $this->dispatchBrowserEvent('play_1');
                }
            }
            $this->emit('refresh');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 22334Agenda"]);
        }
    }
    public function loadData()
    {
        try {
            $this->updateDates();
            $this->citas = $this->useDate();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 59031Agenda"]);
        }
    }
    private function loadCart($withPnS = 1)
    {
        try {
            if (isset($this->itemSelected)) {
                $materiales = [];
                $details_service = [];
                $formas_pago = $this->itemSelected->metodosPago;
                $propinas = $this->itemSelected->propinas;
                $details_service = $this->itemSelected->details->sortBy('start');
                $details_sales = $this->itemSelected->details_product;
                if ($withPnS) {
                    foreach ($details_service as $detail) {
                        $uid = $this->AddItem('servicio', $detail, $detail->servicio ?? servicio::find(99999), 1, $detail->discount_qty, $detail->iva, $detail->empleado_id, null, null, $detail->discount_type, $detail->current_price, $detail->disccount_price, $detail->generated_points, $detail->base_comision);

                        $materiales = $detail->materiales;
                        //Iterar la lista de productos en una colección para guardar en el carrito (Si es que hay)
                        foreach ($materiales as $material) {
                            $this->AddItem('producto', $material, $material->producto, $material->qty, 0, 0.16, $material->empleado_id, $material->qty, $uid);
                        }
                    }
                    if (count($details_sales) > 0) {
                        $this->ventaConstrained = 1;
                        $cartP = new Collection();
                        session()->put('cartPV', $cartP);
                        session()->save();
                        foreach ($details_sales as $detail) {
                            $product = $detail->product;
                            $this->AddProduct($product, $detail->quantity, $detail->discount_qty, $detail->iva, $detail->empleado_id, $detail->current_price, $detail->disccount_price, $detail->quantity, $detail->discount_type, $detail->base_comision);
                        }
                    }
                    if (count($materiales) > 0) {
                        session()->put('cartMaterials', $this->cartP);
                        session()->save();
                    }
                }
                foreach ($formas_pago as $forma_pago) {
                    $this->addMethod($forma_pago->amount, $forma_pago->reference, $forma_pago->payment_method_id, $this->methods, null, $forma_pago->tipo);
                }
                foreach ($propinas as $propina) {
                    $this->addMethod($propina->amount, $propina->reference, $propina->payment_method_id, $this->propinas, $propina->empleado_id);
                }
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 522369Agenda"]);
        }
    }

    private function AddProduct($product, $qty = 1, $disccount_percent = 0, $ind_iva = 0.16, $empleado = NULL, $gross_price = null, $disccount_price = 0, $qty_inicial = 0, $discount_type = "Porcentaje", $base_comision = 0)
    {
        try {
            // iva méxico 16%
            $iva = $ind_iva;
            // determinar precio venta con iva

            $salePrice = ($product->disccount_price > 0 && $product->disccount_price < $product->gross_price ?  $product->disccount_price : $product->gross_price);

            if ($gross_price) {
                $salePrice = $disccount_price > 0 && $disccount_price < $gross_price ?  floatval($disccount_price) : floatval($gross_price);
            }

            if ($disccount_percent) {
                if ($discount_type == 'Porcentaje') {
                    $salePrice = $salePrice - ($salePrice * $disccount_percent / 100);
                } elseif ($discount_type == 'Cantidad') {
                    $salePrice = $salePrice - $disccount_percent;
                }
            }

            //precio unitario sin iva
            $precioUnitarioSinIva = $salePrice / (1 + $iva);
            // subtotal neto
            $subtotalNeto = $precioUnitarioSinIva * intval($qty);
            //monto del iva
            $montoIva = $subtotalNeto * $iva;
            //total con iva
            $totalConIva  = $subtotalNeto + $montoIva;

            $tax  = $montoIva;
            $total = $totalConIva;
            $uid = uniqid() . $product->id;

            $coll = collect(
                [
                    'id' => $uid,
                    'pid' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'reward_points' => floatval($product->reward_points),
                    'intern_sku' => $product->intern_sku,
                    'gross_price' => $gross_price > 0 ? floatval($gross_price) : floatval($product->gross_price),
                    'disccount_price' => $disccount_price > 0 ? floatval($disccount_price) : floatval($product->disccount_price),
                    'discount_type' => $discount_type,
                    'disccount_percent' => floatval($disccount_percent),
                    'sale_price' => $gross_price > 0 ? floatval($gross_price) : floatval($salePrice),
                    'qty' => intval($qty),
                    'ind_iva' => floatval($ind_iva),
                    'tax' => floatval($tax),
                    'total' => floatval($total),
                    'stock' => $product->stock_qty,
                    'type' => $product->type_product,
                    'vendedor' => $empleado,
                    'platform_id' => $product->platform_id,
                    'qty_inicial' => $qty_inicial,
                    'base_comision' => $base_comision
                ]
            );
            $itemCart = Arr::add($coll, null, null);
            $cartP = session('cartPV');
            $cartP->push($itemCart);
            session()->put('cartPV', $cartP);
            session()->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 32966Agenda"]);
        }
    }
    public function setMethod()
    {
        $this->addMethod(0, '', '1', $this->methods);
    }
    private function applyDisccount($global_disccount)
    {
        try {
            // Establecer un valor predeterminado si el descuento está vacío
            $global_disccount = is_numeric($global_disccount) ? $global_disccount : 0;

            $global_disccount = min($global_disccount, 100); // Asegurar que el descuento no sea mayor al 100%

            $disccount = $this->rest - ($this->rest * ($global_disccount / 100));
            $global = $this->rest - $disccount;
            $this->total_disccount += $global;

            $this->rest = $disccount;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 165210Agenda"]);
        }
    }
    private function addMethod($qty, $reference, $paymentMethod, $array, $empleado = null, $type = null, $isAcumulable = null)
    {
        try {
            // validar si ya existe entre los métodos
            if ($paymentMethod == '4') {
                $globalD = 0;
                if ((($this->type_disccount == '%' || $this->type_disccount == 'percent') && $type == null) || $type == 'Porcentaje') {
                    $globalD = $qty;
                    $type = 'Porcentaje';
                } elseif ((($this->type_disccount == '$' || $this->type_disccount == 'currency') && $type == null) || $type == 'Cantidad') {
                    if ($this->rest) $globalD = ($qty / $this->rest) * 100;
                    $type = 'Cantidad';
                }
                $this->applyDisccount($globalD);
            }

            $uid = uniqid();
            $coll = collect(
                [
                    'uid' => $uid,
                    'paymentMethod' => $paymentMethod,
                    'amount' => floatval($qty),
                    'reference' => $reference,
                    'empleado' => $empleado,
                    'tipo' => $type,
                    'isAcumulable' => $isAcumulable
                ]
            );
            $method = Arr::add($coll, null, null);
            $array->push($method);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1031Agenda"]);
        }
    }

    private function AddItem($type, $asignacion, $item, $qty = 1, $disccount_qty = 0, $ind_iva = 0.16, $empleado = null, $qty_inicial = 0, $uid_s = null, $discount_type = "Porcentaje", $gross_price = null, $disccount_price = 0, $reward_points = null, $base_comision = 0)
    {
        try {
            // validar si ya existe en el carrito
            // if ($this->inCart($item->id,$type,$type=='producto' ? $asignacion->asignacion->selected_service : null)) {
            //     if($type=='servicio'){
            //         $this->dispatchBrowserEvent('noty-error', ['msg' => 'EL SERVICIO YA ESTÁ AGREGADO']);
            //         return;
            //     }
            //     return; // => con esta línea se agrupan los items por nombre dentro del carrito
            // }

            $uid = uniqid() . $item->id;

            if ($type == 'producto') {
                $coll = collect(
                    [
                        'uid' => $uid_s,
                        'id' => $uid,
                        'mid' => $item->id,
                        'name' => $item->name,
                        'sale_price' => $asignacion->sale_price,
                        'qty' => intval($qty),
                        'stock' => $item->stock_qty,
                        'type' => $item->type_product,
                        'vendedor' => $empleado,
                        'qty_inicial' => $qty_inicial,
                        'asignacion_id' => $asignacion->asignacion->selected_service
                    ]
                );
            } elseif ($type == 'servicio') {
                // iva 
                $iva = $ind_iva;
                // determinar precio venta con iva
                $salePrice = ($item->disccount_price > 0 && $item->disccount_price < $item->gross_price ?  $item->disccount_price : $item->gross_price);

                if ($gross_price) {
                    $salePrice = $disccount_price > 0 && $disccount_price < $gross_price ?  floatval($disccount_price) : floatval($gross_price);
                }

                if ($disccount_qty) {
                    if ($discount_type == 'Porcentaje') {
                        $salePrice = $salePrice - ($salePrice * $disccount_qty / 100);
                    } elseif ($discount_type == 'Cantidad') {
                        $salePrice = $salePrice - $disccount_qty;
                    }
                }

                //precio unitario sin iva
                $precioUnitarioSinIva = $salePrice / (1 + $iva);
                // subtotal neto
                $subtotalNeto = $precioUnitarioSinIva * intval($qty);
                //monto del iva
                $montoIva = $subtotalNeto * $iva;
                //total con iva
                $totalConIva  = $subtotalNeto + $montoIva;

                $tax  = $montoIva;
                $total = $totalConIva;
                $data = $this->calcularHorarioCita($asignacion ? $asignacion->duration : $item->duration);
                $this->minutes_qty += $asignacion ? $asignacion->duration : $item->duration;
                if ($asignacion) {
                    $end = Carbon::parse($asignacion->start)->addMinutes($asignacion->duration)->format('H:i');
                }
                $coll = collect(
                    [
                        'selected' => $asignacion ? $asignacion->selected : 1,
                        'id' => $uid,
                        'sid' => $item->id,
                        'name' => $item->name,
                        'color' => $asignacion ? $asignacion->color : (empleado::select('color_preset')->find($this->selectedEmpleadoId)->color_preset ?? '#E2BBB4'),
                        'reward_points' => $reward_points ? floatval($reward_points) : floatval(DRG::calculateRewardPoints($item->id, true, $total, null, $this->customerId, Auth::user()->salon->recompensaGeneral()->first())),
                        'gross_price' => $gross_price ? floatval($gross_price) : floatval($item->gross_price),
                        'disccount_price' => $disccount_price > 0 ? floatval($disccount_price) : floatval($item->disccount_price),
                        'disccount_percent' => floatval($disccount_qty),
                        'discount_type' => $discount_type,
                        'sale_price' => $gross_price ? floatval($gross_price) : floatval($salePrice),
                        'ind_iva' => floatval($ind_iva),
                        'tax' => floatval($tax),
                        'total' => floatval($total),
                        'vendedor' => $empleado == null ? $this->selectedEmpleadoId : $empleado,
                        'duration' => $asignacion ? $asignacion->duration : $item->duration,
                        'start' => $asignacion ? Carbon::parse($asignacion->start)->format('H:i') : $data['start'],
                        'end' => $asignacion ? $end : $data['end'],
                        'qty' => 1,
                        'base_comision' => $base_comision
                    ]
                );
            }
            $itemCart = Arr::add($coll, null, null);
            if ($type == 'producto') {
                $this->cartP->push($itemCart);
            } elseif ($type == 'servicio') {
                $this->cartS->push($itemCart);
            }
            $this->save();
            $this->loadCartTotales();
            $this->initializeQuery();
            return $uid;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 578369Agenda"]);
        }
    }
    private function getRewardPoints($excepcion, $total)
    {
        switch ($excepcion->type_comission) {
            case 'percent':
                $points = ($total * $excepcion->qty) / 100;
                break;
            case 'qty':
                $points = $excepcion->qty;
                break;
            default:
                $points = 0;
                break;
        }
        return $points;
    }
    private function calcularHorarioCita($duration)
    {
        try {
            $start_date_DB = Carbon::parse($this->start_date_DB);
            $minutes = $this->calculateTotalMinutes();
            $start = $start_date_DB->addMinutes(intval($minutes));
            $end_DB = Carbon::parse($start);
            $start = Carbon::parse($start)->format('H:i');
            $end = $end_DB->addMinutes(intval($duration));
            $end = Carbon::parse($end)->format('H:i');
            $data = [
                'start' => $start,
                'end' => $end
            ];
            return $data;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 80239Agenda"]);
        }
    }
    protected function calculateTotalMinutes()
    {
        try {
            $totalMinutes = 0;
            foreach ($this->cartS as $item) {
                if (isset($item['duration'])) {
                    $totalMinutes += intval($item['duration']);
                }
            }
            return $totalMinutes;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 47226Agenda"]);
        }
    }
    private function inCart($item_id, $type, $sid = null)
    {
        try {
            if ($type == 'producto') {
                $mycart = $this->cartP;
                if ($sid !== null) {
                    $material = $mycart->where('mid', $item_id)->where('asignacion_id', $sid)->count();
                    return  $material > 0 ? true : false;
                } else {
                    $cont = $mycart->where('mid', $item_id)->count();
                    return  $cont > 0 ? true : false;
                }
            } elseif ($type == 'servicio') {
                $mycart = $this->cartS;
                $cont = $mycart->where('sid', $item_id)->count();
            }
            return  $cont > 0 ? true : false;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 667369Agenda"]);
        }
    }
    public function updateQty($type, $uid, $cant = 1, $item_id = null)
    {
        try {
            if (!is_numeric($cant)) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => $cant . ' NO ES UNA CANTIDAD VÁLIDA']);
                return;
            }

            $newItem  = $this->setOldItem($type, $item_id, $uid);

            $newItem['qty'] = $uid != null ? intval($cant) : intval($newItem['qty'] + $cant);

            $values = $this->Calculator($newItem['disccount_price'] > 0 && $newItem['sale_price'] > $newItem['disccount_price'] ? $newItem['disccount_price'] : $newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'], $newItem['discount_type'], $newItem['disccount_percent']);

            $newItem['tax'] =  $values['iva'];

            $newItem['reward_points'] = DRG::calculateRewardPoints($newItem['sid'], true, $values['total'], null, $this->customer?->id, Auth::user()->salon->recompensaGeneral()->first());

            $newItem['total'] = $values['total'];

            $this->desvincularElementoAnterior($type, $item_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 683369Agenda"]);
        }
    }
    public function selectAssigment($type, $uid, $selected, $item_id = null)
    {
        try {
            $newItem  = $this->setOldItem($type, $item_id, $uid);

            $newItem['selected'] = $selected;

            $values = $this->Calculator($newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'], $newItem['discount_type'], $newItem['disccount_percent']);

            $newItem['tax'] =  $values['iva'];

            $newItem['disccount_price'] = $values['calculated_price'];

            $newItem['subtotal'] = $values['neto'];

            $newItem['reward_points'] = DRG::calculateRewardPoints($newItem['sid'], true, $values['total'], null, $this->customer?->id, Auth::user()->salon->recompensaGeneral()->first());

            $newItem['total'] = $values['total'];

            $this->desvincularElementoAnterior($type, $item_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 5131Agenda"]);
        }
    }
    public function updatePercentage($type, $uid, $disccount_percent = 0, $item_id = null)
    {
        try {
            $valorConPorcentaje = $disccount_percent;
            $valorSinPorcentaje = trim($valorConPorcentaje, "%");
            $valorNumerico = (int) $valorSinPorcentaje;
            if (!is_numeric($valorNumerico)) {
                $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Corrija el porcentaje"]);
                return;
            } else {
                $disccount_percent = $valorNumerico;
            }
            $newItem  = $this->setOldItem($type, $item_id, $uid);
            if (($disccount_percent >= 0 && $disccount_percent <= 100 && $newItem['discount_type'] == 'Porcentaje') || ($newItem['discount_type'] == 'Cantidad' && $disccount_percent >= 0 && $disccount_percent <= $this->totalCart)) {

                $newItem['disccount_percent'] = $item_id == null ? floatval($disccount_percent) : floatval($newItem['disccount_percent'] + $disccount_percent);

                $this->disccount = $newItem['disccount_percent'];

                $values = $this->Calculator($newItem['disccount_price'] > 0 && $newItem['sale_price'] > $newItem['disccount_price'] ? $newItem['disccount_price'] : $newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'], $newItem['discount_type'], $newItem['disccount_percent']);
                $this->disccount = 0;

                // actualizar los valores
                $newItem['reward_points'] = DRG::calculateRewardPoints($newItem['sid'], true, $values['total'], null, $this->customer?->id, Auth::user()->salon->recompensaGeneral()->first());

                $newItem['tax'] =  $values['iva'];

                $newItem['subtotal'] = $values['neto'];

                $newItem['total'] = $values['total'];

                $this->desvincularElementoAnterior($type, $item_id, $uid, $newItem);
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 710369Agenda"]);
        }
    }
    public function updateBaseComision($type, $uid, $base_comision)
    {
        try {
            $newItem  = $this->setOldItem($type, null, $uid);

            $newItem['base_comision'] = $base_comision;

            $this->desvincularElementoAnterior($type, null, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 723469Agenda"]);
        }
    }
    public function updatePercentageType($type, $uid, $discount_type, $item_id = null)
    {
        try {
            $newItem  = $this->setOldItem($type, $item_id, $uid);

            //se agrega 0 por default cuando se agrega por primera vez el producto
            //si ya está agregado el producto, toma lo que esté en el input
            $newItem['discount_type'] = $discount_type;

            $this->disccount = $newItem['disccount_percent'];

            $values = $this->Calculator($newItem['disccount_price'] > 0 && $newItem['sale_price'] > $newItem['disccount_price'] ? $newItem['disccount_price'] : $newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'], $newItem['discount_type'], $newItem['disccount_percent']);

            $this->disccount = 0;

            $newItem['tax'] =  $values['iva'];

            $newItem['subtotal'] = $values['neto'];

            $newItem['reward_points'] = DRG::calculateRewardPoints($newItem['sid'], true, $values['total'], null, $this->customer?->id, Auth::user()->salon->recompensaGeneral()->first());

            $newItem['total'] = $values['total'];

            $this->desvincularElementoAnterior($type, $item_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 45870Agenda"]);
        }
    }
    public function changeTotalCP($type, $uid, $disccount_price = 0, $item_id = null)
    {
        try {
            $disccount_price = $this->eliminarCaracteres($disccount_price);

            $newItem  = $this->setOldItem($type, $item_id, $uid);

            if ($disccount_price < $newItem['sale_price']) {
                $newItem['disccount_price'] = floatval($disccount_price);
            } else {
                $newItem['sale_price'] = floatval($disccount_price);
                $newItem['gross_price'] = floatval($disccount_price);
            }

            $values = $this->Calculator($disccount_price, $newItem['qty'], $newItem['ind_iva'], $newItem['discount_type'], $newItem['disccount_percent']);

            if (!$disccount_price) {
                $newItem['$disccount_price'] = 0;
            }

            $newItem['tax'] =  $values['iva'];

            $newItem['subtotal'] = $values['neto'];

            $newItem['reward_points'] = DRG::calculateRewardPoints($newItem['sid'], true, $values['total'], null, $this->customer?->id, Auth::user()->salon->recompensaGeneral()->first());

            $newItem['total'] = $values['total'];


            $this->desvincularElementoAnterior($type, $item_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 5911Agenda"]);
        }
    }
    public function updateIva($type, $uid, $selectedIva, $item_id = null)
    {
        try {
            $newItem  = $this->setOldItem($type, $item_id, $uid);

            $newItem['ind_iva'] = $selectedIva;

            $values = $this->Calculator($newItem['disccount_price'] > 0 && $newItem['sale_price'] > $newItem['disccount_price'] ? $newItem['disccount_price'] : $newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'], $newItem['discount_type'], $newItem['disccount_percent']);

            $newItem['tax'] =  $values['iva'];

            $newItem['reward_points'] = DRG::calculateRewardPoints($newItem['sid'], true, $values['total'], null, $this->customer?->id, Auth::user()->salon->recompensaGeneral()->first());

            $newItem['total'] = $values['total'];

            $this->desvincularElementoAnterior($type, $item_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 742369Agenda"]);
        }
    }
    public function updateColor($uid, $newColor, $type)
    {
        if ($type == 'cita') {
            $asig = Asignacion_servicio::find($uid);
            $asig->color = $newColor;
            $asig->save();
        } elseif ($type == 'bloqueo') {
            bloqueo::find($uid)->update(['color' => $newColor]);
        }
    }
    public function changeColorEvent($uid, $newColor)
    {

        try {
            $oldItem = $this->setOldItem('servicio', null, $uid);

            $newItem = $oldItem;
            if (!$oldItem) {
                return; // Manejar el caso en que el ítem no se encuentre en el carrito.
            }
            $newItem['color'] = $newColor;

            $this->desvincularElementoAnterior('servicio', null, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 766369Agenda"]);
        }
    }
    public function updateEmpleado($type, $uid, $selectedEmpleado, $item_id = null)
    {
        try {
            $oldItem = $this->setOldItem($type, $item_id, $uid);

            $newItem = $oldItem;
            if (!$oldItem) {
                return; // Manejar el caso en que el ítem no se encuentre en el carrito.
            }
            $newItem['vendedor'] = $selectedEmpleado;
            $newItem['color'] = empleado::select('color_preset')->find($selectedEmpleado)->color_preset;

            $this->desvincularElementoAnterior($type, $item_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 766369Agenda"]);
        }
    }
    private function setOldItem($type, $item_id, $uid)
    {
        try {
            if ($item_id == null) {
                if ($type == 'producto') {
                    $oldItem = $this->cartP->where('id', $uid)->first();
                } elseif ($type == 'servicio') {
                    $oldItem = $this->cartS->where('id', $uid)->first();
                }
            } else {
                if ($type == 'producto') {
                    $oldItem = $this->cartP->where('pid', $item_id)->first();
                } elseif ($type == 'servicio') {
                    $oldItem = $this->cartS->where('sid', $item_id)->first();
                }
            }
            return $oldItem;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 787369Agenda"]);
        }
    }

    private function desvincularElementoAnterior($type, $item_id, $uid, $newItem)
    {
        try {
            if ($type == 'producto') {

                // Encuentra el índice o clave del elemento a reemplazar
                $key = $this->cartP->search(function ($product) use ($uid, $item_id) {
                    return $product['id'] === $uid || $product['pid'] === $item_id;
                });

                // Reemplaza el método directamente por la clave encontrada
                if ($key !== false) {
                    $this->cartP[$key] = $newItem;
                }

                $this->loadCartTotales();
                $this->save();
            } elseif ($type == 'servicio') {

                // Encuentra el índice o clave del elemento a reemplazar
                $key = $this->cartS->search(function ($service) use ($uid, $item_id) {
                    return $service['id'] === $uid || $service['sid'] === $item_id;
                });

                // Reemplaza el método directamente por la clave encontrada
                if ($key !== false) {
                    $this->cartS[$key] = $newItem;
                }

                $this->loadCartTotales();
                $this->save();
            } elseif ($type == 'metodos') {

                // Encuentra el índice o clave del elemento a reemplazar
                $key = $this->methods->search(function ($method) use ($uid) {
                    return $method['uid'] === $uid;
                });

                // Reemplaza el método directamente por la clave encontrada
                if ($key !== false) {
                    $this->methods[$key] = $newItem;
                }
                $this->totalMethods();
                $this->loadCartTotales();
            } elseif ($type == 'propinas') {
                // Encuentra el índice o clave del elemento a reemplazar
                $key = $this->propinas->search(function ($propina) use ($uid) {
                    return $propina['uid'] === $uid;
                });

                // Reemplaza el método directamente por la clave encontrada
                if ($key !== false) {
                    $this->propinas[$key] = $newItem;
                }
                $this->totalPropinas();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 811369Agenda"]);
        }
    }
    public function setDisccount($qty)
    {
        $qty = $this->eliminarCaracteres($qty);
        $this->addMethod($qty, '', '4', $this->methods);
    }
    private function Calculator($price, $qty, $ind_iva, $type, $discount_qty)
    {
        try {
            if ($discount_qty) {
                if ($type == 'Porcentaje') {
                    //determinamos el precio de venta(con iva)
                    $calculatedPrice = $price - (($price * $discount_qty) / 100);
                } elseif ($type == 'Cantidad') {
                    //determinamos el precio de venta(con iva)
                    $calculatedPrice = $price - $discount_qty / $qty;
                }
            } else {
                //determinamos el precio de venta(con iva)
                $calculatedPrice = $price;
            }
            // precio unitario sin iva
            $precioUnitarioSinIva =  $calculatedPrice / (1 + $ind_iva);
            // subtotal neto
            $subtotalNeto =   $precioUnitarioSinIva * intval($qty);
            //monto iva
            $montoIva = $subtotalNeto * $ind_iva;
            //total con iva
            $totalConIva =  $calculatedPrice * intval($qty);
            // dd($subtotalNeto);

            return [
                'calculated_price' => $calculatedPrice,
                'neto' => $subtotalNeto,
                'iva' => $montoIva,
                'total' => $totalConIva
            ];
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 845369Agenda"]);
        }
    }

    private function totalIVA($carts)
    {
        try {
            $iva = 0;
            foreach ($carts as $cart) {
                $iva += $cart->sum(function ($item) {
                    return $item['tax'];
                });
            }
            return $iva;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 878369Agenda"]);
        }
    }

    private function totalCart($carts)
    {
        try {
            $this->pp_cart = 0;
            $amount = 0;
            foreach ($carts as $cart) {
                $amount += $cart->sum(function ($item) {
                    return $item['total'];
                });
            }
            return $amount;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 895369Agenda"]);
        }
    }


    private function subtotalCart($carts)
    {
        try {
            $subt = 0;
            foreach ($carts as $cart) {
                $subt += $cart->sum(function ($item) {
                    if (isset($item['subtotal'])) {
                        $subT = $item['subtotal'];
                        return $subT;
                    } else {
                        $subT = $item['total'] / ($item['ind_iva'] + 1);
                        return $subT;
                    }
                });
            }
            return $subt;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 915369Agenda"]);
        }
    }


    private function generatedPoints($carts)
    {
        try {
            $reward_points = 0;
            foreach ($carts as $cart) {
                $reward_points += $cart->sum(function ($item) {
                    if (isset($item['reward_points'])) {
                        $rewP = ($item['qty'] * $item['reward_points']);
                        return $rewP;
                    } else {
                        return 0;
                    }
                });
            }
            return $reward_points;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 936369Agenda"]);
        }
    }
    public function removeItem($id, $type, $item_id = null)
    {
        try {
            if ($type == 'servicio') {
                //eliminar el item de la coleccion / sesion
                $this->cartS  = $this->cartS->reject(function ($service) use ($id) {
                    return  $service['id'] === $id;
                });
                $this->cartP  = $this->cartP->reject(function ($material) use ($id) {
                    return  $material['id'] === $id;
                });
                $this->save();
                $this->minutes_qty = $this->calculateTotalMinutes();
                $this->loadCartTotales();
            } elseif ($type == 'method') {
                $this->methods = $this->methods->reject(function ($item) use ($id) {
                    return $item['uid'] === $id;
                });
                $this->totalMethods();
            } elseif ($type == 'propina') {
                $this->propinas = $this->propinas->reject(function ($item) use ($id) {
                    return $item['uid'] === $id;
                });
                $this->totalPropinas();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 955369Agenda"]);
        }
    }
    private function updateStarts()
    {
        try {
            $firstCartItem = $this->cartS->first();

            if ($firstCartItem) {
                $start_date = Carbon::parse($this->start_date_DB);
                $first_start_date = Carbon::parse($firstCartItem['start']);
                $dif_minutes = $start_date->diffInMinutes($first_start_date, false); // el tercer parámetro false devuelve negativo si es antes

                $updatedCart = collect($this->cartS)->map(function ($cartItem) use ($dif_minutes) {
                    return $this->updateStart($cartItem, $dif_minutes);
                });
                $this->cartS = collect($updatedCart);
                $this->save();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 119850Agenda"]);
        }
    }
    private function updateStart($oldItem, $dif_minutes)
    {
        try {
            $newItem = $oldItem;
            $newDate = Carbon::parse($newItem['start']);
            $newDate->addMinutes($dif_minutes);
            $newItem['start'] = $newDate->locale('local')->format('H:i');

            return $newItem;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 122251Agenda"]);
        }
    }

    private function initializeCollections()
    {
        $this->cartP = new Collection;
        $this->cartS = new Collection;
        $this->methods = new Collection;
        $this->propinas = new Collection;
        $this->clearSession(['cartPV']);
        $this->save();
    }

    private function initializeTotales()
    {
        $this->totalCart = 0;
        $this->pp_cart = 0;
        $this->taxCart = 0;
        $this->subtotalCart = 0;
        $this->generated_points = 0;
    }
    public function initializeQuery()
    {
        $this->search = null;
        $this->items = null;
    }
    public function disableEditing()
    {
        $this->initializeCollections();
        $this->initializeTotales();
    }
    private function loadCartTotales()
    {
        $this->totalCart = $this->totalCart([$this->cartS, session()->has('cartPV') ? session('cartPV') : new Collection]);
        $this->taxCart = $this->totalIVA([$this->cartS, session()->has('cartPV') ? session('cartPV') : new Collection]);
        $this->subtotalCart = $this->subtotalCart([$this->cartS, session()->has('cartPV') ? session('cartPV') : new Collection]);
        $this->generated_points = $this->generatedPoints([$this->cartS, session()->has('cartPV') ? session('cartPV') : new Collection]);
        $this->itemsCart = count($this->cartS);
        if (session()->has('cartPV')) {
            $this->itemsCart += $this->totalItems();
        }
        $this->total_disccount = $this->calculateTotalDisccount([$this->cartS, session()->has('cartPV') ? session('cartPV') : new Collection]);
        $this->totalCartBase = $this->totalCartBase([$this->cartS, session()->has('cartPV') ? session('cartPV') : new Collection]);
        $this->totalMethods();
        $this->enviarFechas();
    }

    private function totalItems()
    {
        try {
            $items = session('cartPV')->sum(function ($product) {
                return $product['qty'];
            });
            return $items;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 63576Agenda"]);
        }
    }
    private function totalCartBase($carts)
    {
        try {
            $amount = 0;
            foreach ($carts as $cart) {
                $amount += $cart->sum(function ($item) {
                    return $item['gross_price'];
                });
            }
            return $amount;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 895369Agenda"]);
        }
    }
    private function calculateTotalDisccount($carts)
    {
        try {
            $total_disccount = 0;
            foreach ($carts as $cart) {
                $total_disccount += $cart->sum(function ($item) {
                    return ($item['qty'] * $item['gross_price']) - $item['total'];
                });
            }
            return $total_disccount;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 936370Agenda"]);
        }
    }
    private function loadProductos()
    {
        try {
            if (!empty($this->search)) {
                $q = $this->search;
                $query = producto::where('salon_id', Auth::user()->salon->id)
                    ->where('visibility', 'visible')
                    ->where('name', '!=', 'Producto eliminado')
                    ->where(function ($qry) use ($q) {
                        $qry->where('name', 'like', "%{$q}%")
                            ->orWhere('description', 'like', "%{$q}%")
                            ->orWhere('sku', "{$q}")
                            ->orWhere('intern_sku', "{$q}");
                    })
                    ->orderBy('name', 'asc')
                    ->get();
            } else {

                $query =  producto::where('salon_id', Auth::user()->salon->id)->orderBy('stock_qty', 'asc')->get();
            }
            return $query;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1020369Agenda"]);
        }
    }
    private function loadServicios()
    {
        try {
            if (!empty($this->search)) {
                $query = servicio::where('salon_id', Auth::user()->salon->id)
                    ->where('visibility', 'visible')
                    ->where('name', '!=', 'Servicio eliminado')
                    ->where(function ($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                            ->orWhere('description', 'like', "%{$this->search}%");
                    })
                    ->orderBy('name', 'asc')
                    ->get();
            } else {
                $query = servicio::where('salon_id', Auth::user()->salon->id)
                    ->where('visibility', 'visible')
                    ->where('name', '!=', 'Servicio eliminado')
                    ->where(function ($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                            ->orWhere('description', 'like', "%{$this->search}%");
                    })
                    ->orderBy('name', 'asc')
                    ->get();
            }
            return $query;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1040369Agenda"]);
        }
    }
    public function loadItems($type)
    {
        $this->itemType = $type;
        if ($type == 'productos') {
            $this->items = $this->loadProductos();
        } elseif ($type == 'servicios') {
            $this->items = $this->loadServicios();
        }
    }
    public function addNewService($item_id)
    {
        try {
            if (session('recorrido') || $this->recorrido) {
                $this->dispatchBrowserEvent('play');
            }
            $item = servicio::find($item_id);
            $type = 'servicio';
            $this->AddItem($type, null, $item);
            $this->loadData();
            if ($this->customerId !== null) {
                $this->dispatchBrowserEvent('reloadForm');
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1068369Agenda"]);
        }
    }
    private function totalMethods()
    {
        try {
            $recibido = 0;
            $disccount = 0;
            $restante = $this->totalCart;
            foreach ($this->methods as $method) {
                if ($method['paymentMethod'] == '4') {
                    if ($method['tipo'] == 'Porcentaje') {
                        $disccount += ($method['amount'] / 100) * $restante;
                        $restante -= ($method['amount'] / 100) * $restante;
                    } elseif ($method['tipo'] == 'Cantidad') {
                        $disccount += $method['amount'];
                        $restante -= $method['amount'];
                    }
                } else {
                    $recibido += $method['amount'];
                    $restante -= $method['amount'];
                    // if($restante<0 && $method['paymentMethod']!='1'){
                    //     $this->dispatchBrowserEvent('noty-error', ['msg' =>  "El pago electrónico no puede superar la cantidad restante"] );
                    //     $this->removeItem($method['uid'],'method');
                    //     return;
                    // }
                }
            }
            $total_disccount = $this->calculateTotalDisccount([$this->cartS]);
            $this->total_disccount = $disccount + $total_disccount;
            $this->rest = $restante;
            $this->global_disccount = $disccount;
            $this->recibido = $recibido;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1082369Agenda"]);
        }
    }
    public function newMethod()
    {
        $this->addMethod(0, '', '1', $this->methods, 1);
    }
    public function setTip()
    {
        $this->newPropina();
    }
    public function newPropina()
    {
        $empleado = Empleado::where('salon_id', Auth::user()->salon->id)->where('visible', 1)->first();
        $this->addMethod(0, '', '1', $this->propinas, $empleado->id);
    }

    public function setGiftCard()
    {
        try {
            $inCartMethods = $this->inMethods('reference');

            if (!$inCartMethods) {
                $cupon = coupon::firstWhere('password', $this->searchPassword);

                if ($cupon) {
                    $noAcumulablesEnCarrito = $this->methods->whereNotNull('isAcumulable')->where('isAcumulable', false)->count();
                    $acumulablesEnCarrito = $this->methods->where('isAcumulable', true)->count();
                    $carritoVacio = $this->methods->whereNotNull('isAcumulable')->isEmpty();

                    if (!$carritoVacio) {
                        if ($noAcumulablesEnCarrito > 0) {
                            $this->dispatchBrowserEvent('noty-error', ['msg' => "Ya hay un cupón no acumulable en el carrito. No se pueden agregar más."]);
                            return;
                        }

                        if (!$cupon->acumulable && $acumulablesEnCarrito > 0) {
                            $this->dispatchBrowserEvent('noty-error', ['msg' => "No puedes agregar un cupón no acumulable si ya hay cupones acumulables en el carrito."]);
                            return;
                        }
                    }

                    if (!$cupon->redeemed) {
                        $this->addMethod($cupon->value_amount, $cupon->password, '99999', $this->methods, null, null, $cupon->acumulable);
                        $this->totalMethods();
                        $this->loadCartTotales();
                    } else {
                        $this->dispatchBrowserEvent('noty-error', ['msg' => "Código secreto canjeado."]);
                    }
                } else {
                    $this->dispatchBrowserEvent('noty-error', ['msg' => "Código secreto no encontrado."]);
                }
            } else {
                $this->dispatchBrowserEvent('noty-error', ['msg' => "Código secreto ya está en proceso de canje."]);
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41233260Payment"]);
        }
    }

    public function activateCardCust($custId = null, $balance = 0)
    {
        $this->emit('activateCardWithBalance', ['id' => $custId ?? $this->customerId, 'puntaje' => $balance]);
    }
    private function totalPropinas()
    {
        try {
            $recibido = 0;
            foreach ($this->propinas as $propina) {
                $recibido += $propina['amount'];
            }
            $this->propinasRecibidas = $recibido;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1108369Agenda"]);
        }
    }
    public function cambioDataMethods($uid, $data, $type, $array)
    {
        try {
            if ($type == 1) {
                $valorNumerico = $this->eliminarCaracteres($data);
                if (!is_numeric($valorNumerico)) {
                    $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Corrija el porcentaje"]);
                    return;
                } else {
                    $data = $valorNumerico;
                }
            }
            if ($array == 'propinas') {
                $coll = $this->propinas;
            } elseif ($array == 'metodos') {
                $coll = $this->methods;
            }
            $this->cambioFinal($uid, $data, $type, $coll, $array);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1120369Agenda"]);
        }
    }
    private function eliminarCaracteres($data)
    {
        try {
            // Elimina todos los caracteres que no sean números, puntos o comas
            $valorSinCaracter = preg_replace('/[^0-9.]/', '', $data);

            // Convierte el resultado a un float
            $valorNumerico = (float) $valorSinCaracter;

            // Verifica si el resultado es numérico
            if (!is_numeric($valorNumerico)) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => "Corrija el valor numérico"]);
                return;
            } else {
                return $valorNumerico;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 51312Agenda"]);
        }
    }

    public function cambioData($uid, $data, $type)
    {
        try {
            $data = $this->eliminarCaracteres($data);

            $myTips = $this->propinas;
            $oldItem = $myTips->where('uid', $uid)->first();

            $newItem  = $oldItem;

            switch ($type) {
                case 1:
                    $newItem['qty'] = $data;
                    break;
                case 2:
                    $newItem['reference'] = $data;
                    break;
                case 3:
                    $newItem['paymentMethod'] = $data;
                    break;
                case 4:
                    $newItem['empleado'] = $data;
                    break;
            }

            $this->propinas = $this->propinas->reject(function ($method) use ($uid) {
                return $method['uid'] === $uid;
            });

            $this->propinas->push(Arr::add($newItem, null, null));
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 483266AgendaServices"]);
        }
    }
    public function removeTip($uid)
    {
        try {
            $this->propinas = $this->propinas->reject(function ($method) use ($uid) {
                return $method['uid'] === $uid;
            });
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 503267AgendaServices"]);
        }
    }
    private function loadEmployees()
    {
        try {
            $empleados = Empleado::where('salon_id', Auth::user()->salon->id)->where('visible', 1)->get();
            return $empleados;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 523268AgendaServices"]);
        }
    }
    private function cambioFinal($uid, $data, $type, $myMethods, $array)
    {
        try {
            $oldItem = $myMethods->where('uid', $uid)->first();

            $newItem  = $oldItem;

            switch ($type) {
                case 1:
                    $newItem['amount'] = $data;
                    break;
                case 2:
                    $newItem['reference'] = $data;
                    break;
                case 3:
                    $newItem['paymentMethod'] = $data;
                    break;
                case 4:
                    $newItem['empleado'] = $data;
                    break;
                case 5:
                    $newItem['created_at'] = $data;
                    break;
                case 6:
                    $newItem['tipo'] = $data;
                    break;
            }

            $this->desvincularElementoAnterior($array, null, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1134369Agenda"]);
        }
    }
    public function Agendar()
    {
        $this->storeDate(1);
    }
    private function compararMetodos()
    {
        foreach ($this->methods as $method) {
            $methodFound = $this->itemSelected->metodosPago->where("payment_method_id", $method['paymentMethod'])->first();
            if ($methodFound != null) {
                $this->cambioDataMethods($method['uid'], $methodFound->created_at, 5, 'metodos');
            }
        }
    }
    public function storeBlock($editing = false)
    {
        try {

            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }

            $cart = session('cartS');
            if ($editing) {
                bloqueo::find($this->asignacion_id)->update([
                    'start' => carbon::parse($cart[0]['start'])->format('Y-m-d H:i:s') ?? $this->start_date_DB,
                    'end' => carbon::parse($cart[0]['end'])->format('Y-m-d H:i:s') ?? $this->end_date_DB,
                    'description' => $this->description,
                    'color' => $cart[0]['color'] ?? '#E2BBB4',
                    'empleado_id' => $cart[0]['vendedor'],
                ]);
            } else {
                bloqueo::create([
                    'start' => $this->start_date_DB,
                    'end' => $this->end_date_DB,
                    'description' => $this->description,
                    'salon_id' => Auth::user()->salon_id,
                    'color' => $cart[0]['color'] ?? '#E2BBB4',
                    'empleado_id' => $cart[0]['vendedor'],
                ]);
            }

            $this->clear();
            $this->cancelarCaptura();

            $this->dispatchBrowserEvent('noty', ['msg' => "SOLICITUD PROCESADA CON ÉXITO"]);
            $this->dispatchBrowserEvent('cerrarBlockMenuForm');

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1144369Agenda"]);
        }
    }

    public function storeDate($agendar = 0)
    {
        if ($this->billRequired && !$this->billed) {
            $this->validate([
                'usoCfdi' => 'required',
            ]);
        }
        try {
            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }

            session()->put('cust', $this->customer);
            session()->save();

            if (count($this->methods) > 0) {
                $agendar = 0;
            }

            if (count($this->cartS) <= 0) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'NO HAY SERVICIOS AGREGADOS']);
                return;
            }
            if (session()->has('cust')) {
                $this->customer = session('cust');
                $this->customerId = $this->customer?->id;
            }

            if ($this->customerId == null) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'SELECCIONA UN CLIENTE']);
                return;
            }

            if (!$this->verificarApertura() && !$agendar) {
                $this->dispatchBrowserEvent('aperturar');
                session()->put('cust', $this->customer);
                session()->save();
                return;
            }

            $respaldoData = null;
            if ($this->asignacion_id !== null) {
                $asignacion = Asignacion_servicio::with('date.details', 'date.details_product', 'date.propinas', 'date.metodosPago')->find($this->asignacion_id);
                $this->itemSelected = $asignacion->date;
            }
            if ($this->itemSelected !== null) {
                $respaldoData = $this->respaldarInfo();
                $this->compararMetodos();
                $this->vincularAbonos($respaldoData['abonos']);
                $this->vincularAbonos($respaldoData['abonoPropinas']);
                $this->cancelarStock();
                if ($this->itemSelected->details_product) {
                    foreach ($this->itemSelected->details_product as $item) {
                        $this->ajustarStockProd($item);
                    }
                }
                $this->deleteRelations();
            }

            $movimiento = null;
            $movimiento = new cita;
            $movimiento->id = $respaldoData ? $respaldoData['cid'] : $movimiento->id;
            $movimiento->description = $this->description;
            $movimiento->start = $this->start_date_DB;
            $movimiento->end = $this->end_date_DB;
            $movimiento->remember = $respaldoData !== null ? 0 : $this->remember;
            $cita_id = $this->setMovimiento($movimiento, $respaldoData, $agendar);
            if ($respaldoData) {
                $this->vincularAbonos($respaldoData['abonos'], $cita_id);
                $this->vincularAbonos($respaldoData['abonoPropinas'], $cita_id);
            }
            if ($this->respaldoFiles) {
                $this->vincularFiles($cita_id);
            }

            if (session()->has('cartPV') && count(session('cartPV')) > 0) {
                foreach (session('cartPV') as $item) {
                    $asignacion = new Asignacion_venta;
                    $asignacion->cita_id = $cita_id;
                    $asignacion->selected_item = $item['pid'];
                    $asignacion->quantity = $item['qty'];
                    $comission = DS::defineComisionProduct($item);
                    $asignacion->comission = $comission['balance'];
                    $asignacion->type_comision_calculated = $comission['type'] ?? 'percent';
                    $this->ajustarStock($item);
                    $this->setDetail($asignacion, $item, false);
                }
            }
            if (count($this->cartS) > 0) {
                $cartM = $this->recuperarCart('cartMaterials');
                foreach ($this->cartS as $item) {
                    $fecha = $this->calculateStart($item);
                    $asignacion = new Asignacion_servicio;
                    $asignacion->start = $fecha;
                    $asignacion->selected = $item['selected'];
                    $asignacion->selected_service = $item['sid'];
                    $asignacion->cita_id = $cita_id;
                    $comission = DRG::defineComisionService($item);
                    $asignacion->comission = $comission['balance'] ?? 0;
                    $asignacion->type_comision_calculated = $comission['type'] ?? 'percent';
                    $asignacion->duration = $item['duration'];
                    $asignacion->color = $item['color'] ?? '#E2BBB4';
                    $asignacion_id = $this->setDetail($asignacion, $item);

                    $materials = $cartM->where('uid', $item['id']);

                    foreach ($materials as $material) {

                        $newMaterial = new Material;
                        $newMaterial->producto_id = $material['mid'];
                        $newMaterial->sale_price = $material['sale_price'];
                        $newMaterial->qty = $material['qty'];
                        $newMaterial->salon_id = Auth()->user()->salon_id;
                        $newMaterial->user_id = Auth()->user()->id;
                        $newMaterial->empleado_id = $item['vendedor'];
                        $newMaterial->cliente_id = $this->customerId;
                        $newMaterial->asignacion_id = $asignacion_id;
                        $newMaterial->save();
                        $this->ajustarStockMaterial($material);
                    }
                }
            }
            // Cálculo del nuevo start y end de la cita
            $data = $this->calculateStartEndDate($movimiento);
            $movimiento->start = $data['start'];
            $movimiento->end = $data['end'];
            $movimiento->save();

            // Bandera que determina si se abre el modal para la activación de tarjeta_puntos
            $sendActivateCardCust = false;

            //metodos de pago
            foreach ($this->methods as $method) {
                if ($method['paymentMethod'] == '5' && !isset($method['created_at'])) {
                    $this->customer->tarjetaPuntos->balance -= $method['amount'];
                    $this->customer->tarjetaPuntos->save();
                }
                $payment = new metodo_pago_servicio;
                if (($method['paymentMethod'] == '1' || $method['paymentMethod'] == '99999') && $this->rest < 0) {
                    $payment->change = abs($this->rest);
                }
                $payment->cita_id = $cita_id;
                $payment = $this->setMethods($payment, $method, 1);

                if ($method['paymentMethod'] == '99999') {
                    $gc = coupon::firstWhere('password', $payment->reference);
                    $gc->redeemed = 1;
                    $gc->save();

                    if ($this->customer->tarjetaPuntos && !isset($method['created_at'])) {
                        $this->customer->tarjetaPuntos->balance += abs($this->rest);
                        $this->customer->tarjetaPuntos->save();
                    }

                    if (abs($this->rest) > 0 && $this->customer->tarjetaPuntos === null) {
                        $sendActivateCardCust = true;
                    }
                }
                $payment->save();
            }

            //propinas
            foreach ($this->propinas as $propina) {
                $payment = new Propina;
                $newPropina = $this->setMethods($payment, $propina);
                $newPropina->empleado_id = $propina['empleado'];
                $newPropina->cita_id = $cita_id;
                $newPropina->save();
            }
            $movimiento = cita::find($cita_id);

            //gallery
            if (!empty($this->gallery)) {

                // guardar imagenes nuevas
                foreach ($this->gallery as $file) {
                    $fileName = uniqid() . '_.' . $file->extension();
                    $file->storeAs('public/citas', $fileName);

                    // creamos relacion
                    $img = File::create([
                        'model_id' => $cita_id,
                        'model_type' => 'App\Models\cita',
                        'file' => $fileName
                    ]);

                    // guardar relacion
                    $movimiento->files()->save($img);
                }
            }

            // $this->recuperarMensajes($movimiento);

            if ($this->isBirthDate($this->customer->birth_date, $this->start_date_DB)) {
                $this->listTags = $this->addTagToArray($this->listTags, '2');
            }
            if ($this->customer->citas()->count() == 1) {
                $this->listTags = $this->addTagToArray($this->listTags, '1');
            }

            $listTags = null;
            if ($this->listTags != null && !is_array($this->listTags)) {
                $listTags = explode(",", $this->listTags);
            } else {
                $listTags = $this->listTags;
            }

            // relacionar categorias         
            if ($listTags != null) {
                $listTags = array_map(function ($item) {
                    $catName = trim($item);
                    // verificar si el elemento no es numérico
                    if (!is_numeric($catName)) {
                        // buscar el ID de la categoría en la tabla correspondiente
                        $categoria = etiquetas_cita::where('name', $catName)
                            ->where('salon_id', Auth::user()->salon->id)
                            ->first();
                        // reemplazar el elemento con el ID de la categoría si existe
                        if ($categoria) {
                            return $categoria->id;
                        }
                    }
                    // devolver el elemento sin cambios
                    return $item;
                }, $listTags);

                // Sincronizar etiquetas
                $listTags !== null
                    ? $movimiento->etiquetas()->sync($listTags)
                    : $movimiento->etiquetas()->detach();
            }

            if (intval($this->billRequired) !== 0) {
                $stat = $this->billed ? 2 : 1;
                $movimiento->billing = intval($stat);
                $movimiento->billing_description = $this->usoCfdi;
                $movimiento->tax_data_id = session()->has('rfcSelected') ? session('rfcSelected') : null;
                $movimiento->save();
            }

            if ($agendar == 0) {
                $this->dispatchBrowserEvent('noty', ['msg' => "CITA CERRADA - PAGO REGISTRADO"]);
                if ($movimiento->status == 'Pagada') {
                    $customer = cliente::with('tarjetaPuntos')->find($movimiento->customer_id);
                    if ((!isset($respaldoData) || $respaldoData['status'] != 'Pagada')) {
                        if ($customer->tarjetaPuntos) {
                            $tarjetaPuntos = $customer->tarjetaPuntos;
                            $tarjetaPuntos->balance += $movimiento->generated_points;
                            $tarjetaPuntos->save();
                        }
                    }
                    if (!$sendActivateCardCust) {
                        $this->dispatchBrowserEvent('abrirReview');
                        $this->imprimirTicket($movimiento);
                        $this->reseñaClienteDate($movimiento->customer_id);
                    } elseif (!isset($this->methods[0]['created_at']) && $sendActivateCardCust) {
                        $this->activateCardCust($this->customer?->id, abs($this->rest));
                        return;
                    }
                }
            } else {
                $this->dispatchBrowserEvent('noty', ['msg' => "SOLICITUD PROCESADA CON ÉXITO"]);
                $this->dispatchBrowserEvent('close-form');
            }
            // $this->sendMessage('confirmacion_cita', $movimiento);

            $this->clear();
            $this->cancelarCaptura();

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }

            if ($this->vista === 'livewire.calendar.edit') {
                $this->dispatchBrowserEvent('returnCustomersView');
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1169Agenda"]);
        }
    }
    private function sendMessage($type, $movimiento)
    {
        try {
            $data = [
                'uid' => uniqid(),
                'type' => $type,
                'phone' => $this->customer->phone,
                'salon_name' => Auth::user()->salon->name,
                'date' => Carbon::parse($movimiento->start)->locale('es')->format('d \d\e M'),
                'time' => Carbon::parse($movimiento->start)->format('H:i'),
                'services' => implode(", ", $movimiento->details()->with('servicio')->get()->pluck('servicio.name')->toArray()),
                'cita_id' => $movimiento->id,
            ];
            return redirect()->route('envia', ['uid' => $data['uid'], 'data' => $data]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 213129Agenda"]);
        }
    }
    public function continueStoring()
    {
        try {
            $this->clear();
            $this->cancelarCaptura();

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }

            if ($this->vista === 'livewire.calendar.edit') {
                $this->dispatchBrowserEvent('returnCustomersView');
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 123429Agenda"]);
        }
    }
    private function addTagToArray($listTags, $tagId)
    {
        if ($listTags != "") {
            $listTags .= ',' . $tagId;
        } else {
            $listTags .= $tagId;
        }
        return $listTags;
    }
    private function isBirthDate($birth_date, $date)
    {
        $bd = $birth_date ?? '0000-00-00';
        return $bd == Carbon::parse($date)->format('Y-m-d');
    }
    private function recuperarCart($key)
    {
        if (session()->has($key)) {
            return session($key);
        } else {
            return new Collection;
        }
    }
    private function obtenerStart($item, $fecha)
    {
        $horario = explode(":", $item['start']);

        // Extract the time components from currentDateC
        $hour = $horario[0];
        $minute = $horario[1];

        // Combine the date and time
        $new_date = $fecha->locale('es')->setTime($hour, $minute, '00');

        $start = $new_date->format('Y-m-d H:i:s');
        return $start;
    }
    private function recuperarMensajes($movimiento = null)
    {
        try {
            //mensajes
            if ($movimiento !== null) {
                foreach ($this->mensajesRespaldados as $mensaje) {
                    walog::create([
                        'uid' => $mensaje->uid,
                        'sent' => $mensaje->sent,
                        'cita_id' => $this->type == 'cita' ? $movimiento->id : null,
                        'venta_id' => $this->type == 'venta' ? $movimiento->id : null,
                        'type' => $mensaje->type
                    ])->save();
                }
            } else {
                foreach ($this->mensajesRespaldados as $mensaje) {
                    walog::create([
                        'uid' => $mensaje->uid,
                        'sent' => $mensaje->sent,
                        'cita_id' => null,
                        'venta_id' => null,
                        'type' => $mensaje->type
                    ])->save();
                }
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2131Agenda"]);
        }
    }
    private function ajustarStock($item)
    {
        try {
            $dif = $item['qty'];
            $product = producto::find($item['pid']);
            $product->stock_qty -= $dif;
            $product->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1134369Agenda"]);
        }
    }
    private function ajustarStockProd($item)
    {
        try {
            $dif = $item->quantity;
            $product = producto::find($item->selected_item);
            $product->stock_qty += $dif;
            $product->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 11334369Agenda"]);
        }
    }
    private function ajustarStockMaterial($item)
    {
        try {
            $product = producto::find($item['mid']);
            $product->stock_qty -= $item['qty'];
            $product->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1134369Agenda"]);
        }
    }
    private function setMethods($payment, $method, $isMethod = false)
    {
        try {
            $payment->payment_method_id = $method['paymentMethod'];
            $payment->reference = $method['reference'];
            $payment->amount = $method['amount'];
            if (isset($method['tipo']) && $isMethod) {
                $payment->tipo = $method['tipo'] ?? 'Cantidad';
            }
            if (isset($method['created_at'])) {
                $payment->created_at = $method['created_at'];
            }
            return $payment;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1244369Agenda"]);
        }
    }
    private function calculateStart($item)
    {
        try {
            // Obtener la hora y los minutos de la variable $hora
            $horario = explode(":", $item['start']);

            // Establecer la nueva hora y los nuevos minutos
            $hour = $horario[0];
            $minute = $horario[1];

            $start = Carbon::parse($this->currentDateC);
            $fecha = $start->setTime($hour, $minute, '00');
            return $fecha;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1256369Agenda"]);
        }
    }
    private function setDetail($asignacion, $item, $is_service = true)
    {
        try {
            if ($item['vendedor'] == null) {
                $asignacion->empleado_id = Empleado::where('salon_id', Auth::user()->salon->id)->where('visible', 1)->first()->id;
            } else {
                $asignacion->empleado_id = $item['vendedor'];
            }
            $asignacion->discount_qty = floatval($item['disccount_percent']);
            $asignacion->discount_type = $item['discount_type'];
            $totalPrice = $item['disccount_price'] > 0 && $item['sale_price'] > $item['disccount_price'] ? $item['disccount_price'] : $item['sale_price'];
            $asignacion->generated_points = DRG::calculateRewardPoints($item[$is_service ? 'sid' : 'pid'], $is_service, $totalPrice, null, $this->customer?->id, Auth::user()->salon->recompensaGeneral());
            $asignacion->current_price = floatval($item['gross_price'] > $item['sale_price'] ? $item['gross_price'] : $item['sale_price']);
            $asignacion->disccount_price = floatval($item['disccount_price']);
            $asignacion->iva = floatval($item['ind_iva']);
            $asignacion->base_comision = $item['base_comision'];
            $asignacion->save();
            return $asignacion->id;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1268369Agenda"]);
        }
    }
    private function setMovimiento($movimiento, $respaldoData, $agendar)
    {
        try {
            $movimiento->user_id = Auth::user()->id;
            $movimiento->customer_id = $this->customerId;
            $movimiento->disccount = $this->global_disccount;
            $movimiento->total = $this->totalCart;
            $movimiento->tax_data_id = isset($respaldoData) ? $respaldoData['tax_data_id'] : null;
            $movimiento->billing = isset($respaldoData) ? $respaldoData['billing'] : 0;
            $movimiento->billing_description = isset($respaldoData) ? $respaldoData['billing_description'] : null;
            if ($this->rest > 1) {
                $movimiento->status = 'Pendiente';
            } else {
                $movimiento->status = 'Pagada';
                if (isset($respaldoData)) {
                    if ($respaldoData['end_real'] == null) {
                        $movimiento->end_real = Carbon::now();
                    }
                } else {
                    $movimiento->end_real = Carbon::now();
                }
            }
            if ($agendar) {
                $movimiento->status = 'Agendada';
            }
            $movimiento->generated_points = $this->generated_points;
            $movimiento->salon_id = Auth::user()->salon->id;
            $movimiento->created_at = isset($respaldoData) ? $respaldoData['created_at'] : Carbon::now()->toDateString();
            $movimiento->save();
            return $movimiento->id;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1288369Agenda"]);
        }
    }
    private function validarVendedores($item)
    {
        if ($item['vendedor'] == null) {
            $this->dispatchBrowserEvent('noty-error', ['msg' => "Favor de agregar vendedor"]);
            return;
        }
    }
    private function calculateItems()
    {
        try {
            $qty = 0;
            foreach ($this->cartP as $item) {
                $qty += $item['qty'];
            }
            return $qty;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1310369Agenda"]);
        }
    }
    private function respaldarInfo()
    {
        try {
            // $this->mensajesRespaldados = $this->itemSelected->mensajesEnviados;
            $cid = $this->itemSelected->id;
            $generated_points = $this->itemSelected->generated_points;
            $created_at = $this->itemSelected->created_at;
            $status = $this->itemSelected->status;
            $start = $this->itemSelected->start;
            $end = $this->itemSelected->end;
            $end_real = $this->itemSelected->end_real;
            $customer_id = $this->itemSelected->customer_id;
            $salon_id = $this->itemSelected->salon_id;
            $abonos = $this->itemSelected->abonos;
            $abonoPropinas = $this->itemSelected->abonoPropinas;
            $billing = $this->itemSelected->billing;
            $billing_description = $this->itemSelected->billing_description;
            $tax_data_id = $this->itemSelected->tax_data_id;
            $info = [
                'cid' => $cid,
                'generated_points' => $generated_points,
                'created_at' => $created_at,
                'start' => $start,
                'salon_id' => $salon_id,
                'customer_id' => $customer_id,
                'end' => $end,
                'end_real' => $end_real,
                'status' => $status,
                'abonos' => $abonos,
                'abonoPropinas' => $abonoPropinas,
                'billing' => $billing,
                'billing_description' => $billing_description,
                'tax_data_id' => $tax_data_id,
            ];
            return $info;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1322369Agenda"]);
        }
    }
    private function deleteRelations($delete = 0)
    {
        if ($delete) {
            $this->pictures = [];
        }
        if (isset($this->itemSelected->files)) {
            $this->deleteFiles($this->itemSelected->files);
        }
        $this->deleteItems($this->itemSelected->metodosPago);
        $this->deleteItems($this->itemSelected->propinas);
        // $this->deleteItems($this->itemSelected->mensajesEnviados);
        foreach ($this->itemSelected->details as $detail) {
            if (isset($detail->materiales)) {
                $this->deleteItems($detail->materiales);
            }
        }
        $this->itemSelected->etiquetas()->detach();
        $this->deleteItems($this->itemSelected->details);
        $this->deleteItems($this->itemSelected->details_product);
        $this->deleteItems($this->itemSelected->abonos);
        $this->deleteItems($this->itemSelected->abonoPropinas);
        $this->itemSelected->delete();
    }
    private function vincularAbonos($abonos, $id = null)
    {
        try {
            foreach ($abonos as $abono) {
                $abono->cita_id = $id;
                $abono->save();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 121Agenda"]);
        }
    }
    private function vincularFiles($id)
    {
        try {
            foreach ($this->respaldoFiles as $file_id) {
                $file = File::find($file_id);
                if ($file != null) {
                    $file->model_id = $id;
                    $file->save();
                }
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 14031Agenda"]);
        }
    }
    private function deleteFiles($files)
    {
        try {
            foreach ($files as $file) {
                $found = false;
                $filename = 'storage/citas/' . $file->file;
                foreach ($this->pictures as $picture) {
                    if ($filename == $picture) {
                        $found = true;
                    }
                }
                if (!$found) {
                    unlink($filename);
                    $file->delete();
                } else {
                    $this->respaldoFiles[] = $file->id;
                }
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 545Agenda"]);
        }
    }
    private function deleteItems($relation, $metodo = false)
    {
        try {
            foreach ($relation as $item) {
                if ($metodo) {
                    if ($item->is_real) {
                        $item->delete();
                    }
                } else {
                    $item->delete();
                }
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1354369Agenda"]);
        }
    }
    private function defineBasePrice($item)
    {
        try {
            if ($item['base_comision']) {
                return $item['total'];
            } else {
                return $item['disccount_price'] ? $item['disccount_price'] : $item['sale_price'];
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 138312369Agenda"]);
        }
    }
    public function changeStartDuration($uid, $new_start)
    {
        try {
            $mycart = $this->cartS;
            $oldItem = $mycart->where('id', $uid)->first();
            $this->recorrerEnd($oldItem, $new_start, $uid);

            $data = $this->determinarNewStartEnd();
            $this->recorrerHorarios($data);

            $this->save();
            $this->loadData();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 654Agenda"]);
        }
    }
    public function changeEndDuration($uid, $new_end)
    {
        try {
            $duration = 0;
            $mycart = $this->cartS;
            $oldItem = $mycart->where('id', $uid)->first();

            $old_end = Carbon::createFromFormat('H:i', $oldItem['end']);
            $end = Carbon::createFromFormat('H:i', $new_end);

            $minutes = $old_end->diffInMinutes($end, false);

            $duration = $oldItem['duration'] + $minutes;
            if ($oldItem['start'] > $new_end) {
                $new_start = Carbon::parse($oldItem['start'])->addMinutes($minutes)->format('H:i');
                $this->changeDuration($uid, abs($duration), $new_end, $new_start);
            } else {
                $this->changeDuration($uid, $duration, $new_end);
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 512325Agenda"]);
        }
    }
    private function changeDuration($uid, $duration = 0, $new_end, $new_start = null)
    {
        try {
            $mycart = $this->cartS;
            $oldItem = $mycart->where('id', $uid)->first();
            $newItem  = $oldItem;

            $newItem['duration'] = intval($duration);
            $newItem['end'] = $new_end;
            if ($new_start) {
                $newItem['start'] = $new_start;
            }
            $this->desvincularElementoAnterior('servicio', null, $uid, $newItem);
            $this->minutes_qty = $this->recalculateDuration();
            $this->loadData();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 5776Agenda"]);
        }
    }
    private function recalculateDuration()
    {
        try {
            $total = 0;
            $mycart = $this->cartS;
            foreach ($mycart as $service) {
                $total += $service['duration'];
            }
            return $total;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 25631Agenda"]);
        }
    }
    private function recorrerEnd($oldItem, $new_start, $uid)
    {
        try {
            $newStartC = Carbon::createFromFormat('H:i', $new_start);
            $newItem  = $oldItem;

            $newItem['start'] = $new_start;
            $newItem['end'] = $newStartC->addMinutes($newItem['duration'])->format('H:i');
            $this->desvincularElementoAnterior('servicio', null, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 59251Agenda"]);
        }
    }
    private function determinarNewStartEnd()
    {
        try {
            $items = $this->cartS;
            $minStart = null;
            $maxEnd = null;

            foreach ($items as $item) {

                $start = Carbon::createFromFormat('H:i', $item['start']);
                $end = Carbon::createFromFormat('H:i', $item['end']);

                if (is_null($minStart) || $start < $minStart) {
                    $minStart = $start;
                }

                if (is_null($maxEnd) || $end > $maxEnd) {
                    $maxEnd = $end;
                }
            }
            return ['start' => $minStart->format('H:i'), 'end' => $maxEnd->format('H:i')];
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 7761Agenda"]);
        }
    }
    private function recorrerHorarios($data)
    {
        try {
            $fechas = $this->mergeFechas($data);
            $fechaConHoraStart = $fechas['start'];
            $fechaConHoraEnd = $fechas['end'];

            $this->start_date = $fechaConHoraStart;
            $this->start_date_DB = Carbon::parse($this->start_date)->format('Y-m-d H:i:s');
            $this->start_date = Carbon::parse($this->start_date)->format('H:i');
            $end_date = Carbon::parse($this->start_date);
            $end_date->addMinutes(intval($this->minutes_qty));
            if ($data['end'] > $end_date) {
                $this->end_date = $fechaConHoraEnd;
                $this->end_date_DB = Carbon::parse($this->end_date)->format('Y-m-d H:i:s');
                $this->end_date = Carbon::parse($this->end_date)->format('H:i');
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1641Agenda"]);
        }
    }
    private function mergeFechas($data)
    {
        try {
            // Crear un objeto Carbon a partir de la fecha dada
            $fechaCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $this->start_date_DB);

            // Separar la fecha y la hora
            $fechaParte = $fechaCarbon->format('Y-m-d');
            $horaParte = $data['start'];
            $horaParteE = $data['end'];
            // Unir la fecha y la hora
            $fechaConHoraStart = Carbon::createFromFormat('Y-m-d H:i', $fechaParte . ' ' . $horaParte);
            $fechaConHoraEnd = Carbon::createFromFormat('Y-m-d H:i', $fechaParte . ' ' . $horaParteE);

            return ['start' => $fechaConHoraStart, 'end' => $fechaConHoraEnd];
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 6547Agenda"]);
        }
    }
    public function deleteMov($type)
    {
        if ($type == 'cita') {
            $this->cancelarStock();
            $this->cancelarPuntos();
            // $respaldoData = $this->respaldarInfo();
            // $this->recuperarMensajes();
            $this->deleteRelations(1);
        } elseif ($type == 'bloqueo') {
            bloqueo::find($this->asignacion_id)->delete();
            $this->dispatchBrowserEvent('cerrarBlockMenuForm');
        }
        $this->dispatchBrowserEvent('noty', ['msg' => "MOVIMIENTO ELIMINADO PERMANENTEMENTE"]);
        $this->clear();
    }
    private function cancelarPuntos()
    {
        try {
            if (isset($this->itemSelected->customer->tarjetaPuntos)) {
                $tarjeta = $this->itemSelected->customer->tarjetaPuntos;
                $tarjeta->balance -= $this->itemSelected->generated_points;
                $tarjeta->save();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1575369Agenda"]);
        }
    }
    private function cancelarStock()
    {
        try {
            $details = $this->itemSelected->details;
            foreach ($details as $assigment) {
                $materiales = $assigment->materiales;
                foreach ($materiales as $material) {
                    $qty = $material->qty;
                    $product = $material->producto;
                    $product->stock_qty += $qty;
                    $product->save();
                }
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1585369Agenda"]);
        }
    }
    public function setCitaDragged($casilla, $cita)
    {
        $type = $cita['type'];
        $type_date = $cita['type_date'];
        $cita = $type == 'cita' ? asignacion_servicio::find($cita['id']) : bloqueo::find($cita['id']);
        try {
            if (isset($casilla) && isset($cita)) {
                $fecha = null;
                // Eliminar los paréntesis al principio y al final de la casilla
                $casilla = trim($casilla, '()');

                // Dividir la casilla en dos partes usando la coma como delimitador
                $valores = explode(',', $casilla);

                // Limpiar los espacios en blanco alrededor de cada valor
                $hora = trim($valores[0], " '");

                // Convertir la fecha actual a objeto DateTime
                $fecha = new DateTime($this->currentDateC);


                // Obtener la hora y los minutos de la variable $hora
                $horario = explode(":", $hora);

                // Establecer la nueva hora y los nuevos minutos
                $hour = $horario[0];
                $minute = $horario[1];
                $fecha = $fecha->setTime($hour, $minute, '00');

                // Obtener la fecha y hora con la nueva hora
                $nuevaFecha = $fecha->format('Y-m-d H:i:s');
                $empleado = trim($valores[1], " '");
            } else {
                $this->loadDateByAgenda();
                return;
            }

            $same_empl = $cita->empleado_id != $empleado;
            $color = empleado::select('color_preset')->find($empleado)->color_preset;
            if (($cita->start != $nuevaFecha || $same_empl)) {
                if ($type == 'cita') {
                    $item = [];
                    $cita->start = $nuevaFecha;
                    if ($same_empl) {
                        $cita->empleado_id = $empleado;
                        $cita->color = $color;
                        $item['sid'] = $cita->selected_service;
                        $item['vendedor'] = $empleado;
                        $item['base_comision'] = boolval($cita->base_comision);
                        $item['disccount_price'] = floatval($cita->disccount_price);
                        $item['sale_price'] = floatval($cita->current_price);
                        $price = $cita->disccount_price > 0 ? $cita->disccount_price : $cita->current_price;
                        $item['total'] = $cita->discount_qty > 0 ? ($cita->discount_type == 'Porcentaje' ? floatval($price - (($cita->discount_qty / 100) * $price)) : floatval($price - $cita->discount_qty)) : floatval($price);
                        $comision = DRG::defineComisionService($item);
                        $cita->comission = $comision['balance'];
                        $cita->type_comision_calculated = $comision['type'];
                    }
                    $citas_continuas = $this->identificarCitasContinuas($cita, $type_date);

                    foreach ($citas_continuas as $cita_cont) {
                        if ($same_empl) {
                            $cita_cont->empleado_id = $empleado;
                            $cita_cont->color = $color;
                        }
                        $cita_cont->start = $nuevaFecha;
                        $nuevaFecha = Carbon::parse($nuevaFecha)->addMinutes($cita_cont->duration);
                        $cita_cont->save();
                    }

                    $cita->save();
                    $cita = asignacion_servicio::with('date.details')->find($cita->id);
                    $date = $cita->date;
                    $data = $this->calculateStartEndDate($date);
                    $date->start = $data['start'];
                    $date->end = $data['end'];
                    $date->save();
                } elseif ($type == 'bloqueo') {
                    $minutes_qty = carbon::parse($cita->end)->diffInMinutes(Carbon::parse($cita->start));
                    $cita->start = $nuevaFecha;
                    $cita->end = Carbon::parse($nuevaFecha)->addMinutes($minutes_qty)->format('Y-m-d H:i:s');
                    $cita->empleado_id = $empleado;
                    $cita->color = $color;
                    $cita->save();
                }
                $this->currentDateC = Carbon::parse($nuevaFecha);
                $this->currentDate = $this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
                $this->loadDateByAgenda();
            } else {
                $this->cancelarCaptura();
                return;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1491300Agenda"]);
        }
    }
    private function calculateStartEndDate($date)
    {
        try {
            // Ordenar los detalles por el campo 'start'
            $details = $date->details->sortBy('start');

            // Obtener el primer y último elemento después de ordenar
            $newStart = $details->first()->start;
            $lastDetail = $details->last();

            // Calcular 'newEnd' sumando la duración del último detalle al 'start' del último detalle
            $lastStart = Carbon::parse($lastDetail->start);
            $newEnd = $lastStart->addMinutes($lastDetail->duration)->format('Y-m-d H:i:s');

            return [
                'start' => $newStart,
                'end' => $newEnd
            ];
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 645Agenda"]);
        }
    }
    public function changeWindow($tipo, $cita_id = null)
    {
        try {
            $this->loadCartTotales();
            $this->pestaña = $tipo;
            if ($cita_id != null) {
                $this->clear();
                $this->itemSelected = cita::with('user', 'customer', 'metodosPago', 'propinas', 'details')->find($cita_id);
                if ($this->itemSelected !== null && $this->itemSelected->salon_id == Auth::user()->salon_id) {
                    $this->billRequired = $this->itemSelected->billing;
                    $this->billed = $this->billRequired === 2 ? true : false;
                    $this->usoCfdi = $this->itemSelected->billing_description;
                    $this->rfcSelected($this->itemSelected->tax_data_id);

                    $this->methods = new Collection;
                    $this->propinas = new Collection;
                    $this->loadCart();
                    $this->currentDateC = Carbon::parse($this->itemSelected->start)->format('Y-m-d');
                    $this->dateSelected();
                    $this->enviarFechas();
                    $this->emit('loadFlatForm');
                    $this->action = 2;
                    $this->loadCartTotales();
                    if ($this->itemSelected->customer != null) {
                        $this->setCustomerId($this->itemSelected->customer->id, false);
                    }
                    $this->save();
                } else {
                    return redirect()->to(route('/'));
                }
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2341Agenda"]);
        }
    }
    private function enviarFechas()
    {
        try {
            $cart = $this->cartS;
            $starts = [];
            foreach ($cart as $item) {
                $starts[] = $item['start'];
            }
            $info = [
                'start' => $this->start_date_DB,
                'end' => $this->end_date_DB,
                'starts' => $starts,
            ];
            $this->infoDate = $info;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1416298Agenda"]);
        }
    }
    public function recibirMethods($data)
    {
        $this->methods = collect($data);
    }
    public function recibirPropinas($data)
    {
        $this->propinas = collect($data);
    }
    public function editTaxData()
    {
        $this->emit('viewTaxData', $this->customerId);
    }
    public function rfcSelected($rfcid)
    {
        session()->put('rfcSelected', $rfcid);
        session()->save();
    }
}
