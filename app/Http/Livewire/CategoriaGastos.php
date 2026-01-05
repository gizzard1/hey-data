<?php

namespace App\Http\Livewire;

use App\Models\categoria_gasto;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriaGastos extends Component
{
    use WithPagination;

    public $categoria_gastos=[],$configuration=1;


    public $type=3,$name,$sub=4;
    public categoria_gasto $itemSelected;

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
            $gasto->update(['categoria_id' => null]);
        }
        // // eliminar los deliveries asociados al gasto de la tabla deliveries
        // foreach ($this->itemSelected->excepciones as $item) {
        //     $item->delete();
        // }
        $this->itemSelected->delete();
        $this->loadDefault();
        $this->dispatchBrowserEvent('hideModal');
        $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"] );
    }

    private function loadDefault()
    {
        $this->categoria_gastos = Auth()->user()->salon->categoriaGastos;
        $this->itemSelected = new categoria_gasto;
        $this->name = '';
        $this->emit('refresh');
    }
    public function render()
    {
        try{
            return view('livewire.ajustes.modulos.categoria',['categorias' => $this->categoria_gastos]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 40144CategoriaClientes"] );
        }
    }
    
    public function edit(categoria_gasto $categoria_gasto)
    {
        $this->name = $categoria_gasto->name;
        $this->itemSelected = $categoria_gasto;
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41180CatGastos"] );
        }
    }
}
