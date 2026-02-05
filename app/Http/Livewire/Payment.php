<?php

namespace App\Http\Livewire;

use App\Models\Asignacion_venta;
use App\Models\cliente;
use App\Models\coupon;
use App\Models\Empleado;
use App\Models\caja_apertura;
use App\Models\metodo_pago_venta;
use App\Models\producto;
use App\Models\Propina;
use App\Models\venta;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use App\Http\Controllers\DataResourceGrid as DRG;

class Payment extends Component
{
    use WithPagination;
    public $total_disccount, $totalCart, $itemsCart, $cash, $reference, $paymentMethod, $tips, $generated_points, $global_disccount = 0, $disccount = 0, $rest, $recibido, $propinasRecibidas = 0, $indexTotal = 0, $metodoProp, $qtyProp, $referenceProp;
    public Collection $methods, $propinas;
    public $cust_message, $sale_id;

    public $clientes = [], $query, $disccount_form = false;

    public $customerId, $customer;
    protected $paginationTheme = 'bootstrap';
    protected $itemSelected;
    public $metodosSalon = [];
    public $type_disccount = '%', $searchPassword = null;
    public $billRequired = 0, $usoCfdi = null, $billed = false;

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
    public function __construct()
    {
        // Inicializa las propiedades en el constructor
        $this->methods = collect();
        $this->propinas = collect();
    }
    private function getMetodosSalon()
    {
        foreach (Auth::user()->salon->metodosPago as $metodosSalon) {
            $this->metodosSalon[] = $metodosSalon;
        }
    }
    public function mount($itemsCart)
    {
        try {
            $this->getMetodosSalon();
            $this->itemsCart = $itemsCart;
            if (session()->has('methods')) {
                $this->methods = session('methods');
                $this->rest = $this->totalCart;
                $this->totalMethods();
            } else {
                $this->methods = new Collection;
                $this->rest = $this->totalCart;
            }
            if (session()->has('propinas')) {
                $this->propinas = session('propinas', []);
            } else {
                $this->propinas = new Collection;
            }
            $this->removeRewardMethods();

            if (session()->has('editSale')) {
                $this->editingSale(venta::with('details.product', 'propinas', 'customer', 'metodosPago')->find(session('editSale')));
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 38204Payment"]);
        }
    }

    protected $listeners = [
        'refresh' => '$refresh',
        'removeItem',
        'setCustomerId',
        'newMethod' => 'setMethod',
        'newPropina' => 'setTip',
        'cambioData',
        'cambioDataMethods',
        'setReward',
        'dataChange',
        'enviarCliente' => 'recibirClienteNuevo',
        'collectMethod',
        'enviarData' => 'recibirData',
        'store' => 'storeDate',
        'GCInMethods',
        'rfcSelected',
        'continueStoring'
    ];
    public function recibirData($mov)
    {
        if ($mov['customer'] !== null) {
            $this->setCustomerId($mov['customer']['id']);
        }
        foreach ($mov['metodos_pago'] as $method) {
            $this->collectMethod($method['amount'], $method['reference'], $method['payment_method_id'], 1, null, $method['tipo']);
        }
        foreach ($mov['propinas'] as $propina) {
            $this->collectMethod($propina['amount'], $propina['reference'], $propina['payment_method_id'], 0, $propina['empleado_id'], null);
        }

        $this->billRequired = $mov['billing'];
        $this->billed = intval($mov['billing']) === 2 ? true : false;
        $this->usoCfdi = $mov['billing_description'];
        $this->rfcSelected($mov['tax_data_id']);

        $this->totalMethods();
        $this->save();
        session()->put('itemSelected', $mov['id']);
        session()->save();
    }
    public function editingSale($mov)
    {
        $this->clear();

        if ($mov->customer !== null) {
            $this->setCustomerId($mov->customer->id);
        }
        foreach ($mov->metodosPago as $method) {
            $this->collectMethod($method->amount, $method->reference, $method->payment_method_id, 1, null, $method->tipo);
        }
        foreach ($mov->propinas as $propina) {
            $this->collectMethod($propina->amount, $propina->reference, $propina->payment_method_id, 0, $propina->empleado_id, null);
        }
        $this->totalMethods();
        $this->totalPropinas();
        $this->save();
    }

    public function collectMethod($qty, $reference, $paymentMethod, $is_method, $empleado = null, $type = null)
    {
        // validar si ya existe entre los métodos
        if ($paymentMethod == '4') {
            $globalD = 0;
            if ($type == 'Porcentaje') {
                $globalD = $qty;
            } elseif ($type == 'Cantidad') {
                $globalD = $qty / $this->rest;
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
                'tipo' => $type
            ]
        );
        $method = Arr::add($coll, null, null);
        if ($is_method) {
            $this->methods->push($method);
        } else {
            $this->propinas->push($method);
        }
    }
    public function activateCardCust($custId = null, $balance = 0)
    {
        $this->emit('activateCardWithBalance', ['id' => $custId ?? $this->customerId, 'puntaje' => $balance]);
    }
    public function recibirClienteNuevo($cust_id)
    {
        $this->setCustomerId($cust_id);
        $this->emit('closeModalCust');
        $this->dispatchBrowserEvent('play_1');
    }
    public function newCust()
    {
        $this->emit('activateModalForm');
    }
    public function enableDisccount()
    {
        $this->disccount_form = true;
    }
    public function dataChange()
    {
        $this->subReward();
        $this->recuperarMethods();
    }
    private function recuperarMethods()
    {
        if (session()->has('methods')) {
            $this->methods = session('methods');
        } else {
            $this->methods = new Collection;
        }
        if (session()->has('propinas')) {
            $this->propinas = session('propinas');
        } else {
            $this->propinas = new Collection;
        }

        $this->totalMethods();
        $this->totalPropinas();
        $this->emit('refresh');
    }
    private function generatedPoints()
    {
        try {
            $rewP = 0;
            $cart = session('cartP');
            foreach ($cart as $product) {
                if (isset($product['reward_points'])) {
                    $rewP += $product['reward_points'];
                }
            }
            return $rewP;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 74206Payment"]);
        }
    }
    public function setCustomerId($customerId, $recorrido = true)
    {
        if ($customerId != null) {
            $this->customerId = $customerId;
            $this->customer = cliente::with('tarjetaPuntos')->find($customerId);
            session()->put('customerId', $customerId);
            session()->save();
        }
        if (session('recorrido') && $recorrido) {
            $this->dispatchBrowserEvent('play_1');
        }
    }
    public function unsetCustomer()
    {
        $this->customerId = null;
        $this->customer = new cliente;
        $this->removeRewardMethods();
        session()->forget('customerId');
    }
    private function sumReward()
    {
        $this->paymentMethod = '5';
        if ($this->inMethods()) {
            if ($this->rest > 0) {
                $puntos = $this->methods->where('paymentMethod', $this->paymentMethod)->first();
                if ($puntos['amount'] < $this->customer->tarjetaPuntos->balance) {
                    $puntosNew = $this->customer->tarjetaPuntos->balance;
                    $this->cambioDataMethods($puntos['uid'], $puntosNew, 1, 'metodos');
                }
            } else {
                $this->subReward();
            }
        }
    }
    private function subReward()
    {
        $this->paymentMethod = '5';
        if ($this->inMethods()) {
            $puntos = $this->methods->where('paymentMethod', $this->paymentMethod)->first();
            if (count($this->methods) < 1 && $this->rest < 0) {
                $this->cambioDataMethods($puntos['uid'], $this->totalCart, 1, 'metodos');
            }
        } else {
            return;
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
            $this->paymentMethod = '5';
            $this->cash = 0;

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

                if ($cust->tarjetaPuntos->balance <= 0)
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

            $this->cash = $this->recibido = $puntos;
            $this->AddMethod();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 101207Payment"]);
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
    public function setTips($tips)
    {
        $this->tips = $tips;
    }

    public function setMethod()
    {
        $this->paymentMethod = '1';
        $this->AddMethod();
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
            $this->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1120369InformeMovimientos"]);
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

            $this->desvincularElementoAnterior($array, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1134369InformeMovimientos"]);
        }
    }

    private function desvincularElementoAnterior($type, $uid, $newItem)
    {
        try {
            if ($type == 'metodos') {
                // Encuentra el índice o clave del elemento a reemplazar
                $key = $this->methods->search(function ($method) use ($uid) {
                    return $method['uid'] === $uid;
                });

                // Reemplaza el método directamente por la clave encontrada
                if ($key !== false) {
                    $this->methods[$key] = $newItem;
                }
                $this->totalMethods();
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
    public function setDisccount($qty)
    {
        $qty = $this->eliminarCaracteres($qty);
        $this->paymentMethod = '4';
        $this->global_disccount = $qty;
        $this->AddMethod();
    }
    public function render()
    {
        try {
            //validamos que exista la sesion
            if (session()->has('methods')) {
                //obtenemos los métodos de pago
                $methods = session('methods');
                // ordenar los métodos mediante nombre de forma asc
                $methods = $methods->sortBy(['paymentMethod', ['paymentMethod', 'asc']]);
            } else {
                $methods = new Collection;
            }
            if (session()->has('propinas')) {
                $propinas = session('propinas');
            } else {
                $propinas = new Collection;
            }
            if (session()->has('customerId')) {
                $custId = session('customerId');
                $this->setCustomerId($custId, false);
            } else {
                $this->unsetCustomer();
            }
            return view('livewire.payment', compact('methods', 'propinas'), [
                'disccount_form' => $this->disccount_form,
                'restante' => $this->rest,
                'empleados' => $this->loadEmployees()
            ]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 129208Payment"]);
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097Payment"]);
        }
    }
    public function getChangeProperty()
    {
        try {

            if (!is_numeric($this->totalCart) || !is_numeric($this->cash)) {
                return 0;
            }
            $change = $this->cash - $this->rest;
            if ($change > 0) {
                return $change;
            }
            return 0;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 148209Payment"]);
        }
    }
    private function applyDisccount($global_disccount)
    {
        try {
            // Establecer un valor predeterminado si el descuento está vacío
            $global_disccount = is_numeric($global_disccount) ? $global_disccount : 0;

            $global_disccount = min($global_disccount, 100); // Asegurar que el descuento no sea mayor al 100%

            $disccount = $this->rest - ($this->rest * ($global_disccount / 100));

            $this->rest = $disccount;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 165210Payment"]);
        }
    }
    private function AddMethod()
    {
        $this->validate([
            'paymentMethod' => 'required',
            'cash' => 'numeric | nullable',
            'reference' => 'nullable',
        ]);
        try {
            // validar si ya existe entre los métodos
            if ($this->paymentMethod !== '4') {
                $type = 'Cantidad';
                $qty = $this->cash;
                if ($qty > $this->rest && ($this->paymentMethod == '2' || $this->paymentMethod == '3')) {
                    $this->dispatchBrowserEvent('noty-error', ['msg' =>  "La cantidad para este método no puede superar el monto restante..."]);
                    return;
                }

                if ($this->inMethods()) {
                    $this->paymentMethod = '2';
                    if ($this->inMethods()) {
                        $this->paymentMethod = '3';
                        if ($this->inMethods()) {
                            $this->paymentMethod = '1';
                            return;
                        }
                    }
                }
                $this->rest -= $this->cash;
            } else {
                $globalD = 0;
                $type = 'Porcentaje';
                if ($this->type_disccount == 'percent') {
                    $globalD = $this->global_disccount;
                } elseif ($this->type_disccount == 'currency') {
                    $globalD = $this->rest > 0 ? $this->global_disccount / $this->rest : 0;
                    $type = 'Cantidad';
                }
                $this->applyDisccount($globalD);
                $qty = $this->global_disccount;
            }

            $uid = uniqid();
            $coll = collect(
                [
                    'uid' => $uid,
                    'paymentMethod' => $this->paymentMethod,
                    'amount' => floatval($qty),
                    'reference' => $this->reference,
                    'current_disccount' => $this->disccount,
                    'tipo' => $type,
                ]
            );
            $method = Arr::add($coll, null, null);
            $this->methods->push($method);

            $this->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 182211Payment"]);
        }
    }

    private function updateQty()
    {
        try {
            $mymethods = $this->methods;
            $oldItem = $mymethods->where('paymentMethod', $this->paymentMethod)->first();

            $newItem  = $oldItem;

            $newItem['amount'] += floatval($this->cash);

            $this->rest -= $this->cash;
            //eliminar el item de la coleccion / sesion
            $paymentMethod = $this->paymentMethod;
            $this->methods = $this->methods->reject(function ($method) use ($paymentMethod) {
                return $method['paymentMethod'] === $paymentMethod;
            });

            $this->methods->push(Arr::add($newItem, null, null));
            $this->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 226212Payment"]);
        }
    }
    public function removeItem($id, $type)
    {
        try {
            if ($type == 'method') {
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
            $this->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 955369InformeMovimientos"]);
        }
    }
    private function totalMethods()
    {
        try {
            $this->total_disccount = 0;
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
                    if ($restante < 0 && $method['paymentMethod'] != '1' && $method['paymentMethod'] != '99999') {
                        $this->dispatchBrowserEvent('noty-error', ['msg' =>  "El pago electrónico no puede superar la cantidad restante"]);
                        $this->removeItem($method['uid'], 'method');
                        return;
                    }
                }
            }
            $total_disccount = $this->calculateTotalDisccount();
            $this->total_disccount = $disccount + $total_disccount;
            $this->rest = $restante;
            $this->global_disccount = $disccount;
            $this->recibido = $recibido;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1082369InformeMovimientos"]);
        }
    }

    private function calculateTotalDisccount()
    {
        try {
            $total_disccount = 0;

            //recuperamos carrito
            $cart = session('cartP');
            if ($cart) {
                $total_disccount += $cart->sum(function ($item) {
                    return ($item['gross_price'] * $item['qty']) - ($item['total']);
                    // if($item['discount_type']=='Porcentaje'){
                    //     return ($item['gross_price']*$item['qty'])-($item['total']-($item['total']*$item['disccount_percent']/100));
                    // }elseif($item['discount_type']=='Cantidad'){
                    //     return ($item['gross_price']*$item['qty'])-($item['total']-$item['disccount_percent']);
                    // }
                });
            }
            return $total_disccount;
        } catch (\Throwable $th) {

            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 936369InformeMovimientos"]);
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
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1108369InformeMovimientos"]);
        }
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
    private function imprimirTicket(venta $sale)
    {
        $this->dispatchBrowserEvent('print_on', ['ticket_venta', $sale->id]);
    }
    private function deleteRelations()
    {
        $this->deleteItems($this->itemSelected->metodosPago);
        $this->deleteItems($this->itemSelected->propinas);
        // $this->deleteItems($this->itemSelected->mensajesEnviados);
        $this->deleteItems($this->itemSelected->details, true);
        if (isset($this->itemSelected->details_product)) {
            $this->deleteItems($this->itemSelected->details_product);
        }
        if (!isset($this->itemSelected->metodosPago, $this->itemSelected->propinas, $this->itemSelected->details, $this->itemSelected->details_product)) {
            $this->itemSelected->delete();
        }
    }
    private function deleteItems($relation, $detail = false)
    {
        try {
            foreach ($relation as $item) {
                if ($detail && isset($item->giftCard)) {
                    $item->giftCard->delete();
                }
                $item->delete();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 432423InformeMovimientos"]);
        }
    }
    private function ajustarStock($item)
    {
        try {
            $dif = $item['qty_inicial'];
            $product = producto::find($item['pid']);
            $product->stock_qty += $dif;
            $product->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1134369InformeMovimientos"]);
        }
    }
    private function compararMetodos()
    {
        foreach ($this->methods as $method) {
            $methodFound = $this->itemSelected->metodosPago->where("payment_method_id", $method['paymentMethod'])->where('amount', $method['amount'])->first();
            if ($methodFound != null) {
                $this->cambioDataMethods($method['uid'], $methodFound->created_at, 5, 'metodos');
            }
        }
    }
    private function respaldarInfo()
    {
        try {
            // $this->mensajesRespaldados = $this->itemSelected->mensajesEnviados;
            $cid = $this->itemSelected->id;
            $generated_points = $this->itemSelected->generated_points;
            $created_at = $this->itemSelected->created_at;
            $abonos = $this->itemSelected->abonos;
            $abonoPropinas = $this->itemSelected->abonoPropinas;
            $status = $this->itemSelected->status;
            $billing = $this->itemSelected->billing;
            $billing_description = $this->itemSelected->billing_description;
            $tax_data_id = $this->itemSelected->tax_data_id;
            $info = [
                'cid' => $cid,
                'generated_points' => $generated_points,
                'created_at' => $created_at,
                'abonos' => $abonos,
                'status' => $status,
                'abonoPropinas' => $abonoPropinas,
                'billing' => $billing,
                'billing_description' => $billing_description,
                'tax_data_id' => $tax_data_id,
            ];
            return $info;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1322369Payment"]);
        }
    }
    private function vincularAbonos($abonos, $id = null)
    {
        foreach ($abonos as $abono) {
            $abono->venta_id = $id;
            $abono->save();
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
                $this->emit('setCajaChica', $totalCashReal - $efectivoCorte - $propinasEfectivo + $gastos);
                return false;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 9655Cortes"]);
        }
    }
    private function calculateCustomerPoints($total)
    {

        try {
            $excepcion = $this->customer->excepciones()
                ->latest()
                ->first();

            if ($excepcion) {
                return $this->getRewardPoints($excepcion, $total);
            }

            // Buscar categorías y excepciones asociadas a las categorías
            $categoria = $this->customer->categorias()->latest()->first();

            if ($categoria) {
                $excepcion = $categoria->excepciones()
                    ->whereNotNull('programa_recompensa_id')
                    ->latest()
                    ->first();

                if ($excepcion) {
                    return $this->getRewardPoints($excepcion, $total);
                }
            }

            // Si no hay excepciones ni categorías, retornar 0
            return 0;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 3469Payment"]);
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
    public function storeDate()
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

            if (!session()->has('cartP')) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'NO HAY PRODUCTOS AGREGADOS']);
                return;
            }

            if (session()->has('cust')) {
                $this->customer = session('cust');
                $this->customerId = $this->customer->id;
            }

            if ($this->customerId == null) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'SELECCIONA UN CLIENTE']);
                return;
            }


            if (!$this->verificarApertura()) {
                $this->dispatchBrowserEvent('aperturar');
                session()->put('cust', $this->customer);
                session()->save();
                return;
            }

            //recuperamos carrito
            $cart = session('cartP');
            $respaldoData = null;

            if (session()->has('giftCards')) {
                $giftCards = session('giftCards'); // Convertir a colección
            } else {
                $giftCards = null;
            }

            if (session()->has('itemSelected') || session()->has('editSale')) {
                $id = session('itemSelected') ?? session('editSale');
                $this->itemSelected = venta::with('customer', 'metodosPago', 'propinas', 'details.giftCard')->find($id);
                $respaldoData = $this->respaldarInfo();
                $this->compararMetodos();
                $this->vincularAbonos($respaldoData['abonos']);
                $this->vincularAbonos($respaldoData['abonoPropinas']);
                $this->deleteRelations();
                foreach ($cart as $item) {
                    $this->ajustarStock($item);
                }
                // $this->recuperarMensajes($movimiento);
            }

            $issetRespaldo = isset($respaldoData);
            $id = $issetRespaldo ? $respaldoData['cid'] : DB::table('ventas')->max('id') + 1;
            $sale = new venta;

            $sale->id = $id;
            $sale->total = $this->totalCart;
            $sale->disccount = $this->global_disccount;
            $sale->items = $this->itemsCart;
            $sale->customer_id = $this->customerId;
            $sale->user_id = Auth()->user()->id;
            $sale->salon_id = Auth()->user()->salon->id;
            $sale->created_at = $issetRespaldo ? $respaldoData['created_at'] : Carbon::now();
            $sale->tax_data_id = $issetRespaldo ? $respaldoData['tax_data_id'] : null;
            $sale->billing = $issetRespaldo ? $respaldoData['billing'] : 0;
            $sale->billing_description = $issetRespaldo ? $respaldoData['billing_description'] : null;
            $sale->save();

            if ($this->rest > 1) {
                $sale->status = 'Pendiente';
            } else {
                $sale->status = 'Pagada';
            }
            if ($respaldoData) {
                $this->vincularAbonos($respaldoData['abonos'], $sale->id);
                $this->vincularAbonos($respaldoData['abonoPropinas'], $sale->id);
            }


            //asignaciones de venta
            foreach ($cart as $item) {
                $comission = $this->calcularComision($item);
                $final_price = $item['gross_price'] > $item['sale_price'] ? $item['gross_price'] : $item['sale_price'];
                $rewardPoints = DRG::calculateRewardPoints($item['pid'], false, $final_price, null, $this->customerId, Auth::user()->salon->recompensaGeneral());
                $asignacion = new Asignacion_venta([
                    'selected_item' => $item['pid'],
                    'venta_id' => $sale->id,
                    'quantity' => $item['qty'],
                    'discount_type' => $item['discount_type'],
                    'discount_qty' => floatval($item['disccount_percent']),
                    'disccount_price' => $item['disccount_price'],
                    'iva' => $item['ind_iva'],
                    'empleado_id' => $item['vendedor'] !== null ? $item['vendedor'] : Empleado::where('salon_id', Auth::user()->salon->id)->first()->id,
                    'current_price' => $final_price,
                    'generated_points' => $rewardPoints ? floatval($rewardPoints) : floatval($item['reward_points']),
                    'comission' => $comission['balance'],
                    'type_comision_calculated' => $comission['type'],
                    'base_comision' => $item['base_comision'],
                ]);
                $asignacion->save();

                if (is_array($giftCards)) {
                    $card = $giftCards[$item['id']];
                    if ($card) {
                        $tarjetaRegalo = new coupon([
                            "password" => $card->password,
                            "value_amount" => $card->value_amount,
                            "expires_at" => $card->expires_at,
                            "acumulable" => $card->acumulable ?? false,
                            "asignacion_venta_id" => $asignacion->id,
                            "redeemed" => $card->redeemed,
                            "deleted_at" => $card->deleted_at,
                            "created_at" => $card->created_at,
                            "updated_at" => $card->updated_at,
                        ]);
                        $tarjetaRegalo->save();
                    }
                }
            }
            $sale->generated_points = $this->generatedPoints();
            $sale->save();
            $this->sale_id = $sale->id;

            // Bandera que determina si se abre el modal para la activación de tarjeta_puntos
            $sendActivateCardCust = false;

            //metodos de pago 
            foreach ($this->methods as $method) {
                if ($method['paymentMethod'] == '5' && !isset($method['created_at'])) {
                    $this->customer->tarjetaPuntos->balance -= $method['amount'];
                    $this->customer->tarjetaPuntos->save();
                }
                $payment = new metodo_pago_venta;
                if (($method['paymentMethod'] == '1' || $method['paymentMethod'] == '99999') && $this->rest < 0) {
                    $payment->change = abs($this->rest);
                }
                $payment->venta_id = $sale->id;
                $payment = $this->setMethods($payment, $method, 1);

                if ($method['paymentMethod'] == '99999') {
                    $gc = coupon::firstWhere('password', $payment->reference);
                    $gc->redeemed = 1;
                    $gc->save();

                    if ($this->customer->tarjetaPuntos && !isset($method['created_at'])) {
                        $this->customer->tarjetaPuntos->balance += abs($this->rest);
                        $this->customer->tarjetaPuntos->save();
                    }

                    if (abs($this->rest) > 0 && $this->customer->tarjetaPuntos == null) {
                        $sendActivateCardCust = true;
                    }
                }
                $payment->save();
            }

            foreach ($this->propinas as $propina) {
                $payment = new Propina;
                $newPropina = $this->setMethods($payment, $propina);
                $newPropina->empleado_id = $propina['empleado'];
                $newPropina->venta_id = $sale->id;
                $newPropina->save();
            }

            $this->cust_message = $this->customer;

            //sync stocks
            // $dataProducts = ['update' => []];

            foreach ($cart as $item) {
                $product = producto::find($item['pid']);
                $product->stock_qty -= $item['qty'];
                $product->save();


                // $newStock = $product->stock_qty;
                // $dataProducts['update'][] = [
                //     'id' => $product->platform_id,
                //     'stock_quantity' => $newStock
                // ];
            }

            // $resultSync = $this->SyncBatchStock($dataProducts);

            if (intval($this->billRequired) !== 0) {
                $stat = $this->billed ? 2 : 1;
                $sale->billing = intval($stat);
                $sale->billing_description = $this->usoCfdi;
                $sale->tax_data_id = session()->has('rfcSelected') ? session('rfcSelected') : null;
                $sale->save();
            }

            if ($sale->status == 'Pagada') {
                if ((!isset($respaldoData) || $respaldoData['status'] != 'Pagada')) {
                    if ($this->customer->tarjetaPuntos) {
                        $tarjetaPuntos = $this->customer->tarjetaPuntos;
                        $tarjetaPuntos->balance += $sale->generated_points;
                        $tarjetaPuntos->save();
                    }
                }
                if (!$sendActivateCardCust) {
                    $this->emit('reseñaCliente', $this->customerId);
                    $this->imprimirTicket($sale);
                } elseif (!isset($this->methods[0]['created_at']) && $sendActivateCardCust) {
                    $custId = $this->customer->id;
                    $this->activateCardCust($custId, abs($this->rest));
                    return;
                }
            }
            $this->dispatchBrowserEvent('noty', ['msg' =>  "VENTA REGISTRADA CON ÉXITO"]);


            if (!session()->has('giftCards') && !$sendActivateCardCust) {
                $this->emit('clear-cart');
                $this->clear();
            }
            if (session()->has('editSale')) {
                return redirect()->route('productos');
            }
            // $this->enviarWa();
            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "537263Payment"]);
        }
    }
    public function continueStoring()
    {
        try {
            $this->dispatchBrowserEvent('noty', ['msg' =>  "VENTA REGISTRADA CON ÉXITO"]);

            if (!session()->has('giftCards')) {
                $this->emit('clear-cart');
                $this->clear();
            }
            if (session()->has('editSale')) {
                return redirect()->route('productos');
            }
            // $this->enviarWa();
            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 123429Agenda"]);
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1244369Payment"]);
        }
    }

    // private function formatPhoneNumber($phoneNumber) {
    //     // Quitar el '+' y los espacios
    //     $cleanedNumber = str_replace(['+', ' '], '', $phoneNumber);

    //     // Verificar si el número no tiene el prefijo '52'
    //     if (substr($cleanedNumber, 0, 2) !== '52') {
    //         $cleanedNumber = '52' . $cleanedNumber;
    //     }

    //     return $cleanedNumber;
    // }
    // private function enviarWa()
    // {
    //     if($this->cust_message->want_custom_messages){

    //         $uid = uniqid();

    //         $salon = Auth::user()->salon;

    //         if($salon->token!==null && $salon->url_wa!==null){

    //             $cust_phone = $this->cust_message->phone;
    //             $cust_phone = $this->formatPhoneNumber($cust_phone);

    //             $data = [
    //                 'token' => $salon->token,
    //                 'phone' => $cust_phone,
    //                 'cust_name' => $this->cust_message->first_name,
    //                 'url' => $salon->url_wa,
    //                 'mov_type' => 'compra',
    //                 'venta_id' => $this->sale_id,
    //                 'cita_id' => null,
    //                 'type' => 'solicitud_encuesta'
    //             ];


    //             // Convertir el array data a una cadena de consulta
    //             $queryData = http_build_query($data);

    //             // Construir la URL correctamente
    //             $url = route('envia', ['uid' => $uid]) . '?' . $queryData;
    //             // Realizar la solicitud HTTP
    //             $this->emit('redirect', $url);

    //         }
    //     }
    // }

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
    private function calcularComision($item)
    {
        $empleado = Empleado::with('comision.excepcion_producto', 'comision.excepcion_cat_producto')->find($item['vendedor']);
        $balance = 0;
        $type = 'percent';

        if (!$empleado || !$empleado->comision) {
            return ['balance' => $balance, 'type' => $type];
        }

        $base_price = $this->defineBasePrice($item);
        $comision = $empleado->comision;

        // Buscar excepciones específicas
        $excepcionProducto = $comision->excepcion_producto->firstWhere('producto_id', $item['pid']);
        $excepcionCategoria = null;

        $producto = producto::with('categorias')->find($item['pid']);
        if ($producto && $producto->categorias) {
            foreach ($producto->categorias as $cat) {
                $ex = $comision->excepcion_cat_producto->firstWhere('categoria_producto_id', $cat->id);
                if ($ex) {
                    $excepcionCategoria = $ex;
                    break;
                }
            }
        }

        // Valores por defecto
        $cant = $comision->qty_p;
        $type = $comision->type_comission_p;

        // Excepciones sobreescriben si existen
        if ($excepcionCategoria) {
            $cant = $excepcionCategoria->qty;
            $type = $excepcionCategoria->type_comission;
        }

        if ($excepcionProducto) {
            $cant = $excepcionProducto->qty;
            $type = $excepcionProducto->type_comission;
        }

        // Cálculo de la comisión
        if ($type === 'percent') {
            $balance = ($cant / 100) * $base_price;
        } elseif ($type === 'qty') {
            $balance = $cant;
        }

        return ['balance' => $balance, 'type' => $type];
    }

    private function save()
    {
        try {
            session()->put('propinas', $this->propinas);
            session()->put('methods', $this->methods);
            session()->save();
            $this->totalMethods();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 443217Payment"]);
        }
    }
    private function clear()
    {
        $this->methods = new Collection;
        $this->propinas = new Collection;

        session()->forget('rfcSelected');
        session()->save();

        $this->unsetCustomer();
        $this->itemSelected = null;
        $this->save();
    }

    public function setGiftCard()
    {
        try {
            $cart = session('cartP');
            $GCInCart = $cart != null ? $cart->where('pid', 99999)->count() > 0 : false;
            if (!$GCInCart) {
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
                            $uid = uniqid();
                            $coll = collect([
                                'uid' => $uid,
                                'amount' => $cupon->value_amount,
                                'reference' => $cupon->password,
                                'paymentMethod' => "99999",
                                'isAcumulable' => $cupon->acumulable
                            ]);

                            $this->methods->push($coll);
                            $this->save();
                        } else {
                            $this->dispatchBrowserEvent('noty-error', ['msg' => "Código secreto canjeado."]);
                        }
                    } else {
                        $this->dispatchBrowserEvent('noty-error', ['msg' => "Código secreto no encontrado."]);
                    }
                } else {
                    $this->dispatchBrowserEvent('noty-error', ['msg' => "Código secreto ya está en proceso de canje."]);
                }
            } else {
                $this->dispatchBrowserEvent('noty-error', ['msg' => "No puedes pagar una Giftcard con otra."]);
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41233260Payment"]);
        }
    }
    public function setTip()
    {
        try {
            $empleado = Empleado::where('salon_id', Auth::user()->salon->id)->first();
            $uid = uniqid();
            $coll = collect(
                [
                    'uid' => $uid,
                    'amount' => 0,
                    'reference' => "",
                    'paymentMethod' => "1",
                    'empleado' => $empleado->id ?? null
                ]
            );
            $propina = Arr::add($coll, null, null);
            $this->propinas->push($propina);

            $this->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 473260Payment"]);
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
            }

            $this->propinas = $this->propinas->reject(function ($method) use ($uid) {
                return $method['uid'] === $uid;
            });

            $this->propinas->push(Arr::add($newItem, null, null));
            $this->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 497261Payment"]);
        }
    }
    public function removeTip($uid)
    {
        try {
            $this->propinas = $this->propinas->reject(function ($method) use ($uid) {
                return $method['uid'] === $uid;
            });
            $this->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 557264Payment"]);
        }
    }
    private function loadEmployees()
    {
        try {
            $empleados = Empleado::where('salon_id', Auth::user()->salon->id)->get();
            return $empleados;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 555270Payment"]);
        }
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
