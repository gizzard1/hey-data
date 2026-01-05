<?php

namespace App\Http\Livewire;

use App\Models\cita;
use App\Models\metodo_pago_servicio;
use App\Models\metodo_pago_venta;
use App\Models\venta;
use Livewire\Component;
use Livewire\WithPagination;

class Reporte extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $tipo,$data,$metodosPago,$info,$salon,$cambio,$total;
    private $action=null;
    public function mount($tipo,$data)
    {
        $this->tipo = $tipo;
        $this->data = $data;
    }

    protected $listeners = ['refresh' => '$refresh'];

    public function render()
    {
        try{
            if($this->tipo === 'ticket_venta'){
                $this->action=1;
                $this->info = venta::with('metodosPago','details.product','customer.tarjetaPuntos','user','salon')->find($this->data);
                $this->metodosPago = $this->info->metodosPago;
            }elseif($this->tipo === 'ticket_servicio'){
                $this->action=0;
                $this->info = cita::with('metodosPago','details.servicio','customer.tarjetaPuntos','user','salon','details_product.product')->find($this->data);
                $this->metodosPago = $this->info->metodosPago;
            }
            foreach($this->metodosPago as $metodo){
                $this->cambio += $metodo->change;
            };
            $this->total = $this->info->total - $this->info->disccount;
            return view('livewire.reportes.reporte',['action' => $this->action]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 42146Reporte"] );
        }
    }
}
