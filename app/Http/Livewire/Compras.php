<?php


namespace App\Http\Livewire;

use App\Models\Empleado;
use App\Models\Entrada;
use App\Models\marca;
use App\Models\producto;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Compras extends Component
{

    use WithPagination;

    public $search, $query, $queryMarcas, $empleados, $marcas = [], $productos = [];
    public $product, $productId;
    public $folio_fiscal, $folio_interno, $comentarios, $marca_id, $marca;
    public Collection $cart;
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        session()->has('cartCompras') ? $this->cart = session('cartCompras') : $this->cart = new Collection;
        $this->empleados = Empleado::where('salon_id', Auth::user()->salon_id)->orderBy('first_name')->get();
        $this->unsetMarca();
    }

    public function searching($searchText)
    {
        try {
            $this->search = trim($searchText);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 5853Compras"]);
        }
    }
    public function loadProducts()
    {
        $this->cargarProductos();
    }

    private function desvincularElementoAnterior($item_id, $uid, $newItem)
    {
        try {

            // Encuentra el índice o clave del elemento a reemplazar
            $key = $this->cart->search(function ($product) use ($uid, $item_id) {
                return $product['id'] === $uid || $product['cid'] === $item_id;
            });
            $newItem['subtotal'] = $newItem['cost'] * $newItem['qty'];

            // Reemplaza el método directamente por la clave encontrada
            if ($key !== false) {
                $this->cart[$key] = $newItem;
            }

            $this->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 811369InformeMovimientos"]);
        }
    }
    private function setOldItem($item_id, $uid)
    {
        try {
            $mycart = $this->cart;

            if ($item_id == null) {
                $oldItem = $mycart->where('id', $uid)->first();
            } else {
                $oldItem = $mycart->where('cid', $item_id)->first();
            }
            return $oldItem;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 787369InformeMovimientos"]);
        }
    }

    private function cargarProductos()
    {
        try {
            $this->resetPage();
            if (!empty($this->search)) {

                $search = $this->search;

                $query = producto::basicQuery();
                $product = Productos::searchProduct($query, $search)->orderBy('name', 'asc')->get();
            } else {
                $product = '';
            }
            // Verifica si se encontró un producto
            if ($product) {
                // Llama a la función para agregar el producto al carrito
                $this->addProductFromCart($product);
            }

            // Restablece el valor de búsqueda después de agregar el producto
            $this->search = '';
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 7054Compras"]);
        }
    }
    protected $listeners = [
        'refresh' => '$refresh',
        'search' => 'searching',
        'add-product' => 'addProductFromCart',
        'newProduct',
        'removeItemCart',
        'updateQty',
        'clear-cart' => 'clear',
        'setProductoId',
        'enviarProducto' => 'recibirProductoNuevo',
        'updateCost',
        'setMarcaId',
        'updateIva',
        'openBrandModal',
        'enviarProveedor' => 'setMarcaId'
    ];

    public function openBrandModal()
    {
        $this->dispatchBrowserEvent('openBrandModal');
    }
    protected $rules =
    [
        'comentarios' => "nullable|min:3|max:100",
        'folio_fiscal' => "nullable|min:36|max:36",
        'folio_interno' => "nullable|min:1|max:36",
    ];
    public function setMarcaId($marca_id)
    {
        $this->marca_id = $marca_id;
        $this->marca = marca::find($marca_id);
    }
    public function unsetMarca()
    {
        $this->marca_id = null;
        $this->marca = null;
    }
    public function recibirProductoNuevo($product_id)
    {
        $product = producto::find($product_id);
        $this->addProductFromCart($product);
    }
    public function newProduct()
    {
        $this->emit('createModalForm');
    }

    public function clear()
    {
        try {
            $this->cart = new Collection;
            $this->comentarios = null;
            $this->folio_fiscal = null;
            $this->folio_interno = null;
            session()->forget('cartCompras');
            $this->unsetMarca();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 68379Compras"]);
        }
    }
    public function updatedQuery()
    {
        try {
            $search = $this->query;
            $query = producto::basicQuery();
            $this->productos = Productos::searchProduct($query, $search)->orderBy('name', 'asc')->get();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097Compras"]);
        }
    }
    public function updatedQueryMarcas()
    {
        try {
            $salon_id = Auth::user()->salon->id;
            $this->marcas = marca::where('salon_id', $salon_id)
                ->where('name', 'like', "%{$this->queryMarcas}%")
                ->orWhere('contact_name', 'like', "%{$this->queryMarcas}%")
                ->orWhere('rfc', 'like', "%{$this->queryMarcas}%")
                ->orderBy('name', 'asc')
                ->get();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097CartView"]);
        }
    }
    public function render()
    {
        return view('livewire.compras', ['productos' => $this->loadProducts(), 'cartInfo' => $this->cart]);
    }
    function addProductFromCart(producto $product)
    {
        $this->AddProduct($product);
    }
    private function inCart($product_id)
    {
        try {
            $mycart = $this->cart;

            $cont = $mycart->where('cid', $product_id)->count();

            return  $cont > 0 ? true : false;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 40068Compras"]);
        }
    }
    public function updateQty($uid, $cant = 1, $product_id = null)
    {
        try {
            if (!is_numeric($cant)) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => $cant . ' NO ES UNA CANTIDAD VÁLIDA']);
                return;
            }

            $oldItem = $this->setOldItem($product_id, $uid);

            $newItem  = $oldItem;

            $newItem['qty'] = $product_id == null ? intval($cant) : intval($newItem['qty'] + $cant);

            $newItem['subtotal'] = $newItem['cost'] * $newItem['qty'];

            $this->desvincularElementoAnterior($product_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41369Compras"]);
        }
    }
    public function updateCost($uid, $cost, $product_id = null)
    {
        try {

            $oldItem = $this->setOldItem($product_id, $uid);

            $newItem  = $oldItem;

            $newItem['cost'] = intval($cost);
            $newItem['subtotal'] = $newItem['cost'] * $newItem['qty'];

            $this->desvincularElementoAnterior($product_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41369Compras"]);
        }
    }
    public function updateIva($uid, $iva, $product_id = null)
    {
        try {

            $oldItem = $this->setOldItem($product_id, $uid);

            $newItem  = $oldItem;

            $newItem['iva'] = floatval($iva);

            $this->desvincularElementoAnterior($product_id, $uid, $newItem);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41369Compras"]);
        }
    }
    private function AddProduct($product, $qty = 1, $iva = 0.16)
    {
        try {
            // validar si ya existe en el carrito
            if ($this->inCart($product->id)) {
                $this->updateQty(null, $qty, $product->id);
                return; // => con esta línea se agrupan los productos por nombre dentro del carrito
            }

            //agregar al carrito

            $uid = uniqid() . $product->id;

            $coll = collect(
                [
                    'id' => $uid,
                    'cid' => $product->id,
                    'name' => $product->name,
                    'cost' => floatval($product->cost),
                    'qty' => intval($qty),
                    'iva' => floatval($iva),
                    'subtotal' => $product->cost * $qty,
                    'unit_type' => $product->unit_type,
                ]
            );
            $itemCart = Arr::add($coll, null, null);
            $this->cart->push($itemCart);
            $this->save();
            $this->query = null;
            $this->productos = [];
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 32966Compras"]);
        }
    }
    function save()
    {
        try {
            session()->put('cartCompras', $this->cart);
            session()->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 38967Compras"]);
        }
    }
    public function removeItemCart($id)
    {
        try {
            $this->cart = $this->cart->reject(function ($product) use ($id) {
                return $product['id'] === $id;
            });

            $this->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 31065Compras"]);
        }
    }
    public function Store()
    {
        $this->validate($this->rules);
        try {
            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }

            $user = Auth::user();
            $salon_id = $user->salon->id;
            if (count($this->cart) <= 0) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'NO HAY PRODUCTOS AGREGADOS']);
                return;
            }

            if (count($this->cart) > 0) {
                foreach ($this->cart as $item) {
                    $movimiento = new Entrada;
                    $movimiento->producto_id = $item['cid'];
                    $movimiento->cost = $item['cost'];
                    $movimiento->qty = $item['qty'];
                    $movimiento->iva = floatval($item['iva']);
                    $movimiento->salon_id = $salon_id;
                    $movimiento->user_id = $user->id;
                    $movimiento->marca_id = $this->marca_id;
                    $movimiento->description = $this->comentarios;
                    $movimiento->folio_fiscal = $this->folio_fiscal;
                    $movimiento->folio_interno = $this->folio_interno;
                    $movimiento->save();
                    $this->ajustarStock($item);
                }
            }

            $this->dispatchBrowserEvent('noty', ['msg' => "SOLICITUD PROCESADA CON ÉXITO"]);
            $this->clear();

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 31065Compras"]);
        }
    }
    private function ajustarStock($item)
    {
        try {
            $product = producto::find($item['cid']);
            $product->stock_qty += $item['qty'];
            $product->cost = $item['cost'];
            $product->save();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1134369InformeMovimientos"]);
        }
    }
}
