<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\categoria_producto;
use Illuminate\Support\Facades\Auth;

class CategoriaProductos extends Component
{
    use WithPagination;
    public $categoria_productos=[];

    public $type=3,$name,$sub=1,$configuration=1;
    public categoria_producto $itemSelected;

    protected $rules = [
        'name' => 'required|min:1|max:45'
    ];
    public function mount()
    {
        $this->loadDefault();
    }
    public function delete()
    {
        foreach($this->itemSelected->productos as $producto){
            $producto->categorias()->detach($this->itemSelected->id);
        }
        foreach($this->itemSelected->excepciones as $excepcion){
            $excepcion->delete();
        }
        $this->itemSelected->delete();
        $this->loadDefault();
        $this->dispatchBrowserEvent('hideModal');
        $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"] );
    }

    private function loadDefault()
    {
        $this->categoria_productos = Auth()->user()->salon->categoriasProductos;
        $this->itemSelected = new categoria_producto;
        $this->name = '';
        $this->emit('refresh');
    }
    public function render()
    {
        try{
            return view('livewire.ajustes.modulos.categoria',['categorias' => $this->categoria_productos]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 60148CategoriaProductos"] );
        }
    }
    
    public function edit(categoria_producto $categoria_producto)
    {
        $this->name = $categoria_producto->name;
        $this->itemSelected = $categoria_producto;
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
