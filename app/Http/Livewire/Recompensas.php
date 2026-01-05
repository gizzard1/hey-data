<?php

namespace App\Http\Livewire;

use App\Models\categoria_cliente;
use App\Models\categoria_producto;
use App\Models\categoria_servicio;
use App\Models\cliente;
use App\Models\excepcion_cat_cliente;
use App\Models\excepcion_cliente;
use App\Models\excepcion_cat_producto;
use App\Models\excepcion_cat_servicio;
use App\Models\excepcion_producto;
use App\Models\excepcion_servicio;
use App\Models\producto;
use App\Models\programa_recompensa;
use App\Models\servicio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Recompensas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $rewardTypeS,$rewardQtyS;
    public $rewardTypeP,$rewardQtyP;
    public $pestaña=1;
    public $comision,$query,$tipoExc,$listado=[],$qty,$tipo,$selectedItem;
    public $editing=false, $exceptionToEdit=null, $excepcion;
    protected $rules = [
        'comision.qty_s' => "nullable",
        'comision.qty_p' => "nullable",
        'comision.type_comission_p' => "nullable|in:percent,qty",
        'comision.type_comission_s' => "nullable|in:percent,qty",
    ];
    public function mount()
    {
        $this->loadDefault();
    }
    private function loadDefault()
    {
        $this->excepcion = null;
        $this->editing = false;
        $this->exceptionToEdit = null;

        $this->rewardTypeP='percent';
        $this->rewardQtyP=0;
        $this->rewardTypeS='percent';
        $this->rewardQtyS=0;
        
        $this->tipoExc = 'percent';
        $this->listado = [];
        $this->tipo=false;
        $this->query='';
        $this->qty=null;
        $this->selectedItem=null;

        if(Auth::user()->salon->recompensaGeneral){
            $this->comision = Auth::user()->salon->recompensaGeneral;
        }else{
            $this->comision = new programa_recompensa;
            $this->comision->type_comission_p = 'percent';
            $this->comision->type_comission_s = 'percent';
        }
    }
    public function storeGeneral()
    {
        $this->validate($this->rules);
        try{

            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }

            $this->comision->qty_s = $this->comision->qty_s != '' ?$this->comision->qty_s: 0;
            $this->comision->qty_p = $this->comision->qty_p != '' ?$this->comision->qty_p: 0;

            $this->comision->salon_id = Auth::user()->salon_id;
            $this->comision->save();
            $this->loadDefault();
            $this->dispatchBrowserEvent('noty',['msg'=>'RECOMPENSAS MODIFICADA CON ÉXITO']);
            $this->emit('refresh');

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 232Recompensas"] );
        }
    }

    public function selectedItem($itemId)
    {
        try{
            switch($this->tipo){
                case 1:$this->selectedItem = producto::find($itemId);break;
                case 2:$this->selectedItem = servicio::find($itemId);break;
                case 3:$this->selectedItem = categoria_producto::find($itemId);break;
                case 4:$this->selectedItem = categoria_servicio::find($itemId);break;
                case 5:$this->selectedItem = cliente::find($itemId);break;
                case 6:$this->selectedItem = categoria_cliente::find($itemId);break;
            }
            $this->tipo!=5 ? $this->query=$this->selectedItem->name : $this->query=$this->selectedItem->first_name . ' ' .$this->selectedItem->last_name;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 35994Recompensas"] );
        }
    }
    public function Add($tipo)
    {
        try{
            $this->tipo=$tipo;
            $this->dispatchBrowserEvent('openCreateException');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 33192Recompensas"] );
        }
    }
    protected $listeners = ['refresh' => '$refresh','changeWindow','Delete'];

    public function render()
    {
        return view('livewire.recompensas.main');
    }
    public function changeWindow($tipo)
    {
        $this->pestaña = $tipo;
    }
    public function updatedQuery()
    {
        try{
            switch($this->tipo){
                case 1:
                    $q = $this->query;
                    $this->listado = producto::where('salon_id', Auth::user()->salon->id)
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
                    break;
                case 2:
                    $this->listado= servicio::where('salon_id', Auth::user()->salon->id)
                        ->where('visibility','visible')
                        ->where('name', '!=', 'Servicio eliminado')
                        ->where(function ($q) {
                            $q->where('name', 'like', "%{$this->query}%")
                            ->orWhere('description', 'like', "%{$this->query}%");
                        })
                        ->orderBy('name', 'asc')
                        ->get();      
                    break;
                case 3:$this->listado= categoria_producto::where('name','like',"%{$this->query}%")->where('salon_id',Auth::user()->salon->id)->orderBy('name')->get();break;
                case 4:$this->listado= categoria_servicio::where('name','like',"%{$this->query}%")->where('salon_id',Auth::user()->salon->id)->orderBy('name')->get();break;
                case 5:
                    $this->listado= cliente::where('salon_id', Auth::user()->salon->id)
                        ->where(function ($query) {
                            $query->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', "%{$this->query}%")
                                ->orWhere('first_name', 'like', "%{$this->query}%")
                                ->orWhere('last_name', 'like', "%{$this->query}%")
                                ->orWhere('email', 'like', "%{$this->query}%")
                                ->orWhere('phone', 'like', "%{$this->query}%");
                        })
                        ->orderBy('first_name', 'asc')
                        ->get();      
                    break;
                case 6:$this->listado= categoria_cliente::where('name','like',"%{$this->query}%")->where('salon_id',Auth::user()->salon->id)->orderBy('name')->get();break;
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 34293Recompensas"] );
        }
    }
    public function cancel()
    {
        $this->loadDefault();
    }
    public function StoreException()
    {
        $rules = [
            'query' => "nullable",
            'qty' => "required|numeric",
            'tipoExc' => 'required|in:percent,qty'
        ];

        $this->validate($rules);
        try{

            if (session()->has('customDate')) {
                Carbon::setTestNow(Carbon::createFromFormat('Y-m-d', session('customDate')));
            }
            switch($this->tipo){
                case 1:
                    $excepcion= $this->excepcion ?? new excepcion_producto();
                    $excepcion->producto_id= $this->editing ? $this->excepcion->producto_id : $this->selectedItem->id;
                    break;
                case 2:
                    $excepcion= $this->excepcion ?? new excepcion_servicio();
                    $excepcion->servicio_id= $this->editing ? $this->excepcion->servicio_id : $this->selectedItem->id;
                    break;
                case 3:
                    $excepcion= $this->excepcion ?? new excepcion_cat_producto();
                    $excepcion->categoria_producto_id= $this->editing ? $this->excepcion->categoria_producto_id : $this->selectedItem->id;
                    break;
                case 4:
                    $excepcion= $this->excepcion ?? new excepcion_cat_servicio();
                    $excepcion->categoria_servicio_id= $this->editing ? $this->excepcion->categoria_servicio_id : $this->selectedItem->id;
                    break;
                case 5:
                    $excepcion= $this->excepcion ?? new excepcion_cliente();
                    $excepcion->cliente_id= $this->editing ? $this->excepcion->cliente_id : $this->selectedItem->id;
                    break;
                case 6:
                    $excepcion= $this->excepcion ?? new excepcion_cat_cliente();
                    $excepcion->categoria_cliente_id= $this->editing ? $this->excepcion->categoria_cliente_id : $this->selectedItem->id;
                    break;
            }

            $excepcion->qty=$this->qty;
            $excepcion->type_comission=$this->tipoExc;
            $excepcion->programa_recompensa_id=$this->comision->id;
            
            // if(Auth::user()->salon->recompensaGeneral){
            //     $excepcion->programa_recompensa_id=$this->comision->id;
            // }else{
            //     $this->comision->salon_id = Auth::user()->salon_id;
            //     $this->comision->save();
            //     $excepcion->programa_recompensa_id=$this->comision->id;
            // }

            $excepcion->save();
            $this->loadDefault();
            $this->dispatchBrowserEvent('noty',['msg'=>'EXCEPCIÓN CREADA CON ÉXITO']);
            $this->dispatchBrowserEvent('hideCreateException');
            $this->emit('refresh');

            if (session()->has('customDate')) {
                Carbon::setTestNow();
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 37395Recompensas"] );
        }
    }
    
    public function editException($excepcion_id,$tipo)
    {
        if($this->editing && $this->exceptionToEdit == $excepcion_id){
            $this->editing = false;
            $this->exceptionToEdit = null;

            return;
        }

        switch($tipo){
            case 1:
                $excepcion= excepcion_producto::find($excepcion_id);
                break;
            case 2:
                $excepcion= excepcion_servicio::find($excepcion_id);
                break;
            case 3:
                $excepcion= excepcion_cat_producto::find($excepcion_id);
                break;
            case 4:
                $excepcion= excepcion_cat_servicio::find($excepcion_id);
                break;
        }
        $this->tipo=$tipo;
        $this->editing = true;
        $this->exceptionToEdit = $excepcion_id;
        $this->excepcion = $excepcion;

        $this->qty = $excepcion->qty;
        $this->tipoExc = $excepcion->type_comission;
        $this->emit('refresh');
    }
    
    public function Delete($excepcion_id,$tipo)
    {
        try{
            switch($tipo){
                case 1:
                    $excepcion= excepcion_producto::find($excepcion_id);
                    break;
                case 2:
                    $excepcion= excepcion_servicio::find($excepcion_id);
                    break;
                case 3:
                    $excepcion= excepcion_cat_producto::find($excepcion_id);
                    break;
                case 4:
                    $excepcion= excepcion_cat_servicio::find($excepcion_id);
                    break;
                case 5:
                    $excepcion= excepcion_cliente::find($excepcion_id);
                    break;
                case 6:
                    $excepcion= excepcion_cat_cliente::find($excepcion_id);
                    break;
            }
            $excepcion->delete();
            $this->loadDefault();
            $this->dispatchBrowserEvent('noty',['msg'=>'EXCEPCIÓN ELIMINADA CON ÉXITO']);
            $this->emit('refresh');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 44796Ajustes"] );
        }
    }
}
