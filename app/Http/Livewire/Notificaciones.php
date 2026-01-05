<?php

namespace App\Http\Livewire;

use App\Models\cita;
use App\Models\venta;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Notificaciones extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refresh' => '$refresh'];
    public $ventasCounter=0,$citasCounter=0;

    public function mount()
    {
        $this->ventasCounter = $this->countMovs(1);
        $this->citasCounter = $this->countMovs(0);
    }
    public function render()
    {
        return view('livewire.notificaciones');
    }

    private function countMovs($type)
    {
        return $this->getDataMov($type);
    }
    
    private function getDataBilled($modelName)
    {
        return $modelName
            ->where('billing',2)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    private function getDataNoBilled($modelName)
    {
        return $modelName
            ->where('billing',1)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    private function getDataNoPayed($modelName)
    {
        return $modelName
            ->where('status','Pendiente')
            ->orWhere('status','Agendada')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function getDataMov($type)
    {
        $dataNoPayed = new Collection;
        $dataBilled = new Collection;
        $dataNoBilled = new Collection;
        $salon_id = Auth::user()->salon_id;
        $dataBilled = $this->getDataBilled($type ? venta::where('salon_id',$salon_id) : cita::where('salon_id',$salon_id));
        $dataNoPayed = $this->getDataNoPayed($type ? venta::where('salon_id',$salon_id) : cita::where('salon_id',$salon_id));
        $dataNoBilled = $this->getDataNoBilled($type ? venta::where('salon_id',$salon_id) : cita::where('salon_id',$salon_id));

        
        $merged = $dataBilled->merge($dataNoBilled)->merge($dataNoPayed);

        $unique = $merged->unique('id');

        return $unique->count();
    }
}
