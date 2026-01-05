<?php

namespace App\Http\Livewire;

use App\Models\etiquetas_cita;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Etiquetas extends Component
{
    use WithPagination;

    public $configuration=1;
    public $name,$color="#6f42c1";
    public etiquetas_cita $itemSelected;

    protected $paginationTheme = 'bootstrap';
    protected $rules = [
        'name' => 'required|min:1|max:200',
        'color' => 'required|min:1|max:10',
    ];
    public function mount()
    {
        $this->loadDefault();
    }
    private function loadDefault()
    {
        $this->itemSelected = new etiquetas_cita;
        $this->name = "";
        $this->color = "#6f42c1";
    }
    protected $listeners = ['refresh' => '$refresh','setColor','storeTag'=>'store'];

    public function render()
    {
        return view('livewire.etiquetas.etiquetas',['etiquetas'=>Auth::user()->salon->etiquetas]);
    }
    public function create()
    {
        $this->loadDefault();
        $this->dispatchBrowserEvent('openModalTags');
    }
    public function edit(etiquetas_cita $etiquetas_cita)
    {
        $this->name = $etiquetas_cita->name;
        $this->color = $etiquetas_cita->color;
        $this->itemSelected = $etiquetas_cita;
        $this->dispatchBrowserEvent('openModalTags');
    }
    public function setColor($color)
    {
        $this->color = $color;
        $this->dispatchBrowserEvent('updateColor', ['color' => $color]);
    }
    public function store()
    {
        $this->validate($this->rules);
        try{
            $this->itemSelected->name = $this->name;
            $this->itemSelected->color = $this->color;
            $this->itemSelected->salon_id = Auth::user()->salon->id;
            $this->itemSelected->save();
            $this->loadDefault();
            $this->dispatchBrowserEvent('hideModalTags');
            $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"] );
        }catch(\Throwable $th){
            dd($th);
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 41180CatGastos"] );
        }
    }
    public function delete()
    {
        foreach($this->itemSelected->citas as $cita){
            $cita->categorias()->detach($this->itemSelected->id);
        }
        $this->itemSelected->delete();
        $this->loadDefault();
        $this->dispatchBrowserEvent('hideModalTags');
        $this->dispatchBrowserEvent('noty', ['msg' =>  "SOLICITUD PROCESADA CON ÉXITO"] );
    }
}
