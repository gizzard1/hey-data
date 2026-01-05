<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\DB;
use App\Exports\ReporteComisiones;
use App\Http\Livewire\Agenda;
use App\Models\Asignacion_servicio;
use App\Models\Asignacion_venta;
use App\Models\Empleado;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Comisiones extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $currentDate, $currentDateEnd,$is_interval=false;
    public $start,$currentDateC,$end,$currentDateCEnd;
    private $dataComisiones=[],$qty_com_s=0,$total_comisiones=0,$qty_com_p=0,$total_comisiones_neto=0,$qty_com_s_neto=0,$qty_com_p_neto=0;
    private $employeeIndex=[];
    public $selectedEmployeeId=null,$selectedEmployee=null;
    public $pestaña = 1,$balance = 0;

    public function mount($selectedEmployeeId=null)
    {
        if($selectedEmployeeId){
            $this->selectedEmployeeId=$selectedEmployeeId;
            $this->selectedEmployee = Empleado::select(
                    'id',
                    DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as name"),
                    'salon_id',
                )->find($this->selectedEmployeeId);
        }else{
            if(session()->has('selectedDates')){
                $this->setDatesFromPeriod(session('selectedDates'));
            }else{
                $this->loadFecha();
            }
        }

    }
    protected $listeners = ['refresh' => '$refresh','datesSelected' => 'setDatesFromPeriod',
    'prevDay','dateSelected' => 'setDate','changeWindow'];

    public function render()
    {
        if($this->selectedEmployeeId){
            if($this->selectedEmployee->salon_id == Auth::user()->salon_id){
                return view('livewire.empleados.historial-comisiones',['movs' => session()->has('selectedDates') ? $this->setDatesFromPeriod(session('selectedDates')) : $this->loadFecha()]);
            }else{
                return view('livewire.comisiones-empleados',['dataComisiones' => $this->dataComisiones]);
            }
        }else{
            return view('livewire.comisiones-empleados',['dataComisiones' => $this->dataComisiones]);
        }
    }
    private function loadFecha()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 42302Comisiones"] );
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
            $this->getGeneralData();

            if($this->selectedEmployeeId){
                return $this->getEmpComData();
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 30127Comisiones"] );
        }
    }
    public function setDate($selectedDate)
    {
        try{
            $this->setDatesFromPeriod([Carbon::parse($selectedDate[0])]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 53128Comisiones"] );
        }
    }

    #Función que establece un día anterior 
    public function prevDay()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()->subDay()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 84130Comisiones"] );
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 379141Comisiones"] );
        }
    }
    public function setMonth()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 395142Comisiones"] );
        }
    }
    public function setYear()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()->startOfYear(),Carbon::now()->endOfYear()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 411143Comisiones"] );
        }
    }
    private function getGeneralData()
    {
        try{
            $salon_id = Auth::user()->salon_id;
            $empleados = Empleado::where('salon_id',$salon_id)->get();
            
            $asignaciones_ventas = Asignacion_venta::select('id','cita_id','venta_id','empleado_id','quantity','comission','iva')
                ->with(['cita', 'sale'])
                ->where(function ($q) use($salon_id) {
                    $q->whereHas('cita', function ($cita) use($salon_id) {
                        $cita->where('salon_id',$salon_id)
                            ->whereBetween('start', [$this->currentDateC, $this->currentDateCEnd]);
                    })
                    ->orWhereHas('sale', function ($sale) use($salon_id) {
                        $sale->where('salon_id',$salon_id)
                            ->whereBetween('created_at', [$this->currentDateC, $this->currentDateCEnd]);
                    });
                })
                ->get();
            
            $asignaciones_servicios = Asignacion_servicio::select('id','cita_id','empleado_id','comission','iva')
                ->whereHas('date', function ($query) use($salon_id)  {
                    $query->where('salon_id',$salon_id)
                        ->whereBetween('start', [$this->currentDateC, $this->currentDateCEnd]);
                })->get();

            $info =[
                'asignaciones_servicios' => $asignaciones_servicios,
                'asignaciones_ventas' => $asignaciones_ventas,
                'empleados' => $empleados,
            ];
            $this->recalculate($info);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 182310Comisiones"] );
        }
    }
    private function recalculate($info)
    {
        try{
            $this->dataComisiones=$this->calculateComisiones($info['asignaciones_ventas'],$info['asignaciones_servicios'],$info['empleados']);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 229311Comisiones"] );
        }
        
    }
    private function calculateComisiones($ventas,$citas,$empleados)
    {
        try{
            $dataComisionesSales = $this->acumularComisiones($ventas,$empleados,false);
            $dataComisionesServices = $this->acumularComisiones($citas,$empleados,true);
            $totales = $this->fusionarArreglos($dataComisionesSales,$dataComisionesServices);
            // Verifica si los arrays son nulos antes de llamar a array_merge()
            if ($dataComisionesSales !== null && $dataComisionesServices !== null && $totales !== null) {
                $dataComisiones = array_merge($dataComisionesSales, $dataComisionesServices, $totales);
            } elseif ($dataComisionesSales !== null && $dataComisionesServices !== null) {
                $dataComisiones = array_merge($dataComisionesSales, $dataComisionesServices);
            } elseif ($dataComisionesSales !== null && $totales !== null) {
                $dataComisiones = array_merge($dataComisionesSales, $totales);
            } elseif ($dataComisionesServices !== null && $totales !== null) {
                $dataComisiones = array_merge($dataComisionesServices, $totales);
            } elseif ($dataComisionesSales !== null) {
                $dataComisiones = $dataComisionesSales;
            } elseif ($dataComisionesServices !== null) {
                $dataComisiones = $dataComisionesServices;
            } elseif ($totales !== null) {
                $dataComisiones = $totales;
            }
            
            $index = count($dataComisiones['empleado']);
            // Incrementar los valores correspondientes al último índice
            $dataComisiones['empleado'][$index] = 'Total';
            $dataComisiones['qty_com_s'][$index] = $this->qty_com_s;
            $dataComisiones['qty_com_p'][$index] = $this->qty_com_p;
            $dataComisiones['total_comisiones'][$index] = $this->total_comisiones;
            $dataComisiones['empleado'][$index] = 'Total';
            $dataComisiones['qty_com_s_neto'][$index] = $this->qty_com_s_neto;
            $dataComisiones['qty_com_p_neto'][$index] = $this->qty_com_p_neto;
            $dataComisiones['total_comisiones_neto'][$index] = $this->total_comisiones_neto;

            return $dataComisiones;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 240312Comisiones"] );
        }
    }
    private function acumularComisiones($items,$empleados,$esServicio)
    {
        try{
            foreach($empleados as $empleado){
                $dataComisiones['empleado'][]=$empleado->first_name . ' ' . $empleado->last_name;
                $dataComisiones['id'][]=$empleado->id;
            }
            foreach ($empleados as $index => $empleado) {
                $this->employeeIndex[$empleado->id] = $index;
            }

            // Inicializar el arreglo qty con valores predeterminados
            if($esServicio){
                $data = $this->inicializarData(['qty_com_s','qty_com_s_neto','total_comisiones','total_comisiones_neto'],count($empleados));
                $dataComisiones = array_merge($data,$dataComisiones);
            }else{
                $data = $this->inicializarData(['qty_com_p','qty_com_p_neto','total_comisiones','total_comisiones_neto'],count($empleados));
                $dataComisiones = array_merge($data,$dataComisiones);
            }
            // Llenar el arreglo qty basado en las asignaciones
            foreach ($items as $asignacion) {
                $index = $this->employeeIndex[$asignacion->empleado_id] ?? null;

                if ($index !== null) {
                    $iva = $asignacion->iva;
                    $comission_neto = $asignacion->comission / ($iva+1);
                    $comission = $asignacion->comission;

                    // Actualizar totales globales
                    $this->total_comisiones_neto += $comission_neto;
                    $this->total_comisiones += $comission;

                    // Actualizar totales p/empleado
                    $dataComisiones['total_comisiones_neto'][$index] += $comission_neto;
                    $dataComisiones['total_comisiones'][$index] += $comission;

                    // Actualizar cantidades en bruto y neto según el tipo de servicio
                    $qty_com = $esServicio ? 'qty_com_s' : 'qty_com_p';
                    $qty_com_neto = $esServicio ? 'qty_com_s_neto' : 'qty_com_p_neto';

                    $this->{$qty_com_neto} += $comission_neto;
                    $this->{$qty_com} += $comission;

                    // Actualizar datos en el array de comisiones
                    $dataComisiones[$qty_com_neto][$index] += $comission_neto;
                    $dataComisiones[$qty_com][$index] += $comission;
                } 
            }
            return $dataComisiones;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 263313Comisiones"] );
        }
    }
        private function fusionarArreglos($dataComisiones1, $dataComisiones2)
    {
        try{
            // Fusionar los arreglos
            $dataComisionesMerged = [
                'empleado' => array_merge($dataComisiones1['empleado']),
                'total_comisiones'   => array_map(function($qty1, $qty2) {
                    return $qty1 + $qty2;
                }, $dataComisiones1['total_comisiones'], $dataComisiones2['total_comisiones']),
                'total_comisiones_neto'   => array_map(function($qty1, $qty2) {
                    return $qty1 + $qty2;
                }, $dataComisiones1['total_comisiones_neto'], $dataComisiones2['total_comisiones_neto']),
            ];

            return $dataComisionesMerged;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 347316Comisiones"] );
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 366317Comisiones"] );
        }
    }
    
    public function generateExcel()
    {
        $date = $this->currentDateC;
        $date = $date->format('Y_m_d_H_i_s');
        $fileName = 'comisiones' . $date . '.xlsx';
        $this->getGeneralData();
        return Excel::download(new ReporteComisiones($this->dataComisiones),$fileName);
    }
    public function employeeSelecter($emplId)
    {
        return redirect()->route('comisiones-empleados',['selectedEmployeeId' => $emplId]);
    }
    public function getEmpComData()
    {
        if($this->pestaña === 1) {
            $ref = 'servicio';
            $key = 'qty_com_s';
            $movs = Asignacion_servicio::select(
                'id',
                'selected_service',
                'cita_id',
                'empleado_id',
                'discount_qty',
                'discount_type',
                'current_price',
                'disccount_price',
                'comission',
                'base_comision',
                'type_comision_calculated',
                DB::raw("start as created_at"),
            )
            ->with([
                'servicio' => function($q) {
                    $q->select('id', 
                        DB::raw("name as nombre"), 
                    ); 
                },
                'date' => function($q) {
                    $q->select('id',
                        'customer_id',
                    )->with(['metodosPago' => function ($query) {
                        $query->select('amount','cita_id','payment_method_id','tipo','reference','amount','change','created_at')
                            ->with(['metodoPago' => function($q) {
                                $q->select('id','Payment_method');
                        }]);
                    },
                    'customer' => function($q) {
                        $q->select('id','first_name','last_name');
                    }]);
                },
            ])
            ->where('comission','>',0)
            ->where('empleado_id', $this->selectedEmployeeId)
            ->whereBetween('created_at', [$this->currentDateC, $this->currentDateCEnd])
            ->paginate(6);
        }elseif($this->pestaña === 2) {
            $ref = 'product';
            $key = 'qty_com_p';
            $movs = Asignacion_venta::select(
                'id',
                'selected_item',
                'venta_id',
                'cita_id',
                'empleado_id',
                'quantity',
                'discount_qty',
                'discount_type',
                'current_price',
                'disccount_price',
                'comission',
                'base_comision',
                'type_comision_calculated',
                'created_at',
            )
            ->with([
                'product' => function($q) {
                    $q->select('id',
                        DB::raw("name as nombre"), 
                    ); 
                },
                'sale' => function($q) {
                    $q->select('id',
                        'customer_id',
                    )->with(['metodosPago' => function ($query) {
                        $query->select('amount','venta_id','payment_method_id','tipo','reference','amount','change','created_at')
                            ->with(['metodoPago' => function($q) {
                                $q->select('id','Payment_method');
                        }]);
                    },
                    'customer' => function($q) {
                        $q->select('id','first_name','last_name');
                    }]);
                },
                'cita' => function($q) {
                    $q->select('id',
                        'customer_id',
                    )->with(['metodosPago' => function ($query) {
                        $query->select('amount','cita_id','payment_method_id','tipo','reference','amount','change','created_at')
                            ->with(['metodoPago' => function($q) {
                                $q->select('id','Payment_method');
                        }]);
                    },
                    'customer' => function($q) {
                        $q->select('id','first_name','last_name');
                    }]);
                },
            ])
            ->where('comission','>',0)
            ->where('empleado_id', $this->selectedEmployeeId)
            ->whereBetween('created_at', [$this->currentDateC, $this->currentDateCEnd])
            ->paginate(6);
        }
        
        $movs->transform(function ($mov) {
            $mov->customer = $mov->cita ? $mov->cita->customer : ($mov->sale ? $mov->sale->customer : $mov->date->customer);
            $mov->metodosPago = $mov->cita ? $mov->cita->metodosPago : ($mov->sale ? $mov->sale->metodosPago : $mov->date->metodosPago);
            return $mov;
        });

        foreach($movs as $mov){
            $mov->price = $this->getPrice($mov);
            $mov->type_comission = $this->getTypeComission($mov);
            $mov->date = $mov->created_at->format('Y-m-d H:i');
            $mov->name = $mov->{$ref} ? $mov->{$ref}->nombre : 'Desconocido';
        }
        $this->balance = $this->dataComisiones[$key][$this->employeeIndex[$this->selectedEmployeeId] ?? 0] ?? 0;
        $this->resetPage();

        return $movs;
    }
    
    public function editar($movId)
    {
        $link = null;

        $mov = $this->pestaña === 1 
            ? Asignacion_servicio::with(['date'])->find($movId) 
            : Asignacion_venta::with(['cita','sale'])->find($movId);

        $link = $mov->date
            ? route('citas', ['cita_id' => $mov->date?->id, 'pestaña' => 1, 'action' => 2])
            : ($mov->cita ? route('citas', ['cita_id' => $mov->cita?->id, 'pestaña' => 1, 'action' => 2]) 
            : route('ventas', ['venta_id' => ($mov->sale?->id)]));

        return redirect()->to($link);
    }
    private function getTypeComission($item)
    {
        try{
            if($item->type_comision_calculated=='percent'){
                return ($item->comission>0 ? $item->comission/$item->price*100 : 0);
            }elseif($item->type_comision_calculated=='qty'){
                return $item->comission;
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 12369Agenda"] );
        }
    }
    private function getPrice($item)
    {
        try{
            $obj = [];
            $obj['base_comision'] = boolval($item->base_comision);
            $obj['disccount_price'] = floatval($item->disccount_price);
            $obj['sale_price'] = floatval($item->current_price);
            $price = $item->disccount_price > 0 ? $item->disccount_price : $item->current_price;
            $obj['total'] = $item->discount_type == 'Porcentaje' ? floatval($price - (($item->discount_qty/100)*$price)) : floatval($price - $item->discount_qty);
            return $this->defineBasePrice($obj);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1312369Agenda"] );
        }
    }
    private function defineBasePrice($item)
    {
        try{
            if($item['base_comision']){
                return $item['total'];
            }else{
                return $item['disccount_price'] ? $item['disccount_price'] : $item['sale_price'];
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 138312369Agenda"] );
        }
    }
    public function changeWindow($pestaña)
    {
        $this->pestaña = $pestaña;
    }
}
