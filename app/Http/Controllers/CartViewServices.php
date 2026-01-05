<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use App\Models\Empleado;
use App\Models\Material;
use App\Models\servicio;
use App\Models\producto;
use Livewire\WithPagination;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CartViewServices extends Component
{
    public $servicios=[],$productos=[],$searchTerm,$empleados,$service,$serviceSelected,$query,$show=false,$materials=[],$start_date,$end_date,$minutesQty=0,$minutes,$selectedEmpleadoId;

    private $cartInfo, $cartInfoServices;

    public $search;

    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refresh' => '$refresh','rangeSelected','sumDuration','resetQuery','cambiarHorario','setEmpleado','add-product-service' => 'addProduct',
    'search' => 'searching'];

    function setEmpleado($empleadoId)
    {
        try{
            $this->selectedEmpleadoId=$empleadoId;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2499CartViewServices"] );
        }
    }
    function sumDuration($minutes)
    {
        try{
            $this->minutesQty=$minutes;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 32100CartViewServices"] );
        }
    }
    function resetQuery()
    {
        try{
            $this->reset('query');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 40101CartViewServices"] );
        }
    }
    function updatedQuery()
    {
        try{
            $this->servicios= servicio::where('salon_id',Auth::user()->salon->id)->where('name','like',"%{$this->query}%")->orderBy('name')->get()->take(3);
            $this->productos= producto::where('salon_id',Auth::user()->salon->id)->where('name','like',"%{$this->query}%")->orderBy('name')->get()->take(3);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 48102CartViewServices"] );
        }
    }
    function rangeSelected($start,$end)
    {
        try{
            $start = Carbon::parse($start)->locale('es')->format('l d-m-Y H:i');
            $end = Carbon::parse($end);
            
        
            // Asignar las fechas formateadas a las propiedades públicas
            $this->start_date = $start;
            $this->end_date = $end->format('H:i'); // Solo mostrar la hora
            $this->minutes = $this->calcularDiferenciaEnMinutos();
            $this->resetPage();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 56103CartViewServices"] );
        }
    }
    public function render()
    {
        try{
            $this->loadCartFromSession();
            $this->calculateCartMetrics();
            $this->empleados = Empleado::where('salon_id',Auth::user()->salon->id)->orderBy('first_name')->get();
            return view('livewire.cart-view-services',compact('cartInfoServices','cartInfo'));
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 72104CartViewServices"] );
        }
    }
    function mostrarMateriales(servicio $servicio)
    {
        try{
            $this->show = $servicio->id;
            $this->materials = Material::where('salon_id',Auth::user()->salon->id)->where('servicio_id', $servicio->id)->get();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 90105CartViewServices"] );
        }
    }
    function calcularDiferenciaEnMinutos()
    {
        try{
            $start_date= Carbon::parse($this->start_date)->format('H:i');
            // Asegurarse de que las fechas sean instancias de Carbon
            $start = Carbon::parse($start_date);
            $end = Carbon::parse($this->end_date);

            // Calcular la diferencia en minutos
            $diferenciaEnMinutos = $start->diffInMinutes($end);

            // Puedes usar $diferenciaEnMinutos como necesites
            return $diferenciaEnMinutos;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 99106CartViewServices"] );
        }
    }
    function close()
    {
        $this->show=false;
    }
    function cambiarHorario()
    {
        try{
            $this->emit('cambiar-horario',1);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 120107CartViewServices"] );
        }
    }
    function regresarAgenda()
    {
        try{
            $this->emit('regresarAgenda');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 128108CartViewServices"] );
        }
    }

    //Cambios
    
    public function searching($searchText)
    {
        try{
            $this->search = trim($searchText);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 5853Ventas"] );
        }
    }
    
    private function loadProducts()
    {
        try{
            $this->resetPage();
            if (!empty($this->search)) {

                $product = producto::where(function ($query) {
                        $query->where('salon_id',Auth::user()->salon_id)
                            ->where('sku', '=', $this->search)
                            ->orWhere('intern_sku', '=', $this->search);
                    })->first();
            }else{
                $product='';
            }
            // Verifica si se encontró un producto
            if ($product) {
                // Llama a la función para agregar el producto al carrito
                $this->addProduct($product);
                
            }
        
            // Restablece el valor de búsqueda después de agregar el producto
            $this->search = '';
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 7054Ventas"] );
        }
    }
    
    public function removeItem($id)
    {
        try{
            $this->cartPS = $this->cartPS->reject(function ($product) use ($id) {
                return $product['id'] === $id;
            });

            $this->save();

            $this->emit('refresh');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 31065Ventas"] );
        }
    }
    public function addProduct(producto $product)
    {
        $this->AddProducts($product);
    }
    private function AddProducts($product, $qty = 1,$disccount_percent=0, $ind_iva=0.16,$empleado=NULL)
    {
        try{
            // validar si ya existe en el carrito
            if ($this->inCart($product->id)) {
                $this->updateQty(null, $qty, $product->id);
                return; // => con esta línea se agrupan los productos por nombre dentro del carrito
            }

            //agregar al carrito

            // iva méxico 16%
            $iva = $ind_iva;
            // determinar precio venta con iva
            $salePrice = ($product->disccount_price > 0 && $product->disccount_price < $product->gross_price ?  $product->disccount_price : $product->gross_price);
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
                    'gross_price' => floatval($product->gross_price),
                    'disccount_price' => floatval($product->disccount_price),
                    'disccount_percent' => floatval($disccount_percent),
                    'sale_price' => floatval($salePrice),
                    'qty' => intval($qty),
                    'ind_iva' => floatval($ind_iva),
                    'tax' => floatval($tax),
                    'total' => floatval($total),
                    'stock' => $product->stock_qty,
                    'type' => $product->type_product,
                    'vendedor' => $empleado,
                    'platform_id' => $product->platform_id
                ]
            );
            $itemCart = Arr::add($coll, null, null);
            $this->cartPS->push($itemCart);
            $this->save();
            $this->emit('refresh');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 32966Ventas"] );
        }
    }
    private function save()
    {
        try{
            session()->put('cartPS', $this->cartPS);
            session()->save();
            $this->emit('refresh');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 38967Ventas"] );
        }
    }

    private function inCart($product_id)
    {
        try{
            $mycart = $this->cartPS;

            $cont = $mycart->where('pid', $product_id)->count();

            return  $cont > 0 ? true : false;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 40068Ventas"] );
        }
    }

    private function updateQty($uid, $cant = 1, $product_id = null)
    {
        try{
            if (!is_numeric($cant)) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => $cant . ' NO ES UNA CANTIDAD VÁLIDA']);
                return;
            }

            $mycart = $this->cartPS;
            if ($product_id == null) {
                $oldItem = $mycart->where('id', $uid)->first();
            } else {
                $oldItem = $mycart->where('pid', $product_id)->first();
            }


            $newItem  = $oldItem;

            $newItem['qty'] = $product_id == null ? intval($cant) : intval($newItem['qty'] + $cant);

            if($newItem['disccount_price']){
                $values = $this->Calculator($newItem['disccount_price'], $newItem['qty'], $newItem['ind_iva']);    
            }else{
                $values = $this->Calculator($newItem['sale_price'], $newItem['qty'], $newItem['ind_iva']);
            }
            $newItem['tax'] =  $values['iva'];

            $newItem['total'] = $values['total'];


            //eliminar el item de la coleccion / sesion
            $this->cartPS  = $this->cartPS->reject(function ($product) use ($uid, $product_id) {
                return  $product['id'] === $uid || $product['pid'] === $product_id;
            });
            $this->save();

            $this->cartPS->push(Arr::add($newItem, null, null));
            $this->save();

            $this->emit('refresh');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41369Ventas"] );
        }
    }
    private function updatePercentage($uid,$disccount_percent=0, $product_id = null)
    {
        try{
            $mycart = $this->cartPS;
            if ($product_id == null) {
                $oldItem = $mycart->where('id', $uid)->first();
            } else {
                $oldItem = $mycart->where('pid', $product_id)->first();
            }

            $newItem  = $oldItem;

            //se agrega 0 por default cuando se agrega por primera vez el producto
            //si ya está agregado el producto, toma lo que esté en el input
            $newItem['disccount_percent'] = $product_id == null ? intval($disccount_percent) : intval($newItem['disccount_percent'] + $disccount_percent);

            $this->disccount=$newItem['disccount_percent'];

            $values = $this->Calculator($newItem['sale_price'], $newItem['qty'], $newItem['ind_iva']);

            $this->disccount=0;

            if(!$disccount_percent){
                $newItem['$disccount_percent']=0;
            }

            $newItem['tax'] =  $values['iva'];

            $newItem['disccount_price'] = $values['sale_price'];

            $newItem['subtotal'] = $values['neto'];

            $newItem['total'] = $values['total'];


            //eliminar el item de la coleccion / sesion
            $this->cartPS  = $this->cartPS->reject(function ($product) use ($uid, $product_id) {
                return  $product['id'] === $uid || $product['pid'] === $product_id;
            });
            $this->save();

            $this->cartPS->push(Arr::add($newItem, null, null));
            $this->save();

            $this->emit('refresh');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 45870Ventas"] );
        }
    }
    private function updateIva($uid, $selectedIva, $product_id = null)
    {
        try{
            $mycart = $this->cartPS;
            if ($product_id == null) {
                $oldItem = $mycart->where('id', $uid)->first();
            } else {
                $oldItem = $mycart->where('pid', $product_id)->first();
            }

            $newItem  = $oldItem;

            $newItem['ind_iva']= $selectedIva;
            if($newItem['disccount_price']){
                $values = $this->Calculator($newItem['disccount_price'], $newItem['qty'], $newItem['ind_iva']);    
                $newItem['disccount_price'] = $values['sale_price'];
            }else{
                $values = $this->Calculator($newItem['sale_price'], $newItem['qty'], $newItem['ind_iva']);
            }
            $newItem['tax'] =  $values['iva'];

            $newItem['total'] = $values['total'];


            //eliminar el item de la coleccion / sesion
            $this->cartPS  = $this->cartPS->reject(function ($product) use ($uid, $product_id) {
                return  $product['id'] === $uid || $product['pid'] === $product_id;
            });
            $this->save();

            $this->cartPS->push(Arr::add($newItem, null, null));
            $this->save();

            $this->emit('refresh');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 50871Ventas"] );
        }
    }
    private function updateEmpleado($uid, $selectedEmpleado, $product_id = null)
    {
        try{
            $mycart = $this->cartPS;
            if ($product_id == null) {
                $oldItem = $mycart->where('id', $uid)->first();
            } else {
                $oldItem = $mycart->where('pid', $product_id)->first();
            }

                
            if (!$oldItem) {
                return; // Manejar el caso en que el ítem no se encuentre en el carrito.
            }
            $oldItem['vendedor'] = $selectedEmpleado;

            $this->cartPS = $this->cartPS->reject(function ($product) use ($uid, $product_id) {
                return $product['id'] === $uid || $product['pid'] === $product_id;
            });
        
            $this->cartPS->push($oldItem);
        
            $this->save();
            $this->emit('refresh');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 54772Ventas"] );
        }

    }

    private function Calculator($price, $qty, $ind_iva)
    {
        try{
            if($this->disccount){
                //determinamos el precio de venta(con iva)
                $salePrice = $price-(($price*$this->disccount)/100);
            }else{
                //determinamos el precio de venta(con iva)
                $salePrice = $price;
            }
            // precio unitario sin iva
            $precioUnitarioSinIva =  $salePrice / (1 + $ind_iva);
            // subtotal neto
            $subtotalNeto =   $precioUnitarioSinIva * intval($qty);
            //monto iva
            $montoIva = $subtotalNeto  * $ind_iva;
            //total con iva
            $totalConIva =  $subtotalNeto + $montoIva;
            // dd($subtotalNeto);

            return [
                'sale_price' => $salePrice,
                'neto' => $subtotalNeto,
                'iva' => $montoIva,
                'total' => $totalConIva
            ];
            
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 57773Ventas"] );
        }
    }


    private function totalIVA()
    {
        try{
            $iva = $this->cartPS->sum(function ($product) {
                return $product['tax'];
            });

            return $iva;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 61074Ventas"] );
        }
    }

    private function totalCart()
    {
        try{
            $amount = $this->cartPS->sum(function ($product) {
                return $product['total'];
            });
            return $amount;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 62375Ventas"] );
        }
    }

    private function totalItems()
    {
        try{
            $items = $this->cartPS->sum(function ($product) {
                return $product['qty'];
            });
            return $items;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 63576Ventas"] );
        }
    }


    private function subtotalCart()
    {
        try{
            $subt = $this->cartPS->sum(function ($product) {
                if($product['disccount_price']){
                    $subT=($product['qty']*$product['disccount_price'])/($product['ind_iva']+1);
                    return $subT;
                }else{
                    $subT=($product['qty']*$product['sale_price'])/($product['ind_iva']+1);
                    return $subT;
                }
            });
            return $subt;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 64877Ventas"] );
        }
    }

    private function generatedPoints()
    {
        try{
            $reward_points = $this->cartPS->sum(function ($product) {
                if(isset($product['reward_points'])){
                    $rewP=($product['qty']*$product['reward_points']);
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

    private function clear()
    {
        try{
            $this->cartPS = new Collection;
            $this->save();
            $this->emit('refresh');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 68379Ventas"] );
        }
    }
    
    private function calculateCartMetrics()
    {
        $taxCart = $this->totalIVA();
        $itemsCart = $this->totalItems();
        $subtotalCart = $this->subtotalCart();
        $totalCart = $this->totalCart();
        $generated_points = $this->generatedPoints();
        $info = [
            'taxCart' => $taxCart,
            'itemsCart' => $itemCart,
            'subtotalCart' => $subtotalCart,
            'totalCart' => $totalCart,
            'generated_points' => $generated_points,
        ];
        $this->emit('receiveMetricsProducts',$info);
    }
 
    private function loadCartFromSession()
    {
        try{
            $this->cartInfo = session()->get('cartS', new Collection);
            $this->cartInfoServices = session()->get('cartPS', new Collection);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 61432Calendar"] );
        }
    }   
}