<?php

namespace App\Http\Livewire;

use App\Models\cliente;
use App\Models\Empleado;
use App\Models\Material;
use App\Models\producto;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialUso extends Component
{
    use WithPagination;

    public $search, $query=[],$queryMaterial, $queryCust, $empleados, $productos=[], $clientes=[];
    public $customer, $customerId;
    public $type=0,$view=1,$itemSelected=null;
    public Collection $cart,$cartS;
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        if($this->view === 1){

            $this->cart = $this->recuperarCart('cartMaterials');
            if($this->type){
                $this->cartS = $this->recuperarCart('cartS');
            }else{
                $this->cartS = new Collection;
            }
            $this->empleados = Empleado::where('salon_id',Auth::user()->salon_id)->where('visible',1)->orderBy('first_name')->get();
        
        } 
    }
    public function viewDetails($item_id)
    {
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
        $this->dispatchBrowserEvent('viewDetailTransaccion');
    }

    private function recuperarCart($key)
    {
        if (session()->has($key)) {
            return session($key);
        } else {
            return new Collection;
        }

    }

    public function searching($searchText)
    {
        try{
            $this->search = trim($searchText);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 5853Ventas"] );
        }
    }
    public function loadProducts()
    {
        $this->cargarProductos();
    }
    
    public function updateEmpleado($uid, $selectedEmpleado, $item_id = null)
    {
        try{
            $oldItem = $this->setOldItem($item_id,$uid);

            $newItem = $oldItem;
            if (!$oldItem) {
                return; // Manejar el caso en que el ítem no se encuentre en el carrito.
            }
            $newItem['vendedor'] = $selectedEmpleado;

            $this->desvincularElementoAnterior($item_id,$uid,$newItem);

            $this->save();

            
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 766369InformeMovimientos"] );
        }

    }
    private function desvincularElementoAnterior($item_id,$uid,$newItem)
    {
        try{
            
            // Encuentra el índice o clave del elemento a reemplazar
            $key = $this->cart->search(function ($product) use ($uid, $item_id) {
                return $product['id'] === $uid || $product['mid'] === $item_id;
            });

            // Reemplaza el método directamente por la clave encontrada
            if ($key !== false) {
                $this->cart[$key] = $newItem;
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 811369InformeMovimientos"] );
        }
    }
    private function setOldItem($item_id,$uid)
    {
        try{
            $mycart = $this->cart; 

            if ($item_id == null) {
                $oldItem = $mycart->where('id', $uid)->first();
            } else {
                $oldItem = $mycart->where('mid',$item_id)->first();
            }
            return $oldItem;
            
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 787369InformeMovimientos"] );
        }
    }

    private function cargarProductos()
    {
        try{
            $this->resetPage();
            if (!empty($this->search)) {
                $q = $this->search;
                $product = producto::where('salon_id', Auth::user()->salon->id)
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
            }else{
                $product='';
            }
            // Verifica si se encontró un producto
            if ($product) {
                // Llama a la función para agregar el producto al carrito
                $this->addProductFromCart($product);

            }
        
            // Restablece el valor de búsqueda después de agregar el producto
            $this->search = '';
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 7054Ventas"] );
        }
    }
    protected $listeners = [
        'refresh' => '$refresh',
        'search' => 'searching',
        'add' => 'addProductFromCart','updateEmpleado','newCust',
        'removeItemCart', 'updateQty','clear-cart' => 'clear','setCustomerId',
        'enviarCliente' => 'recibirClienteNuevo','cleanFormasPago'
    ];
    public function cleanFormasPago()
    {
        return;
    }
    public function recibirClienteNuevo($cust_id)
    {
        $this->setCustomerId($cust_id);
    }
    public function newCust()
    {
        $this->emit('activateModalForm');
    }

    public function setCustomerId($customer)
    {
        try{
            if($customer!=null){
                $this->customer = cliente::with('tarjetaPuntos')->find($customer);
                $this->customerId = $customer; 
                $this->clientes = [];
            }
        }catch(\Throwable $th){
        }
    }
    public function unsetCustomer()
    {
        $this->clearCliente();
    }
    private function clearCliente()
    {
        $this->customer = null;
        $this->customerId = null;
    }
    public function clear()
    {
        try{
            $this->cart = new Collection;
            session()->forget('cartMaterials');
            $this->clearCliente();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 68379Ventas"] );
        }
    }
    // Función que muestra el listado de productos cuando se consulta en una cita.
    public function mostrarListado($id)
    {
        try{
            if(!isset($this->query[$id])){
                return;
            }

            $q = $this->query[$id];
            $this->productos[$id] = producto::where('salon_id', Auth::user()->salon->id)
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
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097CartView"] );
        }
    }
    // Función que muestra los productos desde la pestaña de Uso en la ruta Productos.
    public function updatedQueryMaterial()
    {
        try{
            $this->productos = [];

            if (!isset($this->queryMaterial)) {
                return;
            }
            
            $q = $this->queryMaterial;
            $this->productos = producto::where('salon_id', Auth::user()->salon->id)
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
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097CartView"] );
        }
    }

    public function updatedQueryCust()
    {
        try{
            $q =$this->queryCust;

            $this->clientes = cliente::where(function ($query) {
                $words = preg_split('/\s+/', trim($this->queryCust));
            
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

        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097MaterialUso"] );
        }
    }

    public function render()
    {
        if($this->view === 1){
            return view('livewire.material-uso',['productos' => $this->productos,'cartInfo' => $this->cart,'cartServices' => $this->cartS]);
        }elseif ($this->view === 2){
            return view('livewire.historial-uso',['usos' => $this->loadUsos()]);
        }
    }

    public function deleteMov()
    {
        $this->cancelarStock();
        $this->dispatchBrowserEvent('noty', ['msg' => "MOVIMIENTO ELIMINADO PERMANENTEMENTE"]);
    }
    public function deleteUso()
    {
        $this->deleteMov();
        foreach($this->itemSelected as $material){
            $mat = Material::find($material['id']);
            $mat->delete();
        }
    }
    private function cancelarStock()
    {
        foreach($this->itemSelected as $material){
            $product = producto::find($material['producto']['id']);
            $product->stock_qty += $material['qty'];
            $product->save();
        }
    }

    private function loadUsos()
    {
        $usos = Material::where('salon_id',Auth::user()->salon_id)
            ->with('producto','customer','empleado','user')
            ->orderBy('created_at','desc')
            ->get()
            ->groupBy(function($item) {
                return $item->asignacion_id  ?? $item->created_at->format('Y-m-d H:i:s');
            });;
            
        return $usos;
    }
    public function addProductFromCart(producto $product,$vendedor=null,$sid=null,$uid=null)
    {
        $this->AddProduct($product,1, $vendedor,$sid,$uid);
    }
    private function inCart($product_id,$sid=null)
    {
        try{
            $mycart = $this->cart;
            if($sid!==null){
                $material = $mycart->where('mid', $product_id)->where('asignacion_id',$sid)->count();
                return  $material > 0 ? true : false;
            }else{
                $cont = $mycart->where('mid', $product_id)->count();
                return  $cont > 0 ? true : false;
            }

        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 40068Ventas"] );
        }
    }
    public function updateQty($uid, $cant = 1, $product_id = null)
    {
        try{
            if (!is_numeric($cant)) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => $cant . ' NO ES UNA CANTIDAD VÁLIDA']);
                return;
            }

            $mycart = $this->cart;
            if ($product_id == null) {
                $oldItem = $mycart->where('id', $uid)->first();
            } else {
                $oldItem = $mycart->where('mid', $product_id)->first();
            }

            $newItem  = $oldItem;

            $newItem['qty'] = $product_id == null ? intval($cant) : intval($newItem['qty'] + $cant);
            
            $this->desvincularElementoAnterior($product_id,$uid,$newItem);
            $this->save();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41369Ventas"] );
        }
    }

    private function AddProduct($product, $qty = 1,$empleado=NULL,$sid=null,$asignacion_id=null)
    {
        try{
            // validar si ya existe en el carrito
            if ($this->inCart($product->id,$sid)) {
                $this->updateQty(null, $qty, $product->id);
                return; // => con esta línea se agrupan los productos por nombre dentro del carrito
            }

            //agregar al carrito

            $uid = uniqid() . $product->id;

            $coll = collect(
                [
                    'uid' => $asignacion_id,
                    'id' => $uid,
                    'mid' => $product->id,
                    'name' => $product->name,
                    'sale_price' => floatval($product->gross_price),
                    'qty' => intval($qty),
                    'stock' => $product->stock_qty,
                    'type' => $product->type_product,
                    'vendedor' => $empleado,
                    'asignacion_id' => $sid
                ]
            );
            $itemCart = Arr::add($coll, null, null);
            $this->cart->push($itemCart);
            $this->save();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 32966Ventas"] );
        }
    }
    function save()
    {
        try{
            session()->put('cartMaterials', $this->cart);
            session()->save();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 38967Ventas"] );
        }
    }
    public function removeItemCart($id)
    {
        try{
            $this->cart = $this->cart->reject(function ($product) use ($id) {
                return $product['id'] === $id;
            });

            $this->save();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 31065Ventas"] );
        }
    }
    public function Store()
    {
        try{
            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }

            $user = Auth::user();
            $salon_id = $user->salon->id;
            if (count($this->cart)<=0) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'NO HAY PRODUCTOS AGREGADOS']);
                return;
            }

            if(count($this->cart)>0){
                foreach($this->cart as $item){
                    $movimiento = new Material;
                    $movimiento->producto_id = $item['mid'];
                    $movimiento->sale_price = $item['sale_price'];
                    $movimiento->qty = $item['qty'];
                    $movimiento->salon_id = $salon_id;
                    $movimiento->user_id = $user->id;
                    $movimiento->empleado_id = $item['vendedor'];
                    $movimiento->cliente_id = $this->customerId;
                    $movimiento->save();
                    $this->ajustarStock($item);
                }
            }

            $this->dispatchBrowserEvent('noty', ['msg' => "SOLICITUD PROCESADA CON ÉXITO"]);
            $this->clear();
            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 31065Ventas"] );
        }
    }
    private function ajustarStock($item)
    {
        try{
            $product = producto::find($item['mid']);
            $product->stock_qty -= $item['qty'];
            $product->save();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1134369InformeMovimientos"] );
        }
    }
}
