<?php

namespace App\Http\Livewire;

use App\Models\caja_apertura;
use App\Models\coupon;
use App\Models\Empleado;
use App\Models\producto;
use App\Models\venta;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Ventas extends Component
{
    use WithPagination;
    public $search,$taxCart = 0,$total_disccount=0,$totalCartBase=0, $itemsCart, $subtotalCart = 0, $totalCart = 0, $empleados, $disccount=0, $generated_points=0,$totalCorte_neto=0,$query;
    public $description;
    public $cliente;
    public $productos=[];
    public Collection $cartP;
    public $clientes=[],$constrained=0;
    public $cajaChica = 0;
    protected $paginationTheme = 'bootstrap';
    public $cupon,$password;
    public function mount($venta_id=null)
    {
        try{
            $this->clear();

            if($venta_id!=null){
                $this->recibirMovimiento($venta_id,true);
            }
            $this->empleados = Empleado::where('salon_id',Auth::user()->salon_id)->where('visible',1)->where('visible',1)->orderBy('first_name')->get();

        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 3452Ventas"] );
        }
    }
    protected $listeners = [
        'refresh' => '$refresh',
        'search' => 'searching',
        'add-product' => 'addProductFromCart',
        'removeItemCart','enviarDetalles'=>'recibirDetalles',
        'clear-cart' => 'clear','clear-cart-pv' => 'clear','generatedPoints','aperturaCaja',
        'reimpresion','initializeTomSelect','recalculate','enviarMovimiento'=>'recibirMovimiento',
        'GCInCart' => 'inCart','setCajaChica'
    ];
    public function setCajaChica($qty)
    {
        $this->cajaChica = $qty;
    }
    public function aperturaCaja()
    {
        $this->aperturarCaja();
        $this->dispatchBrowserEvent('noty', ['msg' => 'CAJA APERTURADA CON ÉXITO']);
    }
    private function aperturarCaja()
    {
        try{
            $apertura = new caja_apertura;
            $apertura->user_id = Auth()->user()->id;
            $apertura->caja_chica = $this->cajaChica;
            $apertura->save();
            $this->dispatchBrowserEvent('aperturarOk');
            $this->emit('store');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 11456Cortes"] );
        }
    }
    public function recibirMovimiento($venta_id,$editing=false)
    {
        $this->resetPage();
        $this->clear();
        $venta = venta::with('details.product','details.giftCard','propinas','customer','metodosPago')->find($venta_id);
        if($venta->salon_id == Auth::user()->salon_id && $venta!==null){
            foreach($venta->details as $detail){
                $product = $detail->product;
                $uid = $this->AddProduct($product, $detail->quantity,$detail->discount_qty, $detail->iva,$detail->empleado_id,$detail->current_price,$detail->disccount_price,$detail->quantity,$detail->discount_type,$detail->generated_points,$detail->base_comision);
                if($product->id == 99999){
                    $giftCards[$uid] = $detail->giftCard;
                    session()->put('giftCards', $giftCards);
                    session()->save();
                }
            }
            $this->calculateCartMetrics();
            if($editing){
                session()->put('editSale', $venta->id);
                session()->save();
            }else{
                $this->emit('enviarData',$venta);
            }
        }else{        
            return redirect()->to(route('productos'));
        }
    }
    // private function enviarData($venta)
    // {
    //     dd($venta);
    //     if($venta->customer_id!==null){
    //         session()->put('customerId', $venta->customer_id);
    //         session()->save();
    //     }
    //     foreach($venta->metodosPago as $method){
    //         $this->collectMethod($method->amount,$method->reference,$method->payment_method_id,1,null,$method->tipo);
    //     }
    //     foreach($venta->propinas as $propina){
    //         $this->collectMethod($propina->amount,$propina->reference,$propina->payment_method_id,0,$propina->empleado_id,null);
    //     }
    //     session()->put('itemSelected', $venta->id);
    //     session()->save();
    // }
    public function collectMethod($qty,$reference,$paymentMethod,$is_method,$empleado=null,$type=null)
    {
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
        if($is_method){
            session()->put('methods', $this->methods);
            $this->methods->push($method);
        }else{
            $this->propinas->push($method);
        }

    }
    public function recibirDetalles($details)
    {
        $this->resetPage();
        $this->clear();
        foreach($details as $detail){
            $product = $detail->product;
            $this->AddProduct($product, $detail->quantity,$detail->discount_qty, $detail->iva,$detail->empleado_id,$detail->current_price,$detail->disccount_price,$detail->quantity,$detail->discount_type);
        }
    }
    public function initializeTomSelect()
    {
        $this->emit('initialize');
    }

    public function searching($searchText)
    {
        try{
            $this->dispatchBrowserEvent('next');
            $this->search = trim($searchText);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 5853Ventas"] );
        }
    }
    public function loadProducts()
    {
        $this->cargarProductos();
    }
    private function cargarProductos()
    {
        try{
            $this->resetPage();
            if (!empty($this->search)) {
                $product = producto::where(function ($query) {
                        $query->where('name', 'like', "%{$this->search}%")
                            ->orWhere('description', 'like', "%{$this->search}%")
                            ->orWhere('sku', "{$this->search}")
                            ->orWhere('intern_sku', "{$this->search}");
                    })
                    ->where('visibility','visible')
                    ->where('salon_id',Auth::user()->salon->id)
                    ->orderBy('name', 'asc')
                    ->get();
            }else{
                $product='';
            }
            // Verifica si se encontró un producto
            if ($product) {
                // Llama a la función para agregar el producto al carrito
                $this->addProductFromCart($product->id);
                
            }
        
            // Restablece el valor de búsqueda después de agregar el producto
            $this->search = '';
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 7054Ventas"] );
        }
    }
    public function reimpresion()
    {
        $venta = venta::with('metodosPago')->where('status','Pagada')->where('salon_id',Auth::user()->salon_id)->latest('id')->first();
        if(isset($venta)&&$venta!=null){
            $this->imprimirTicket($venta);
        }
    }
    private function imprimirTicket(venta $sale)
    {
        try{
            $this->dispatchBrowserEvent('print_on',['ticket_venta',$sale->id]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 18959Ventas"] );
        }
    }
    function updatedQuery()
    {
        try{
            $query = $this->query;

            $this->productos = producto::where('salon_id', Auth::user()->salon->id)
                ->where('visibility', 'visible')
                ->where('name', '!=', 'Producto eliminado')
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('sku', "{$query}")
                    ->orWhere('intern_sku', "{$query}");
                })
                ->orderBy('name', 'asc')
                ->get();

        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097CartView"] );
        }
    }
    public function render()
    {
        try{
            $isAdmin = Auth::user()->role == 'admin';
            $salonNameIsNull = Auth::user()->salon->name ===null;
            $this->calculateCartMetrics();
            $propinas = session()->has('propinas') ? session('propinas') : new Collection();
            $data = [
                'productos' => $this->loadProducts(), 'isAdmin' => $isAdmin, 'salonNameIsNull' => $salonNameIsNull, 'propinasRecibidas' => $this->totalPropinas(), 'propinas' => $propinas,
            ];
            if($this->constrained){
                session()->has('cartPV') ? $this->cartP= session('cartPV') : $this->cartP = new Collection;
                $data['cartInfo'] = $this->cartP;
                return view('livewire.calendar.ventas.ventas', $data);
            }else{
                session()->has('cartP') ? $this->cartP= session('cartP') : $this->cartP = new Collection;
                $data['cartInfo'] = $this->cartP;
                return view('livewire.ventas', $data);
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 29464Ventas"] );
        }
    }
    private function totalPropinas()
    {
        try{
            if(!session()->has('propinas')){
                return 0;
            }
            $propinas = session('propinas');
            $recibido = 0;
            foreach($propinas as $propina){
                $recibido += $propina['amount'];
            }
            return $recibido;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1108369Ventas"] );
        }
    }
    function removeItemCart($id)
    {
        try{
            $this->cartP = $this->cartP->reject(function ($product) use ($id) {
                return $product['id'] === $id;
            });

            $this->save();

        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 31065Ventas"] );
        }
    }
    public function addProductFromCart($item_id)
    {
        $item = producto::find($item_id);
        $this->AddProduct($item,1,0,$item->iva,Empleado::firstWhere('salon_id',Auth::user()->salon_id)->id);
        $this->calculateCartMetrics();
    }
    private function AddProduct($product, $qty = 1,$disccount_qty=0, $ind_iva=0.16,$empleado=null,$gross_price=null,$disccount_price=0,$qty_inicial=0,$discount_type="Porcentaje",$reward_points=null,$base_comision=0)
    {
        try{
            $cart = session('methods');
            $GCInMethods = $cart != null ? $cart->where('paymentMethod',99999)->count() > 0 : false;
            if(!$GCInMethods){
                // validar si ya existe en el carrito
                if ($this->inCart($product->id) && $product->id != 99999) {
                    $this->updateQty(null, $qty, $product->id);
                    return; // => con esta línea se agrupan los productos por nombre dentro del carrito
                }

                //agregar al carrito

                // iva méxico 16%
                $iva = $ind_iva;
                // determinar precio venta con iva
                
                $salePrice = ($product->disccount_price > 0 && $product->disccount_price < $product->gross_price ?  $product->disccount_price : $product->gross_price);

                if($gross_price){
                    $salePrice = $disccount_price > 0 && $disccount_price < $gross_price ?  floatval($disccount_price) : floatval($gross_price);
                }
                
                if($disccount_qty){
                    if($discount_type=='Porcentaje'){
                        $salePrice = $salePrice-($salePrice*$disccount_qty/100);
                    }elseif($discount_type=='Cantidad'){
                        $salePrice = $salePrice-$disccount_qty;
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

                $pid = $product->id;

                $coll = collect(
                    [
                        'id' => $uid,
                        'pid' => $pid,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'reward_points' => $reward_points ? floatval($reward_points) : floatval($this->calculateRewardPoints($product->id,$total)),
                        'intern_sku' => $product->intern_sku,
                        'gross_price' => $gross_price ? floatval($gross_price) : floatval($product->gross_price),
                        'disccount_price' => $disccount_price > 0 ? floatval($disccount_price) : floatval($product->disccount_price),
                        'disccount_percent' => floatval($disccount_qty),
                        'discount_type' => $discount_type,
                        'sale_price' => $gross_price ? floatval($gross_price) : floatval($salePrice),
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
                $this->cartP->push($itemCart);
                $this->save();

                return $uid;
            }else{
                $this->dispatchBrowserEvent('noty-error', ['msg' => "No puedes pagar una Giftcard con otra."]);
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 32966Ventas"] );
        }
    }
    private function getRewardPoints($excepcion, $total)
    {
        // Asegurar valores por defecto para evitar errores
        $type_comission = $excepcion->type_comission ?? 'default';
        $qty = $excepcion->qty ?? 0;

        // Calcular puntos según el tipo de comisión
        return match ($type_comission) {
            'percent' => ($total * $qty) / 100,
            'qty' => $qty,
            default => 0,
        };
    }
    private function calculateRewardPoints($item_id, $total)
    {
        try{

            $item = producto::with('excepciones', 'categorias.excepciones')
                            ->find($item_id);
        
            // Verificar si el item existe
            if (!$item) {
                return 0; // Retorna 0 si el item no se encuentra
            }
        
            // Buscar excepciones asociadas al item
            $excepcion = $item->excepciones()
                            ->whereNotNull('programa_recompensa_id')
                            ->latest()
                            ->first();
        
            if ($excepcion) {
                return $this->getRewardPoints($excepcion, $total);
            }
        
            // Buscar categorías y excepciones asociadas a las categorías
            $categoria = $item->categorias()->latest()->first();
        
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
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 93634Ventas"] );
        }
    }
    function save()
    {
        try{
            if($this->constrained){
                session()->put('cartPV', $this->cartP);
                $this->emit('productAdded');
            }else{
                session()->put('cartP', $this->cartP);
            }
            session()->save();
            $this->emit('dataChange');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 38967Ventas"] );
        }
    }
    function inCart($product_id)
    {
        try{
            $mycart = $this->cartP;

            $cont = $mycart->where('pid', $product_id)->count();

            return  $cont > 0 ? true : false;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 40068Ventas"] );
        }
    }

    function updateQty($uid, $cant = 1, $product_id = null)
    {
        try{
            if (!is_numeric($cant)) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => $cant . ' NO ES UNA CANTIDAD VÁLIDA']);
                return;
            }

            $mycart = $this->cartP;
            if ($product_id == null) {
                $oldItem = $mycart->where('id', $uid)->first();
            } else {
                $oldItem = $mycart->where('pid', $product_id)->first();
            }

            $newItem  = $oldItem;


            $newItem['qty'] = ($product_id == null ? intval($cant) : intval($newItem['qty'] + $cant));

            $values = $this->Calculator($newItem['disccount_price']>0 && $newItem['sale_price'] > $newItem['disccount_price'] ? $newItem['disccount_price'] : $newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'],$newItem['discount_type'],$newItem['disccount_percent']);
            
            $newItem['tax'] =  $values['iva'];

            $reward_points = $newItem['reward_points'];

            $newItem['reward_points'] = ($newItem['total'] > 0 ? $values['total'] / $newItem['total'] : 0) * $reward_points;

            $newItem['total'] = $values['total'];

            // Encuentra el índice o clave del elemento a reemplazar
            $key = $this->cartP->search(function ($product) use ($uid, $product_id) {
                return $product['id'] === $uid || $product['pid'] === $product_id;
            });

            // Reemplaza el método directamente por la clave encontrada
            if ($key !== false) {
                $this->cartP[$key] = $newItem;
            }
            $this->save();

            $this->emit('dataChange');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41369Ventas"] );
        }
    }
    public function updateTotalCP($uid,$disccount_price=0, $product_id = null)
    {
        try{
        $valorConPorcentaje = $disccount_price;
        $valorSinPorcentaje = trim($valorConPorcentaje, "$");
        $valorNumerico = (int) $valorSinPorcentaje; 
        if(!is_numeric($valorNumerico)){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Corrija el total"] );
            return;
        }else{
            $disccount_price = $valorNumerico;
        }

        $mycart = $this->cartP;
        if ($product_id == null) {
            $oldItem = $mycart->where('id', $uid)->first();
        } else {
            $oldItem = $mycart->where('pid', $product_id)->first();
        }

        $newItem  = $oldItem;

        //Validamos que el precio descuento sea menor al precio público
        //se agrega 0 por default cuando se agrega por primera vez el producto
        //si ya está agregado el producto, toma lo que esté en el input        
        
        if($disccount_price<$newItem['sale_price']){
            $newItem['disccount_price'] = $product_id == null ? floatval($disccount_price) : floatval($newItem['disccount_price'] + $disccount_price);
        }else{
            $newItem['sale_price'] = $product_id == null ? floatval($disccount_price) : floatval($newItem['sale_price'] + $disccount_price);
            $newItem['gross_price'] = $newItem['sale_price'];
        }

        $values = $this->Calculator($disccount_price, $newItem['qty'], $newItem['ind_iva'],$newItem['discount_type'],$newItem['disccount_percent']);
        
        if(!$disccount_price){
            $newItem['$disccount_price']=0;
        }

        $newItem['tax'] =  $values['iva'];

        $newItem['subtotal'] = $values['neto'];

        $reward_points = $newItem['reward_points'];

        $newItem['reward_points'] = ($newItem['total'] > 0 ? $values['total'] / $newItem['total'] : 0) * $reward_points;

        $newItem['total'] = $values['total'];

        // Encuentra el índice o clave del elemento a reemplazar
        $key = $this->cartP->search(function ($product) use ($uid, $product_id) {
            return $product['id'] === $uid || $product['pid'] === $product_id;
        });

        // Reemplaza el método directamente por la clave encontrada
        if ($key !== false) {
            $this->cartP[$key] = $newItem;
        }
        $this->save();

        $this->emit('dataChange');
    }catch(\Throwable $th){
        $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 34220Ventas"] );
    }
    }
    function updatePercentage($uid,$disccount_percent=0, $product_id = null)
    {
        try{
            $valorConPorcentaje = $disccount_percent;
            $valorSinPorcentaje = trim($valorConPorcentaje, "%");
            $valorNumerico = (int) $valorSinPorcentaje; 
            if(!is_numeric($valorNumerico)){
                $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Corrija el porcentaje"] );
                return;
            }else{
                $disccount_percent = $valorNumerico;
            }
            
            $mycart = $this->cartP;
            if ($product_id == null) {
                $oldItem = $mycart->where('id', $uid)->first();
            } else {
                $oldItem = $mycart->where('pid', $product_id)->first();
            }

            $newItem  = $oldItem;

            if(($disccount_percent>=0 && $disccount_percent<=100 && $newItem['discount_type']=='Porcentaje') || ($newItem['discount_type']=='Cantidad' && $disccount_percent>=0 && $disccount_percent<=$this->totalCart)){
    
                //se agrega 0 por default cuando se agrega por primera vez el producto
                //si ya está agregado el producto, toma lo que esté en el input
                $newItem['disccount_percent'] = $product_id == null ? floatval($disccount_percent) : floatval($newItem['disccount_percent'] + $disccount_percent);
    
                $this->disccount=$newItem['disccount_percent'];
    
                $values = $this->Calculator($newItem['disccount_price']>0 && $newItem['sale_price'] > $newItem['disccount_price'] ? $newItem['disccount_price'] : $newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'],$newItem['discount_type'],$newItem['disccount_percent']);
    
                $this->disccount=0;

                $newItem['total'] = $values['total'];
    
                $newItem['tax'] =  $values['iva'];
    
                $newItem['subtotal'] = $values['neto'];
    
                $reward_points = $newItem['reward_points'];
    
                $newItem['reward_points'] = ($newItem['total'] > 0 ? $values['total'] / $newItem['total'] : 0) * $reward_points;
    
                // Encuentra el índice o clave del elemento a reemplazar
                $key = $this->cartP->search(function ($product) use ($uid, $product_id) {
                    return $product['id'] === $uid || $product['pid'] === $product_id;
                });
    
                // Reemplaza el método directamente por la clave encontrada
                if ($key !== false) {
                    $this->cartP[$key] = $newItem;
                }
                $this->save();
    
                $this->emit('dataChange');
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 45870Ventas"] );
        }
    }
    
    public function updateBaseComision($uid,$base_comision)
    {
        try{
            $mycart = $this->cartP;
            $newItem = $mycart->where('id', $uid)->first();

            $newItem['base_comision'] = $base_comision;

            // Encuentra el índice o clave del elemento a reemplazar
            $key = $this->cartP->search(function ($product) use ($uid) {
                return $product['id'] === $uid;
            });

            // Reemplaza el método directamente por la clave encontrada
            if ($key !== false) {
                $this->cartP[$key] = $newItem;
            }

            $this->save();

            $this->emit('dataChange');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 723469Agenda"] );
        }
    }
    public function updatePercentageType($uid,$discount_type)
    {
        try{
            $mycart = $this->cartP;
                $oldItem = $mycart->where('id', $uid)->first();

            $newItem  = $oldItem;

            //se agrega 0 por default cuando se agrega por primera vez el producto
            //si ya está agregado el producto, toma lo que esté en el input
            $newItem['discount_type'] = $discount_type;

            $this->disccount=$newItem['disccount_percent'];

            $values = $this->Calculator($newItem['disccount_price']>0 && $newItem['sale_price'] > $newItem['disccount_price'] ? $newItem['disccount_price'] : $newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'],$newItem['discount_type'],$newItem['disccount_percent']);

            $this->disccount=0;

            $newItem['tax'] =  $values['iva'];

            $newItem['subtotal'] = $values['neto'];

            $reward_points = $newItem['reward_points'];

            $newItem['reward_points'] = ($newItem['total'] > 0 ? $values['total'] / $newItem['total'] : 0) * $reward_points;

            $newItem['total'] = $values['total'];

            // Encuentra el índice o clave del elemento a reemplazar
            $key = $this->cartP->search(function ($product) use ($uid) {
                return $product['id'] === $uid;
            });

            // Reemplaza el método directamente por la clave encontrada
            if ($key !== false) {
                $this->cartP[$key] = $newItem;
            }

            $this->save();

            $this->emit('dataChange');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 45870Ventas"] );
        }
    }
    function updateIva($uid, $selectedIva, $product_id = null)
    {
        try{
            $mycart = $this->cartP;
            if ($product_id == null) {
                $oldItem = $mycart->where('id', $uid)->first();
            } else {
                $oldItem = $mycart->where('pid', $product_id)->first();
            }

            $newItem  = $oldItem;

            $newItem['ind_iva']= $selectedIva;

            $values = $this->Calculator($newItem['disccount_price']>0 && $newItem['sale_price'] > $newItem['disccount_price'] ? $newItem['disccount_price'] : $newItem['sale_price'], $newItem['qty'], $newItem['ind_iva'],$newItem['discount_type'],$newItem['disccount_percent']);
            
            $newItem['tax'] =  $values['iva'];

            $reward_points = $newItem['reward_points'];

            $newItem['reward_points'] = ($newItem['total'] > 0 ? $values['total'] / $newItem['total'] : 0) * $reward_points;

            $newItem['total'] = $values['total'];


            // Encuentra el índice o clave del elemento a reemplazar
            $key = $this->cartP->search(function ($product) use ($uid, $product_id) {
                return $product['id'] === $uid || $product['pid'] === $product_id;
            });

            // Reemplaza el método directamente por la clave encontrada
            if ($key !== false) {
                $this->cartP[$key] = $newItem;
            }
            $this->save();

        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 50871Ventas"] );
        }
    }
    function updateEmpleado($uid, $selectedEmpleado, $product_id = null)
    {
        try{
            $mycart = $this->cartP;
            if ($product_id == null) {
                $oldItem = $mycart->where('id', $uid)->first();
            } else {
                $oldItem = $mycart->where('pid', $product_id)->first();
            }

                
            if (!$oldItem) {
                return; // Manejar el caso en que el ítem no se encuentre en el carrito.
            }
            $oldItem['vendedor'] = $selectedEmpleado;

            // Encuentra el índice o clave del elemento a reemplazar
            $key = $this->cartP->search(function ($product) use ($uid, $product_id) {
                return $product['id'] === $uid || $product['pid'] === $product_id;
            });

            // Reemplaza el método directamente por la clave encontrada
            if ($key !== false) {
                $this->cartP[$key] = $oldItem;
            }
            $this->save();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 54772Ventas"] );
        }

    }

    private function Calculator($price, $qty, $ind_iva, $type, $discount_qty)
    {
        try{
            if($discount_qty){
                if($type=='Porcentaje'){
                    //determinamos el precio de venta(con iva)
                    $calculatedPrice = $price-(($price*$discount_qty)/100);
                }elseif($type=='Cantidad'){
                    //determinamos el precio de venta(con iva)
                    $calculatedPrice = $price-$discount_qty/$qty;
                } 
            }else{
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
            
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 57773Ventas"] );
        }
    }


    function totalIVA()
    {
        try{
            $iva = $this->cartP->sum(function ($product) {
                return $product['tax'];
            });

            return $iva;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 61074Ventas"] );
        }
    }

    function totalCart()
    {
        try{
            $amount = $this->cartP->sum(function ($product) {
                return $product['total'];
                // if($product['discount_type']=='Porcentaje'){
                //     return $product['total']-($product['total']*$product['disccount_percent']/100);
                // }elseif($product['discount_type']=='Cantidad'){
                //     return $product['total']-$product['disccount_percent'];
                // }
            });
            return $amount;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 62375Ventas"] );
        }
    }

    function totalItems()
    {
        try{
            $items = $this->cartP->sum(function ($product) {
                return $product['qty'];
            });
            return $items;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 63576Ventas"] );
        }
    }


    function subtotalCart()
    {
        try{
            $subt = $this->cartP->sum(function ($item) {
                if(isset($item['subtotal'])){
                    return $item['subtotal'];
                    // if($item['discount_type']=='Porcentaje'){
                    //     return $item['subtotal']-($item['subtotal']*$item['disccount_percent']/100);
                    // }elseif($item['discount_type']=='Cantidad'){
                    //     return $item['subtotal']-$item['disccount_percent'];
                    // }
                }else{
                    return $item['total']/($item['ind_iva']+1);
                    // if($item['discount_type']=='Porcentaje'){
                    //     return $subT-($subT*$item['disccount_percent']/100);
                    // }elseif($item['discount_type']=='Cantidad'){
                    //     return $subT-$item['disccount_percent'];
                    // }
                }
            });                
            return $subt;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 64877Ventas"] );
        }
    }

    function generatedPoints()
    {
        try{
            $reward_points = $this->cartP->sum(function ($product) {
                if(isset($product['reward_points'])){
                    $rewP=$product['reward_points'];
                    return $rewP;
                }else{
                    return 0;
                }
            });
            return $reward_points;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 66678Ventas"] );
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
    public function clear()
    {
        try{
            $this->cupon = null;
            $this->cartP = new Collection;
            $this->clearSession(['cartP', 'methods', 'propinas','itemSelected','customerId','editSale','giftCards']);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 68379Ventas"] );
        }
    }
    public function redirectConfiguracion()
    {
        try{

        return redirect()->to(route('configuracion'));
        
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 68379Ventas"] );
        }
    }
    
    public function recalculate()
    {
        $this->total_disccount = $this->totalDisccount();
    }
    private function calculateCartMetrics()
    {
        $this->taxCart = $this->totalIVA();
        $this->itemsCart = $this->totalItems();
        $this->subtotalCart = $this->subtotalCart();
        $this->totalCart = $this->totalCart();
        $this->generated_points = $this->generatedPoints();
        $this->total_disccount = $this->totalDisccount();
        $this->totalCartBase = $this->totalCartBase();
    }
    private function calculateTotalDisccount()
    {
        try{
            $cart = $this->cartP;
            $total_disccount = 0;
            $total_disccount += $cart->sum(function ($item) {
                return ($item['gross_price']*$item['qty'])-($item['total']);
                // if($item['discount_type']=='Porcentaje'){
                //     return $item['gross_price']*$item['qty']-($item['total']-($item['total']*$item['disccount_percent']/100));
                // }elseif($item['discount_type']=='Cantidad'){
                //     return $item['gross_price']*$item['qty']-($item['total']-$item['disccount_percent']);
                // }
            });                
            return $total_disccount;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 936369Ventas"] );
        }
    }
    private function totalDisccount()
    {
        try{
            //recuperamos carrito
            $methods = session('methods');
            $this->total_disccount=0;
            $disccount = 0;
            $restante = $this->totalCart;
            if(isset($methods)){
                foreach($methods as $method){
                    if($method['paymentMethod']=='Descuento'){
                        $disccount += ($method['amount']/100)*$restante;
                    }
                }
            }
            $total_disccount = $this->calculateTotalDisccount();
            $total_disccount=$disccount+$total_disccount;
            return $total_disccount;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1082369Ventas"] );
        }
    }    
    private function totalCartBase()
    {
        try{
            $amount = 0;
            $cart = $this->cartP;
            $amount += $cart->sum(function ($item) {
                return $item['gross_price']*$item['qty'];
            });
            return $amount;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 895369Ventas"] );
        }
    }
    protected $rules = [
        'cupon.password' => "required|min:1|max:255",
        'cupon.value_amount' => "required",
        'cupon.expires_at' => "nullable",
        'cupon.acumulable' => 'required',
    ];

    public function crearCupon()
    {
        try{
            $this->cupon = new coupon();
            $this->cupon->password = substr(bin2hex(random_bytes(4)), 0, 5);
            $this->dispatchBrowserEvent('abrirModalCupon');
            $this->dispatchBrowserEvent('reloadCheck');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 823369Ventas"] );
        }
    }
    public function editarCupon($uid)
    {
        try{
            $giftCards = session('giftCards'); // Convertir a colección
            $card = $giftCards[$uid];

            $this->cupon = $card;
            $this->cupon->asignacion_venta_id = $uid;
            $this->dispatchBrowserEvent('abrirModalCupon');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 823369Ventas"] );
        }
    }
    public function addCupon($uid=null)
    {
        try{
            $giftCards = session('giftCards'); // Convertimos a colección
            if(isset($giftCards[$uid])){
                $giftCards[$uid] = $this->cupon;
            }else{
                $cupon = producto::find(99999);
                $uid = $this->AddProduct($cupon,1,0,0.16,null,$this->cupon->value_amount);
    
                $giftCards[$uid] = $this->cupon;
            }
            session()->put('giftCards', $giftCards);
            session()->save();
            $this->dispatchBrowserEvent('cerrarModalCupon');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2313369Ventas"] );
        }
    }
    public function removeGC($uid)
    {
        
        try{
            $giftCards = session('giftCards'); // Convertimos a colección
            unset($giftCards[$uid]);
            session()->put('giftCards', $giftCards);
            session()->save();
            $this->removeItemCart($uid);
            $this->dispatchBrowserEvent('cerrarModalCupon');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 233429Ventas"] );
        }
    }
    public function searchCupon()
    {
        try{
            $this->dispatchBrowserEvent('abrirModalCupon');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 4329Ventas"] );
        }
    }
}
