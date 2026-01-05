<?php

namespace App\Http\Livewire;

use App\Models\Empleado;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MisGanancias extends Component
{
    use WithPagination;
    public $empleado;
    public $currentDate, $currentDateEnd,$is_interval=false;
    public $start,$currentDateC,$end,$currentDateCEnd;

    public $citasFilter,$ventasFilter,$propinasFilter;
    public $total=0;
    private $dataGanancias;
    private $salon_id;
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->empleado = Auth::user()->empleado ?? null;
        $this->activateCheckers();
        $this->loadFecha();
    }

    protected $listeners = ['refresh' => '$refresh',
        'datesSelected' => 'setDatesFromPeriod',
        'prevDay','dateSelected' => 'setDate'
    ];

    public function aplicarFiltros()
    {
        $this->useDate();
    }
    private function activateCheckers()
    {
        try{
            $this->ventasFilter=true;
            $this->citasFilter=true;
            $this->propinasFilter=true;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 42369MisGanancias"] );
        }
    }
    public function render()
    {
        return view('livewire.mis-ganancias',['dataGanancias' => $this->dataGanancias]);
    }
    
    private function loadFecha()
    {
        try{
            $this->currentDate=Carbon::now()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start=Carbon::now()->toDateString();
            $this->currentDateC=Carbon::now();
            $this->currentDateEnd='';
            $this->end='';
            $this->currentDateCEnd='';
            $this->useDate();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 57369MisGanancias"] );
        }
    }

    public function setDatesFromPeriod($selectedDates)
    {
        try{
            if (count($selectedDates) >= 2) {
                // Actualizar las fechas según la lógica que necesites
                $currentDateC = Carbon::parse($selectedDates[0]);
                $currentDateCEnd = Carbon::parse($selectedDates[1]);
            
            
                $this->currentDateC = Carbon::parse($currentDateC);
                $this->currentDateCEnd = Carbon::parse($currentDateCEnd);
                $this->is_interval=true;
                $this->currentDate=$this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
                $this->start=$this->currentDateC->toDateString();
                $this->currentDateEnd=$this->currentDateCEnd->locale('es')->isoFormat('dddd, D MMMM YYYY');
                $this->end=$this->currentDateCEnd->toDateString();
                $this->useDate();
                $this->loadDatesWithNewPeriod();
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 72369MisGanancias"] );
        }
    }
    public function setDate($selectedDate)
    {
        try{
            // Actualizar las fechas según la lógica que necesites
            $currentDateC = Carbon::parse($selectedDate[0]);
            $this->currentDateC = Carbon::parse($currentDateC);
            $this->is_interval=false;
            $this->currentDate=$this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start=$this->currentDateC->toDateString();
            $this->currentDateEnd='';
            $this->end='';
            $this->currentDateCEnd='';
            $this->useDate();
            $this->loadDatesWithNewPeriod();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 95369MisGanancias"] );
        }
    }
    
    #Función que establece un día anterior 
    public function prevDay()
    {
        try{
            $this->is_interval=false;
            $this->currentDateC= $this->currentDateC->subDay();
            $this->start= $this->currentDateC->toDateString();
            $this->currentDate= $this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->useDate();
            $this->loadDatesWithNewPeriod();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 115369MisGanancias"] );
        }
    }
    #Función que retorna la fecha actual
    public function returnToday()
    {
        $this->is_interval=false;
        $this->loadFecha();
        $this->loadDatesWithNewPeriod();
    }
    #Función que retorna información de "ayer"
    public function returnYesterday()
    {
        $this->loadFecha();
        $this->prevDay();
    }
    
    public function setWeek()
    {
        try{
            $this->is_interval=true;
            $this->currentDate=Carbon::now()->startOfWeek()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start=Carbon::now()->startOfWeek()->toDateString();
            $this->currentDateC=Carbon::now()->startOfWeek();
            $this->currentDateEnd=Carbon::now()->endOfWeek()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->end=Carbon::now()->endOfWeek()->toDateString();
            $this->currentDateCEnd=Carbon::now()->endOfWeek();
            $this->useDate();
            $this->loadDatesWithNewPeriod();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 142369MisGanancias"] );
        }
    }
    public function setMonth()
    {
        try{
            $this->is_interval=true;
            $this->currentDate=Carbon::now()->startOfMonth()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start=Carbon::now()->startOfMonth()->toDateString();
            $this->currentDateC=Carbon::now()->startOfMonth();
            $this->currentDateEnd=Carbon::now()->endOfMonth()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $end=Carbon::now()->endOfMonth()->addDay();
            $this->end = $end->toDateString();
            $this->currentDateCEnd=Carbon::now()->endOfMonth();
            $this->useDate();
            $this->loadDatesWithNewPeriod();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 158369MisGanancias"] );
        }
    }
    public function setYear()
    {
        try{
            $this->is_interval=true;
            $this->currentDate=Carbon::now()->startOfYear()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start=Carbon::now()->startOfYear()->toDateString();
            $this->currentDateC=Carbon::now()->startOfYear();
            $this->currentDateEnd=Carbon::now()->endOfYear()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->end=Carbon::now()->endOfYear()->toDateString();
            $this->currentDateCEnd=Carbon::now()->endOfYear();
            $this->useDate();
            $this->loadDatesWithNewPeriod();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 175369MisGanancias"] );
        }
    }
    #función que actualiza las gráficas con la nueva fecha
    private function loadDatesWithNewPeriod()
    {
        try{
            $this->emit('dateUpdated-movimientos', $this->currentDate,$this->currentDateEnd);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 192369MisGanancias"] );
        }
    }
    private function useDate()
    {
        try{
            $this->clear();
            $ingresosCitas = [];
            $ingresosVentas = [];
            $propinas = [];
            $totalComissions = 0;
            if($this->is_interval==false){
                if($this->citasFilter){
                    $ingresosCitas = $this->empleado->asignacionesServicios()
                        ->with('date.customer','servicio')
                        ->whereDate('start',$this->start)
                        ->orderBy('start', 'desc')
                        ->get();
                }
                if($this->ventasFilter){
                    $ingresosVentas = $this->empleado->asignacionesProductos()
                        ->with('product','cita.customer','sale.customer')
                        ->whereDate('created_at',$this->start)
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
                if($this->propinasFilter){
                    $propinas = $this->empleado->propinas()
                        ->with('cita.customer','venta.customer')
                        ->whereDate('created_at',$this->start)
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
            }else{
                if($this->ventasFilter){
                    $ingresosVentas = $this->empleado->asignacionesProductos()
                        ->with('product','cita.customer','sale.customer')
                        ->whereBetween('created_at', [$this->start,$this->end])
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
                if($this->citasFilter){
                    $ingresosCitas = $this->empleado->asignacionesServicios()
                        ->with('date.customer','servicio')
                        ->whereBetween('start',[$this->start,$this->end])
                        ->orderBy('start', 'desc')
                        ->get();
                }
                if($this->propinasFilter){
                    $propinas = $this->empleado->propinas()
                        ->with('cita.customer','venta.customer')
                        ->whereBetween('created_at',[$this->start,$this->end])
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
            }

            $totalComissions = $this->recalculate($ingresosCitas,1);
            $totalComissions += $this->recalculate($ingresosVentas,1);

            $totalPropinas = $this->recalculate($propinas,0);

            
            $info =[
                'ingresosCitas' => $ingresosCitas,
                'ingresosVentas' => $ingresosVentas,
                'propinas' => $propinas,
                'total' => $this->total,
                'totalPropinas' => $totalPropinas,
                'totalComissions' => $totalComissions,
            ];

            $this->dataGanancias = $info;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 200369MisGanancias"] );
        }
    }
    private function recalculate($info,$type)
    {
        try{
            $balance = 0 ;
            foreach($info as $item){
                if($type){
                    $balance+=$item->comission;
                    $this->total += $item->comission;
                }else{
                    $balance+=$item->amount;
                    $this->total += $item->amount;
                }
            }
            return $balance;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 274369MisGanancias"] );
        }
    }
    private function clear()
    {
        $this->total = 0;
    }
}
