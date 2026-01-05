<?php

namespace App\Http\Livewire;

use App\Models\metodo_pago;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Pago extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $metodos=[],$type=1,$name;
    public metodo_pago $itemSelected;

    protected $rules = [
        'name' => 'required|min:1|max:100'
    ];
    public function mount()
    {
        $this->loadDefault();
    }

    public function delete()
    {
        if(count($this->itemSelected->metodoServicios)==0 && count($this->itemSelected->metodoVentas)== 0){
            $this->itemSelected->delete();
            $this->loadDefault();
            $this->dispatchBrowserEvent('hideModal');
            $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"] );
        }else{
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Existen movimientos que involucran este método de pago"] );
        }
    }

    private function loadDefault()
    {
        $this->itemSelected = new metodo_pago;
        $this->name = '';
        $this->metodos = Auth()->user()->salon->metodosPago;
        $this->emit('refresh');
    }
    protected $listeners = ['refresh' => '$refresh'];

    public function render()
    {
        return view('livewire.ajustes.modulos.pagos');
    }
    public function edit(metodo_pago $metodo_pago)
    {
        $this->name = $metodo_pago->Payment_method;
        $this->itemSelected = $metodo_pago;
        $this->dispatchBrowserEvent('openModal');
    }
    public function create()
    {
        $this->dispatchBrowserEvent('openModal');
    }
    public function store()
    {
        $this->validate($this->rules);
        try{
            $this->itemSelected->Payment_method = $this->name;
            $this->itemSelected->salon_id = Auth::user()->salon->id;
            $this->itemSelected->save();
            $this->loadDefault();
            $this->dispatchBrowserEvent('hideModal');
            $this->dispatchBrowserEvent('play_1');
            $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"] );
        }catch(\Throwable $th){
            dd($th);
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41180Pagos"] );
        }
    }
}
