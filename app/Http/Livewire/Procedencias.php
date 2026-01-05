<?php

namespace App\Http\Livewire;

use App\Models\procedencia;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Procedencias extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $procedencias=[];


    public $type=2,$name;
    public procedencia $itemSelected;

    protected $rules = [
        'name' => 'required|min:1|max:100'
    ];
    public function mount()
    {
        $this->loadDefault();
    }
    public function delete()
    {
        foreach($this->itemSelected->clientes as $cliente){
            $cliente->procedencia_id=null;
            $cliente->save();
        }
        $this->itemSelected->delete();
        $this->loadDefault();
        $this->dispatchBrowserEvent('hideModal');
        $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"] );
    }

    private function loadDefault()
    {
        $this->procedencias = Auth()->user()->salon->procedencias;
        $this->itemSelected = new procedencia;
        $this->name = '';
        $this->emit('refresh');
    }
    protected $listeners = ['refresh' => '$refresh'];

    public function render()
    {
        return view('livewire.ajustes.modulos.procedencias');
    }
    
    public function edit(procedencia $procedencia)
    {
        $this->name = $procedencia->name;
        $this->itemSelected = $procedencia;
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
        }catch(\Throwable $th){
            dd($th);
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41180Pagos"] );
        }
    }
}
