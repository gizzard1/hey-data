<?php

namespace App\Http\Livewire;

use App\Exports\Formulario;
use App\Exports\reporteClientes;
use App\Exports\reporteClientesExtended;
use App\Models\categoria_servicio;
use App\Models\cliente;
use App\Models\categoria_cliente;
use App\Models\categoria_producto;
use App\Models\Empleado;
use App\Models\encuesta;
use App\Models\marca;
use App\Models\tax_data;
use App\Models\procedencia;
use App\Models\producto;
use App\Models\servicio;
use App\Models\tarjetas_punto;
use App\Traits\CustomerTrait;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Livewire\Agenda;

class Clientes extends Component
{
    use WithPagination;
    use CustomerTrait;
    public $is_modal = 0;

    public $search, $records,  $editing, $action = 1, $infoSelected = 1, $card, $customerSelected, $categorias, $cliente_id;
    public cliente $cliente; // propiedad de tipo cliente
    public $tax_data; // databinding / vinculacion de datos anidados con la notacion dot

    public $currentDate, $currentDateEnd, $is_interval = false;
    public $start, $currentDateC, $end, $currentDateCEnd;
    private $dataMovimientos, $clientes;
    private $hay_query = false;
    public $sort = 'asc', $by = 'first_name';
    public Collection $filtros;
    public $listCategories = [], $promedio, $topProducts, $topServices;
    private $empleados;

    public $customer_card, $barcode;

    public $selectedItems = [], $merge = [], $sumPoints = 0, $puntaje = 0, $clientes_tarjeta = 0;
    public $servicesAverage = 0, $productsAverage = 0;
    public $pestaña = 1, $infoSales = [], $infoDates = [], $lada, $procedencias = [];
    public $showmeMore = 0, $productos = [], $categoriaProductos = [], $servicios = [], $categoriaServicios = [], $proveedores = [];
    public $editingTaxData = false;
    public $orderRankingTable = 'service';
    public $tax_systems = [
        "601" => "General de Ley Personas Morales",
        "603" => "Personas Morales con Fines no Lucrativos",
        "605" => "Sueldos y Salarios e Ingresos Asimilados a Salarios",
        "606" => "Arrendamiento",
        "607" => "Régimen de Enajenación o Adquisición de Bienes",
        "608" => "Demás ingresos",
        "609" => "Consolidación",
        "611" => "Ingresos por Dividendos (socios y accionistas)",
        "612" => "Personas Físicas con Actividades Empresariales y Profesionales",
        "614" => "Ingresos por intereses",
        "615" => "Régimen de los ingresos por obtención de premios",
        "616" => "Sin obligaciones fiscales",
        "620" => "Sociedades Cooperativas de Producción que optan por Diferir sus Ingresos",
        "621" => "Incorporación Fiscal",
        "622" => "Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras",
        "623" => "Opcional para Grupos de Sociedades",
        "624" => "Coordinados",
        "626" => "Régimen Simplificado de Confianza (RESICO)",
        "628" => "Hidrocarburos",
        "629" => "De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales",
        "630" => "Enajenación de acciones en bolsa de valores",
    ];
    protected $rules =
    [
        'cliente.first_name' => "required|min:3|max:35",
        'cliente.last_name' => "nullable|min:3|max:35",
        'cliente.email' => "nullable|max:65",
        'cliente.postcode' => 'nullable',
        'cliente.birth_date' => 'nullable',
        'cliente.phone' => "nullable|min:10|max:15",
        'cliente.description' => "nullable|min:7|max:100",
        'cliente.sexo' => 'nullable|in:masculino,femenino,noBinario',
        'cliente.procedencia_id' => 'nullable',
        'cliente.want_offers' => 'nullable',
        'cliente.want_custom_messages' => 'nullable',
        'cliente.platform_id' => 'nullable',
        'cliente.record' => 'nullable',
        'cliente.created_at' => 'nullable',


        'tax_data.rfc' => ['required', 'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/i'],
        'tax_data.company_name' => 'required|min:3|max:100',
        'tax_data.tax_system' => 'required|max:3',
        'tax_data.postcode' => 'required|max:10',
        'tax_data.email' => 'nullable|max:55',
        'tax_data.phone' => 'nullable|max:15',
    ];

    protected function onlyClienteRules()
    {
        return collect($this->rules)
            ->filter(fn($_, $key) => str_starts_with($key, 'cliente.'))
            ->toArray();
    }

    protected function onlyTaxRules()
    {
        return collect($this->rules)
            ->filter(fn($_, $key) => str_starts_with($key, 'tax_data.'))
            ->toArray();
    }
    protected $paginationTheme = 'bootstrap';
    public function changeNorma()
    {
        foreach ($this->selectedItems as $cliente) {
            $cust = cliente::find($cliente);
            $cust->procedencia_id = 1;
            $cust->save();
        }
        $this->emit('refresh');
        $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
    }

    public function mount($custId = null, $search = null)
    {
        try {
            if ($custId) {
                $this->viewCust($custId);
            }
            $this->search = $search;
            $this->loadDefault();

            $this->clientes = $this->loadCustomers();

            $this->empleados = $this->loadEmpleados();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41180Clientes"]);
        }
    }

    private function loadProcedencias()
    {
        return procedencia::where('salon_id', null)->orWhere('salon_id', Auth::user()->salon->id)->get();
    }


    public function toggleSelectAll($isSelected)
    {
        if ($isSelected) {
            $items = $this->loadCustomers(0);
            $this->selectedItems = $items->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }
    public function toggleItem($itemId)
    {
        if (in_array($itemId, $this->selectedItems)) {
            $this->selectedItems = array_diff($this->selectedItems, [$itemId]);
        } else {
            $this->selectedItems[] = $itemId;
        }

        $this->selectedItems = array_values($this->selectedItems); // Reindexar el array

    }

    public function startMerge()
    {
        $this->cliente = new cliente;
        $this->clientes_tarjeta = 0;
        foreach ($this->selectedItems as $cliente) {
            $cust = cliente::with('tarjetaPuntos', 'procedencia')->find($cliente);
            $this->merge[] = $cust;
            if (isset($cust->tarjetaPuntos)) {
                $this->sumPoints += $cust->tarjetaPuntos->balance;
                $this->barcode = $cust->tarjetaPuntos->intern_barcode;
                $this->clientes_tarjeta += 1;
            }

            $this->cliente->salon_id = $cust->salon_id;
            $this->cliente->first_name = $cust->first_name;
            $this->cliente->last_name = $cust->last_name;
            $this->cliente->birth_date = $cust->birth_date;
            $this->cliente->want_custom_messages = $cust->want_custom_messages;
            $this->cliente->want_offers = $cust->want_offers;
            $this->cliente->sexo = $cust->sexo;
            $this->cliente->postcode = $cust->postcode;
            $this->cliente->procedencia_id = $cust->procedencia_id;
            $this->cliente->email = $cust->email;
            $this->cliente->phone = $cust->phone;
            $this->cliente->record = $cust->record;
            $this->dispatchBrowserEvent('initRecord', ['content' => $cust->record, 'id' => $cust->id]);
        }
    }
    public function mergeCustomers()
    {
        $this->validate([
            'cliente.postcode' => "nullable",
            'cliente.procedencia_id' => "nullable",
        ]);
    
        // Validar tamaño del json. Limitar a 10,000 caracteres para evitar problemas de rendimiento o almacenamiento
        if (strlen(json_encode($this->cliente->record)) > 10000) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "El contenido del expediente es demasiado grande."]);
            return;
        }

        try {
            DB::beginTransaction();

            $had_card = false;

            // Si la asignación no existe, crea una nueva
            $newCust = cliente::create([
                'salon_id' => Auth::user()->salon->id,
                'first_name' => $this->cliente->first_name,
                'last_name' => $this->cliente->last_name,
                'birth_date' => $this->cliente->birth_date,
                'want_custom_messages' => $this->cliente->want_custom_messages,
                'want_offers' => $this->cliente->want_offers,
                'sexo' => $this->cliente->sexo,
                'postcode' => $this->cliente->postcode,
                'procedencia_id' => $this->cliente->procedencia_id,
                'record' => $this->cliente->record,
                'created_at' => $this->cliente->created_at,
            ]);

            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }

            $newCustId = $newCust->id;

            foreach ($this->selectedItems as $cliente) {
                $cust = cliente::with('excepciones', 'tarjetaPuntos', 'calificaciones', 'reviews', 'materiales', 'respuestas', 'citas', 'categorias', 'compras')->find($cliente);

                if (isset($cust->tarjetaPuntos)) {
                    $had_card = true;
                }
                if (isset($cust->calificaciones)) {
                    $this->transferRelation($cust->calificaciones, $newCustId);
                }
                if (isset($cust->reviews)) {
                    $this->transferRelation($cust->reviews, $newCustId);
                }
                if (isset($cust->datosFacturacion)) {
                    $this->transferRelation($cust->datosFacturacion, $newCustId);
                }
                if (isset($cust->respuestas)) {
                    $this->transferRelation($cust->respuestas, $newCustId);
                }
                if (isset($cust->excepciones)) {
                    $this->transferRelation($cust->excepciones, $newCustId);
                }
                if (isset($cust->materiales)) {
                    $this->transferRelation($cust->materiales, $newCustId, 1);
                }
                if (isset($cust->compras)) {
                    $this->transferRelation($cust->compras, $newCustId, 1);
                }
                if (isset($cust->citas)) {
                    $this->transferRelation($cust->citas, $newCustId, 1);
                }

                if (isset($cust->categorias)) {
                    // Transferir las categorias al nuevo cliente
                    $categoriesList = $cust->categorias->pluck('id'); // Obtener los IDs de las categorias
                    // Asignar las categorias al nuevo cliente
                    $newCust->categorias()->attach($categoriesList);
                }
            }

            foreach ($this->selectedItems as $cliente) {
                $cust = cliente::with('datosFacturacion', 'tarjetaPuntos', 'calificaciones', 'reviews', 'materiales', 'respuestas', 'citas', 'categorias', 'compras', 'excepciones')->find($cliente);

                // Buscar el índice del valor a eliminar
                $key = array_search($cust->id, $this->selectedItems);
                unset($this->selectedItems[$key]);

                $this->destroy($cust);
            }

            if (empty($this->selectedItems)) {
                $newCust->email = $this->cliente->email;
                $newCust->phone = $this->cliente->phone;

                $newCust->save();

                if ($had_card) {
                    $this->customer_card = $newCust;
                    $this->StoreCard();
                }

                $this->endMerge();
            }
            DB::commit();

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41340Clientes"]);
        }
    }
    private function transferRelation($items, $id, $mov = 0)
    {
        foreach ($items as $item) {
            if ($mov) {
                $item->customer()->associate($id);
            } else {
                $item->cliente()->associate($id);
            }
            $item->save();
        }
    }

    public function endMerge()
    {
        $this->merge = [];
        $this->sumPoints = 0;
        $this->selectedItems = [];
        $this->clientes_tarjeta = 0;
        $this->barcode = null;
        $this->dispatchBrowserEvent('closeMerge');
    }
    private function loadDefault()
    {
        $this->infoSelected = 1;
        $this->cliente = new cliente(); // hacemos que la propiedad cliente sea una instancia del modelo
        $this->editing = false;
        $this->cliente->procedencia_id = null;

        $this->lada = '+52';
        $this->tax_data = [
            'state' => 'JC',
            'city' => '120',
            'type' => 'shipping',
            'country' => 'MX'
        ];

        $this->customer_card = null;
        $this->barcode = null;

        $this->listCategories = [];

        if (session()->has('filtros')) {
            $this->filtros = session('filtros');
        } else {
            $this->filtros = new Collection;
        }
        $this->categorias = Auth::user()->salon->categoriasClientes;
        $this->procedencias = $this->loadProcedencias();

        $this->loadDefaultTax();
    }
    protected $listeners = [
        'refreshComponent' => '$refresh',
        'updateRange',
        'search' => 'searching',
        'updateQueryMoney',
        'eliminar',
        'updateType',
        'updateQuery',
        'deleteFilter',
        'deleteCard',
        'datesSelected' => 'setDatesFromPeriod',
        'orderBy',
        'dateSelected' => 'setDate',
        'datesForFilters',
        'activateCard',
        'activateCardWithBalance',
        'activateModalForm',
        'categoriaAgregada',
        'selectedItemToEdit',
        'viewTaxData',
        'saveRecord'
    ];

    public function saveRecord($data)
    {
        try {
            // Validar tamaño del json. Limitar a 10,000 caracteres para evitar problemas de rendimiento o almacenamiento
            if (strlen(json_encode($data['ops'] ?? [])) > 10000) {
                $this->dispatchBrowserEvent('noty-error', ['msg' =>  "El contenido del expediente es demasiado grande."]);
                return;
            }
            $record = $this->customerSelected->record = $data['ops'];
            $this->customerSelected->save();
            $this->emit('refresh');

            if ($record) {
                $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"]);
                $this->dispatchBrowserEvent('closeRecordModal');
                $this->dispatchBrowserEvent('updateReadOnlyRecord', ['content' => $record]);
            } else {
                $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Error al guardar el expediente."]);
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1132Clientes"]);
        }
    }
    public function categoriaAgregada()
    {
        $this->categorias = Auth::user()->salon->categoriasClientes;
    }

    public function render()
    {
        try {
            return view('livewire.clientes', ['merge' => $this->merge, 'sumPoints' => $this->sumPoints, 'dataServices' => $this->topServices, 'dataProducts' => $this->topProducts, 'calificacion' => $this->promedio, 'categoriasCliente' => $this->listCategories, 'clientes' => $this->loadCustomers(), 'empleados' => $this->loadEmpleados(), 'productos' => $this->productos, 'categoriaProductos' => $this->categoriaProductos, 'categoriaServicios' => $this->categoriaServicios, 'proveedores' => $this->proveedores, 'servicios' => $this->servicios, 'servicesAverage' => $this->servicesAverage, 'productsAverage' => $this->productsAverage]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 71181Clientes"]);
        }
    }
    public function crearCliente()
    {
        $this->activateModalForm();
    }
    public function activateModalForm($custId = null)
    {
        if($custId) {
            $this->selectedItems[0] = $custId;
            $this->Edit();
        }
        $this->dispatchBrowserEvent('activateModal');
    }
    private function loadEmpleados()
    {
        $empleados = Empleado::where('salon_id', Auth::user()->salon_id)->get();
        return $empleados;
    }
    public function activateCardWithBalance($data)
    {
        try {
            $this->puntaje = $data['puntaje'];
            $this->activateCard($data['id']);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 8432182Clientes"]);
        }
    }
    public function activateCard($cliente_id = null)
    {
        try {
            $cliente = null;
            if ($cliente_id) {
                $cliente = cliente::with('tarjetaPuntos')->find($cliente_id);
            } else if (isset($this->selectedItems[0])) {
                $cliente = cliente::with('tarjetaPuntos')->find($this->selectedItems[0]);
            }
            if (!$cliente) {
                $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Cliente no encontrado."]);
                return;
            }
            $this->customer_card = $cliente;
            $this->barcode = $cliente->tarjetaPuntos->intern_barcode ?? null;
            $this->dispatchBrowserEvent('activateCardCustomer');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 82182Clientes"]);
        }
    }
    public function editCard($barcode)
    {

        try {
            $this->barcode = $barcode ?? tarjetas_punto::where('cliente_id', $this->selectedItems[0])->first()->intern_barcode;
            $this->dispatchBrowserEvent('editCardCustomer');
            $this->emit('refresh');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 82182Clientes"]);
        }
    }
    public function saveCard($wBC = 1)
    {
        try {
            $card = tarjetas_punto::where('cliente_id', $this->selectedItems[0])->first();
            if ($wBC) {
                $card->intern_barcode = $this->barcode;
            } else {
                $card->intern_barcode = null;
            }
            $card->save();
            $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"]);
            $this->dispatchBrowserEvent('closeEC');
            $this->viewCust();
            $this->emit('refresh');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 82312Clientes"]);
        }
    }
    public function StoreCard()
    {
        $this->validate([
            'barcode' => "nullable|min:1|max:100|unique:tarjetas_puntos,intern_barcode"
        ]);
        try {
            $tarjeta = $this->customer_card->tarjetaPuntos;
            if ($tarjeta) {
                $tarjeta->intern_barcode = $this->barcode;
                $tarjeta->save();
            } else {
                $card = new tarjetas_punto();
                $card->intern_barcode = $this->barcode;
                $card->cliente()->associate($this->customer_card);
                $card->balance = $this->puntaje;
                $card->save();
            }

            $this->loadDefault();
            $this->dispatchBrowserEvent('closeAC');
            $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"]);
            $this->emit('refresh');
            if ($this->puntaje > 0) {
                $this->emit('continueStoring');
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 113285Clientes"]);
        }
    }
    public function deleteCard()
    {
        try {
            $this->customer_card->tarjetaPuntos->delete();
            $this->loadDefault();
            $this->dispatchBrowserEvent('closeAC');
            $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"]);
            $this->emit('refresh');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 112385Clientes"]);
        }
    }
    public function loadCustomers($wPag = 1)
    {
        try {
            if (!empty($this->search)) {
                $query = cliente::with('procedencia', 'categorias', 'datosFacturacion', 'compras.details', 'citas.details_product', 'citas.details');
                if ($this->by != 'visits' && $this->by != 'birth_dateC') {
                    $query->whereNotNull($this->by);
                }
                $query->where(function ($query) {
                    $words = preg_split('/\s+/', trim($this->search));

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
                    ->where('salon_id', Auth::user()->salon->id);
            } else {
                $query =  cliente::with('categorias', 'datosFacturacion', 'compras.details', 'citas.details_product', 'citas.details');
                if ($this->by != 'visits' && $this->by != 'birth_dateC') {
                    $query->whereNotNull($this->by);
                }
                $query->where('salon_id', Auth::user()->salon->id);
            }

            if (count($this->filtros) > 0) {
                // Apply each filter accumulatively
                foreach ($this->filtros as $filtro) {
                    $itemQuery = $filtro['query'];
                    $fecha_inicio = $filtro['fecha_inicio'] ?? null;
                    $fecha_fin = $filtro['fecha_fin'] ?? null;
                    $is_interval = $filtro['is_interval'];
                    if ($filtro['type'] != 'productos' && $filtro['type'] != 'servicios' && $filtro['type'] != 'categoria-productos' && $filtro['type'] != 'categoria-servicios' && $filtro['type'] != 'proveedor-productos' && $filtro['type'] != 'proveedor-servicios') {
                        if ($filtro['type'] == 'atencion') {

                            $query->where(function ($query) use ($itemQuery, $fecha_inicio, $fecha_fin, $is_interval) {
                                if ($is_interval) {
                                    $query->whereHas('citas.details', function ($query) use ($itemQuery, $fecha_inicio, $fecha_fin) {
                                        // Filtrar por `empleado_id` en `citas.details`
                                        $query->where('empleado_id', $itemQuery);

                                        // Filtrar por el rango de fechas en la creación de la cita
                                        $query->whereBetween('created_at', [
                                            Carbon::parse($fecha_inicio)->startOfDay(),
                                            Carbon::parse($fecha_fin)->endOfDay()
                                        ]);
                                    });
                                } else {
                                    $query->whereHas('citas.details', function ($query) use ($itemQuery) {
                                        // Filtrar por `empleado_id` en `citas.details`
                                        $query->where('empleado_id', $itemQuery);
                                    });
                                }
                            })->orWhere(function ($query) use ($itemQuery, $fecha_inicio, $fecha_fin, $is_interval) {
                                if ($is_interval) {
                                    $query->whereHas('compras.details', function ($query) use ($itemQuery, $fecha_inicio, $fecha_fin) {
                                        // Filtrar por `empleado_id` en `compras.details`
                                        $query->where('empleado_id', $itemQuery);

                                        // Filtrar por el rango de fechas en la creación de la cita
                                        $query->whereBetween('created_at', [
                                            Carbon::parse($fecha_inicio)->startOfDay(),
                                            Carbon::parse($fecha_fin)->endOfDay()
                                        ]);
                                    });
                                } else {
                                    $query->whereHas('compras.details', function ($query) use ($itemQuery) {
                                        // Filtrar por `empleado_id` en `compras.details`
                                        $query->where('empleado_id', $itemQuery);
                                    });
                                }
                            })->get();
                            // $query = $this->atendidosPor($filtro['query'],$query);
                        } elseif ($filtro['type'] == 'visitas') {
                            // Filtro por número de visitas (citas + compras)
                            if (isset($filtro['min']) && isset($filtro['max'])) {
                                if ($is_interval) {
                                    $query->visitsBetween($fecha_inicio, $fecha_fin);
                                } else {
                                    $query->visits();
                                }
                                $query->havingRaw('citas_count + compras_count BETWEEN ? AND ?', [$filtro['min'], $filtro['max']])
                                    ->where('salon_id', Auth::user()->salon->id);
                            }
                        } elseif ($filtro['type'] == 'inactivo') {
                            $query->where(function ($query) use ($fecha_inicio, $fecha_fin, $is_interval) {
                                $query->when($is_interval, function ($query) use ($fecha_inicio, $fecha_fin) {
                                    $query->whereDoesntHave('citas', function ($q) use ($fecha_inicio, $fecha_fin) {
                                        $q->whereBetween('start', [
                                            Carbon::parse($fecha_inicio)->startOfDay(),
                                            Carbon::parse($fecha_fin)->endOfDay()
                                        ]);
                                    })->whereDoesntHave('compras', function ($q) use ($fecha_inicio, $fecha_fin) {
                                        $q->whereBetween('created_at', [
                                            Carbon::parse($fecha_inicio)->startOfDay(),
                                            Carbon::parse($fecha_fin)->endOfDay()
                                        ]);
                                    });
                                }, function ($query) {
                                    $query->whereDoesntHave('citas')->whereDoesntHave('compras');
                                });
                            });
                        } elseif ($filtro['type'] == 'birth_date') {
                            if ($is_interval) {
                                // Extraer mes y día de las fechas de inicio y fin
                                $start = explode('-', $fecha_inicio);
                                $end = explode('-', $fecha_fin);

                                $startMonth = $start[1];
                                $startDay = $start[2];

                                $endMonth = $end[1];
                                $endDay = $end[2];

                                // Condición para el rango de fechas de cumpleaños
                                $query->where(function ($query) use ($startMonth, $startDay, $endMonth, $endDay) {
                                    $query->whereRaw("(MONTH(birth_date) > ? OR (MONTH(birth_date) = ? AND DAY(birth_date) >= ?))", [$startMonth, $startMonth, $startDay])
                                        ->whereRaw("(MONTH(birth_date) < ? OR (MONTH(birth_date) = ? AND DAY(birth_date) <= ?))", [$endMonth, $endMonth, $endDay]);
                                });
                            }
                        } elseif ($filtro['type'] == 'edad') {
                            if (isset($filtro['min']) && isset($filtro['max'])) {
                                $query->selectRaw('clientes.*, FLOOR(DATEDIFF(CURRENT_DATE, birth_date) / 365.25) AS edad')
                                    ->having('edad', '>=', $filtro['min'])
                                    ->having('edad', '<=', $filtro['max']);
                            }
                        } elseif ($filtro['type'] == 'horario') {
                            if (isset($filtro['min']) && isset($filtro['max'])) {
                                $minHora = $filtro['min']; // Formato H:i (ej. 08:00)
                                $maxHora = $filtro['max']; // Formato H:i (ej. 18:00)

                                if ($is_interval) {
                                    $query->whereHas('citas', function ($q) use ($fecha_inicio, $fecha_fin, $minHora, $maxHora) {
                                        $q->whereBetween('start', [
                                            Carbon::parse($fecha_inicio)->startOfDay(),
                                            Carbon::parse($fecha_fin)->endOfDay()
                                        ])
                                            // Filtrar citas que comienzan o terminan en el rango de horas
                                            ->where(function ($query) use ($minHora, $maxHora) {
                                                $query->whereRaw("TIME(start) BETWEEN ? AND ?", [$minHora, $maxHora])
                                                    ->orWhereRaw("TIME(end) BETWEEN ? AND ?", [$minHora, $maxHora])
                                                    // O que la cita esté completamente dentro del rango
                                                    ->orWhere(function ($q) use ($minHora, $maxHora) {
                                                        $q->whereRaw("? BETWEEN TIME(start) AND TIME(end)", [$minHora])
                                                            ->whereRaw("? BETWEEN TIME(start) AND TIME(end)", [$maxHora]);
                                                    });
                                            });
                                    });
                                } else {
                                    $query->whereHas('citas', function ($q) use ($minHora, $maxHora) {
                                        // Filtrar citas por horas cuando no hay intervalo de fechas
                                        $q->where(function ($query) use ($minHora, $maxHora) {
                                            $query->whereRaw("TIME(start) BETWEEN ? AND ?", [$minHora, $maxHora])
                                                ->orWhereRaw("TIME(end) BETWEEN ? AND ?", [$minHora, $maxHora])
                                                // O que la cita esté completamente dentro del rango
                                                ->orWhere(function ($q) use ($minHora, $maxHora) {
                                                    $q->whereRaw("? BETWEEN TIME(start) AND TIME(end)", [$minHora])
                                                        ->whereRaw("? BETWEEN TIME(start) AND TIME(end)", [$maxHora]);
                                                });
                                        });
                                    });
                                }
                            }
                        } elseif ($filtro['type'] == 'created_at') {
                            if ($is_interval) {
                                $query->whereBetween('created_at', [
                                    Carbon::parse($fecha_inicio)->startOfDay(),
                                    Carbon::parse($fecha_fin)->endOfDay()
                                ])->get();
                            }
                        } elseif ($filtro['type'] == 'hasReward') {
                            $query->whereHas('tarjetaPuntos')->get();
                        } elseif ($filtro['type'] == 'gastado') {
                            if (isset($filtro['min']) && isset($filtro['max'])) {
                                $minGasto = $filtro['min'];
                                $maxGasto = $filtro['max'];

                                if ($is_interval) {

                                    $query->where(function ($query) use ($minGasto, $maxGasto, $fecha_inicio, $fecha_fin) {
                                        $query->where(function ($query) use ($minGasto, $maxGasto, $fecha_inicio, $fecha_fin) {
                                            // Filtrar por compras dentro del rango de fecha y gasto, y que no estén canceladas
                                            $query->whereHas('compras', function ($query) use ($minGasto, $maxGasto, $fecha_inicio, $fecha_fin) {
                                                $query->where('status', '!=', 'Cancelada')
                                                    ->whereHas('metodosPago', function ($query) use ($minGasto, $maxGasto, $fecha_inicio, $fecha_fin) {
                                                        $query->whereBetween('created_at', [Carbon::parse($fecha_inicio)->startOfDay(), Carbon::parse($fecha_fin)->endOfDay()])
                                                            ->whereRaw('(amount - COALESCE(`change`, 0)) BETWEEN ? AND ?', [$minGasto, $maxGasto]);
                                                    });
                                            });

                                            // Filtrar por citas dentro del rango de fecha y gasto, y que no estén canceladas
                                            $query->orWhereHas('citas', function ($query) use ($minGasto, $maxGasto, $fecha_inicio, $fecha_fin) {
                                                $query->where('status', '!=', 'Cancelada')
                                                    ->whereHas('metodosPago', function ($query) use ($minGasto, $maxGasto, $fecha_inicio, $fecha_fin) {
                                                        $query->whereBetween('created_at', [Carbon::parse($fecha_inicio)->startOfDay(), Carbon::parse($fecha_fin)->endOfDay()])
                                                            ->whereRaw('(amount - COALESCE(`change`, 0)) BETWEEN ? AND ?', [$minGasto, $maxGasto]);
                                                    });
                                            });
                                        });
                                    });
                                } else {
                                    // Filtrar por compras dentro del rango de fecha y gasto, y que no estén canceladas
                                    $query->whereHas('compras', function ($query) use ($minGasto, $maxGasto) {
                                        $query->where('status', '!=', 'Cancelada')
                                            ->whereHas('metodosPago', function ($query) use ($minGasto, $maxGasto) {
                                                $query->whereRaw('(amount - COALESCE(`change`, 0)) BETWEEN ? AND ?', [$minGasto, $maxGasto]);
                                            });
                                    });
                                    // Filtrar por citas dentro del rango de fecha y gasto, y que no estén canceladas
                                    $query->orWhereHas('citas', function ($query) use ($minGasto, $maxGasto) {
                                        $query->where('status', '!=', 'Cancelada')
                                            ->whereHas('metodosPago', function ($query) use ($minGasto, $maxGasto) {
                                                $query->whereRaw('(amount - COALESCE(`change`, 0)) BETWEEN ? AND ?', [$minGasto, $maxGasto]);
                                            });
                                    });
                                }
                            }
                        } elseif ($filtro['type'] == 'descontadoVentas') {

                            if (isset($filtro['min']) && isset($filtro['max'])) {
                                $minGasto = $filtro['min'];
                                $maxGasto = $filtro['max'];

                                if ($is_interval) {
                                    $query->where(function ($query) use ($minGasto, $maxGasto, $fecha_inicio, $fecha_fin) {
                                        // Acumular descuentos de compras y sus detalles
                                        $query->whereHas('compras', function ($query) use ($minGasto, $maxGasto, $fecha_inicio, $fecha_fin) {
                                            $query->where('status', '!=', 'Cancelada')
                                                ->whereBetween('created_at', [
                                                    Carbon::parse($fecha_inicio)->startOfDay(),
                                                    Carbon::parse($fecha_fin)->endOfDay()
                                                ])
                                                ->with(['details' => function ($query) {
                                                    $query->where('disccount_price', '>', 0)
                                                        ->selectRaw('SUM(current_price - disccount_price) as detail_disccount');
                                                }])
                                                ->groupBy('ventas.id')
                                                ->havingRaw('(SUM(disccount) + COALESCE(SUM((SELECT SUM(current_price - disccount_price) FROM asignacion_ventas WHERE asignacion_ventas.venta_id = ventas.id AND disccount_price > 0)), 0)) BETWEEN ? AND ?', [$minGasto, $maxGasto]);
                                        });
                                    });
                                } else {
                                    // Sin intervalo de fecha
                                    $query->where(function ($query) use ($minGasto, $maxGasto) {
                                        // Acumular descuentos de compras y sus detalles
                                        $query->whereHas('compras', function ($query) use ($minGasto, $maxGasto) {
                                            $query->where('status', '!=', 'Cancelada')
                                                ->with(['details' => function ($query) {
                                                    $query->where('disccount_price', '>', 0)
                                                        ->selectRaw('SUM(current_price - disccount_price) as detail_disccount');
                                                }])
                                                ->groupBy('ventas.id')
                                                ->havingRaw('(SUM(disccount) + COALESCE(SUM((SELECT SUM(current_price - disccount_price) FROM asignacion_ventas WHERE asignacion_ventas.venta_id = ventas.id AND disccount_price > 0)), 0)) BETWEEN ? AND ?', [$minGasto, $maxGasto]);
                                        });
                                    });
                                }
                            }
                        } elseif ($filtro['type'] == 'descontadoCitas') {

                            if (isset($filtro['min']) && isset($filtro['max'])) {
                                $minGasto = $filtro['min'];
                                $maxGasto = $filtro['max'];

                                if ($is_interval) {
                                    $query->where(function ($query) use ($minGasto, $maxGasto, $fecha_inicio, $fecha_fin) {
                                        // Acumular descuentos de compras y sus detalles
                                        $query->whereHas('citas', function ($query) use ($minGasto, $maxGasto, $fecha_inicio, $fecha_fin) {
                                            $query->where('status', '!=', 'Cancelada')
                                                ->whereBetween('created_at', [
                                                    Carbon::parse($fecha_inicio)->startOfDay(),
                                                    Carbon::parse($fecha_fin)->endOfDay()
                                                ])
                                                ->with(['details' => function ($query) {
                                                    $query->where('disccount_price', '>', 0)
                                                        ->selectRaw('SUM(current_price - disccount_price) as detail_disccount');
                                                }])
                                                ->groupBy('citas.id')
                                                ->havingRaw('(SUM(disccount) + COALESCE(SUM((SELECT SUM(current_price - disccount_price) FROM asignacion_servicios WHERE asignacion_servicios.cita_id = citas.id AND disccount_price > 0)), 0)) BETWEEN ? AND ?', [$minGasto, $maxGasto]);
                                        });
                                    });
                                } else {
                                    // Sin intervalo de fecha
                                    $query->where(function ($query) use ($minGasto, $maxGasto) {
                                        // Acumular descuentos de compras y sus detalles
                                        $query->whereHas('citas', function ($query) use ($minGasto, $maxGasto) {
                                            $query->where('status', '!=', 'Cancelada')
                                                ->with(['details' => function ($query) {
                                                    $query->where('disccount_price', '>', 0)
                                                        ->selectRaw('SUM(current_price - disccount_price) as detail_disccount');
                                                }])
                                                ->groupBy('citas.id')
                                                ->havingRaw('(SUM(disccount) + COALESCE(SUM((SELECT SUM(current_price - disccount_price) FROM asignacion_servicios WHERE asignacion_servicios.cita_id = citas.id AND disccount_price > 0)), 0)) BETWEEN ? AND ?', [$minGasto, $maxGasto]);
                                        });
                                    });
                                }
                            }
                        } elseif ($filtro['type'] == 'cat') {
                            if ($filtro['query'] != null) {
                                $categoryId = $filtro['query'];
                                $query->whereHas('categorias', function ($query) use ($categoryId) {
                                    $query->where('categoria_cliente_id', $categoryId);
                                })->get();
                            }
                        } elseif ($filtro['type'] == 'hasCancelled') {
                            $minCancelled = $filtro['query'] ?? 0;

                            if ($is_interval) {
                                // Filtrar clientes con al menos $minCancelled citas canceladas en el rango de fechas
                                $query->whereHas('citas', function ($query) use ($fecha_inicio, $fecha_fin) {
                                    $query->where('status', 'Cancelada')
                                        ->whereBetween('created_at', [
                                            Carbon::parse($fecha_inicio)->startOfDay(),
                                            Carbon::parse($fecha_fin)->endOfDay()
                                        ]);
                                })
                                    ->withCount(['citas' => function ($query) use ($fecha_inicio, $fecha_fin) {
                                        $query->where('status', 'Cancelada')
                                            ->whereBetween('created_at', [
                                                Carbon::parse($fecha_inicio)->startOfDay(),
                                                Carbon::parse($fecha_fin)->endOfDay()
                                            ]);
                                    }])
                                    ->having('citas_count', '>=', $minCancelled);
                            } else {
                                // Filtrar clientes con al menos $minCancelled citas canceladas sin un rango de fechas
                                $query->whereHas('citas', function ($query) {
                                    $query->where('status', 'Cancelada');
                                })
                                    ->withCount(['citas' => function ($query) {
                                        $query->where('status', 'Cancelada');
                                    }])
                                    ->having('citas_count', '>=', $minCancelled);
                            }
                        } else {
                            $query->where($filtro['type'], $filtro['query']);
                        }
                    } elseif ($filtro['type'] == 'productos' && isset($filtro['psid'])) {
                        $psid = $filtro['psid'];
                        $fecha_inicio = Carbon::parse($fecha_inicio)->startOfDay();
                        $fecha_fin = Carbon::parse($fecha_fin)->endOfDay();

                        if ($is_interval) {
                            $query->where(function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                $query->whereHas('compras', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                    $query->whereHas('details', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                        $query->whereBetween('created_at', [
                                            $fecha_inicio,
                                            $fecha_fin
                                        ])->where('selected_item', $psid);
                                    });
                                })
                                    ->orWhereHas('citas', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                        $query->whereHas('details_product', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                            $query->whereBetween('created_at', [
                                                $fecha_inicio,
                                                $fecha_fin
                                            ])->where('selected_item', $psid);
                                        });
                                    });
                            });
                        } else {
                            $query->where(function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                $query->whereHas('compras.details', function ($query) use ($psid) {
                                    $query->where('selected_item', $psid);
                                })->orWhereHas('citas.details_product', function ($query) use ($psid) {
                                    $query->where('selected_item', $psid);
                                });
                            });
                        }
                    } elseif ($filtro['type'] == 'categoria-productos' && isset($filtro['psid'])) {
                        $psid = $filtro['psid'];
                        $fecha_inicio = Carbon::parse($fecha_inicio)->startOfDay();
                        $fecha_fin = Carbon::parse($fecha_fin)->endOfDay();

                        if ($is_interval) {
                            $query->where(function ($q) use ($psid, $fecha_inicio, $fecha_fin) {
                                $q->whereHas('compras', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                    $query->whereHas('details', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                        $query->whereBetween('created_at', [
                                            $fecha_inicio,
                                            $fecha_fin
                                        ])
                                            ->whereHas('product', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                                $query->whereHas('categorias', function ($query) use ($psid) {
                                                    $query->where('categoria_producto_id', $psid);
                                                });
                                            });
                                    });
                                })
                                    ->orWhereHas('citas', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                        $query->whereHas('details_product', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                            $query->whereBetween('created_at', [
                                                $fecha_inicio,
                                                $fecha_fin
                                            ])
                                                ->whereHas('product', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                                    $query->whereHas('categorias', function ($query) use ($psid) {
                                                        $query->where('categoria_producto_id', $psid);
                                                    });
                                                });
                                        });
                                    });
                            });
                        } else {
                            $query->where(function ($q) use ($psid) {
                                $q->whereHas('compras.details.product.categorias', function ($has_query) use ($psid) {
                                    $has_query->where('categoria_producto_id', $psid);
                                })->orWhereHas('citas.details_product.product.categorias', function ($has_query) use ($psid) {
                                    $has_query->where('categoria_producto_id', $psid);
                                });
                            });
                        }
                    } elseif ($filtro['type'] == 'proveedor-productos' && isset($filtro['psid'])) {
                        $psid = $filtro['psid'];
                        $fecha_inicio = Carbon::parse($fecha_inicio)->startOfDay();
                        $fecha_fin = Carbon::parse($fecha_fin)->endOfDay();

                        if ($is_interval) {
                            $query->where(function ($q) use ($psid, $fecha_inicio, $fecha_fin) {
                                $q->whereHas('compras', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                    $query->whereHas('details', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                        $query->whereBetween('created_at', [
                                            $fecha_inicio,
                                            $fecha_fin
                                        ])
                                            ->whereHas('product', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                                $query->where('brand_id', $psid);
                                            });
                                    });
                                })
                                    ->orWhereHas('citas', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                        $query->whereHas('details_product', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                            $query->whereBetween('created_at', [
                                                $fecha_inicio,
                                                $fecha_fin
                                            ])
                                                ->whereHas('product', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                                    $query->where('brand_id', $psid);
                                                });
                                        });
                                    });
                            });
                        } else {
                            $query->where(function ($q) use ($psid) {
                                $q->whereHas('compras.details.product', function ($has_query) use ($psid) {
                                    $has_query->where('brand_id', $psid);
                                })->orWhereHas('citas.details_product.product', function ($has_query) use ($psid) {
                                    $has_query->where('brand_id', $psid);
                                });
                            });
                        }
                    } elseif ($filtro['type'] == 'proveedor-servicios' && isset($filtro['psid'])) {
                        $psid = $filtro['psid'];
                        $fecha_inicio = Carbon::parse($fecha_inicio)->startOfDay();
                        $fecha_fin = Carbon::parse($fecha_fin)->endOfDay();

                        if ($is_interval) {
                            $query->whereHas('citas', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                $query->whereHas('details', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                    $query->whereBetween('created_at', [
                                        $fecha_inicio,
                                        $fecha_fin
                                    ])
                                        ->whereHas('servicio', function ($query) use ($psid) {
                                            $query->where('brand_id', $psid);
                                        });
                                });
                            });
                        } else {
                            $query->where(function ($q) use ($psid) {
                                $q->whereHas('citas.details.servicio', function ($has_query) use ($psid) {
                                    $has_query->where('brand_id', $psid);
                                });
                            });
                        }
                    } elseif ($filtro['type'] == 'servicios' && isset($filtro['psid'])) {
                        $psid = $filtro['psid'];
                        $fecha_inicio = Carbon::parse($fecha_inicio)->startOfDay();
                        $fecha_fin = Carbon::parse($fecha_fin)->endOfDay();

                        if ($is_interval) {
                            $query->whereHas('citas', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                $query->whereHas('details', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                    $query->whereBetween('start', [$fecha_inicio, $fecha_fin])
                                        ->where('selected_service', $psid);
                                });
                            });
                        } else {
                            $query->whereHas('citas.details', function ($query) use ($psid) {
                                $query->where('selected_service', $psid);
                            })->get();
                        }
                    } elseif ($filtro['type'] == 'categoria-servicios' && isset($filtro['psid'])) {
                        $psid = $filtro['psid'];
                        $fecha_inicio = Carbon::parse($fecha_inicio)->startOfDay();
                        $fecha_fin = Carbon::parse($fecha_fin)->endOfDay();

                        if ($is_interval) {
                            $query->whereHas('citas', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                $query->whereHas('details', function ($query) use ($psid, $fecha_inicio, $fecha_fin) {
                                    $query->whereBetween('start', [$fecha_inicio, $fecha_fin])
                                        ->whereHas('servicio.categorias', function ($query) use ($psid) {
                                            $query->where('categoria_servicio_id', $psid);
                                        });
                                });
                            });
                        } else {
                            $query->whereHas('citas.details.servicio.categorias', function ($query) use ($psid) {
                                $query->where('categoria_servicio_id', $psid);
                            })->get();
                        }
                    }
                }
            }
            if ($wPag) {
                if ($this->by == 'birth_dateC') {
                    $query = cliente::orderByBirthdayProximity('asc')->paginate(12);
                } elseif ($this->by == 'visits') {
                    $visitsFilter = $this->filtros->firstWhere('type', 'visitas');
                    $query = !empty($visitsFilter) ?
                        $query->orderByRaw("(citas_count + compras_count) {$this->sort}")->paginate(12) :
                        cliente::visits()->where('salon_id', Auth::user()->salon_id)->orderByRaw("(citas_count + compras_count) {$this->sort}")->paginate(12);
                } else {
                    $query = $query->orderBy($this->by, $this->sort)->paginate(12);
                }
                $this->records = $query->total();
                $this->resetPage();
            } else {
                $query = $query->get();
            }
            return $query;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 12385Clientes"]);
        }
    }
    public function filtroPSActualizado($uid, $psid)
    {
        try {
            $oldItem  = $this->setOldItem($uid);

            $newItem = $oldItem;
            if (!$oldItem) {
                return; // Manejar el caso en que el ítem no se encuentre en el carrito.
            }
            $newItem['psid'] = $psid;
            switch ($newItem['type']) {
                case 'productos':
                    $ps = producto::select('name')->find($psid);
                    break;
                case 'servicios':
                    $ps = servicio::select('name')->find($psid);
                    break;
                case 'categoria-productos':
                    $ps = categoria_producto::select('name')->find($psid);
                    break;
                case 'categoria-servicios':
                    $ps = categoria_servicio::select('name')->find($psid);
                    break;
                case 'proveedor-productos':
                    $ps = marca::select('name')->find($psid);
                    break;
                case 'proveedor-servicios':
                    $ps = marca::select('name')->find($psid);
                    break;
            }
            $newItem['query'] = $ps->name;

            $this->desvincularElementoAnterior($uid, $newItem);
            $this->clientes = $this->loadCustomers();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1594325Clientes"]);
        }
    }
    private function atendidosPor($id, $query)
    {
        try {
            $clientesAtendidos = [];
            $clientes = $query->get();
            foreach ($clientes as $cliente) {
                $atendidoPor = false;
                $compras = $cliente->compras;
                $citas = $cliente->citas;
                foreach ($compras as $compra) {
                    $details = $compra->details;
                    foreach ($details as $detail) {
                        $empleado_id = $detail->empleado_id;
                        if ($empleado_id == $id) {
                            $atendidoPor = true;
                        }
                    }
                }
                foreach ($citas as $cita) {
                    $details = $cita->details;
                    $details_product = $cita->details_product;
                    foreach ($details as $detail) {
                        $empleado_id = $detail->empleado_id;
                        if ($empleado_id == $id) {
                            $atendidoPor = true;
                        }
                    }
                    foreach ($details_product as $detail_product) {
                        $empleado_id = $detail_product->empleado_id;
                        if ($empleado_id == $id) {
                            $atendidoPor = true;
                        }
                    }
                }
                if ($atendidoPor) {
                    $clientesAtendidos[] = $cliente->id;
                }
            }
            // Filter the original query to include only the attended clients
            $clientesAtendidosQuery = cliente::whereIn('id', $clientesAtendidos);
            return $clientesAtendidosQuery;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 154235Clientes"]);
        }
    }

    public function searching($searchText)
    {
        $this->search = trim($searchText);
    }
    public function eliminar()
    {
        try {
            foreach ($this->selectedItems as $cliente) {
                $cust = cliente::with('datosFacturacion', 'excepciones', 'reviews', 'citas', 'materiales', 'compras', 'calificaciones', 'categorias', 'respuestas')->find($cliente);
                $this->destroy($cust);
            }
            $this->selectedItems = [];

            $this->action = 1;
            $this->emit('refresh');


            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 4235Clientes"]);
        }
    }
    public function joinGroup($categoryId)
    {
        try {
            foreach ($this->selectedItems as $cliente) {
                $cust = cliente::with('categorias')->find($cliente);
                $cust->categorias()->syncWithoutDetaching([$categoryId]);
            }
            if ($this->action != 1) {
                $this->viewCust();
            }
            $this->emit('refresh');
            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 152315Clientes"]);
        }
    }
    public function Add()
    {
        try {
            $this->resetValidation();
            $this->resetExcept('cliente');
            $this->cliente = new cliente();
            $this->action = 2;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 134184Clientes"]);
        }
    }
    public function Edit()
    {
        try {
            if (isset($this->selectedItems[0])) {
                $cliente = cliente::with('categorias')->find($this->selectedItems[0]);
            } else {
                $cliente = $this->customerSelected;
            }
            if (count($cliente->categorias) > 0) {
                $this->listCategories = implode(", ", $cliente->categorias->pluck('name')->toArray());
            }

            $phone = substr($cliente->phone, -10);


            // Obtener el resto de la cadena
            $this->lada = substr($cliente->phone, 0, strlen($cliente->phone) - 10);

            $this->resetValidation();
            $this->cliente = $cliente;
            $this->cliente->phone = $phone;
            $this->activateModalForm();
            $this->editing = true;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 231Clientes"]);
        }
    }
    public function cancelEdit()
    {
        try {
            $this->resetValidation();
            $this->cliente = new cliente();
            $this->action = 1;
            $this->editing = false;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 159185Clientes"]);
        }
    }


    public function Store()
    {
        $this->validate($this->onlyClienteRules());

        try {

            Agenda::setCustomDate(Carbon::parse(session('customDate')));

            if ($this->cliente->phone) {
                $this->cliente->phone = $this->lada . $this->cliente->phone;
            } else {
                $this->cliente->phone = null; // O manejarlo según tus necesidades
            }
            if (!$this->cliente->created_at) {
                $this->cliente->created_at = Carbon::now();
            }

            $this->cliente->salon_id = Auth::user()->salon->id;
            //save
            $this->cliente->save();

            $listCategories = null;
            if ($this->listCategories != null)  $listCategories =  explode(",", $this->listCategories);


            //relacionar categorias         
            if ($listCategories != null) {

                if ($listCategories != null) {

                    $listCategories = array_map(function ($item) {
                        $catName = trim($item);
                        // verificar si el elemento no es numérico
                        if (!is_numeric($catName)) {
                            // buscar el ID de la categoría en la tabla correspondiente
                            $categoria = categoria_cliente::where('name', $catName)->where('salon_id', Auth::user()->salon->id)->first();
                            // reemplazar el elemento con el ID de la categoría si existe
                            if ($categoria) {
                                return $categoria->id;
                            }
                        }

                        // devolver el elemento sin cambios              
                        return $item;
                    }, $listCategories);
                }
                $listCategories !== null ? $this->cliente->categorias()->sync($listCategories) : $this->cliente->categorias()->detach();
            }


            if ($this->action == 3) {
                $this->emit('enviarCliente', $this->cliente->id);
                $this->dispatchBrowserEvent('closeModalCust');
                return;
            }

            $this->dispatchBrowserEvent('closeModalCust');
            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
            if (!$this->editing) {
                $this->loadDefault();
            }

            Agenda::setCustomDate();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 96202Marcas"]);
        }
    }

    public function regresarView()
    {
        $this->infoSelected = 1;
        $this->action = 2;
        $this->emit('refresh');
    }
    public function unsetCat($categoryId)
    {
        $this->customerSelected->categorias()->detach($categoryId);
        $this->emit('refresh');
        $this->viewCust();
        $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
    }
    public function selectedItemToEdit($itemId)
    {
        if ($this->action == 3) {
            $this->emit('enviarCliente', $itemId);
            $this->dispatchBrowserEvent('closeModalCust');
        } else {
            $this->viewCust($itemId);
        }
    }
    public function viewCust($cliente = null)
    {
        try {
            $this->infoSelected = 1;
            if ($cliente == null) {
                $cliente = $this->selectedItems[0];
            }
            $cliente = cliente::with('calificaciones', 'categorias', 'citas.details.servicio', 'citas.details.empleado')->find($cliente);
            if ($cliente->birth_date != null) {
                // Fecha de nacimiento
                $birthDate = Carbon::parse($cliente->birth_date);

                // Fecha actual
                $currentDate = Carbon::now();

                // Calcular la diferencia en años
                $age = $birthDate->diffInYears($currentDate);

                $cliente->edad = $age;
            }
            $this->customerSelected = $cliente;

            if (count($cliente->categorias) > 0) {
                $this->listCategories = $this->loadCategories($cliente);
            }

            $this->promedio = $this->loadPromedio();

            $this->action = 2;
            $this->emit('refresh');
            $this->dispatchBrowserEvent('initRecord',['content' => $this->customerSelected->record]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 15913Clientes"]);
        }
    }

    private function eliminarRelacion($items)
    {
        // eliminar los datosFacturacion asociados al cliente de la tabla datosFacturacion
        foreach ($items as $item) {
            $item->delete();
        }
    }
    private function desvincularRelacion($items, $mov = 0)
    {
        foreach ($items as $item) {
            if ($mov) {
                $item->customer_id = null;
            } else {
                $item->cliente_id = null;
            }
            $item->save();
        }
    }

    private function destroy($cliente)
    {
        try {
            if (isset($cliente->compras)) {
                $this->desvincularRelacion($cliente->compras, 1);
            }
            if (isset($cliente->citas)) {
                $this->desvincularRelacion($cliente->citas, 1);
            }
            if (isset($cliente->reviews)) {
                $this->desvincularRelacion($cliente->reviews);
            }
            if (isset($cliente->materiales)) {
                $this->desvincularRelacion($cliente->materiales);
            }
            if (isset($cliente->datosFacturacion)) {
                $this->desvincularRelacion($cliente->datosFacturacion);
            }

            if (isset($cliente->calificaciones)) {
                $this->eliminarRelacion($cliente->calificaciones);
            }
            if (isset($cliente->categorias)) {
                // Obtener los IDs de las categorias
                $categoriesList = $cliente->categorias->pluck('id');

                // Eliminar las categorias del cliente original
                $cliente->categorias()->detach($categoriesList);
            }

            if (isset($cliente->respuestas)) {
                $this->eliminarRelacion($cliente->respuestas);
            }
            if (isset($cliente->excepciones)) {
                $this->eliminarRelacion($cliente->excepciones);
            }

            if (isset($cliente->tarjetaPuntos)) {
                $cliente->tarjetaPuntos->delete();
            }

            //eliminar cliente
            $cliente->delete();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 231187Clientes"]);
        }
    }

    public function viewTaxData($cust)
    {
        $this->loadDefaultTax();
        $this->customerSelected = cliente::with('datosFacturacion')->find($cust);
        $this->dispatchBrowserEvent('openTaxDataModal');
    }

    private function loadDefaultTax()
    {
        $this->tax_data = new tax_data;
        $this->editingTaxData = false;
    }
    public function saveDelivery()
    {
        $validatedData = $this->validate($this->onlyTaxRules());

        try {
            // extraer el subarray 'tax_data' del array validado
            $deliveryData = $validatedData['tax_data'];

            // crear el modelo tax_data con los datos validados
            $tax_data = tax_data::create($deliveryData);

            $tax_data->cliente_id = $this->customerSelected->id;
            $tax_data->save();

            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD REALIZADA CON ÉXITO']);

            $this->customerSelected->load('datosFacturacion');

            $this->loadDefaultTax();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 268188Clientes"]);
        }
    }

    public function editDelivery(tax_data $tax_data)
    {
        $editDeliveryData = $tax_data->toArray();
        $this->editingTaxData = true;

        $this->tax_data = $editDeliveryData;
    }
    public function removeDelivery(tax_data $tax_data)
    {

        try {
            // Eliminar la tax_data
            $tax_data->delete();

            $this->customerSelected->load('datosFacturacion');

            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);

            $this->loadDefaultTax();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 319189Clientes"]);
        }
    }

    public function updateDelivery()
    {
        $validatedData = $this->validate([
            'tax_data.rfc' => 'required|min:10|max:15',
            'tax_data.company_name' => 'required|min:3|max:100',
            'tax_data.tax_system' => 'required|max:3',
            'tax_data.postcode' => 'required|max:10',
            'tax_data.email' => 'nullable|email|max:55',
            'tax_data.phone' => 'nullable|max:15',
        ]);

        try {
            // xtraer el subarray 'delivery' del array validado
            $deliveryData = $validatedData['tax_data'];

            //buscamos la tax_data y lo actualizamos
            tax_data::find($this->tax_data['id'])->update($deliveryData);

            $this->customerSelected->load('datosFacturacion');

            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);

            $this->editingTaxData = false;

            $this->loadDefaultTax();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 345190Clientes"]);
        }
    }

    public function orderBy($cat, $type)
    {
        switch ($cat) {
            case 'first_name':
                $this->by = 'first_name';
                break;
            case 'last_name':
                $this->by = 'last_name';
                break;
            case 'edad':
                $this->by = 'birth_date';
                $type = !$type;
                break;
            case 'cumpleaños':
                $this->by = 'birth_dateC';
                break;
            case 'añadido':
                $this->by = 'created_at';
                break;
            case 'visitas':
                $this->by = 'visits';
                break;
        }
        if ($type) {
            $this->sort = 'desc';
        } else {
            $this->sort = 'asc';
        }
        $this->clientes = $this->loadCustomers();
    }
    #Filtros
    public function agregarFiltro($type)
    {
        if ($type == 'visitas') $this->by = 'first_name'; // reset order by
        $this->addFilter($type);
        $this->clientes = $this->loadCustomers();
    }

    public function mostrarListadoServicios($uid)
    {
        try {
            $salon_id = Auth::user()->salon->id;

            // Consulta específica para el ID del servicio
            if (isset($this->filtros) && $this->filtros->contains('uid', $uid)) {
                $filtro = $this->filtros->where('uid', $uid)->first();
                $search = $filtro['query'] ?? '';

                $query = servicio::basicQuery();

                $this->servicios[$uid] = Servicios::searchService($query, $search)->orderBy('name', 'asc')->get();
            }
        } catch (\Throwable $th) {
            // Registrar el error para mayor información

            // Disparar el evento de error en el frontend
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097Clientes"]);
        }
    }
    public function mostrarListadoCategoriaServicios($uid)
    {
        try {
            $salon_id = Auth::user()->salon->id;

            // Consulta específica para el ID del servicio
            if (isset($this->filtros) && $this->filtros->contains('uid', $uid)) {
                $filtro = $this->filtros->where('uid', $uid)->first();
                $query = $filtro['query'] ?? '';

                $this->categoriaServicios[$uid] = categoria_servicio::where('salon_id', Auth::user()->salon->id)
                    ->where('name', '!=', 'Categoría eliminada')
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    })
                    ->orderBy('name', 'asc')
                    ->get();
            }
        } catch (\Throwable $th) {
            // Registrar el error para mayor información

            // Disparar el evento de error en el frontend
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097Clientes"]);
        }
    }
    public function mostrarListadoProductos($uid)
    {
        try {
            // Consulta específica para el ID del servicio
            if (isset($this->filtros) && $this->filtros->contains('uid', $uid)) {
                $filtro = $this->filtros->where('uid', $uid)->first();
                $search = $filtro['query'] ?? '';

                $query = producto::basicQuery();
                $this->productos[$uid] = Productos::searchProduct($query, $search)->orderBy('name', 'asc')->get();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097Clientes"]);
        }
    }
    public function mostrarListadoCategoriaProductos($uid)
    {
        try {
            // Consulta específica para el ID del servicio
            if (isset($this->filtros) && $this->filtros->contains('uid', $uid)) {
                $filtro = $this->filtros->where('uid', $uid)->first();
                $query = $filtro['query'] ?? '';

                $this->categoriaProductos[$uid] = categoria_producto::where('salon_id', Auth::user()->salon->id)
                    ->where('name', '!=', 'Categoría eliminada')
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    })
                    ->orderBy('name', 'asc')
                    ->get();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097Clientes"]);
        }
    }
    public function mostrarListadoProveedores($uid)
    {
        try {
            // Consulta específica para el ID del servicio
            if (isset($this->filtros) && $this->filtros->contains('uid', $uid)) {
                $filtro = $this->filtros->where('uid', $uid)->first();
                $query = $filtro['query'] ?? '';

                $this->proveedores[$uid] = marca::where('salon_id', Auth::user()->salon->id)
                    ->where('name', '!=', 'Marca eliminada')
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%")
                            ->orWhere('contact_name', 'like', "%{$query}%")
                            ->orWhere('rfc', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%")
                            ->orWhere('phone_number', 'like', "%{$query}%");
                    })
                    ->orderBy('name', 'asc')
                    ->get();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097Clientes"]);
        }
    }
    private function addFilter($type)
    {
        $uid = uniqid();

        $coll = collect(
            [
                'uid' => $uid,
                'type' => $type,
                'query' => null,
                'is_interval' => false,
                'type_range' => 'Sin fecha'
            ]
        );
        $filtro = Arr::add($coll, null, null);

        $this->filtros->push($filtro);
        $this->save();
    }
    public function updateToday($uid)
    {
        $start = Carbon::now()->toDateString();
        $this->updateRange($uid, [$start, $start]);
    }
    public function updateYesterday($uid)
    {
        $start = Carbon::now()->subDay()->toDateString();
        $this->updateRange($uid, [$start, $start]);
    }
    public function updateWeek($uid)
    {
        $start = Carbon::now()->startOfWeek()->toDateString();
        $end = Carbon::now()->endOfWeek()->addDay();
        $this->updateRange($uid, [$start, $end]);
    }
    public function updateMonth($uid)
    {
        $start = Carbon::now()->startOfMonth()->toDateString();
        $end = Carbon::now()->endOfMonth()->addDay();
        $this->updateRange($uid, [$start, $end]);
    }
    public function updateYear($uid)
    {
        $start = Carbon::now()->startOfYear()->toDateString();
        $end = Carbon::now()->endOfYear()->addDay();
        $this->updateRange($uid, [$start, $end]);
    }
    public function updateRange($uid, $selectedDates)
    {
        if (count($selectedDates) >= 2) {
            // Actualizar las fechas según la lógica que necesites
            $start = Carbon::parse($selectedDates[0])->toDateString();
            $end = Carbon::parse($selectedDates[1])->toDateString();


            $oldItem  = $this->setOldItem($uid);
            $newItem = $oldItem;
            if (!$oldItem) {
                return; // Manejar el caso en que el ítem no se encuentre en el carrito.
            }
            $newItem['fecha_inicio'] = $start;
            $newItem['fecha_fin'] = $end;
            $newItem['is_interval'] = true;
            $newItem['type_range'] = $start . '-' . $end;

            $this->desvincularElementoAnterior($uid, $newItem);
            $this->clientes = $this->loadCustomers();
        }
    }
    public function deleteRange($uid)
    {
        $oldItem  = $this->setOldItem($uid);

        $newItem = $oldItem;
        if (!$oldItem) {
            return; // Manejar el caso en que el ítem no se encuentre en el carrito.
        }
        $newItem['is_interval'] = false;
        $newItem['type_range'] = 'Sin fecha';

        $this->desvincularElementoAnterior($uid, $newItem);
        $this->clientes = $this->loadCustomers();
    }
    public function updateType($uid, $type)
    {
        $oldItem  = $this->setOldItem($uid);

        $newItem = $oldItem;
        if (!$oldItem) {
            return; // Manejar el caso en que el ítem no se encuentre en el carrito.
        }
        $newItem['type'] = $type;

        $this->desvincularElementoAnterior($uid, $newItem);
        if ($type == 'birth_date') {
            $this->emit('cargarFlat');
        }
        $this->clientes = $this->loadCustomers();
    }
    public function updateQueryMoney($uid, $min = null, $max = null)
    {
        if ($min) {
            $min = $this->eliminarCaracteres($min);
        }
        if ($max) {
            $max = $this->eliminarCaracteres($max);
        }
        $this->updateQuery($uid, null, 1, $min, $max);
    }
    private function eliminarCaracteres($data)
    {
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
    }
    public function updateQuery($uid, $query, $is_range, $min = null, $max = null)
    {
        $this->hay_query = true;
        $oldItem  = $this->setOldItem($uid);

        $newItem = $oldItem;
        if (!$oldItem) {
            return; // Manejar el caso en que el ítem no se encuentre en el carrito.
        }
        if (!$is_range) {
            $newItem['query'] = $query;
        } else {
            if ($min !== null) {
                $newItem['min'] = $min;
            }
            if ($max !== null) {
                $newItem['max'] = $max;
            }
        }

        $this->desvincularElementoAnterior($uid, $newItem);
        $this->save();
        $this->clientes = $this->loadCustomers();
    }

    private function save()
    {
        session()->put('filtros', $this->filtros);
        session()->save();
    }
    public function cleanFilters()
    {
        foreach ($this->filtros as $filtro) {
            $this->deleteFilter($filtro['uid']);
        }
    }
    public function deleteFilter($uid)
    {
        $this->desvincularElementoAnterior($uid);
        $this->dispatchBrowserEvent('openFilter', ['uid' => $uid]);
        if (count($this->filtros) > 0) {
            $this->hay_query = false;
        }
        $this->clientes = $this->loadCustomers();
    }

    private function desvincularElementoAnterior($uid, $newItem = null)
    {
        try {
            if (!$newItem) {
                $this->filtros  = $this->filtros->reject(function ($filtro) use ($uid) {
                    return  $filtro['uid'] === $uid;
                });
                $this->save();
                return;
            }

            // Encuentra el índice o clave del elemento a reemplazar
            $key = $this->filtros->search(function ($filtro) use ($uid) {
                return $filtro['uid'] === $uid;
            });

            // Reemplaza el método directamente por la clave encontrada
            if ($key !== false) {
                $this->filtros[$key] = $newItem;
            }
            $this->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 811369Cliente"]);
        }
    }
    private function setOldItem($uid)
    {
        try {
            $mycart = $this->filtros;
            $oldItem = $mycart->where('uid', $uid)->first();
            return $oldItem;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 787369Cliente"]);
        }
    }

    public function aplicarFiltros()
    {
        $this->clientes = $this->loadCustomers();
    }
    public function showInfo()
    {
        try {
            $this->emit('refresh');
            $this->infoSelected = 2;
            $this->cliente_id = $this->customerSelected->id;
            $this->setAllTimes();
            $this->dispatchBrowserEvent('load-flat');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2431Cliente"]);
        }
    }
    public function showHistory()
    {
        try {
            $this->emit('refresh');
            $this->infoSelected = 3;
            $this->setAllTimes();
            $this->dispatchBrowserEvent('load-flat');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2342431Cliente"]);
        }
    }
    private function useDate()
    {
        $start = $this->start;
        $end = $this->end;
        $this->recalculate($start, $end);
    }
    public function setAllTimes()
    {
        try {
            $this->is_interval = true;
            $this->start = null;
            $this->end = null;
            $this->useDate();
            $this->loadDatesWithNewPeriod();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 204361Cliente"]);
        }
    }
    private function loadDatesWithNewPeriod()
    {
        try {
            $this->emit('dateUpdated', $this->currentDate, $this->currentDateEnd);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 343365Cliente"]);
        }
    }

    private function loadFecha()
    {
        try {
            $this->setDatesFromPeriod([Carbon::now()]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 365140Informe"]);
        }
    }

    public function returnToday()
    {
        $this->loadFecha();
    }
    public function returnYesterday()
    {
        $this->prevDay();
    }

    #Función que establece un día anterior 
    public function prevDay()
    {
        try {
            $this->setDatesFromPeriod([Carbon::now()->subDay()]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 84130Informe"]);
        }
    }
    public function setWeek()
    {
        try {
            $this->setDatesFromPeriod([Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 379141Informe"]);
        }
    }
    public function setMonth()
    {
        try {
            $this->setDatesFromPeriod([Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 395142Informe"]);
        }
    }
    public function setYear()
    {
        try {
            $this->setDatesFromPeriod([Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 411143Informe"]);
        }
    }
    public function setDate($selectedDate)
    {
        try {
            $this->setDatesFromPeriod([Carbon::parse($selectedDate[0])]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 53128Informe"]);
        }
    }


    public function setDatesFromPeriod($selectedDates)
    {
        try {

            session()->put('selectedDates', $selectedDates);
            session()->save();
            $this->is_interval = true;
            if (count($selectedDates) >= 2) {
                $currentDateC = Carbon::parse($selectedDates[0]);
                $currentDateCEnd = Carbon::parse($selectedDates[1]);
            } elseif (count($selectedDates) == 1) {
                $currentDateC = Carbon::parse($selectedDates[0]);
                $currentDateCEnd = $currentDateC->endOfDay();
            } else {
                $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Intente de nuevo"]);
            }
            $this->currentDateC = Carbon::parse($currentDateC);
            $this->currentDateCEnd = Carbon::parse($currentDateCEnd);
            $this->currentDate = $this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start = $this->currentDateC->toDateString();
            $this->currentDateEnd = $this->currentDateCEnd->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->end = $this->currentDateCEnd->toDateString();
            $this->useDate();
            $this->loadChartsWithNewPeriod();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 30127Informe"]);
        }
    }
    public function exportarFormulario()
    {
        try {
            $encuesta = encuesta::with('preguntas.respuestas.cliente.respuestas', 'preguntas.respuestas.cliente.reviews')->where('salon_id', 5)->first();
            $date = Carbon::now()->format('Y_m_d_H_i_s');
            $fileName = 'formulario_' . $date . '.xlsx';
            return Excel::download(new Formulario($encuesta), $fileName);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 497369Cliente"]);
        }
    }
    public function exportarClientes()
    {
        try {
            $clientes = $this->loadCustomers(0);
            $date = Carbon::now()->format('Y_m_d_H_i_s');
            $fileName = 'clientes_' . $date . '.xlsx';
            return Excel::download(new reporteClientes($clientes, $this->filtros), $fileName);
        } catch (\Throwable $th) {

            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 497363Cliente"]);
        }
    }
    public function exportarClientesCompleto()
    {
        try {
            set_time_limit(0);
            $clientes = $this->loadCustomers(0);
            $date = Carbon::now()->format('Y_m_d_H_i_s');
            $fileName = 'clientes_' . $date . '.xlsx';
            return Excel::download(new reporteClientesExtended($clientes, $this->filtros), $fileName);
        } catch (\Throwable $th) {
            dd($th);
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 4973323Cliente"]);
        }
    }

    public function generateExcel()
    {
        // try{
        //     $this->aplicarFiltros();
        // }catch(\Throwable $th){
        //     $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 497369Cliente"] );
        // }
    }


    public function regresarListado()
    {
        $this->selectedItems = [];
        $this->filtros = new Collection;

        $this->loadDefault();
        $this->clientes = $this->loadCustomers();

        $this->empleados = $this->loadEmpleados();
        $this->emit('refresh');
        $this->action = 1;
        $this->infoSelected = 1;
    }
    private function loadAverage($total, $mov_qty)
    {
        $promedio = 0;
        if ($total > 0) {
            $promedio = ($mov_qty / $total) * 100;
            $promedio = number_format($promedio, 2);
        }
        return $promedio;
    }
    private function recalculate($start, $end)
    {
        try {
            if ($this->infoSelected == 2) {
                $this->topProducts = $this->loadProductsData($start, $end);
                $this->topServices = $this->loadServicesData($start, $end);
                $totalCitas = $start && $end ? count($this->customerSelected->citas->whereBetween('start', [$start, $end])) : count($this->customerSelected->citas);
                $totalCompras = $start && $end ? count($this->customerSelected->compras->whereBetween('created_at', [$start, $end])) : count($this->customerSelected->compras);
                $total = $totalCitas + $totalCompras;
                $this->productsAverage = $this->loadAverage($total, $totalCompras);
                $this->servicesAverage = $this->loadAverage($total, $totalCitas);
                $this->emit('iniciarChartist', [$this->productsAverage, $this->servicesAverage]);
                $this->emit('refrescarChartist', [$this->productsAverage, $this->servicesAverage]);
            } elseif ($this->infoSelected == 3) {
                $this->changeWindow($this->pestaña);
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 235363Cliente"]);
        }
    }
    private function loadCategories($cliente = null)
    {
        if ($cliente != null) {
            $categoriesList = $cliente->categorias;
        } else {
            $categoriesList = $this->customerSelected->categorias;
        }
        return $categoriesList;
    }
    private function loadPromedio()
    {
        $total = 0;
        $qty = 0;
        foreach ($this->customerSelected->calificaciones as $calificacion) {
            if ($calificacion->puntaje != null) {
                $total += $calificacion->puntaje;
                $qty += 1;
            }
        }

        if ($qty > 0) {
            $promedio = $total / $qty;
        } else {
            $promedio = 0;
        }
        return $promedio;
    }
    private function loadProductsData($start, $end)
    {
        $topProducts = DB::table('asignacion_ventas')
            ->select(
                'productos.id',
                'productos.name',
                DB::raw('SUM(asignacion_ventas.quantity) as total_qty'),
                DB::raw('AVG(asignacion_ventas.current_price) as avg_price'),
                DB::raw('SUM(asignacion_ventas.quantity * asignacion_ventas.current_price) as total_price')
            )
            ->join('productos', 'asignacion_ventas.selected_item', '=', 'productos.id')
            ->join('ventas', 'asignacion_ventas.venta_id', '=', 'ventas.id')
            ->where('ventas.customer_id', $this->cliente_id);
        if ($start && $end) {
            $topProducts = $topProducts->when($this->is_interval, function ($query) use ($start, $end) {
                return $query->whereBetween('asignacion_ventas.created_at', [$start, $end]);
            }, function ($query) use ($start) {
                return $query->whereDate('asignacion_ventas.created_at', $start);
            });
        }
        $topProducts = $topProducts->groupBy('productos.id', 'productos.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();
        return $topProducts;
    }
    private function loadServicesData($start, $end)
    {
        $topServices = DB::table('asignacion_servicios')
            ->select(
                'servicios.id',
                'servicios.name',
                DB::raw('COUNT(asignacion_servicios.id) as total_assignments'), // Contar las asignaciones de servicio
                DB::raw('AVG(asignacion_servicios.current_price) as avg_price'),
                DB::raw('SUM(asignacion_servicios.current_price) as total_price')
            )
            ->join('servicios', 'asignacion_servicios.selected_service', '=', 'servicios.id')
            ->join('citas', 'asignacion_servicios.cita_id', '=', 'citas.id')
            ->where('citas.customer_id', $this->cliente_id);
        if ($start && $end) {
            $topServices = $topServices->when($this->is_interval, function ($query) use ($start, $end) {
                return $query->whereBetween('asignacion_servicios.start', [$start, $end]);
            }, function ($query) use ($start) {
                return $query->whereDate('asignacion_servicios.start', $start);
            });
        }
        $topServices = $topServices->groupBy('servicios.id', 'servicios.name')
            ->orderByDesc('total_assignments')
            ->limit(10)
            ->get();

        return $topServices;
    }
}
