<?php

namespace App\Http\Livewire;

use App\Models\categoria_cliente;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriaClientes extends Component
{
    use WithPagination;

    public $categoria_clientes=[],$configuration=1;


    public $type=3,$name,$sub=3;
    public categoria_cliente $itemSelected;

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
        foreach($this->itemSelected->clientes as $cliente){
            $cliente->categorias()->detach($this->itemSelected->id);
        }
        
        // eliminar los deliveries asociados al cliente de la tabla deliveries
        foreach ($this->itemSelected->excepciones as $item) {
            $item->delete();
        }
        $this->itemSelected->delete();
        $this->loadDefault();
        $this->dispatchBrowserEvent('hideModal');
        $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"] );
    }

    private function loadDefault()
    {
        $this->categoria_clientes = Auth()->user()->salon->categoriasClientes;
        $this->itemSelected = new categoria_cliente;
        $this->name = '';
        $this->emit('refresh');
    }
    public function render()
    {
        try{
            return view('livewire.ajustes.modulos.categoria',['categorias' => $this->categoria_clientes]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 40144CategoriaClientes"] );
        }
    }
    
    public function edit(categoria_cliente $categoria_cliente)
    {
        $this->name = $categoria_cliente->name;
        $this->itemSelected = $categoria_cliente;
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
            dd($th);
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41180Pagos"] );
        }
    }
}
