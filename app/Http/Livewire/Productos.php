<?php

namespace App\Http\Livewire;

use App\Exports\reporteServicios;
use App\Models\categoria_producto;
use App\Models\excepcion_producto;
use App\Models\File;
use App\Models\producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\calificacion_empleado_cliente;
use App\Models\categoria_cliente;
use App\Models\cliente;

class Productos extends Component
{
    use WithFileUploads;
    use WithPagination;
    public $isService=0;

    public $records, $search, $action =1, $marcas=[], $productSelected, $categoriesList,$percent=0,$finalD=0;

    public producto $product;
    protected $paginationTheme = 'bootstrap';

    public $pestaña=1;
    public $gallery=[],$pictures=[],$categorias;

    public $selectedItems = [],$cat;
    private $servicios;
    public $rewardType=0,$rewardQty;
    public $fromCompras=0;
    public $queryTag,$listCategoriesIds,$categoriasTag=[];
    public $calificacion,$listCategoriesCust;

    public $orderByMostOrLessSelled=null;
    public $orderBy = 'name', $direction = 'asc';

    protected $rules =    [
        'product.name' => "required|min:3|max:60",
        'product.sku' => "nullable|max:25",
        'product.intern_sku' => "nullable|max:25",
        'product.description' => "nullable|max:1000",
        'product.type_product' => "required|in:simple,variable",
        'product.unit_type' => "required|in:Unidad,Mililitro,Ampolleta,Artículo,Onza,Gramo,Envase",
        'product.status' => "required|in:publish,pending,draft",
        'product.visibility' => "required|in:visible,hide",
        'product.gross_price' => "required",
        'product.disccount_price' => "nullable",
        'product.reward_points' => "nullable",
        'product.cost' => "nullable",
        'product.stock_status' => "nullable|in:instock,outofstock,onbackorder",
        'product.manage_stock' => "nullable",
        'product.stock_qty' => "nullable",
        'product.min_stock' => "nullable",
        'product.brand_id' => "nullable",
        'product.iva' => "nullable",
    ];

    public function mount($pestaña=null,$search=null)
    {
        try{
            if(!$this->validateSuscription()){
                return redirect()->route('suscripcion');
            }
            $this->search=trim($search);
            $this->pestaña=$pestaña??1;
            $this->loadDefault();
            $this->cat = null;
            $this->calculateFinalDS();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 51232Productos"] );
        } 
    }
    private function validateSuscription()
    {
        $rights = true;

        $suscription = Auth::user()->salon->suscription;

        if($suscription == 'free') {
            $hoy = Carbon::now();
            $salon = Auth::user()->salon;
            $lastest_suscription = $salon->suscripciones()->latest()->first();
            if($lastest_suscription){
                if($hoy->diffInDays($lastest_suscription) > 7) {
                    $rights = false;
                }
            }else{
                if($hoy->diffInDays($salon->created_at) > 7) {
                    $rights = false;
                }
            }
            
            $rights = false;
        }

        return $rights;
    }

    public function updatedQueryTag()
    {
        try{
            
            $this->categoriasTag = categoria_cliente::where('salon_id', Auth::user()->salon->id)
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->queryTag}%");
            })
            ->orderBy('name', 'asc')
            ->get();      

        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097Agenda"] );
        }
    }
    
    public function reseñaCliente($cliente)
    {
        try{
            $this->dispatchBrowserEvent('abrirModalReseña');
            if(isset($cliente)){
                $cliente = cliente::with('categorias')->find($cliente);
                $categoriesList = $cliente->categorias->pluck('name')->toArray();
                $this->listCategoriesCust = $categoriesList;
                $this->listCategoriesIds = $cliente->categorias->pluck('id')->toArray();
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 926340Ventas"] );
        }
    }
    public function createTag()
    {
        //guardar categoría
        $newCat =  new categoria_cliente;
        $newCat->name = $this->queryTag;
        $newCat->salon_id = Auth::user()->salon_id;
        $newCat->save();
        $this->listCategoriesCust[] = $this->queryTag;
        $this->listCategoriesIds[] = $newCat->id;
        $this->emit('refresh');
    }
    public function addTag($tagId,$name)
    {
        try{
            // Verificamos si el tagId ya está en listCategoriesIds
            if (!in_array($tagId, $this->listCategoriesIds)) {
                $this->listCategoriesCust[] = $name;
                $this->listCategoriesIds[] = $tagId;
                $this->emit('refresh');
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1651350Agenda"] );
        }
    }
    public function StoreReview()
    {
        try{
            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }

            $listCategories = $this->listCategoriesIds;

            $cliente = session('cust');

            $listCategories !== null ? $cliente->categorias()->sync($listCategories) : $cliente->categorias()->detach();

            calificacion_empleado_cliente::create([
                'puntaje' => $this->calificacion,
                'calificado' => 'cliente',
                'cliente_id' => $cliente->id,
                'user_id' => Auth::user()->id 
            ]);
            $this->clearSession(['cust']);
            $this->dispatchBrowserEvent('close-review');
            $this->calificacion = null;
            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }

            if(session()->has('giftCards')){
                return redirect()->route('productos');
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1662351Ventas"] );
        }
    }
    public function eliminarCategoria($categoriaName)
    {
        try{
            $categories = $this->listCategoriesCust;
            // Buscar el índice del ID de la categoría en la lista
            $index = array_search($categoriaName, $this->listCategoriesCust);

            // Si se encuentra el ID, eliminarlo de la lista
            if ($index !== null) {
                unset($categories[$index]);
                $this->categoriesList = array_values($categories);
            }
            
            // Recargar la lista de categorías completas desde la base de datos
            $this->listCategoriesCust= categoria_cliente::whereIn('name', $this->categoriesList)->pluck('name')->toArray();
            $this->listCategoriesIds = categoria_cliente::whereIn('name', $this->categoriesList)->pluck('id')->toArray();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 976342Ventas"] );
        }
    }
    public function actualizarCalificacion($puntaje)
    {
        try{
            $this->calificacion=$puntaje;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 996343Ventas"] );
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
    private function loadDefault()
    {

        $this->marcas = Auth::user()->salon->marcas->where('name','!=','Marca eliminada');
        $this->categorias = Auth::user()->salon->categoriasProductos;
        $this->product = new producto();
        $this->product->type_product = 'simple';
        $this->product->status = 'publish';
        $this->product->visibility = 'visible';
        $this->product->stock_status = 'instock';
        $this->product->manage_stock = 1;
        $this->product->unit_type = 'Unidad';
        $this->product->iva = 0.16;
        $this->product->brand_id = null;
        $this->product->gross_price = 0;
        $this->productSelected = null;
        $this->categoriesList = null;
        $this->gallery=[];
        $this->pictures=[];
        $this->action=1;
        $this->fromCompras = 0;
        $this->calificacion = null;
    }

    protected $listeners = [
        'refresh' => '$refresh','eliminar' => 'Delete',
        'search' => 'searching','categoriaAgregada','createProductFromCompras' => 'Add','actualizarCalificacion',
        'calculateFinalD','SyncAll','calculateP','viewProduct','changeWindow','StoreReview','reseñaCliente'
    ];

    public function toggleItem($itemId)
    {
        if (in_array($itemId, $this->selectedItems)) {
            $this->selectedItems = array_diff($this->selectedItems, [$itemId]);
        } else {
            $this->selectedItems[] = $itemId;
        }
        
        $this->selectedItems = array_values($this->selectedItems); // Reindexar el array

    }
    public function toggleSelectAll($isSelected)
    {
        if ($isSelected) {
            $items = $this->loadProducts(0);
            $this->selectedItems = $items->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function categoriaAgregada()
    {
        $this->categorias = Auth::user()->salon->categoriasProductos;
    }
    public function filtrarCategoria($categoriaId=null)
    {
        $this->cat = $categoriaId;
        $this->orderByMostOrLessSelled = null;
        $this->search = null;
        $this->selectedItems = [];
        $this->resetPage();
    }
    public function orderByMostOrLessSelled($type)
    {
        $this->orderByMostOrLessSelled = $type;
        $this->selectedItems = [];
        $this->resetPage();
    }

    public function changeWindow($tipo,$movimiento_id=null)
    {
        $this->pestaña = $tipo;
        $this->emit('refresh');
        if($movimiento_id!=null){
            $this->emit('enviarMovimiento',$movimiento_id);
        }
    }
    public function render()
    {
        try{
            return view('livewire.productos.productos',[
                'productos' => $this->loadProducts(),'categoriasCliente' => $this->listCategoriesCust
                // 'finalD' => $this->product->disccount_price,
            ]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 78233Productos"] );
        } 
    }
    public function updatedPercent()
    {
        $this->calculateFinalD();
    }
    function loadProducts($wP=1)
    {

        try{
            $visibility = $this->orderByMostOrLessSelled === 'archives' ? 'hide' : 'visible';
            $query = producto::with('marca','categorias','asignaciones')
                ->where('productos.visibility',$visibility)
                ->where('productos.salon_id', Auth::user()->salon->id)
                ->where('productos.name','!=','Producto eliminado');

            // Si hay una categoría, filtrarla usando whereHas
            if ($this->cat != null) {
                $cat = $this->cat;
                $query->whereHas('categorias', function ($q) use ($cat) {
                    $q->where('categoria_productos.id', $cat);
                });
            }
            
            // Si hay búsqueda, agregar las condiciones
            if (!empty($this->search)) {
                $query->where(function ($q) {
                    $q->where('productos.name', 'like', "%{$this->search}%")
                    ->orWhere('productos.sku', 'like', "%{$this->search}%")
                    ->orWhere('productos.description', 'like', "%{$this->search}%");
                });
            }

            // Ordenar por más o menos vendidos
            if($this->orderByMostOrLessSelled && $this->orderByMostOrLessSelled !== 'archives'){ 
                if($this->orderByMostOrLessSelled=='noSales'){
                    $query = $query->withSum('asignaciones', 'quantity')
                        ->havingRaw('asignaciones_sum_quantity IS NULL');
                }else{
                    $query = $query->withSum('asignaciones', 'quantity')
                        ->having('asignaciones_sum_quantity', '>', 0)
                        ->orderBy('asignaciones_sum_quantity',$this->orderByMostOrLessSelled);
                }
            }else{
                if ($this->orderBy == "marca.name") {
                    $query = $query->join('marcas', 'productos.brand_id', '=', 'marcas.id')
                        ->select('productos.*', 'marcas.name as marca_name')
                        ->orderBy('marca_name', $this->direction);
                } else {
                    $query = $query->orderBy($this->orderBy, $this->direction);
                }
            }

            // Si $wP es verdadero, paginar y contar los registros
            if ($wP) {
                $query = $query->paginate(8);
                $this->records = $query->total();
            // Si no hay paginación, obtener todos los resultados sin paginar
            } else {
                $query = $query->get();
            }
            return $query;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 94234Productos"] );
        } 
    }
    public function orderBy($by, $direction)
    {
        $this->orderBy = $by;
        $this->direction = $direction;
        $this->orderByMostOrLessSelled = null;
        $this->selectedItems = [];
        $this->resetPage();
    }

    public function searching($searchText)
    {
        $this->search = trim($searchText);
    }

    public function Add($fromCompras=0){
        $this->resetValidation();
        $this->resetExcept('product','marcas','categorias');
        $this->loadDefault();
        $this->dispatchBrowserEvent('createProduct' );
        if($fromCompras){
            $this->pestaña = 4;
            $this->fromCompras = $fromCompras;
        }
    }
    public function Delete()
    {
        foreach($this->selectedItems as $producto){
            $this -> destroy($producto);
        }
        $this->loadDefault();
        
        $this->emit('refresh');
        $this->dispatchBrowserEvent('noty',['msg'=>'SOLICITUD PROCESADA CON ÉXITO']);
    }

    public function Edit($id=null){
        try{
            $this->loadDefault();
            $product = producto::find($id ?? $this->selectedItems[0]);
            $this->categoriesList = implode(", ", $product->categorias->pluck('name')->toArray());
            $this->pictures = $product->photos;
            $this->resetValidation();
            $this->product = $product;
            $this->dispatchBrowserEvent('createProduct');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 135235Productos"] );
        } 
    }

    public function cancelEdit()
    {
        $this->resetValidation();
        $this->resetExcept(['product', 'marcas','categorias']);
        $this->categoriesList = '';
        $this->product = new producto();
        $this->product->type = 'simple';
        $this->product->status = 'publish';
        $this->product->visibility = 'visible';
        $this->product->stock_status = 'instock';
        $this->product->manage_stock = 1;
        $this->product->brand_id = null;
        $this->action = 1;
        $this->dispatchBrowserEvent('reset-tom-select');
    }

    function viewProduct(producto $product)
    {
        $this->productSelected = $product;
        $this->dispatchBrowserEvent('view-product', ['id' => $this->productSelected]);
    }
    function calculateFinalD()
    {
        try{
            if($this->percent){
                $disccountP=floatval($this->product->gross_price)-(floatval($this->product->gross_price)*floatval($this->percent/100));
                $this->product->disccount_price=floatval($disccountP);
                $this->dispatchBrowserEvent('next');
                return $this->finalD;
            }
            return;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 169236Productos"] );
        } 
    }

    function calculateP()
    {
        $this->finalD=null;
    }
    public function removeFile($filename,$fromGallery)
    {
        try{
            if($fromGallery){
                // Filtrar el arreglo para eliminar el archivo con el nombre coincidente
                $this->gallery = array_filter($this->gallery, function($file) use ($filename) {
                    return $file->getFilename() !== $filename;
                });
            }else{
                // Filtrar la colección para eliminar el archivo con la ruta coincidente
                $this->pictures = $this->pictures->filter(function ($picture) use ($filename) {
                    return $picture !== $filename;
                });
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 115459Agenda"] );
        }
    }   
    function Store($enviar=0)
    {
        $this->validate($this->rules);
        try{
            
            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }

            $this->product->disccount_price = $this->product->disccount_price ? $this->product->disccount_price : null;
            $this->product->disccount_price = $this->product->disccount_price != '' ? $this->product->disccount_price: null;
            $this->product->salon_id=Auth::user()->salon->id;
            $this->product->save();

            $listCategories = null;
            if ($this->categoriesList != null)  $listCategories =  explode(",", $this->categoriesList);


            //relacionar categorias         
            if ($listCategories != null) {

                if ($listCategories != null) {

                    $listCategories = array_map(function ($item) {
                        $catName = trim($item);
                        // verificar si el elemento no es numérico
                        if (!is_numeric($catName)) {
                            // buscar el ID de la categoría en la tabla correspondiente
                            $categoria = categoria_producto::where('name', $catName)->where('salon_id',Auth::user()->salon->id)->first();
                            // reemplazar el elemento con el ID de la categoría si existe
                            if ($categoria) {
                                return $categoria->id;
                            }
                        }

                        // devolver el elemento sin cambios              
                        return $item;
                    }, $listCategories);
                }
                $listCategories !== null ? $this->product->categorias()->sync($listCategories) : $this->product->categorias()->detach();
            }


            // Rutina para eliminar los archivos que ya no se encuentren en el arreglo de pictures
            if(isset($this->product->files)){
                $this->deleteFiles($this->product->files);
            }
            
            // Rutina que guarda los nuevos archivos subidos
            if (!empty($this->gallery)) {

                // guardar imagenes nuevas
                foreach ($this->gallery as $file) {
                    $fileName = uniqid() . '_.' . $file->extension();
                    $file->storeAs('public/productos', $fileName);

                    // creamos relacion
                    $img = File::create([
                        'model_id' => $this->product->id,
                        'model_type' => 'App\Models\producto',
                        'file' => $fileName
                    ]);

                    // guardar relacion
                    $this->product->files()->save($img);
                }
            }

            // //sync con woocommerce
            // // if ($this->action == 2) $this->createProduct($this->product);
            // // if ($this->action == 3) $this->updateProduct($this->product);
            // $this->action == 2 ? $this->createProduct($this->product) : ($this->action == 3 ? $this->updateProduct($this->product) : null);

            if($this->fromCompras){
                $this->emit('enviarProducto',$this->product->id);
                $this->dispatchBrowserEvent('closeCreateModalForm');
                return;
            }



            $this->loadDefault();
            $this->emit('refresh');
            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
            $this->resetExcept(['product', 'marcas','categorias']);
            $this->dispatchBrowserEvent('closeCreateModalForm');

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 187237Productos"] );
        } 

    }
    private function deleteFiles($files)
    {
        try{
            foreach($files as $file) {
                $found=false;
                $filename = 'storage/productos/' . $file->file; 
                foreach($this->pictures as $picture){
                    if($filename == $picture){
                        $found = true;
                    }
                }
                
                if(!file_exists($filename)){
                    $this->dispatchBrowserEvent('noty-error', ['msg'=> 'ARCHIVO NO ENCONTRADO']);
                }
                
                if(!$found){
                    unlink($filename);
                    $file->delete();
                }
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 545Productos"] );
        }
    }
    public function destroy($productoId)
    {
        try{

            $producto = producto::with('categorias', 'asignaciones', 'excepciones')->find($productoId);

            // Desvincula las categorías y excepciones del producto
            $producto->categorias()->detach();
            $producto->excepciones()->delete();

            //eliminar el archivo físicamente    ----------------------------        
            $producto->files()->each(function ($img){
                unlink('storage/productos/' . $img->file);
            });
            //Eliminar archivo de la base de datos
            $producto->files()->delete();
            // Verifica si tiene asignaciones sin cargar todas las relaciones
            if ($producto->asignaciones->count() > 0 || $producto->entradas->count() > 0 || $producto->salidas->count() > 0) {
                // Si tiene asignaciones, solo cambia el nombre y guarda
                $producto->name = "Producto eliminado";
                $producto->sku = null;
                $producto->save();
            } else {
                // Si no tiene asignaciones, desvincula y elimina el producto
                $producto->delete();
            }
            $this->selectedItems = [];
            
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 297238Productos"] );
        }
    }

    public function joinGroup($categoryId)
    {
        try{
            foreach($this->selectedItems as $producto){
                $service = producto::with('categorias')->find($producto);
                $service->categorias()->syncWithoutDetaching([$categoryId]);
            }
            $this->emit('refresh');
            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 93Productos"] );
        }
    }
    
    public function archiveItem($status='hide')
    {
        try{
            foreach($this->selectedItems as $producto){
                $item = producto::find($producto);
                $item->visibility = $status;
                $item->save();
            }
            $this->resetPage();
            $this->selectedItems = [];
            $this->dispatchBrowserEvent("noty", ["msg"=> "SOLICITUD PROCESADA CON ÉXITO"]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent("noty-error", ['msg' => 'Código de error: 123432Productos'] );
        }
    }
    public function recalculateReward()
    {
        try{
            $this->rewardQty = $this->eliminarCaracteres($this->rewardQty);

            $comision = Auth::user()->salon->recompensaGeneral;
            
            foreach($this->selectedItems as $item)
            {
                $excepcion= new excepcion_producto();
                $excepcion->producto_id=$item;
                $excepcion->qty=$this->rewardQty;
                
                if(!$this->rewardType){
                    $excepcion->type_comission='percent';
                }else{
                    $excepcion->type_comission='qty';
                }

                if($comision){
                    $excepcion->programa_recompensa_id=$comision->id;
                }else{
                    $comision->salon_id = Auth::user()->salon_id;
                    $comision->save();
                    $excepcion->programa_recompensa_id=$comision->id;
                }
                $excepcion->save();
            }
            $this->dispatchBrowserEvent('noty', ['msg' => 'SOLICITUD PROCESADA CON ÉXITO']);
            $this->dispatchBrowserEvent('hideModalRewardForm');
            $this->loadDefault();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 9231Productos"] );
        }
    }
    private function eliminarCaracteres($data)
    {
        try{
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
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 51312Agenda"] );
        }
    }
    public function exportar()
    {
        try{
            $cat = null;
            $productos = $this->loadProducts(0);
            if($this->cat!=null){
                $cat = categoria_producto::find($this->cat);
                $cat = $cat->name;
            }
            $date = Carbon::now()->format('Y_m_d_H_i_s');
            $fileName = 'productos_' . $date . '.xlsx';
            return Excel::download(new reporteServicios($productos,$cat,'productos'),$fileName);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 497369Cliente"] );   
        }
    }

    // function Sync(producto $product){
    //     try{
    //         $this->findOrCreateProductByName($product);
    //     }catch(\Throwable $th){
    //         $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 324239Productos"] );
    //     }
    // }

    // function SyncAll(){
    //     try{
    //         $productos = producto::all();
    //         $this->resetPage();
    //         foreach($productos as $product){
    //             $this->sync($product);
    //         }
    //         $this->dispatchBrowserEvent('noty',['msg'=>'SINCRONIZACIÓN COMPLETA']);
    //     }catch(\Throwable $th){
    //         $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 332240Productos"] );
    //     }
    // }
}
