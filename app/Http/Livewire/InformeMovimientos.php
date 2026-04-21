<?php

namespace App\Http\Livewire;

use App\Exports\ReporteMovimientos;
use App\Models\Asignacion_servicio;
use App\Models\Asignacion_venta;
use App\Models\caja_apertura;
use App\Models\caja_corte;
use App\Models\cita;
use App\Models\Empleado;
use App\Models\Entrada;
use App\Models\excepcion_cat_producto;
use App\Models\excepcion_cat_servicio;
use App\Models\excepcion_producto;
use App\Models\excepcion_servicio;
use App\Models\File;
use App\Models\Material;
use App\Models\metodo_pago_corte;
use App\Models\metodo_pago_servicio;
use App\Models\metodo_pago_venta;
use App\Models\metodo_propina_corte;
use App\Models\producto;
use App\Models\Propina;
use App\Models\servicio;
use App\Models\venta;
use App\Models\walog;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class InformeMovimientos extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $currentDate, $currentDateEnd, $is_interval = false;
    public $start, $currentDateC, $end, $currentDateCEnd;
    protected $paginationTheme = 'bootstrap';
    private $dataMovimientos;
    private $salon_id;

    public $ventasFilter, $citasFilter, $aperturasFilter, $cortesFilter, $usosFilter, $entradasFilter, $giftcardsFilter;

    public $max, $min;

    public $balance = 0, $balanceDisccounts = 0;
    public $type, $itemSelected = null, $terminales = [], $terminales_propina = [], $total_real = 0, $total_propinas_real = 0, $total_propinas = 0;
    public $empleados;

    public Collection $cartP, $cartM, $cartS, $methods, $propinas, $terminales_real, $terminales_propina_real;
    public $disccount = 0;

    public $totalCart = 0, $itemsCart = 0, $impuestos = 0, $subtotalCart = 0, $totalPoints = 0, $totalMethods = 0, $global_disccount = 0, $restante = 0, $recibido = 0, $propinasRecibidas = 0;

    public $search, $items, $itemType;

    public $description = '', $total_cash_real = 0, $total_NF_real = 0, $total_card_real = 0, $propinas_efectivo_real = 0, $propinas_banorte_real = 0, $propinas_tarjeta_real = 0, $total_fp = 0, $total_p = 0;
    public $mensajesRespaldados = [];
    public $metodosSalon = [], $pestaña = 1;
    public $informe = 1;
    public $pictures = [], $gallery = [], $uploadFiles = 0, $respaldoFiles;
    public $lookingServices;
    public $caja_chica, $caja_chica_real, $itemsQty;
    private function getMetodosSalon()
    {
        foreach (Auth::user()->salon->metodosPago as $metodosSalon) {
            $this->metodosSalon[] = $metodosSalon;
        }
    }

    public function mount()
    {
        $this->activateCheckers(null);

        if (session()->has('selectedDates')) {
            $this->setDatesFromPeriod(session('selectedDates'));
        } else {
            $this->loadFecha();
        }
    }

    protected $listeners = [
        'refresh' => '$refresh',
        'datesSelected' => 'setDatesFromPeriod',
        'prevDay',
        'dateSelected' => 'setDate',
        'viewDetails',
        'filesDroped',
        'recuperarInfo',
        'editar',
        'updateEmpleado',
        'updateQty',
        'deleteUso',
        'updateIva',
        'removeItem',
        'updatePercentage',
        'loadItems',
        'addNewProduct',
        'cambioDataMethods',
        'newPropina',
        'newMethod',
        'changeTerminalQty',
        'changeQty',
        'StoreCorte',
        'selectAssigment',
        'changeWindow',
        'storeDate' => 'Store',
        'deactivateCheckers'
    ];

    public function removeImage($index)
    {
        array_splice($this->gallery, $index, 1);
    }
    public function removeFile($filename, $fromGallery)
    {
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
    }
    public function filesDroped($files)
    {
        $this->gallery[] = $files;
    }
    public function changeWindow($tipo)
    {
        $this->pestaña = $tipo;
    }
    public function deactivateCheckers()
    {
        $this->ventasFilter = false;
        $this->citasFilter = false;
        $this->aperturasFilter = false;
        $this->cortesFilter = false;
        $this->usosFilter = false;
        $this->entradasFilter = false;
        $this->giftcardsFilter = false;
    }
    private function activateCheckers($filter)
    {
        try {
            if ($filter == null) {
                $this->ventasFilter = true;
                $this->citasFilter = true;
                $this->aperturasFilter = true;
                $this->cortesFilter = true;
                $this->usosFilter = true;
                $this->entradasFilter = true;
                $this->giftcardsFilter = true;
            } else {
                switch ($filter) {
                    case 0:
                        $this->ventasFilter = true;
                        break;
                    case 1:
                        $this->citasFilter = true;
                        break;
                    case 2:
                        $this->usosFilter = true;
                        break;
                    case 3:
                        $this->entradasFilter = true;
                        break;
                    case 4:
                        $this->giftcardsFilter = true;
                        break;
                }
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 76353InformeMovimientos"]);
        }
    }
    public function render()
    {
        try {
            return view('livewire.informe-movimientos', ['dataMovimientos' => $this->dataMovimientos, 'type' => $this->type, 'item' => $this->itemSelected, 'totalCart' => $this->totalCart, 'impuestos' => $this->impuestos, 'subtotalCart' => $this->subtotalCart, 'totalPoints' => $this->totalPoints, 'items' => $this->items]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 88354InformeMovimientos"]);
        }
    }
    private function loadEmpleados()
    {
        try {
            $this->empleados = Empleado::where('salon_id', Auth::user()->salon_id)->get();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 95355InformeMovimientos"]);
        }
    }
    public function setDatesFromPeriod($selectedDates)
    {
        try {
            session()->put('selectedDates', $selectedDates);
            session()->save();
            $this->is_interval = true;
            if (count($selectedDates) >= 2) {
                // Actualizar las fechas según la lógica que necesites
                $currentDateC = Carbon::parse($selectedDates[0]);
                $currentDateCEnd = Carbon::parse($selectedDates[1])->endOfDay();
            } elseif (count($selectedDates) == 1) {
                // Actualizar las fechas según la lógica que necesites
                $currentDateC = Carbon::parse($selectedDates[0])->startOfDay();
                $currentDateCEnd = $currentDateC->copy()->endOfDay();
            }
            $this->currentDateC = Carbon::parse($currentDateC);
            $this->currentDateCEnd = Carbon::parse($currentDateCEnd);
            $this->currentDate = $this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start = $this->currentDateC->toDateString();
            $this->currentDateEnd = $this->currentDateCEnd->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->end = $this->currentDateCEnd->toDateString();
            $this->aplicarFiltros();
            $this->loadDatesWithNewPeriod();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 30127Informe"]);
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
    private function useDate()
    {
        try {
            $citas = [];
            $ventas = [];
            $aperturas = [];
            $cortes = [];
            $usos = [];
            $entradas = [];
            $this->salon_id = Auth::user()->salon_id;
            if ($this->ventasFilter) {
                $ventas = venta::transactionsBetweenDates($this->salon_id, $this->currentDateC, $this->currentDateCEnd)->get();
            }
            if ($this->citasFilter) {
                $citas = cita::transactionsBetweenDates($this->salon_id, $this->currentDateC, $this->currentDateCEnd)->get();
            }
            if ($this->aperturasFilter) {
                $aperturas = caja_apertura::transactionsBetweenDates($this->salon_id, $this->currentDateC, $this->currentDateCEnd)->get();
            }
            if ($this->cortesFilter) {
                $cortes = caja_corte::transactionsBetweenDates($this->salon_id, $this->currentDateC, $this->currentDateCEnd)->get();
            }
            if ($this->usosFilter) {
                $usos = Material::transactionsBetweenDates($this->salon_id, $this->currentDateC, $this->currentDateCEnd);
            }
            if ($this->entradasFilter) {
                $entradas = Entrada::transactionsBetweenDates($this->salon_id, $this->currentDateC, $this->currentDateCEnd);
            }

            $info = [
                'aperturas' => $aperturas,
                'citas' => $citas,
                'cortes' => $cortes,
                'ventas' => $ventas,
                'entradas' => $entradas,
                'usos' => $usos,
            ];
            $this->dataMovimientos = $info;
            $this->recalculate($info);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 235363InformeMovimientos"]);
        }
    }
    private function recalculate($info)
    {
        try {
            $this->setBalance($info);
            if (count($info['citas']) > 0) {
                $this->acumularDescuentos($info['citas']);
                $this->acumularDescuentos($info['citas'], 'details_product');
                $this->setBalanceDisccounts($info['citas']);
            }
            if (count($info['ventas']) > 0) {
                $this->acumularDescuentos($info['ventas']);
                $this->setBalanceDisccounts($info['ventas']);
            }
            $this->itemsQty = $this->itemsQty($info);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 327364InformeMovimientos"]);
        }
    }
    private function itemsQty($items)
    {
        $qty = 0;
        foreach ($items as $item) {
            $qty += count($item);
        }
        return $qty;
    }
    private function acumularDescuentos($transacciones, $relation = 'details')
    {
        foreach ($transacciones as $transaccion) {
            foreach ($transaccion->$relation as $detail) {
                // Obtener cantidad de ítems
                $items_qty = $detail->quantity ?? 1;
                // Calcular descuento total
                $total_discount = $detail->disccount_price > 0 ? ($detail->current_price - $detail->disccount_price) * $items_qty : 0;
                // Acumular el descuento en la transacción
                $transaccion->disccount += $total_discount;
                // Determinar total del detalle para cálculo de descuento por cantidad
                $total = $total_discount > 0 ? $detail->disccount_price : $detail->current_price;
                $discount_qty = $detail->discount_qty;
                if ($discount_qty > 0) {
                    $qty = $detail->discount_type == 'Porcentaje' ? $total * $items_qty * $discount_qty / 100 : $discount_qty;
                    $transaccion->disccount += $qty;
                }
            }
        }
    }
    private function setBalance($info)
    {
        $this->balance = 0;
        foreach ($info as $item => $value) {
            // Solo acumular para ventas y citas, los demás no afectan el balance de ingresos
            if ($item !== 'ventas'  && $item !== 'citas') {
                continue;
            }
            // Acumular el balance de cada transacción
            foreach ($value as $movimiento) {
                foreach ($movimiento->metodosPago as $method) {
                    if ($method->payment_method_id === 4) {
                        continue;
                    }
                    $this->balance += $method->amount - $method->change;
                }
            }
        }
    }
    private function setBalanceDisccounts($transacciones)
    {
        $this->balanceDisccounts = 0;
        foreach ($transacciones as $transaccion) {
            $this->balanceDisccounts += $transaccion->disccount;
        }
    }

    #función que actualiza las gráficas con la nueva fecha
    private function loadDatesWithNewPeriod()
    {
        try {
            $this->emit('dateUpdated-movimientos', $this->currentDate, $this->currentDateEnd);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 343365InformeMovimientos"]);
        }
    }

    public function aplicarFiltros()
    {
        if (isset($this->max)) {
            $this->consultaRangos();
        } else {
            $this->useDate();
        }
    }
    private function consultaRangos()
    {
        try {
            $citas = [];
            $ventas = [];
            $cortes = [];
            $this->salon_id = Auth::user()->salon_id;
            if ($this->ventasFilter) {
                $ventas = venta::transactionsBetweenDatesBetweenTotal($this->salon_id, $this->currentDateC, $this->currentDateCEnd, $this->min, $this->max)->get();
            }
            if ($this->citasFilter) {
                $citas = cita::transactionsBetweenDatesBetweenTotal($this->salon_id, $this->currentDateC, $this->currentDateCEnd, $this->min, $this->max)->get();
            }
            if ($this->cortesFilter) {
                $cortes = caja_corte::transactionsBetweenDatesBetweenTotal($this->salon_id, $this->currentDateC, $this->currentDateCEnd, $this->min, $this->max)->get();
            }

            $info = [
                'citas' => $citas,
                'cortes' => $cortes,
                'ventas' => $ventas,
            ];
            $this->dataMovimientos = $info;
            $this->recalculate($info);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 360366InformeMovimientos"]);
        }
    }
    public function viewDetails($item_id, $key)
    {
        try {
            if ($key == 'aperturas') {
                $this->type = 'apertura';
                $this->itemSelected = caja_apertura::with('user')->find($item_id);
            } elseif ($key == 'cortes') {
                $this->type = 'corte';
                $this->itemSelected = caja_corte::with('user', 'metodosZettle', 'metodosZettlePropina')->find($item_id);
                $terminales = $this->itemSelected->metodosZettle;
                $terminales_propina = $this->itemSelected->metodosZettlePropina;
                $this->terminales_propina = $this->obtenerCantidadesReales($terminales_propina, true);
                $this->terminales = $this->obtenerCantidadesReales($terminales, false);
            } elseif ($key == 'ventas') {
                $this->type = 'venta';
                $this->itemSelected = venta::with('details.product', 'details.empleado', 'propinas', 'metodosPago.metodoPago', 'customer.tarjetaPuntos', 'details.giftCard')->find($item_id);
                $this->acumularDescuentos([$this->itemSelected]);
            } elseif ($key == 'citas') {
                $this->type = 'cita';
                $this->itemSelected = cita::with('etiquetas', 'details_product.product', 'details_product.empleado', 'details.servicio', 'details.empleado', 'propinas', 'metodosPago.metodoPago', 'customer.tarjetaPuntos')->find($item_id);
                $this->pictures = $this->itemSelected->photos;
                $this->acumularDescuentos([$this->itemSelected]);
                $this->acumularDescuentos([$this->itemSelected], 'details_product');
            } elseif ($key == 'usos' || $key == 'entradas') {
                $key == 'usos' ? $this->type = 'uso' : $this->type = 'entrada';
                // Limpiar caracteres de control (si es necesario)
                $json = preg_replace('/[\x00-\x1F\x80-\xFF]/u', '', $item_id);

                // Asegurar que la cadena JSON esté en la codificación UTF-8
                $json = mb_convert_encoding($json, 'UTF-8', 'UTF-8');
                // Decodificar el JSON
                $data = json_decode($json, true);
                // Verificar que se haya decodificado correctamente
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Error al decodificar JSON: ' . json_last_error_msg());
                }

                $this->itemSelected = $data;
            }
            $this->dispatchBrowserEvent('viewDetailTransaccion', ['itemSelected' => $this->itemSelected, 'type' => $this->type, 'terminales', $this->terminales]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 435367InformeMovimientos"]);
        }
    }
    private function obtenerCantidadesReales($terminales, $es_propina)
    {
        try {
            $cantidades_por_referencia = [];
            foreach ($terminales as $terminal) {
                $referencia = $terminal->payment_method;
                $cantidad = $terminal->qty;

                // Verifica si la referencia ya está en el arreglo
                if (array_key_exists($referencia, $cantidades_por_referencia)) {
                    $cantidades_por_referencia[$referencia]['real'] = $cantidad;
                    if ($es_propina) {
                        $this->total_propinas_real += $cantidad;
                    } else {
                        $this->total_real += $cantidad;
                    }
                } else {
                    // Si la referencia no existe, crea una nueva entrada en el arreglo
                    $cantidades_por_referencia[$referencia]['resumen'] = $cantidad;
                    if ($es_propina) {
                        $this->total_propinas += $cantidad;
                    }
                }
            }
            return $cantidades_por_referencia;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 460368InformeMovimientos"]);
        }
    }
    public function recuperarInfo()
    {
        $this->itemSelected = null;
        $this->aplicarFiltros();
    }
    public function generatePdf()
    {
        $this->aplicarFiltros();
        $this->emit('print');
    }
    public function generateExcel()
    {
        try {
            $date = $this->currentDateC;
            $date = $date->format('Y_m_d_H_i_s');
            $fileName = 'movimientos_' . $date . '.xlsx';
            $this->aplicarFiltros();
            return Excel::download(new ReporteMovimientos($this->dataMovimientos, $this->type, $this->itemSelected), $fileName);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 497369InformeMovimientos"]);
        }
    }
    public function editar()
    {
        if ($this->type == 'cita') {
            return redirect()->to(route('citas', ['cita_id' => $this->itemSelected->id, 'pestaña' => 1, 'action' => 2]));
        } elseif ($this->type == 'venta') {
            return redirect()->to(route('ventas', ['venta_id' => $this->itemSelected->id]));
        } else {
            $this->cleanFormasPago();
            $this->loadDataCorte();
        }
    }
    private function loadCartProducts()
    {
        try {
            $details_product = [];
            $details_service = [];
            $formas_pago = $this->itemSelected->metodosPago;
            $propinas = $this->itemSelected->propinas;
            if ($this->type == 'venta') {
                $details_product = $this->itemSelected->details;
            } elseif ($this->type == 'cita') {
                $details_product = $this->itemSelected->details_product;
                $details_service = $this->itemSelected->details;
            }
            //Iterar la lista de productos en una colección para guardar en el carrito (Si es que hay)
            foreach ($details_product as $detail) {
                $this->AddItem('producto', $detail, $detail->product, $detail->quantity, $detail->discount_qty, $detail->iva, $detail->empleado_id, $detail->quantity, null, $detail->discount_type);
            }
            foreach ($details_service as $detail) {
                $uid = $this->AddItem('servicio', $detail, $detail->servicio, $detail->quantity, $detail->discount_qty, $detail->iva, $detail->empleado_id, 0, null, $detail->discount_type);

                $materiales = $detail->materiales;
                //Iterar la lista de productos en una colección para guardar en el carrito (Si es que hay)
                foreach ($materiales as $material) {
                    $this->AddItem('material', $material, $material->producto, $material->qty, 0, 0.16, $material->empleado_id, $material->qty, $uid);
                }
            }
            foreach ($formas_pago as $forma_pago) {
                $this->addMethod($forma_pago->amount, $forma_pago->reference, $forma_pago->payment_method_id, $this->methods, null, $forma_pago->tipo);
            }
            foreach ($propinas as $propina) {
                $this->addMethod($propina->amount, $propina->reference, $propina->payment_method_id, $this->propinas, $propina->empleado_id);
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 522369InformeMovimientos"]);
        }
    }
    public function newMethod()
    {
        $this->addMethod(0, '', 1, $this->methods);
        $this->emit('refresh');
    }
    public function newPropina()
    {
        $this->addMethod(0, '', 1, $this->propinas);
        $this->emit('refresh');
    }
    private function applyDisccount($global_disccount)
    {
        try {
            // Establecer un valor predeterminado si el descuento está vacío
            $global_disccount = is_numeric($global_disccount) ? $global_disccount : 0;

            $global_disccount = min($global_disccount, 100); // Asegurar que el descuento no sea mayor al 100%

            $disccount = $this->restante - ($this->restante * ($global_disccount / 100));

            $this->restante = $disccount;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 165210Agenda"]);
        }
    }
    private function addMethod($qty, $reference, $paymentMethod, $array, $empleado = null, $type = null)
    {
        try {
            // validar si ya existe entre los métodos
            if ($paymentMethod == '4') {
                $globalD = 0;
                if ($type == 'Porcentaje') {
                    $globalD = $qty;
                } elseif ($type == 'Cantidad') {
                    $globalD = $qty / $this->restante;
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
                ]
            );
            $method = Arr::add($coll, null, null);
            $array->push($method);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1031InformeMovimientos"]);
        }
    }

    private function AddItem($type, $asignacion, $item, $qty = 1, $disccount_percent = 0, $ind_iva = 0.16, $empleado = NULL, $qty_inicial = 0, $uid_s = null, $discount_type = 'Porcentaje')
    {
        try {
            // validar si ya existe en el carrito
            if ($this->inCart($item->id, $type)) {
                if ($type == 'producto') {
                    $this->updateQty($type, null, $qty, $item->id);
                    return; // => con esta línea se agrupan los items por nombre dentro del carrito
                }
            }

            $uid = uniqid() . $item->id;

            if ($type == 'producto' || $type == 'servicio') {
                // iva 
                $iva = $ind_iva;
                // determinar precio venta con iva
                $salePrice = ($item->disccount_price > 0 && $item->disccount_price < $item->gross_price ?  $item->disccount_price : $item->gross_price);
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
            }

            if ($type == 'producto') {
                $coll = collect(
                    [
                        'id' => $uid,
                        'pid' => $item->id,
                        'name' => $item->name,
                        'sku' => $item->sku,
                        'reward_points' => $asignacion ? floatval($asignacion->generated_points) : 0,
                        'intern_sku' => $item->intern_sku,
                        'gross_price' => $asignacion ? floatval($asignacion->current_price) : floatval($item->gross_price),
                        'disccount_price' => $asignacion ? floatval($asignacion->disccount_price) : floatval($item->disccount_price),
                        'disccount_percent' =>  floatval($disccount_percent),
                        'discount_type' => $discount_type,
                        'sale_price' => $asignacion ? floatval($asignacion->current_price) : floatval($salePrice),
                        'qty' => intval($qty),
                        'ind_iva' => floatval($ind_iva),
                        'tax' => floatval($tax),
                        'total' => floatval($total),
                        'stock' => $item->stock_qty,
                        'type' => $item->type_product,
                        'vendedor' => $empleado,
                        'qty_inicial' => $qty_inicial,
                    ]
                );
            } elseif ($type == 'servicio') {

                $coll = collect(
                    [
                        'selected' => $asignacion ? $asignacion->selected : true,
                        'id' => $uid,
                        'sid' => $item->id,
                        'name' => $item->name,
                        'reward_points' => $asignacion ? floatval($asignacion->generated_points) : 0,
                        'gross_price' => $asignacion ? floatval($asignacion->current_price) : floatval($item->gross_price),
                        'disccount_price' => $asignacion ? floatval($asignacion->disccount_price) : floatval($item->disccount_price),
                        'disccount_percent' => floatval($disccount_percent),
                        'discount_type' => $discount_type,
                        'sale_price' => $asignacion ? floatval($asignacion->current_price) : floatval($salePrice),
                        'ind_iva' => floatval($ind_iva),
                        'tax' => floatval($tax),
                        'total' => floatval($total),
                        'vendedor' => $empleado,
                        'duration' => $item->duration,
                        'start' => $asignacion ? $asignacion->start : '2024-04-30 09:15:00',
                        'qty' => $qty,
                        'color' => $asignacion->color,
                    ]
                );
            } elseif ($type == 'material') {
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
            }
            $itemCart = Arr::add($coll, null, null);
            if ($type == 'producto' || $type == 'material') {
                if ($type == 'material') {
                    $this->cartM->push($itemCart);
                    session()->put('cartMaterials', $this->cartM);
                    session()->save();
                } elseif ($type == 'producto') {
                    $this->cartP->push($itemCart);
                    session()->put('cartP', $this->cartP);
                    session()->save();
                }
            } elseif ($type == 'servicio') {
                $this->cartS->push($itemCart);
                session()->put('cartS', $this->cartS);
                session()->save();
            }
            $this->loadCartTotales();
            $this->initializeQuery();
            if ($type == 'servicio') {
                return $uid;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 578369InformeMovimientos"]);
        }
    }
    private function inCart($item_id, $type)
    {
        try {
            $cont = 0;
            if ($type == 'producto') {
                $mycart = $this->cartP;
                $cont = $mycart->where('pid', $item_id)->count();
            } elseif ($type == 'servicio') {
                $mycart = $this->cartS;
                $cont = $mycart->where('sid', $item_id)->count();
            }
            return  $cont > 0 ? true : false;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 667369InformeMovimientos"]);
        }
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

    public function selectAssigment($type, $uid, $selected, $item_id = null)
    {
        $newItem  = $this->setOldItem($type, $item_id, $uid);

        $newItem['selected'] = $selected;

        $values = $this->Calculator($newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'], $newItem['discount_type']);

        $newItem['tax'] =  $values['iva'];

        $newItem['disccount_price'] = $values['calculated_price'];

        $newItem['subtotal'] = $values['neto'];

        $newItem['total'] = $values['total'];

        $this->desvincularElementoAnterior($type, $item_id, $uid, $newItem);
    }
    public function updateQty($type, $uid, $cant = 1, $item_id = null)
    {
        try {
            $this->eliminarCaracteres($cant);
            if (!is_numeric($cant)) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => $cant . ' NO ES UNA CANTIDAD VÁLIDA']);
                return;
            }

            $newItem  = $this->setOldItem($type, $item_id, $uid);

            $newItem['qty'] = $uid != null ? intval($cant) : intval($newItem['qty'] + $cant);

            $values = $this->Calculator($newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'], $newItem['discount_type']);

            $newItem['tax'] =  $values['iva'];

            $newItem['total'] = $values['total'];

            $this->desvincularElementoAnterior($type, $item_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 683369InformeMovimientos"]);
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

            //se agrega 0 por default cuando se agrega por primera vez el producto
            //si ya está agregado el producto, toma lo que esté en el input
            $newItem['disccount_percent'] = $item_id == null ? intval($disccount_percent) : intval($newItem['disccount_percent'] + $disccount_percent);

            $this->disccount = $newItem['disccount_percent'];
            $values = $this->Calculator($newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'], $newItem['discount_type']);
            $this->disccount = 0;

            if (!$disccount_percent) {
                $newItem['$disccount_percent'] = 0;
            }

            $newItem['tax'] =  $values['iva'];

            $newItem['disccount_price'] = $values['calculated_price'];

            $newItem['subtotal'] = $values['neto'];

            $newItem['total'] = $values['total'];


            $this->desvincularElementoAnterior($type, $item_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 710369InformeMovimientos"]);
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

            $values = $this->Calculator($newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'], $discount_type);

            $this->disccount = 0;

            $newItem['tax'] =  $values['iva'];

            $newItem['disccount_price'] = $values['calculated_price'];

            $newItem['subtotal'] = $values['neto'];

            $newItem['total'] = $values['total'];

            $this->desvincularElementoAnterior($type, $item_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 45870Ventas"]);
        }
    }
    public function updateIva($type, $uid, $selectedIva, $item_id = null)
    {
        try {
            $newItem  = $this->setOldItem($type, $item_id, $uid);

            $newItem['ind_iva'] = $selectedIva;

            $values = $this->Calculator($newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'], $newItem['discount_type']);

            $newItem['tax'] =  $values['iva'];

            $newItem['total'] = $values['total'];


            $this->desvincularElementoAnterior($type, $item_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 742369InformeMovimientos"]);
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

            $this->desvincularElementoAnterior($type, $item_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 766369InformeMovimientos"]);
        }
    }
    private function setOldItem($type, $item_id, $uid)
    {
        try {
            if ($type == 'producto') {
                $mycart = $this->cartP;
            } elseif ($type == 'servicio') {
                $mycart = $this->cartS;
            }
            if ($item_id == null) {
                $oldItem = $mycart->where('id', $uid)->first();
            } else {
                if ($type == 'producto') {
                    $oldItem = $mycart->where('pid', $item_id)->first();
                } elseif ($type == 'servicio') {
                    $oldItem = $mycart->where('sid', $item_id)->first();
                }
            }
            return $oldItem;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 787369InformeMovimientos"]);
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
            } elseif ($type == 'metodos') {

                // Encuentra el índice o clave del elemento a reemplazar
                $key = $this->methods->search(function ($method) use ($uid) {
                    return $method['uid'] === $uid;
                });

                // Reemplaza el método directamente por la clave encontrada
                if ($key !== false) {
                    $this->methods[$key] = $newItem;
                }
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 811369InformeMovimientos"]);
        }
    }
    private function Calculator($price, $qty, $ind_iva, $type)
    {
        try {
            if ($this->disccount) {
                if ($type == 'Porcentaje') {
                    //determinamos el precio de venta(con iva)
                    $calculatedPrice = $price - (($price * $this->disccount) / 100);
                } elseif ($type == 'Cantidad') {
                    //determinamos el precio de venta(con iva)
                    $calculatedPrice = $price - ($this->disccount / ($qty ?? 1));
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
            $montoIva = $subtotalNeto  * $ind_iva;
            //total con iva
            $totalConIva =  $subtotalNeto + $montoIva;
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 878369InformeMovimientos"]);
        }
    }

    private function totalCart($carts)
    {

        try {
            $amount = 0;
            foreach ($carts as $cart) {
                $amount += $cart->sum(function ($item) {
                    if ($item['total'] > 0) {
                        return $item['total'];
                    } elseif ($item['disccount_price'] > 0 && $item['disccount_price'] < $item['gross_price']) {
                        return $item['disccount_price'];
                    } else {
                        return $item['gross_price'];
                    }
                });
            }
            return $amount;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 895369InformeMovimientos"]);
        }
    }


    private function subtotalCart($carts)
    {
        try {
            $subt = 0;
            foreach ($carts as $cart) {
                $subt += $cart->sum(function ($item) {
                    if ($item['disccount_price']) {
                        $subT = ($item['qty'] * $item['disccount_price']) / ($item['ind_iva'] + 1);
                        return $subT;
                    } else {
                        $subT = ($item['qty'] * $item['sale_price']) / ($item['ind_iva'] + 1);
                        return $subT;
                    }
                });
            }
            return $subt;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 915369InformeMovimientos"]);
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 936369InformeMovimientos"]);
        }
    }
    public function removeItem($id, $type)
    {
        try {

            if ($type == 'producto') {
                $this->cartP = $this->cartP->reject(function ($product) use ($id) {
                    return $product['id'] === $id;
                });
                $this->loadCartTotales();
                $this->totalMethods();
            } elseif ($type == 'servicio') {
                $this->cartS = $this->cartS->reject(function ($service) use ($id) {
                    return $service['id'] === $id;
                });
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
            $this->emit('refresh');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 955369InformeMovimientos"]);
        }
    }
    private function initializeCollections()
    {
        $this->cartP = new Collection;
        $this->cartS = new Collection;
        $this->cartM = new Collection;
        $this->methods = new Collection;
        $this->propinas = new Collection;
        session()->forget('cartMaterials');
        session()->forget('cartS');
        session()->forget('cartP');
    }

    private function initializeTotales()
    {
        $this->totalCart = 0;
        $this->impuestos = 0;
        $this->subtotalCart = 0;
        $this->totalPoints = 0;
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
        $this->gallery = null;
    }
    private function loadCartTotales()
    {
        $this->totalCart = $this->totalCart([$this->cartP, $this->cartS]);
        $this->impuestos = $this->totalIVA([$this->cartP, $this->cartS]);
        $this->subtotalCart = $this->subtotalCart([$this->cartP, $this->cartS]);
        $this->totalPoints = $this->generatedPoints([$this->cartP, $this->cartS]);
        $this->totalMethods();
    }
    private function loadProductos()
    {
        try {
            if (!empty($this->search)) {
                $query = producto::where(function ($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhere('sku', "{$this->search}")
                        ->orWhere('intern_sku', "{$this->search}");
                })
                    ->where('name', '!=', 'Producto eliminado')
                    ->where('salon_id', Auth::user()->salon->id)
                    ->orderBy('name', 'asc')
                    ->get();
            } else {

                $query =  producto::where('salon_id', Auth::user()->salon->id)->orderBy('stock_qty', 'asc')->get();
            }
            return $query;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1020369InformeMovimientos"]);
        }
    }
    private function loadServicios()
    {
        try {
            if (!empty($this->search)) {
                $query = servicio::where(function ($query) {
                    $query->where('name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhere('gross_price', "{$this->search}")
                        ->orWhere('duration', "{$this->search}");
                })
                    ->where('name', '!=', 'Servicio eliminado')
                    ->where('salon_id', Auth::user()->salon->id)
                    ->orderBy('name', 'asc')
                    ->get();
            } else {
                $query =  servicio::where('salon_id', Auth::user()->salon->id)->orderBy('name', 'asc')->get();
            }
            return $query;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1040369InformeMovimientos"]);
        }
    }
    public function updatedSearch()
    {
        if ($this->lookingServices) {
            $this->loadS();
        } else {
            $this->loadP();
        }
    }
    public function loadP()
    {
        $this->lookingServices = false;
        $this->items = $this->loadProductos();
    }
    public function loadS()
    {
        $this->lookingServices = true;
        $this->items = $this->loadServicios();
    }
    public function addNewProduct($item_id)
    {
        try {
            if (!$this->lookingServices) {
                $item = producto::find($item_id);
                $type = 'producto';
            } else {
                $item = servicio::find($item_id);
                $type = 'servicio';
            }
            $this->AddItem($type, null, $item);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1068369InformeMovimientos"]);
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
                    if ($restante < 0 && $method['paymentMethod'] != '1') {
                        $this->dispatchBrowserEvent('noty-error', ['msg' =>  "El pago electrónico no puede superar la cantidad restante"]);
                        $this->removeItem($method['uid'], 'method');
                        return;
                    }
                }
            }
            $this->restante = $restante;
            $this->global_disccount = $disccount;
            $this->recibido = $recibido;
            $this->emit('refresh');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1082369InformeMovimientos"]);
        }
    }
    private function totalPropinas()
    {
        try {
            $recibido = 0;
            foreach ($this->propinas as $propina) {
                $recibido += $propina['amount'];
            }
            $this->propinasRecibidas = $recibido;
            $this->emit('refresh');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1108369InformeMovimientos"]);
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1120369InformeMovimientos"]);
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
    private function compararMetodos()
    {
        foreach ($this->methods as $method) {
            $methodFound = $this->itemSelected->metodosPago->where("payment_method_id", $method['paymentMethod'])->first();
            if ($methodFound != null) {
                $this->cambioDataMethods($method['uid'], $methodFound->created_at, 5, 'metodos');
            }
        }
    }
    private function vincularAbonos($abonos, $id = null)
    {
        foreach ($abonos as $abono) {
            if ($this->type == 'venta') {
                $abono->venta_id = $id;
            } elseif ($this->type == 'cita') {
                $abono->cita_id = $id;
            }
            $abono->save();
        }
    }
    private function vincularMateriales($materiales, $id = null)
    {
        foreach ($materiales as $material) {
            $material->asignacion_id = $id;
            $material->save();
        }
    }

    private function recuperarCart($key)
    {
        if (session()->has($key)) {
            return session($key);
        } else {
            return new Collection;
        }
    }
    private function vincularFiles($id)
    {
        foreach ($this->respaldoFiles as $file_id) {
            $file = File::find($file_id);
            if ($file != null) {
                $file->model_id = $id;
                $file->save();
            }
        }
    }
    public function Store()
    {
        try {
            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }
            $respaldoData = $this->respaldarInfo();
            $this->compararMetodos();
            $this->vincularAbonos($respaldoData['abonos']);
            $this->vincularAbonos($respaldoData['abonoPropinas']);
            $this->cancelarStock();
            $this->deleteRelations();
            $movimiento = null;
            if ($this->type == 'venta') {
                $movimiento = new venta;
                $movimiento->items = $this->calculateItems();

                if ($this->restante > 1) {
                    $movimiento->status = 'Pendiente';
                } else {
                    $movimiento->status = 'Pagada';
                }
            } elseif ($this->type == 'cita') {
                $movimiento = new cita;
                $movimiento->start = $respaldoData['start'];
                $movimiento->end = $respaldoData['end'];
                $movimiento->remember = 0;

                if ($this->restante > 1) {
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
            }
            $this->setMovimiento($movimiento, $respaldoData);

            $this->vincularAbonos($respaldoData['abonos'], $movimiento->id);
            $this->vincularAbonos($respaldoData['abonoPropinas'], $movimiento->id);

            if ($this->respaldoFiles) {
                $this->vincularFiles($movimiento->id);
            }

            if (count($this->cartP) > 0) {
                foreach ($this->cartP as $item) {
                    $asignacion = new Asignacion_venta;
                    if ($this->type == 'venta') {
                        $asignacion->venta_id = $movimiento->id;
                    } elseif ($this->type == 'cita') {
                        $asignacion->cita_id = $movimiento->id;
                    }
                    $asignacion->selected_item = $item['pid'];
                    $asignacion->quantity = $item['qty'];
                    $comission = $this->calcularComision($item, 'producto');
                    $asignacion->comission = $comission;
                    $this->ajustarStock($item);
                    $trash = $this->setDetail($asignacion, $item);
                }
            }
            if (count($this->cartS) > 0) {
                $cartM = $this->recuperarCart('cartMaterials');
                foreach ($this->cartS as $item) {
                    $start = $item['start'];
                    $asignacion = new Asignacion_servicio;
                    $asignacion->start = $start;
                    $asignacion->selected = $item['selected'];
                    $asignacion->selected_service = $item['sid'];
                    $asignacion->duration = $item['duration'];
                    $asignacion->cita_id = $movimiento->id;
                    $asignacion->color = $item['color'];
                    $comission = $this->calcularComision($item, 'servicio');
                    $asignacion->comission = $comission;
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
                        $newMaterial->cliente_id = $respaldoData['customer_id'];
                        $newMaterial->asignacion_id = $asignacion_id;
                        $newMaterial->save();
                        $this->ajustarStockMaterial($material);
                    }
                }
            }
            //metodos de pago
            foreach ($this->methods as $method) {

                if ($this->type == 'cita') {
                    $payment = new metodo_pago_servicio;
                    $payment->cita_id = $movimiento->id;
                } elseif ($this->type == 'venta') {
                    $payment = new metodo_pago_venta;
                    $payment->venta_id = $movimiento->id;
                }

                if ($method['paymentMethod'] == '1' && $this->restante < 0) {
                    $payment->change = abs($this->restante);
                }

                $payment = $this->setMethods($payment, $method, 1);
                $payment->save();
            };

            //propinas
            foreach ($this->propinas as $propina) {
                $newPropina = new Propina;
                $newPropina = $this->setMethods($newPropina, $propina);
                $newPropina->empleado_id = $propina['empleado'];
                if ($this->type == 'venta') {
                    $newPropina->venta_id = $movimiento->id;
                } elseif ($this->type == 'cita') {
                    $newPropina->cita_id = $movimiento->id;
                }
                $newPropina->save();
            }

            //gallery
            if (!empty($this->gallery)) {

                // guardar imagenes nuevas
                foreach ($this->gallery as $file) {
                    $fileName = uniqid() . '_.' . $file->extension();
                    $file->storeAs('public/citas', $fileName);

                    // creamos relacion
                    $img = File::create([
                        'model_id' => $movimiento->id,
                        'model_type' => 'App\Models\cita',
                        'file' => $fileName
                    ]);

                    // guardar relacion
                    $movimiento->files()->save($img);
                }
            }

            if ($respaldoData['etiquetas']) {
                $listTags = $respaldoData['etiquetas'];
                $movimiento->etiquetas()->sync($listTags);
            }

            $this->dispatchBrowserEvent('closeAll');
            $this->recuperarMensajes($movimiento);
            $this->disableEditing();
            $this->dispatchBrowserEvent('noty', ['msg' => "MOVIMIENTO EDITADO CORRECTAMENTE"]);
            $this->itemSelected = null;
            $this->recuperarInfo();

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1133469InformeMovimientos"]);
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
    private function recuperarMensajes($movimiento = null)
    {
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
    }
    private function ajustarStock($item)
    {
        try {
            $dif = $item['qty'];
            $product = producto::find($item['pid']);
            $product->stock_qty -= $dif;
            $product->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1134369InformeMovimientos"]);
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1244369InformeMovimientos"]);
        }
    }
    private function calculateStart($item, $total_minutes)
    {
        try {
            $total_minutes += $item['duration'];
            $start = Carbon::parse($item['start']);
            $start->addMinutes(intval($total_minutes));
            return $start;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1256369InformeMovimientos"]);
        }
    }
    private function setDetail($asignacion, $item)
    {
        try {
            if ($item['vendedor'] == null) {
                $asignacion->empleado_id = Empleado::where('salon_id', Auth::user()->salon->id)->first()->id;
            } else {
                $asignacion->empleado_id = $item['vendedor'];
            }
            $asignacion->discount_qty = floatval($item['disccount_percent']);
            $asignacion->discount_type = $item['discount_type'];
            $asignacion->generated_points = floatval($item['reward_points']);
            $asignacion->current_price = floatval($item['sale_price']);
            $asignacion->disccount_price = floatval($item['disccount_price']);
            $asignacion->iva = floatval($item['ind_iva']);
            $asignacion->save();
            return $asignacion->id;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1268369InformeMovimientos"]);
        }
    }
    private function setMovimiento($movimiento, $respaldoData)
    {
        try {
            $movimiento->disccount = $this->global_disccount;
            $movimiento->total = $this->totalCart;
            $movimiento->generated_points = $respaldoData['generated_points'];
            $movimiento->customer_id = $respaldoData['customer_id'];
            $movimiento->user_id = Auth::user()->id;
            $movimiento->salon_id = $respaldoData['salon_id'];
            $movimiento->created_at = $respaldoData['created_at'];
            $movimiento->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1288369InformeMovimientos"]);
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1310369InformeMovimientos"]);
        }
    }
    private function respaldarInfo()
    {
        try {
            // $this->mensajesRespaldados = $this->itemSelected->mensajesEnviados;
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
            if (isset($this->itemSelected->etiquetas)) {
                $etiquetas = $this->itemSelected->etiquetas;
            }
            $info = [
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
                'etiquetas' => $etiquetas ?? null,
            ];
            return $info;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1322369InformeMovimientos"]);
        }
    }
    private function deleteRelations($delete = 0)
    {

        if (isset($this->itemSelected->details_product)) {
            $this->deleteItems($this->itemSelected->details_product);
        }
        if ($delete) {
            $this->pictures = [];
        }
        if (isset($this->itemSelected->files)) {
            $this->deleteFiles($this->itemSelected->files);
        }

        if (isset($this->itemSelected->details)) {
            $this->deleteItems($this->itemSelected->metodosPago);
            $this->deleteItems($this->itemSelected->propinas);
            $this->deleteItems($this->itemSelected->mensajesEnviados);
            foreach ($this->itemSelected->details as $detail) {
                if (isset($detail->materiales)) {
                    $this->deleteItems($detail->materiales);
                }
            }
            if (isset($this->itemSelected->etiquetas)) {
                $this->itemSelected->etiquetas()->detach();
            }
            $this->deleteItems($this->itemSelected->abonos);
            $this->deleteItems($this->itemSelected->abonoPropinas);
            foreach ($this->itemSelected->details as $detail) {
                if (isset($detail->giftCard)) {
                    $this->deleteItems([$detail->giftCard]);
                }
            }
            // Eliminar asignaciones antes de eliminar la venta
            $this->itemSelected->details()->delete();

            $this->itemSelected->delete();
        }
    }
    private function deleteFiles($files)
    {
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1354369InformeMovimientos"]);
        }
    }
    private function calcularComision($item, $tipo)
    {
        try {
            $empleado = Empleado::with('comision')->find($item['vendedor']);
            $balance = 0;

            if (isset($empleado->comision)) {
                if ($tipo == 'producto') {
                    $excepcion = excepcion_producto::where('producto_id', $item['pid'])->where('comision_id', $empleado->comision->id)->first();
                    $excepcionCat = 'excepcion_cat_' . $tipo;
                    $obj = producto::find($item['pid']);
                    $categorias = $obj->categorias;
                    foreach ($categorias as $cat) {
                        $excepcionCat = excepcion_cat_producto::where('categoria_producto_id', $cat->id)->where('comision_id', $empleado->comision->id)->first();
                    }
                    $cant = $empleado->comision->qty_p;
                    $type = $empleado->comision->type_comission_p;
                } elseif ($tipo == 'servicio') {
                    $excepcion = excepcion_servicio::where('servicio_id', $item['sid'])->where('comision_id', $empleado->comision->id)->first();
                    $obj = servicio::find($item['sid']);
                    $categorias = $obj->categorias;
                    foreach ($categorias as $cat) {
                        $excepcionCat = excepcion_cat_servicio::where('categoria_servicio_id', $cat->id)->where('comision_id', $empleado->comision->id)->first();
                    }
                    $cant = $empleado->comision->qty_s;
                    $type = $empleado->comision->type_comission_s;
                }
                if (isset($empleado->comision->excepcion_servicio) || isset($empleado->comision->excepcion_cat_servicio)) {
                    $precioSinIva = $item['sale_price'] - ($item['sale_price'] * $item['ind_iva']);
                    $excepcionesObj = $excepcion;
                    $categorias = $obj->categorias;
                    foreach ($categorias as $cat) {
                        $excepcionesCategoria = $excepcionCat;
                    }
                    if (isset($excepcionesObj)) {
                        $cant = $excepcionesObj->qty;
                        $type = $excepcionesObj->type_comission;
                    } elseif (isset($excepcionesCategoria)) {
                        $cant = $excepcionesCategoria->qty;
                        $type = $excepcionesCategoria->type_comission;
                    }
                }
                if ($type == 'percent') {
                    $balance = ($cant / 100) * $precioSinIva;
                } elseif ($type == 'qty') {
                    $balance = $cant;
                }
                return $balance;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1370369InformeMovimientos"]);
        }
    }
    private function loadDataCorte()
    {
        try {
            $this->description = $this->itemSelected->description;
            $this->total_cash_real = $this->itemSelected->total_cash_real;
            $this->total_NF_real = $this->itemSelected->total_NF_real;
            $this->propinas_efectivo_real = $this->itemSelected->propinas_efectivo_real;
            $this->propinas_banorte_real = $this->itemSelected->propinas_banorte_real;
            $this->caja_chica_real = $this->itemSelected->caja_chica_real;
            $this->itemSelected->user_id = Auth()->user()->id;
            $terminales = $this->itemSelected->metodosZettle;
            $terminales_propina = $this->itemSelected->metodosZettlePropina;
            $this->terminales_propina = $this->obtenerCantidadesReales($terminales_propina, true);
            $this->terminales = $this->obtenerCantidadesReales($terminales, false);
            $this->calculateTotal();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1424369InformeMovimientos"]);
        }
    }
    public function changeTerminalQty($type, $terminal, $qty)
    {
        try {
            if ($type == 'terminal') {
                $this->terminales = $this->obtenerArreglo($terminal, $qty, $this->terminales);
            } elseif ($type == 'terminal_propina') {
                $this->terminales_propina = $this->obtenerArreglo($terminal, $qty, $this->terminales_propina);
            }
            $this->calculateTotal();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1442369InformeMovimientos"]);
        }
    }
    private function obtenerArreglo($terminal, $qty, $arr)
    {
        try {
            if (array_key_exists($terminal, $arr)) {
                $arr[$terminal]['real'] = $qty;
                return $arr;
            } else {
                $this->dispatchBrowserEvent('noty-error', ['msg' => "No existe la terminal"]);
                return;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1454369InformeMovimientos"]);
        }
    }
    public function changeQty($type, $forma_pago, $qty)
    {
        try {
            $qty = $this->eliminarCaracteres($qty);
            if ($type == 'forma_pago') {
                if ($forma_pago == 'cash') {
                    $this->total_cash_real = $qty;
                } elseif ($forma_pago == 'NF') {
                    $this->total_NF_real = $qty;
                } elseif ($forma_pago == 'card') {
                    $this->total_card_real = $qty;
                }
            } elseif ($type == 'propina') {
                if ($forma_pago == 'cash') {
                    $this->propinas_efectivo_real = $qty;
                } elseif ($forma_pago == 'NF') {
                    $this->propinas_banorte_real = $qty;
                } elseif ($forma_pago == 'card') {
                    $this->propinas_tarjeta_real = $qty;
                }
            }
            $this->calculateTotal();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1468369InformeMovimientos"]);
        }
    }
    public function cleanFormasPago()
    {
        $this->description = '';
        $this->total_cash_real = 0;
        $this->total_NF_real = 0;
        $this->total_card_real = 0;
        $this->propinas_efectivo_real = 0;
        $this->propinas_banorte_real = 0;
        $this->propinas_tarjeta_real = 0;
        $this->total_fp = 0;
        $this->total_p = 0;
    }
    private function calculateTotal()
    {
        try {
            $this->total_fp = 0;
            $this->total_p = 0;
            $this->total_fp += $this->total_cash_real;
            $this->total_fp += $this->total_NF_real;
            foreach ($this->terminales as $nombre_terminal => $cantidades) {
                $this->total_fp += $cantidades['real'];
            }

            $this->total_p += $this->propinas_efectivo_real;
            $this->total_p += $this->propinas_banorte_real;
            foreach ($this->terminales_propina as $nombre_terminal => $cantidades) {
                $this->total_p += $cantidades['real'];
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1500369InformeMovimientos"]);
        }
    }
    public function StoreCorte()
    {
        $this->setCorte();
    }
    private function setCorte()
    {
        try {
            $this->deleteItems($this->itemSelected->metodosZettle, true);
            $this->deleteItems($this->itemSelected->metodosZettlePropina, true);
            $this->itemSelected->description = $this->description;
            $this->itemSelected->total_cash_real = $this->total_cash_real;
            $this->itemSelected->total_tarjeta_real = $this->total_card_real;
            $this->itemSelected->total_NF_real = $this->total_NF_real;
            $this->itemSelected->propinas_efectivo_real = $this->propinas_efectivo_real;
            $this->itemSelected->propinas_banorte_real = $this->propinas_banorte_real;
            $this->itemSelected->propinas_tarjeta_real = $this->propinas_tarjeta_real;
            $this->itemSelected->description = $this->description;
            $this->itemSelected->caja_chica_real = $this->caja_chica_real;
            $this->itemSelected->save();
            foreach ($this->terminales_propina as $reference => $qty) {
                $metodo_corte = new metodo_propina_corte;
                $metodo_corte->is_real = true;
                $this->setMethodsCorte($metodo_corte, $qty, $reference);
            }
            foreach ($this->terminales as $reference => $qty) {
                $metodo_corte = new metodo_pago_corte;
                $metodo_corte->is_real = true;
                $this->setMethodsCorte($metodo_corte, $qty, $reference);
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1524369InformeMovimientos"]);
        }
        // $this->redirect('informe-movimientos');
    }
    private function setMethodsCorte($method, $qty, $reference)
    {
        try {
            $method->payment_method = $reference;
            $method->qty = $qty['real'];
            $method->caja_corte_id = $this->itemSelected->id;
            $method->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1554369InformeMovimientos"]);
        }
    }
    public function deleteMov()
    {
        $hasGiftCardsRedeemed = $this->itemSelected->details->contains(fn($detail) => optional($detail->giftCard)->redeemed);

        if (!$hasGiftCardsRedeemed) {
            $this->cancelarStock();
            $this->cancelarPuntos();
            $this->recuperarMensajes();
            $this->deleteRelations(1);
            $this->dispatchBrowserEvent('noty', ['msg' => "MOVIMIENTO ELIMINADO PERMANENTEMENTE"]);
        } else {
            $this->dispatchBrowserEvent('noty-error', ['msg' => "La venta contiene una tarjeta de regalo"]);
        }
        $this->recuperarInfo();
    }
    public function deleteUso()
    {
        $this->cancelarStock();
        foreach ($this->itemSelected as $material) {
            $mat = Material::find($material['id']);
            $mat->delete();
        }
        $this->dispatchBrowserEvent('noty', ['msg' => "MOVIMIENTO ELIMINADO PERMANENTEMENTE"]);
        $this->recuperarInfo();
    }
    public function deleteEntrada()
    {
        $this->cancelarStock();
        foreach ($this->itemSelected as $material) {
            $mat = Entrada::find($material['id']);
            $mat->delete();
        }
        $this->dispatchBrowserEvent('noty', ['msg' => "MOVIMIENTO ELIMINADO PERMANENTEMENTE"]);
        $this->recuperarInfo();
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1575369InformeMovimientos"]);
        }
    }
    private function cancelarStock()
    {
        try {
            $assigments = [];
            if ($this->type == 'venta') {
                $assigments = $this->itemSelected->details;
            } elseif ($this->type == 'cita') {
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
                $details = $this->itemSelected->details_product;
                foreach ($details as $assigment) {
                    $qty = $assigment->quantity;
                    $product = $assigment->product;
                    $product->stock_qty += $qty;
                    $product->save();
                }
                return;
            }
            if (count($assigments) > 0) {
                foreach ($assigments as $assigment) {
                    $qty = $assigment->quantity;
                    $product = $assigment->product;
                    $product->stock_qty += $qty;
                    $product->save();
                }
            } else {
                foreach ($this->itemSelected as $material) {
                    $product = producto::find($material['producto']['id']);
                    if ($this->type == 'uso') {
                        $product->stock_qty += $material['qty'];
                    } elseif ($this->type == 'entrada') {
                        $product->stock_qty -= $material['qty'];
                    }
                    $product->save();
                }
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1585369InformeMovimientos"]);
        }
    }
}
