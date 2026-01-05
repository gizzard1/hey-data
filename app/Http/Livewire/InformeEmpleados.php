<?php

namespace App\Http\Livewire;

use App\Exports\ReporteEmpleados;
use App\Models\Asignacion_servicio;
use App\Models\cita;
use App\Models\Empleado;
use App\Models\User;
use App\Models\venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class InformeEmpleados extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $currentDate, $currentDateEnd,$is_interval=false;
    public $start,$currentDateC,$end,$currentDateCEnd;
    private $total_citas_pagadas,$dataComisiones=[],$qty_s=0,$qty_com_s=0,$total_comisiones=0,$total_incomes=0,$qty_p=0,$qty_com_p=0,$total_incomes_neto=0,$total_comisiones_neto=0,$qty_s_neto=0,$qty_com_s_neto=0,$qty_p_neto=0,$qty_com_p_neto=0;
    private $dataPropinas=[],$qty_cash=0,$qty_msi=0,$qty_tarjeta=0,$qty_card=[],$totalPropinas=0,$qty_propinas=0,$terminales=[];
    private $dataClientes=[],$employeeIndex=[],$uniqueCustomerIds=[],$qty=0,$qty_regresan_w_estilista=0,$qty_regresan=0,$qty_clientesNew=0;
    private $dataLog=[];
    private $dataDurationDates=[];
    private $salon_id;

    public function mount()
    {
        $this->loadFecha();
    }
    protected $listeners = ['refresh' => '$refresh','datesSelected' => 'setDatesFromPeriod',
    'prevDay','dateSelected' => 'setDate'];

    public function render()
    {
        return view('livewire.informe-empleados',['dataComisiones' => $this->dataComisiones,'dataPropinas' => $this->dataPropinas,'terminales' => $this->terminales,'dataClientes' => $this->dataClientes,'dataLog' => $this->dataLog,'dataDurationDates' => $this->dataDurationDates]);
    }
    private function loadFecha()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 42302InformeEmpleados"] );
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
            $this->loadChartsWithNewPeriod();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 30127InformeEmpleados"] );
        }
    }
    public function setDate($selectedDate)
    {
        try{
            $this->setDatesFromPeriod([Carbon::parse($selectedDate[0])]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 53128InformeEmpleados"] );
        }
    }

    
    #función que actualiza las gráficas con la nueva fecha
    private function loadChartsWithNewPeriod()
    {
        try{
            $this->emit('dateUpdated-empleados', $this->currentDate,$this->currentDateEnd);
            $this->emit('reloadCharts', ['dataDurationDates'=>$this->dataDurationDates]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 97305InformeEmpleados"] );
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
            $this->salon_id = Auth::user()->salon_id;
            $empleados = Empleado::where('salon_id',$this->salon_id)->get();
            $users = User::with('logs','empleado')->where('salon_id',$this->salon_id)->get();
            $ventas = venta::where('salon_id',$this->salon_id)
                ->with('metodosPago.metodoPago','propinas.metodoPago','customer.citas.details','details')
                ->whereBetween('created_at', [$this->start,$this->end])
                ->get();
            $citas = cita::with('details.date.details','details.servicio','metodosPago.metodoPago','propinas.metodoPago','customer.compras.details')
                ->where('salon_id',$this->salon_id)
                ->whereBetween('created_at',[$this->start,$this->end])
                ->get();
            
            $asignaciones_ventas = $this->loadAssignments($ventas);
            $asignaciones_ventas_cita = $this->loadAssignmentsSP($citas);

            $asignaciones_servicios = $this->loadAssignments($citas);
            $venta_producto = array_merge($asignaciones_ventas, $asignaciones_ventas_cita);

            if(isset($citas)){
                $this->total_citas_pagadas = $citas->count();
            }
            
            $info =[
                'ventas' => $ventas,
                'citas' => $citas,
                'asignaciones_servicios' => $asignaciones_servicios,
                'asignaciones_ventas' => $venta_producto,
                'empleados' => $empleados,
                'users' => $users
            ];
            $this->recalculate($info);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 182310InformeEmpleados"] );
        }
    }
    private function recalculate($info)
    {
        try{
            $this->dataComisiones=$this->calculateComisiones($info['asignaciones_ventas'],$info['asignaciones_servicios'],$info['empleados']);
            $this->dataDurationDates=$this->calculateDurationDates($info['asignaciones_servicios'],$info['empleados']);
            $this->dataPropinas=$this->calculatePropinas($info['ventas'],$info['citas'],$info['empleados']);
            $this->dataClientes=$this->calculateClientes($info['ventas'],$info['citas'],$info['empleados']);
            $this->dataLog=$this->calculateLog($info['users']);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 229311InformeEmpleados"] );
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
            } else {
                // Manejo si todos los arrays son nulos
            }
            
            $index = count($dataComisiones['empleado']);
            // Incrementar los valores correspondientes al último índice
            $dataComisiones['empleado'][$index] = 'Total';
            $dataComisiones['qty_s'][$index] = $this->qty_s;
            $dataComisiones['qty_com_s'][$index] = $this->qty_com_s;
            $dataComisiones['qty_p'][$index] = $this->qty_p;
            $dataComisiones['qty_com_p'][$index] = $this->qty_com_p;
            $dataComisiones['total_comisiones'][$index] = $this->total_comisiones;
            $dataComisiones['total_incomes'][$index] = $this->total_incomes;
            $dataComisiones['empleado'][$index] = 'Total';
            $dataComisiones['qty_s_neto'][$index] = $this->qty_s_neto;
            $dataComisiones['qty_com_s_neto'][$index] = $this->qty_com_s_neto;
            $dataComisiones['qty_p_neto'][$index] = $this->qty_p_neto;
            $dataComisiones['qty_com_p_neto'][$index] = $this->qty_com_p_neto;
            $dataComisiones['total_comisiones_neto'][$index] = $this->total_comisiones_neto;
            $dataComisiones['total_incomes_neto'][$index] = $this->total_incomes_neto;

            return $dataComisiones;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 240312InformeEmpleados"] );
        }
    }
    private function acumularComisiones($items,$empleados,$esServicio)
    {
        try{
            foreach($empleados as $empleado){
                $dataComisiones['empleado'][]=$empleado->first_name . ' ' . $empleado->last_name;
            }
            foreach ($empleados as $index => $empleado) {
                $this->employeeIndex[$empleado->id] = $index;
            }

            // Inicializar el arreglo qty con valores predeterminados
            if($esServicio){
                $data = $this->inicializarData(['qty_s','qty_s_neto','qty_com_s','qty_com_s_neto','total_comisiones','total_comisiones_neto','total_incomes','total_incomes_neto'],count($empleados));
                $dataComisiones = array_merge($data,$dataComisiones);
            }else{
                $data = $this->inicializarData(['qty_p','qty_p_neto','qty_com_p','qty_com_p_neto','total_comisiones','total_comisiones_neto','total_incomes','total_incomes_neto'],count($empleados));
                $dataComisiones = array_merge($data,$dataComisiones);
            }
            // Llenar el arreglo qty basado en las asignaciones
            foreach ($items as $item) {
                foreach ($item as $asignacion) {
                    $index = $this->employeeIndex[$asignacion->empleado->id] ?? null;

                    if ($index !== null) {
                        $disccount_price = $asignacion->disccount_price;
                        $current_price = $asignacion->current_price;
                        $iva = $asignacion->iva;
                        $comission_neto = $asignacion->comission / ($iva+1);
                        $current_price_neto = $current_price - ($current_price * $iva);
                        $disccount_price_neto = $disccount_price - ($disccount_price * $iva);
                        $comission = $asignacion->comission;


                        // Actualizar totales globales
                        $this->total_incomes_neto +=  $disccount_price_neto == 0 ? $current_price_neto : $disccount_price_neto;
                        $this->total_comisiones_neto += $comission_neto;
                        $this->total_incomes += $disccount_price == 0 ? $current_price : $disccount_price;
                        $this->total_comisiones += $comission;

                        // Actualizar totales p/empleado
                        $dataComisiones['total_incomes_neto'][$index] +=  $disccount_price_neto == 0 ? $current_price_neto : $disccount_price_neto;
                        $dataComisiones['total_comisiones_neto'][$index] += $comission_neto;
                        $dataComisiones['total_incomes'][$index] += $disccount_price == 0 ? $current_price : $disccount_price;
                        $dataComisiones['total_comisiones'][$index] += $comission;

                        // Actualizar cantidades en bruto y neto según el tipo de servicio
                        $qty = $esServicio ? 'qty_s' : 'qty_p';
                        $qty_com = $esServicio ? 'qty_com_s' : 'qty_com_p';
                        $qty_neto = $esServicio ? 'qty_s_neto' : 'qty_p_neto';
                        $qty_com_neto = $esServicio ? 'qty_com_s_neto' : 'qty_com_p_neto';

                        $this->{$qty_neto} +=  $disccount_price_neto == 0 ? $current_price_neto : $disccount_price_neto;
                        $this->{$qty_com_neto} += $comission_neto;
                        $this->{$qty} += $disccount_price == 0 ? $current_price : $disccount_price;
                        $this->{$qty_com} += $comission;

                        // Actualizar datos en el array de comisiones
                        $dataComisiones[$qty_neto][$index] +=  $disccount_price_neto == 0 ? $current_price_neto : $disccount_price_neto;
                        $dataComisiones[$qty_com_neto][$index] += $comission_neto;
                        $dataComisiones[$qty][$index] += $disccount_price == 0 ? $current_price : $disccount_price;
                        $dataComisiones[$qty_com][$index] += $comission;

                    } 
                }
            }
            return $dataComisiones;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 263313InformeEmpleados"] );
        }
    }
    
    private function loadAssignments($movimientos) {
        try{
            $asignaciones=[];
            if(isset($movimientos)){
                foreach($movimientos as $movimiento){
                    $asignaciones[]=$movimiento->details;
                }   
            }
            return $asignaciones;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 321314InformeEmpleados"] );
        }
    }
    private function loadAssignmentsSP($movimientos) {
        try{
            $asignaciones=[];
            if(isset($movimientos)){
                foreach($movimientos as $movimiento){
                    $asignaciones[]=$movimiento->details_product;
                }   
            }
            return $asignaciones;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 334315InformeEmpleados"] );
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
                'total_incomes'   => array_map(function($qty1, $qty2) {
                    return $qty1 + $qty2;
                }, $dataComisiones1['total_incomes'], $dataComisiones2['total_incomes']),
                'total_incomes_neto'   => array_map(function($qty1, $qty2) {
                    return $qty1 + $qty2;
                }, $dataComisiones1['total_incomes_neto'], $dataComisiones2['total_incomes_neto'])
            ];

            return $dataComisionesMerged;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 347316InformeEmpleados"] );
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 366317InformeEmpleados"] );
        }
    }
    private function calculatePropinas($ventas,$citas,$empleados)
    {
        try{
            $terminales =[];
            $dataPropinas=[];
            foreach($empleados as $empleado){
                $dataEmpleados['empleado'][]=$empleado->first_name . ' ' . $empleado->last_name;
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
                        $this->qty_tarjeta += $qty;
                        break;
                    default:
                        $method = $propina->metodoPago->Payment_method;
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 378318InformeEmpleados"] );
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
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 444319InformeEmpleados"] );
        }
    }
    private function calculateClientes($ventas,$citas,$empleados)
    {
        try{
            $dataClientes=$this->acumularClientes([$citas,$ventas],$empleados);
            if($dataClientes!==null){

                $index = count($dataClientes['empleado']);
                $dataClientes['empleado'][$index] = 'Total';
                $dataClientes['qty'][$index] = $this->qty;
                $dataClientes['qty_clientesNew'][$index] = $this->qty_clientesNew;
                $dataClientes['qty_regresan'][$index] = $this->qty_regresan;
                $dataClientes['qty_regresan_w_estilista'][$index] = $this->qty_regresan_w_estilista;
            }
            return $dataClientes;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 462320InformeEmpleados"] );
        }
    }
    private function acumularClientes($items,$empleados)
    {
        try{
            $clientes = [];
            $data = [];
            $clientesNew =[];
            $dataEmpleados =[];
            foreach($empleados as $empleado){
                $dataEmpleados['empleado'][]=$empleado->first_name . ' ' . $empleado->last_name;
            }
            $data = $this->inicializarData(['qty','qty_regresan','qty_regresan_w_estilista','qty_clientesNew'],count($empleados));
            $dataEmpleados = array_merge($data,$dataEmpleados);
            foreach($items as $movimientos)
            {
                foreach($movimientos as $movimiento)
                {
                    $empleados_mov =[];
                    $customer = $movimiento->customer;
                    $clientes[] = $customer;
                    foreach($movimiento->details as $asignacion){
                        $empleados_mov []= $asignacion->empleado;
                    }
                    foreach($empleados_mov as $empleado){
                        if (!isset($this->uniqueCustomerIds[$empleado->id])) {
                            $this->uniqueCustomerIds[$empleado->id] = [];
                        }
                        $data = $this->contarClientesAtendidos($dataEmpleados,$customer,$empleado->id);
                        if ($data !== null) {
                            $dataEmpleados = array_merge($dataEmpleados, $data);
                        }
                    }
                }
            }
            $clientesNew = $this->validarCreacion($clientes);
            $primeraAtencion = $this->buscarPrimerosMovimientos($clientesNew);
            $dataEmpleados = array_merge ($primeraAtencion,$dataEmpleados);
            return $dataEmpleados;  
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 479321InformeEmpleados"] );
        }
    }
    private function contarClientesAtendidos($dataEmpleados,$customer,$empleado_id)
    {
        try{
                // Verifica el Id del cliente
            if ($customer!=null && !in_array($customer->id, $this->uniqueCustomerIds[$empleado_id])) {
                $index = $this->employeeIndex[$empleado_id] ?? null;

                if ($index !== null) {
                    $citas = $customer->citas;
                    $compras = $customer->compras;
                    if($citas->count() > 1 || $compras->count() > 1){
                        $ultimo_servicio = $citas->slice(-2, 1)->first();
                        $empleado = $this->acumularEmpleados($ultimo_servicio,true);
                        if($empleado === $empleado_id){
                            $dataEmpleados['qty_regresan_w_estilista'][$index] += 1;
                            $this->qty_regresan_w_estilista += 1;
                        }
                        $dataEmpleados['qty_regresan'][$index] += 1;
                        $this->qty_regresan +=1;
                    }
                    // Incrementa el contador de clientes atendidos si no se ha contabilizado ese cliente
                    $dataEmpleados['qty'][$index] += 1;
                    $this->qty+=1;
                    $this->uniqueCustomerIds[$empleado_id][] = $customer->id;  // Se añade el id para descartarlo en un futuro
                }
            }
            return $dataEmpleados; 
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 516322InformeEmpleados"] );
        }
    }
    private function acumularEmpleados($items,$tomarUno)
    {
        try{
            if($items){
                foreach($items->details as $asignacion)
                {
                    if($tomarUno){
                        $data = $asignacion->empleado_id;
                    }else{
                        $data=[];
                        $data[]=$asignacion->empleado_id;;
                    }
                }
                return $data;
            }
            return [];
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 544323InformeEmpleados"] );
        }
    }
    private function validarCreacion($clientes)
    {
        try{
            $clientesFiltrados = [];
            if($this->is_interval==false){
                foreach ($clientes as $cliente) {
                    if(!empty($cliente['created_at'])){
                        if (date('Y-m-d', strtotime($cliente['created_at'])) === $this->start && $cliente->procedencia_id == 7) {
                            $clientesFiltrados[] = $cliente;
                        }
                    }
                }
            }else{
                foreach ($clientes as $cliente) {
                    var_dump($cliente); // Muestra el valor actual
                    if(!empty($cliente['created_at'])){
                        $fechaCliente = strtotime($cliente['created_at']);
                        $fechaInicio = strtotime($this->start);
                        $fechaFin = strtotime($this->end);
                    
                        if ($fechaCliente >= $fechaInicio && $fechaCliente <= $fechaFin && $cliente->procedencia_id == 7) {
                            $clientesFiltrados[] = $cliente;
                        }
                    }
                }
            }
            return $clientesFiltrados;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 556324InformeEmpleados"] );
        }
    }
    private function buscarPrimerosMovimientos($clientes)
    {
        try{
            $dataClientesNew = $this->inicializarData(['qty_clientesNew'],count($this->employeeIndex));
            foreach($clientes as $cliente)
            {
                $primeraCita = $cliente->citas->first();
                $primeraCompra = $cliente->compras->first();
                if ($primeraCita !== null && $primeraCompra === null ) {
                    $primerosMov = $primeraCita->details;
                }
                // Verificar si hay al menos una compra
                elseif ($primeraCompra !== null && $primeraCita === null) {
                    $primerosMov = $primeraCompra->details;
                }
                elseif ($primeraCita->created_at > $primeraCompra->created_at){
                    $primerosMov=$primeraCompra->details;
                }
                elseif ($primeraCita->created_at > $primeraCompra->created_at){
                    $primerosMov = $primeraCita->details;
                }
                // Si no hay ni citas ni compras para este cliente
                else {
                    continue; // Pasar al siguiente cliente
                }
                $dataClientesNew=$this->determinarCreador($primerosMov,$dataClientesNew);
            }
            return $dataClientesNew;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 581325InformeEmpleados"] );
        }
    }
    private function determinarCreador($primerosMov,$dataClientesNew)
    {
        try{
            $creador = null;
            
            foreach($primerosMov as $primerMov){
                if($creador == null){
                    $creador = $primerMov->empleado_id;
                }
            }
            $this->qty_clientesNew += 1;

            $index = $this->employeeIndex[$creador];
            $dataClientesNew['qty_clientesNew'][$index] +=1;
            return $dataClientesNew;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 612326InformeEmpleados"] );
        }
    }
    private function calculateLog($users)
    {
        try{


            foreach($users as $user){
                $dataUsers['users'][]= $user->empleado!==null ? $user->empleado->first_name . ' ' . $user->empleado->last_name : $user->name;
            }
            
            foreach ($users as $index => $user) {
                $userIndex[$user->id] = $index;
            }
            $data = $this->inicializarData(['connection_time','ip'],count($users));
            $dataUsers = array_merge($data,$dataUsers);
            foreach($users as $user){
                $last_log = $user->logs->last();
                if(isset($last_log->out) && !$last_log->out){
                    $dataUsers['connection_time'][$userIndex[$user->id]]='Conectado Actualmente';
                }else{
                    if($last_log!=null){
                        $last_logout_time=$last_log->created_at;
                        $last_log_time=$user->logs->slice(-2, 1)->first()->created_at;
                        $duration=$last_log_time->diffInMinutes($last_logout_time);
                        $dataUsers['connection_time'][$userIndex[$user->id]]=$duration;
                    }
                }
                if($last_log!=null){
                    $dataUsers['ip'][$userIndex[$user->id]]=$last_log->ip;
                }
            }
            return $dataUsers;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 657327InformeEmpleados"] );
        }
    }
    private function calculateDurationDates($movimientos, $empleados)
    {
        try{
            $dataDuration = [];

            foreach ($empleados as $empleado) {
                $dataDuration['label'][] = $empleado->first_name . ' ' . $empleado->last_name;
            }

            $data = $this->inicializarData(['qty'], count($empleados));
            $dataDuration = array_merge($data, $dataDuration);

            $asignaciones = $this->acumularPropinas($movimientos, true);

            foreach ($asignaciones as $asignacion) {
                $date = $asignacion->date;
                $index = $this->employeeIndex[$asignacion->empleado_id];
                $inicio_real = Carbon::parse($date->start);
                $fin_real = $date->updated_at;
                $duracion_real = $inicio_real->diffInMinutes($fin_real);

                if ($duracion_real < 1440) {
                    $empleados_involucrados = count($this->acumularEmpleados($date, false));
                    $empleados_involucrados = $empleados_involucrados == 1 ? $empleados_involucrados * count($date->details) : $empleados_involucrados;
                    
                    $dataDuration['qty'][$index] += $duracion_real / $empleados_involucrados;
                } else {
                    $dataDuration['qty'][$index] += $asignacion->servicio->duration;
                }
            }
            return json_encode($dataDuration);  
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 737328InformeEmpleados"] );
        }
    }
    
    public function generatePdf(){
        $this->useDate();
        $this->emit('print');
    }

    public function generateExcelEmpleados()
    {
        $date = $this->currentDateC;
        $date = $date->format('Y_m_d_H_i_s');
        $fileName = 'informe' . $date . '.xlsx';
        $this->useDate();
        return Excel::download(new ReporteEmpleados($this->dataComisiones,$this->terminales,$this->dataPropinas,$this->dataClientes,$this->dataLog),$fileName);
    }
}
