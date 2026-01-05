<?php

namespace App\Http\Livewire;

use App\Models\tipo_gasto;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TipoGastos extends Component
{
    use WithPagination;

    public $tipo_gastos=[],$configuration=1;

    public $type=4,$name,$sub=5;
    public tipo_gasto $itemSelected;

    protected $rules = [
        'name' => 'required|min:1|max:200'
    ];
    
    protected $listeners = ['refresh' => '$refresh','create'];
    public function mount()
    {
        $this->loadDefault();
    }
    public function delete()
    {
        foreach($this->itemSelected->gastos as $gasto){
            $gasto->update(['tipo_id' => 7]);
        }
        $this->itemSelected->delete();
        $this->loadDefault();
        $this->dispatchBrowserEvent('hideModal');
        $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"] );
    }

    private function loadDefault()
    {
        $this->tipo_gastos = tipo_gasto::where('salon_id',Auth::user()->salon_id)->orWhere('salon_id',null)->get();
        $this->itemSelected = new tipo_gasto;
        $this->name = '';
        $this->emit('refresh');
    }
    public function render()
    {
        try{
            return view('livewire.ajustes.modulos.categoria',['categorias' => $this->tipo_gastos]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 40140TipoGastos"] );
        }
    }
    
    public function edit(tipo_gasto $tipo_gasto)
    {
        $this->name = $tipo_gasto->name;
        $this->itemSelected = $tipo_gasto;
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
            $this->itemSelected->name = $this->name;
            $this->itemSelected->salon_id = Auth::user()->salon->id;
            $this->itemSelected->save();
            $this->loadDefault();
            $this->dispatchBrowserEvent('hideModal');
            $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"] );
            $this->emit('refresh');
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41180TipoGastos"] );
        }
    }
}
