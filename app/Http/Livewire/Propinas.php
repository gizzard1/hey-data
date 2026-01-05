<?php

namespace App\Http\Livewire;

use App\Exports\ReportePropinas;
use App\Models\cita;
use App\Models\Empleado;
use App\Models\venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Propinas extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $currentDate, $currentDateEnd,$is_interval=false;
    public $start,$currentDateC,$end,$currentDateCEnd;
    private $dataPropinas=[],$qty_cash=0,$qty_msi=0,$qty_tarjeta=0,$qty_card=[],$totalPropinas=0,$qty_propinas=0,$terminales=[];
    private $employeeIndex=[];
    public function mount()
    {
        if(session()->has('selectedDates')){
            $this->setDatesFromPeriod(session('selectedDates'));
        }else{
            $this->loadFecha();
        }
    }
    protected $listeners = ['refresh' => '$refresh','datesSelected' => 'setDatesFromPeriod',
    'prevDay','dateSelected' => 'setDate'];

    public function render()
    {
        return view('livewire.propinas-empleados',['dataPropinas' => $this->dataPropinas,'terminales' => $this->terminales]);
    }
    private function loadFecha()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 42302Propinas"] );
        }
    }
    
    public function setDatesFromPeriod($selectedDates)
    {
        try{
            session()->put('selectedDates', $selectedDates);
            session()->save();
            $this->is_interval=true;
            if (count($selectedDates) >= 2) {
                // Actualizar las fechas según la lógica que necesites
                $currentDateC = Carbon::parse($selectedDates[0]);
                $currentDateCEnd = Carbon::parse($selectedDates[1])->endOfDay();
            }elseif(count($selectedDates)==1){
                // Actualizar las fechas según la lógica que necesites
                $currentDateC = Carbon::parse($selectedDates[0])->startOfDay();
                $currentDateCEnd = $currentDateC->copy()->endOfDay();
            }
            $this->currentDateC = Carbon::parse($currentDateC);
            $this->currentDateCEnd = Carbon::parse($currentDateCEnd);
            $this->currentDate=$this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start=$this->currentDateC->toDateString();
            $this->currentDateEnd=$this->currentDateCEnd->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->end=$this->currentDateCEnd->toDateString();
            $this->useDate();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 30127Propinas"] );
        }
    }
    public function setDate($selectedDate)
    {
        try{
            $this->setDatesFromPeriod([Carbon::parse($selectedDate[0])]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 53128Propinas"] );
        }
    }
    #Función que establece un día anterior 
    public function prevDay()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()->subDay()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 84130Informe"] );
        }
    }
    public function returnToday()
    {
        $this->is_interval=false;
        $this->loadFecha();
    }
    public function returnYesterday()
    {
        $this->prevDay();
    }
    
    public function setWeek()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 379141Informe"] );
        }
    }
    public function setMonth()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 395142Informe"] );
        }
    }
    public function setYear()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()->startOfYear(),Carbon::now()->endOfYear()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 411143Informe"] );
        }
    }
    private function useDate()
    {
        try{
            $salon_id = Auth::user()->salon_id;
            $empleados = Empleado::where('salon_id',$salon_id)->get();
            $ventas = venta::where('salon_id',$salon_id)
                ->with('propinas.metodoPago')
                ->whereHas('propinas')
                ->whereBetween('created_at', [$this->start,$this->end])
                ->get();
            $citas = cita::with('propinas.metodoPago')
                ->where('salon_id',$salon_id)
                ->whereHas('propinas')
                ->whereBetween('created_at',[$this->start,$this->end])
                ->get();

            $info =[
                'ventas' => $ventas,
                'citas' => $citas,
                'empleados' => $empleados,
            ];
            $this->recalculate($info);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 182310Propinas"] );
        }
    }
    private function recalculate($info)
    {
        try{
            $this->dataPropinas=$this->calculatePropinas($info['ventas'],$info['citas'],$info['empleados']);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 229311Propinas"] );
        }
        
    }
    private function inicializarData($keys, $count)
    {
        try{
            $data = [];
            foreach ($keys as $key) {
                if($key!=='ip'){
                    $data[$key] = array_fill(0, $count, 0);                    
                }else{
                    $data[$key] = array_fill(0,$count,'no-ip');
                }
            }
            return $data;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 366317Propinas"] );
        }
    }
    private function calculatePropinas($ventas,$citas,$empleados)
    {
        try{
            $terminales =[];
            $dataPropinas=[];
            foreach($empleados as $index => $empleado){
                $dataEmpleados['empleado'][]=$empleado->first_name . ' ' . $empleado->last_name;
                $this->employeeIndex[$empleado->id] = $index;
            }
            $dataPropinas = $this->acumularPropinas([$ventas,$citas],false);
            $propinas = $this->acumularPropinas($dataPropinas,true);

            foreach($propinas as $propina){
                if($propina->payment_method_id != 1 && $propina->payment_method_id != 2 && $propina->payment_method_id != 3 && $propina->payment_method_id != 4 && $propina->payment_method_id != 5){
                    $terminales[] = $propina->metodoPago->Payment_method;
                }
            }
            $this->terminales = $terminales;
            $data = $this->inicializarData($terminales,count($empleados));
            $dataPropinas = $this->inicializarData(['qty','Efectivo','MSI','Tarjeta','total'],count($empleados));
            $this->qty_card = $data;

            $dataPropinas = array_merge($data,$dataPropinas,$dataEmpleados);


            foreach ($propinas as $propina) {
                $qty = $propina->amount;
                $index = $this->employeeIndex[$propina->empleado_id];
                
                switch ($propina->metodoPago->Payment_method) {
                    case 'Efectivo':
                        $method = 'Efectivo';
                        $this->qty_cash += $qty;
                        break;
                    case 'MSI':
                        $method = 'MSI';
                        $this->qty_msi += $qty;
                        break;
                    case 'Tarjeta':
                        $method = 'Tarjeta';
                        $this->qty_tarjeta += $qty;
                        break;
                    default:
                        $method = $propina->metodoPago->Payment_method ?? 'Desconocido';
                        $this->qty_card[$method][$index] += $qty;
                        break;
                }
                $dataPropinas[$method][$index] += $qty;
                $dataPropinas['qty'][$index] += 1;
                $dataPropinas['total'][$index] += $qty;
                $this->totalPropinas += $qty;
                $this->qty_propinas += 1;
            }
            $index = count($dataPropinas['empleado']);
            // Incrementar los valores correspondientes al último índice
            foreach($this->qty_card as $name => $cantidades){
                $total=0;
                foreach($cantidades as $qty){
                    $total += $qty;
                }
                $dataPropinas[$name][$index] = $total;
            }
            $dataPropinas['empleado'][$index] = 'Total';
            $dataPropinas['Efectivo'][$index] = $this->qty_cash;
            $dataPropinas['Msi'][$index] = $this->qty_msi;
            $dataPropinas['Tarjeta'][$index] = $this->qty_tarjeta;
            $dataPropinas['total'][$index] = $this->totalPropinas;
            $dataPropinas['qty'][$index] = $this->qty_propinas;

            return $dataPropinas;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 378318Propinas"] );
        }
    }
    private function acumularPropinas($items,$data)
    {
        try{
            $propinas=[];
            foreach($items as $movimientos)
            {
                foreach($movimientos as $movimiento)
                {
                    if(!$data){
                        $propinas[] = $movimiento->propinas;
                    }else{
                        $propinas[] = $movimiento;
                    }
                }
            }
            return $propinas;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 444319Propinas"] );
        }
    }
    public function generateExcel()
    {
        $date = $this->currentDateC;
        $date = $date->format('Y_m_d_H_i_s');
        $fileName = 'propinas' . $date . '.xlsx';
        $this->useDate();
        return Excel::download(new ReportePropinas($this->terminales,$this->dataPropinas),$fileName);
    }
}
