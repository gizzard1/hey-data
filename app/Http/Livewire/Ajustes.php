<?php

namespace App\Http\Livewire;

use App\Models\categoria_producto;
use App\Models\categoria_servicio;
use App\Models\Comision;
use App\Models\Empleado;
use App\Models\excepcion_cat_cliente;
use App\Models\excepcion_cat_producto;
use App\Models\excepcion_cat_servicio;
use App\Models\excepcion_cliente;
use App\Models\excepcion_producto;
use App\Models\excepcion_servicio;
use App\Models\producto;
use App\Models\recompensas_producto;
use App\Models\recompensas_servicio;
use App\Models\servicio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Ajustes extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $categoriasP, $categoriasS, $categoriesList, $categoriesListP, $empleados, $productos, $viewProducts = true, $viewServices, $action = 1, $excepcion, $listado = [], $query, $tipoExc, $qty, $tipo, $creating = false, $comision, $selectedItem, $empleado;
    public $pestaña = 2;
    public recompensas_producto $recompensaP;
    public recompensas_servicio $recompensaS;
    public Comision $comisionGen;

    public $infoSelected = 1;
    public $editing = false, $exceptionToEdit = null;


    protected $rules =    [
        'query' => "nullable",
        'comisionGen.qty_s' => "nullable",
        'comisionGen.qty_p' => "nullable",
        'comisionGen.type_comission_p' => "nullable|in:percent,qty",
        'comisionGen.type_comission_s' => "nullable|in:percent,qty",
    ];

    public function cancelGen()
    {
        $this->comisionGen = new Comision;
        $this->comisionGen->type_comission_p = 'percent';
        $this->comisionGen->type_comission_s = 'percent';
    }
    public function StoreGeneralException()
    {
        $this->validate($this->rules);
        try {

            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }
            $comision = $this->comision;
            $comision->qty_s = $this->comisionGen->qty_s != '' ? $this->comisionGen->qty_s : 0;
            $comision->qty_p = $this->comisionGen->qty_p != '' ? $this->comisionGen->qty_p : 0;
            $comision->type_comission_p = $this->comisionGen->type_comission_p;
            $comision->type_comission_s = $this->comisionGen->type_comission_s;
            $comision->save();
            $this->dispatchBrowserEvent('noty', ['msg' => 'COMISION GENERADA']);

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 4880Ajustes"]);
        }
    }

    public function cambiarModalP()
    {
        $this->action = 2;
    }
    public function cambiarModalS()
    {
        $this->action = 3;
    }
    private function loadGenComision()
    {
        try {
            if (isset($this->empleado->comision)) {
                $this->comisionGen->qty_s = $this->empleado->comision->qty_s;
                $this->comisionGen->qty_p = $this->empleado->comision->qty_p;
                $this->comisionGen->type_comission_p = $this->empleado->comision->type_comission_p;
                $this->comisionGen->type_comission_s = $this->empleado->comision->type_comission_s;
            } else {
                $this->comisionGen = new Comision;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 7281Ajustes"]);
        }
    }
    private function loadDefault()
    {
        try {
            $this->editing = false;
            $this->exceptionToEdit = null;
            $this->excepcion = null;

            $this->tipoExc = 'percent';
            $this->listado = [];
            $this->selectedItem = null;
            $this->tipo = false;
            $this->creating = false;
            $this->query = '';
            $this->qty = null;
            $this->empleados = Empleado::with('comision')->where('salon_id', Auth::user()->salon->id)->where('visible', 1)->orderBy('first_name')->get();
            $this->loadGenComision();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 8782Ajustes"]);
        }
    }
    // private function validateReward()
    // {
    //     try{
    //         if(!$this->recompensaS->where('salon_id',Auth::user()->salon->id)->first()){
    //             $this->recompensaS->percent = '0';
    //             $this->recompensaS->to_all = false;
    //             $this->categoriasS = categoria_servicio::orderBy('name')->where('salon_id',Auth::user()->salon->id)->get();
    //         }else{
    //             $this->recompensaS=$this->recompensaS->where('salon_id',Auth::user()->salon->id)->first();
    //             $this->categoriesList = implode(", ", $this->recompensaS->categorias->pluck('name')->toArray());
    //         }    

    //         if(!$this->recompensaP->where('salon_id',Auth::user()->salon->id)->first()){
    //             $this->recompensaP->percent = '0';
    //             $this->recompensaP->to_all = false;
    //             $this->categoriasP = categoria_producto::orderBy('name')->where('salon_id',Auth::user()->salon->id)->get();
    //         }else{
    //             $this->recompensaP=$this->recompensaP->where('salon_id',Auth::user()->salon->id)->first();
    //             $this->categoriesListP = implode(", ", $this->recompensaP->categorias->pluck('name')->toArray());
    //         }    
    //     }catch(\Throwable $th){
    //         $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 10083Ajustes"] );
    //     }
    // }
    public function mount()
    {
        if (!$this->validateSuscription()) {
            return redirect()->route('suscripcion');
        }
        // $this->loadGenComision();
        $this->loadDefault();
    }

    private function validateSuscription()
    {
        $rights = true;

        $suscription = Auth::user()->salon->suscription;

        if ($suscription == 'free') {
            $hoy = Carbon::now();
            $salon = Auth::user()->salon;
            $lastest_suscription = $salon->suscripciones()->latest()->first();
            if ($lastest_suscription) {
                if ($hoy->diffInDays($lastest_suscription) > 7) {
                    $rights = false;
                }
            } else {
                if ($hoy->diffInDays($salon->created_at) > 7) {
                    $rights = false;
                }
            }

            $rights = false;
        }

        return $rights;
    }
    // function ApplyChangeP()
    // {
    //     try{
    //         $this->resetPage();
    //         if($this->recompensaP->to_all){
    //             $products = producto::where('salon_id',Auth::user()->salon->id)->get();
    //         }elseif($this->recompensaP->categorias){
    //             $products = collect();
    //             foreach ($this->recompensaP->categorias as $categoria) {
    //                 $categoriaProducts = $categoria->productos()->get();
    //                 $products = $products->merge($categoriaProducts);
    //             }
    //         }else{
    //             $this->dispatchBrowserEvent('noty-error',['msg'=>'NO SE HA SELECCIONADO UN MÉTODO DE AJUSTE']);
    //             return;
    //         }
    //         foreach($products as $product){
    //             $price=$product->gross_price;
    //             $calculatedReward=$price*(min(floatval($this->recompensaP->percent),100)/100);
    //             $product->reward_points=$calculatedReward;
    //             $product->save();
    //         }
    //         $this->dispatchBrowserEvent('stop-loader');
    //         $this->dispatchBrowserEvent('noty',['msg'=>'POCENTAJE APLICADO']);
    //     }catch(\Throwable $th){
    //         $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 13184Ajustes"] );
    //     }
    // }
    // function ApplyChangeS()
    // {
    //     try{
    //         $this->resetPage();
    //         if($this->recompensaS->to_all){
    //             $services = servicio::where('salon_id',Auth::user()->salon->id)->get();
    //         }elseif($this->recompensaS->categorias){
    //             $services = collect();
    //             foreach ($this->recompensaS->categorias as $categoria) {
    //                 $categoriaServices = $categoria->servicio()->get();
    //                 $services = $services->merge($categoriaServices);
    //             }
    //         }
    //         else{
    //             $this->dispatchBrowserEvent('noty-error',['msg'=>'NO SE HA SELECCIONADO UN MÉTODO DE AJUSTE']);
    //             return;
    //         }
    //         foreach($services as $service){
    //             $price=$service->gross_price;
    //             $calculatedReward=$price*(min(floatval($this->recompensaS->percent),100)/100);
    //             $service->reward_points=$calculatedReward;
    //             $service->save();
    //         }
    //         $this->dispatchBrowserEvent('stop-loader');
    //         $this->dispatchBrowserEvent('noty',['msg'=>'POCENTAJE APLICADO']);
    //     }catch(\Throwable $th){
    //         $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 15985Ajustes"] );
    //     }
    // }
    protected $listeners = ['refresh' => '$refresh', 'Add', 'Delete', 'changeWindow', 'infoSelected', 'recorrido'];

    public function infoSelected($window)
    {
        $this->infoSelected = $window;
        $this->emit('refresh');
    }
    public function render()
    {
        try {
            return view('livewire.ajustes.ajustes');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 19086Ajustes"]);
        }
    }
    function cancelRP()
    {
        try {
            if ($this->recompensaP->id) {
                $recompensaP = recompensas_producto::where('salon_id', Auth::user()->salon->id)->first();
                $recompensaP->categorias()->detach();
                $recompensaP->delete();
                $this->resetPage();

                $this->dispatchBrowserEvent('noty', ['msg' => 'POCENTAJE ELIMINADO - REINICIE LA PÁGINA PARA EFECTUAR CAMBIOS']);
                $this->dispatchBrowserEvent('stop-loader');
                return;
            }
            $this->dispatchBrowserEvent('noty-error', ['msg' => 'NO HAY PORCENTAJE ALMACENADO - REINICIE LA PÁGINA PARA EFECTUAR CAMBIOS']);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 19887Ajustes"]);
        }
    }

    function cancelRS()
    {
        try {
            if ($this->recompensaS->id) {
                $recompensa = recompensas_servicio::where('salon_id', Auth::user()->salon->id)->first();
                $recompensa->categorias()->detach();
                $recompensa->delete();
                $this->resetPage();

                $this->dispatchBrowserEvent('noty', ['msg' => 'POCENTAJE ELIMINADO - REINICIE LA PÁGINA PARA EFECTUAR CAMBIOS']);
                $this->dispatchBrowserEvent('stop-loader');
                return;
            }
            $this->dispatchBrowserEvent('noty-error', ['msg' => 'NO HAY PORCENTAJE ALMACENADO - REINICIE LA PÁGINA PARA EFECTUAR CAMBIOS']);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 21788Ajustes"]);
        }
    }


    function StoreService()
    {
        $this->validate($this->rules);
        try {
            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }

            if ($this->recompensaS->percent > 100 || $this->recompensaS->percent < 0) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'EL PORCENTAJE DEBE ESTAR EN UN RANGO DE 0-100%']);
                return;
            }

            $this->recompensaS->salon_id = Auth::user()->salon->id;
            $this->recompensaS->save();

            $listCategories = null;
            if ($this->categoriesList != null)  $listCategories =  explode(",", $this->categoriesList);

            if ($listCategories != null) {

                $listCategories = array_map(function ($item) {
                    $catName = trim($item);
                    // verificar si el elemento no es numérico
                    if (!is_numeric($catName)) {
                        // buscar el ID de la categoría en la tabla correspondiente
                        $categoria = categoria_servicio::where('name', $catName)->where('salon_id', Auth::user()->salon->id)->first();
                        // reemplazar el elemento con el ID de la categoría si existe
                        if ($categoria) {
                            return $categoria->id;
                        }
                    }

                    // devolver el elemento sin cambios              
                    return $item;
                }, $listCategories);
            }
            $listCategories !== null ? $this->recompensaS->categorias()->sync($listCategories) : $this->recompensaS->categorias()->detach();

            $this->dispatchBrowserEvent('noty', ['msg' => 'PORCENTAJE GUARDADO - NO OLVIDES APLICAR PARA CALCULAR']);

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 23789Ajustes"]);
        }
    }
    function StoreProduct()
    {
        $this->validate($this->rules);
        try {
            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }

            if ($this->recompensaP->percent > 100 || $this->recompensaP->percent < 0) {
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'EL PORCENTAJE DEBE ESTAR EN UN RANGO DE 0-100%']);
                return;
            }
            $this->recompensaP->salon_id = Auth::user()->salon->id;
            $this->recompensaP->save();

            $listCategories = null;
            if ($this->categoriesListP != null)  $listCategories =  explode(",", $this->categoriesListP);

            if ($listCategories != null) {

                $listCategories = array_map(function ($item) {
                    $catName = trim($item);
                    // verificar si el elemento no es numérico
                    if (!is_numeric($catName)) {
                        // buscar el ID de la categoría en la tabla correspondiente
                        $categoria = categoria_producto::where('name', $catName)->where('salon_id', Auth::user()->salon->id)->first();
                        // reemplazar el elemento con el ID de la categoría si existe
                        if ($categoria) {
                            return $categoria->id;
                        }
                    }

                    // devolver el elemento sin cambios              
                    return $item;
                }, $listCategories);
            }
            $listCategories !== null ? $this->recompensaP->categorias()->sync($listCategories) : $this->recompensaP->categorias()->detach();

            $this->dispatchBrowserEvent('noty', ['msg' => 'PORCENTAJE GUARDADO - NO OLVIDES APLICAR PARA CALCULAR']);

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 27690Ajustes"]);
        }
    }
    public function View(Empleado $empleado)
    {
        try {
            $this->empleado = $empleado;
            $this->pestaña = 2;
            if ($this->empleado) {
                $this->comision = $this->empleado->comision;
                $this->action = 2;
            } else {
                $this->dispatchBrowserEvent('noty-error', ['msg' => 'ERROR - NO SE ENCUENTRA EL EMPLEADO']);
            }
            $this->loadGenComision();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 31491Ajustes"]);
        }
    }

    public function Add($tipo)
    {
        try {
            $this->creating = true;
            $this->tipo = $tipo;
            $this->dispatchBrowserEvent('openCreateException');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 33192Ajustes"]);
        }
    }
    public function updatedQuery()
    {
        try {
            switch ($this->tipo) {
                case 1:
                    $search = $this->query;

                    $query = producto::basicQuery();
                    $this->listado = Productos::searchProduct($query, $search)->orderBy('name', 'asc')->get();

                    break;
                case 2:

                    $search = $this->query;

                    $query = servicio::basicQuery();

                    $this->listado = Servicios::searchService($query, $search)->orderBy('name', 'asc')->get();

                    break;
                case 3:
                    $this->listado = categoria_producto::where('name', 'like', "%{$this->query}%")->where('salon_id', Auth::user()->salon->id)->orderBy('name')->get();
                    break;
                case 4:
                    $this->listado = categoria_servicio::where('name', 'like', "%{$this->query}%")->where('salon_id', Auth::user()->salon->id)->orderBy('name')->get();
                    break;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 34293Ajustes"]);
        }
    }
    function cancel()
    {
        $this->creating = false;
        $this->loadDefault();
    }
    function selectedItem($itemId)
    {
        try {
            switch ($this->tipo) {
                case 1:
                    $this->selectedItem = producto::find($itemId);
                    break;
                case 2:
                    $this->selectedItem = servicio::find($itemId);
                    break;
                case 3:
                    $this->selectedItem = categoria_producto::find($itemId);
                    break;
                case 4:
                    $this->selectedItem = categoria_servicio::find($itemId);
                    break;
            }
            $this->query = $this->selectedItem->name;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 35994Ajustes"]);
        }
    }
    public function StoreException()
    {
        $rules = [
            'query' => "nullable",
            'qty' => "required|numeric",
            'tipoExc' => 'required|in:percent,qty'
        ];

        $this->validate($rules);
        try {

            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }
            switch ($this->tipo) {
                case 1:
                    $this->excepcion = $this->excepcion ?? new excepcion_producto();
                    $this->excepcion->producto_id = $this->editing ? $this->excepcion->producto_id : $this->selectedItem->id;
                    break;
                case 2:
                    $this->excepcion = $this->excepcion ?? new excepcion_servicio();
                    $this->excepcion->servicio_id = $this->editing ? $this->excepcion->servicio_id : $this->selectedItem->id;
                    break;
                case 3:
                    $this->excepcion = $this->excepcion ?? new excepcion_cat_producto();
                    $this->excepcion->categoria_producto_id = $this->editing ? $this->excepcion->categoria_producto_id : $this->selectedItem->id;
                    break;
                case 4:
                    $this->excepcion = $this->excepcion ?? new excepcion_cat_servicio();
                    $this->excepcion->categoria_servicio_id = $this->editing ? $this->excepcion->categoria_servicio_id : $this->selectedItem->id;
                    break;
            }

            $this->excepcion->qty = $this->qty;
            $this->excepcion->type_comission = $this->tipoExc;
            $this->excepcion->comision_id = $this->empleado->comision->id;


            $this->excepcion->save();
            $this->loadDefault();
            $this->dispatchBrowserEvent('noty', ['msg' => 'EXCEPCIÓN CREADA CON ÉXITO']);
            $this->dispatchBrowserEvent('hideCreateException');
            $this->emit('refresh');

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 37395Ajustes"]);
        }
    }
    function set($action)
    {
        $this->action = $action;
        $this->loadGenComision();
        $this->loadDefault();
        // $this->validateReward();
    }
    public function Delete($excepcion_id, $tipo)
    {
        try {
            switch ($tipo) {
                case 1:
                    $excepcion = excepcion_producto::find($excepcion_id);
                    break;
                case 2:
                    $excepcion = excepcion_servicio::find($excepcion_id);
                    break;
                case 3:
                    $excepcion = excepcion_cat_producto::find($excepcion_id);
                    break;
                case 4:
                    $excepcion = excepcion_cat_servicio::find($excepcion_id);
                    break;
                case 5:
                    $excepcion = excepcion_cliente::find($excepcion_id);
                    break;
                case 6:
                    $excepcion = excepcion_cat_cliente::find($excepcion_id);
                    break;
            }
            if (!$excepcion) {
                return;
            }
            $excepcion->delete();
            $this->loadDefault();
            $this->dispatchBrowserEvent('noty', ['msg' => 'EXCEPCIÓN ELIMINADA CON ÉXITO']);
            $this->emit('refresh');
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 44796Ajustes"]);
        }
    }
    public function changeWindow($tipo)
    {
        $this->pestaña = $tipo;
    }
    public function editException($excepcion_id, $tipo)
    {
        if ($this->editing && $this->exceptionToEdit == $excepcion_id) {
            $this->editing = false;
            $this->exceptionToEdit = null;

            return;
        }

        switch ($tipo) {
            case 1:
                $excepcion = excepcion_producto::find($excepcion_id);
                break;
            case 2:
                $excepcion = excepcion_servicio::find($excepcion_id);
                break;
            case 3:
                $excepcion = excepcion_cat_producto::find($excepcion_id);
                break;
            case 4:
                $excepcion = excepcion_cat_servicio::find($excepcion_id);
                break;
        }
        $this->tipo = $tipo;
        $this->editing = true;
        $this->exceptionToEdit = $excepcion_id;
        $this->excepcion = $excepcion;

        $this->qty = $excepcion->qty;
        $this->tipoExc = $excepcion->type_comission;
        $this->emit('refresh');
    }
}
