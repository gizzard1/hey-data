<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\categoria_servicio;
use Illuminate\Support\Facades\Auth;

class CategoriaServicios extends Component
{
    use WithPagination;
    public $categoria_servicios=[];

    public $type=3,$name,$sub=2,$configuration=1;
    public categoria_servicio $itemSelected;

    protected $rules = [
        'name' => 'required|min:1|max:45'
    ];
    public function mount()
    {
        $this->loadDefault();
    }
    public function delete()
    {
        foreach($this->itemSelected->servicio as $servicio){
            $servicio->categorias()->detach($this->itemSelected->id);
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
        $this->categoria_servicios = Auth()->user()->salon->categoriaServicios;
        $this->itemSelected = new categoria_servicio;
        $this->name = '';
        $this->emit('refresh');
    }
    public function render()
    {
        try{
            return view('livewire.ajustes.modulos.categoria',['categorias' => $this->categoria_servicios]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 40152CategoriaServicios"] );
        }
    }
    public function edit(categoria_servicio $categoria_servicio)
    {
        $this->name = $categoria_servicio->name;
        $this->itemSelected = $categoria_servicio;
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