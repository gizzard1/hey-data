<?php

namespace App\Http\Livewire;

use App\Exports\ReporteHistorico;
use App\Models\Asignacion_servicio;
use App\Models\Asignacion_venta;
use App\Models\cita;
use App\Models\cliente;
use App\Models\Empleado;
use App\Models\gasto;
use App\Models\metodo_pago_servicio;
use App\Models\metodo_pago_venta;
use App\Models\Propina;
use App\Models\venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class Informe extends Component
{
    use WithPagination;
    public $currentDate,$start,$end,$currentDateEnd,$currentDateC,$currentDateCEnd,$is_interval=false,$dataEmpleados;
    public $lastCurrentDateC, $lastCurrentDateCEnd;
    private $total_s=0,$total_v=0,$comisiones=0,$gastos_a=0,$gastos_n=0,$gastos_a_neto=0,$gastos_n_neto=0,$total_citas_pagadas=0,$total_ventas_pagadas=0,$total_citas_pendientes=0,$duracion_citas=0,$clientes_nuevos=0,$clientes_nuevos_last_period=0;
    public $comparison_table = [
        'incomes' => 0, 'pending_dates' => 0,'new_custs'=> 0,'dates'=> 0,'sales'=> 0,
    ];
    // private $total_citas_canceladas=0;
    private $dataSales,$dataExpenses,$totalIncomes,$totales;
    protected $paginationTheme = 'bootstrap';
    public $newPeriodo;
    private $iva,$iva_s,$ingresoTotal,$ingresoTotalPasado,$ingresoTotal_neto,$ingresoTotal_neto_s,$ingresoTotal_neto_p;
    private $ingresoTarjeta=[],$ingresoEfectivo=0,$ingresoCard=0,$puntosCanjeados=0,$ingresoMsi=0,$ingresoTarjetaPropina=[];
    private $total_ventas=0,$total_ventas_neto=0,$total_cust=0,$total_servicios=0,$total_servicios_neto=0;
    private $dataSalesPdf=[];
    public $dataExpensesList;
    public $totalGastos=0,$gastosAcreditables=[],$gastosNoAcreditables=[],$gastos=[],$dataExpensesFinal=[],$pestaña=1;
    private $uniqueCustomerIds=[];
    public $clientesAtendidos=0;
    public $totalTips=0,$totalTipsNeto=0;
    public $nextDates=[], $diff_period=0;
    public $dataPeriodNormalizado=null,$serviceOrCategory='service';
    private $ranking_services = null,$ranking_categories = null;
    private $global_services_counter= 0, $global_categories_counter= 0;

    public function mount()
    {
        if(!$this->validateSuscription()){
            return redirect()->route('suscripcion');
        }
        if(session()->has('selectedDates')){
            $this->setDatesFromPeriod(session('selectedDates'));
        }else{
            $this->loadFecha();
        }
    }
    private function validateSuscription()
    {
        $rights = true;

        $suscription = Auth::user()->salon->suscription;

        if($suscription == 'free') {
            $hoy = Carbon::now();
            $salon = Auth::user()->salon;
            $lastest_suscription = $salon->suscripciones()->latest()->first();
            if($lastest_suscription){
                if($hoy->diffInDays($lastest_suscription) > 7) {
                    $rights = false;
                }
            }else{
                if($hoy->diffInDays($salon->created_at) > 7) {
                    $rights = false;
                }
            }
            
            $rights = false;
        }

        return $rights;
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

            $this->diff_period = $this->currentDateC->diffInDays($this->currentDateCEnd);
            $this->lastCurrentDateC = $this->currentDateC->copy()->subDays($this->diff_period + 1);
            $this->lastCurrentDateCEnd = $this->currentDateCEnd->copy()->subDays($this->diff_period + 1);

            $this->useDate();
            $this->loadChartsWithNewPeriod();
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 30127Informe"] );
        }
    }
    
    private function loadFecha()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 365140Informe"] );
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
    public function setDate($selectedDate)
    {
        try{
            $this->setDatesFromPeriod([Carbon::parse($selectedDate[0])]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 53128Informe"] );
        }
    }

    protected $listeners = ['refresh' => '$refresh','datesSelected' => 'setDatesFromPeriod',
    'prevDay','dateSelected' => 'setDate','loadCharts','changeWindow'];

    public function loadCharts()
    {
        $this->loadChartsWithNewPeriod();
    }

    #función que actualiza las gráficas con la nueva fecha
    private function loadChartsWithNewPeriod()
    {
        try{
            $this->emit('dateUpdated', $this->currentDate,$this->currentDateEnd);
            $this->emit('refrescarCharts',['dataSales' => $this->dataSales,'dataPeriodNormalizado' => $this->dataPeriodNormalizado, 'ranking_services' => $this->ranking_services, 'ranking_categories' => $this->ranking_categories, 'dataEmpleados' => $this->dataEmpleados]);
            // $this->emit('refrescarCharts',['dataSales' => $this->dataSales,'dataExpenses' => $this->dataExpenses]);
            // $this->emit('refrescarCharts',['dataSales' => $this->dataSales,'dataExpenses' => $this->dataExpenses,'totalIncomes' => $this->totalIncomes]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 75129Informe"] );
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
    private function acumularMetodosPeriodoPasado($arrays)
    {
        $total = 0;
        foreach($arrays as $items){
            foreach($items as $metodo){
                if($metodo->payment_method_id  != 4){
                    $change = $metodo->change ?? 0;
                    //Declarar el ingreso del método
                    $qty=$metodo->amount-$change;
                    $total+=$qty;
                }
            }
        }
        return $total;
    }
    private function acumularEstatus($arrays)
    {
        $isDate = false;
        $total_citas_pagadas = 0;
        $total_ventas_pagadas = 0;
        $total_citas_pendientes = 0;
        foreach($arrays as $transacciones){
            foreach($transacciones as $transaccion){
                switch($transaccion->status){
                    case 'Pagada':
                        $isDate ? $total_citas_pagadas += 1 : $total_ventas_pagadas += 1;
                        break;
                    case 'Pendiente' || 'Agendada':
                        $total_citas_pendientes += 1;
                        break;
                }       
            }
            $isDate = true;
        }
        return ['total_citas_pagadas' => $total_citas_pagadas, 'total_ventas_pagadas' => $total_ventas_pagadas, 'total_citas_pendientes' => $total_citas_pendientes];
    }
    
    /**
     * Determina la granularidad óptima según el período
     * Retorna array con: 'type' (day|week|month), 'interval' (cantidad de unidades), 'format' (formato Carbon)
     */
    private function determinarGranularidad()
    {
        $dias = $this->diff_period + 1;
        
        // Rango de 1-3 días: agregación por día
        if ($dias <= 3) {
            return [
                'type' => 'day',
                'interval' => 1,
                'format' => 'D MMM', // "17 Dic"
                'count' => $dias
            ];
        }
        
        // Rango de 4-14 días: agregación por día (máximo 14 segmentos)
        if ($dias <= 14) {
            return [
                'type' => 'day',
                'interval' => 1,
                'format' => 'D MMM',
                'count' => $dias
            ];
        }
        
        // Rango de 14-40 días: agregación por semana
        if ($dias <= 40) {
            $semanas = ceil($dias / 7);
            return [
                'type' => 'week',
                'interval' => 7,
                'format' => 'w \s\e\m', // "1 sem", "2 sem"
                'count' => $semanas
            ];
        }
        
        // Rango de 91-366 días: agregación por mes
        if ($dias <= 366) {
            $meses = $this->currentDateCEnd->diffInMonths($this->currentDateC) + 1;
            return [
                'type' => 'month',
                'interval' => 1,
                'format' => 'MMM', // "Dic", "Ene"
                'count' => $meses
            ];
        }
        
        // Más de 365 días: agregación por año
        $años = $this->currentDateCEnd->diffInYears($this->currentDateC) + 1;
        return [
            'type' => 'year',
            'interval' => 1,
            'format' => 'YYYY', // "2024", "2025"
            'count' => $años
        ];
    }
    
    /**
     * Crea la estructura de períodos vacía
     * Retorna array con periodos inicializados en 0
     */
    private function crearEstructuraPeriodos()
    {
        $granularidad = $this->determinarGranularidad();
        $periodos = [];
        $periodoActual = $this->currentDateC->copy();
        
        switch ($granularidad['type']) {
            case 'day':
                for ($i = 0; $i <= $this->diff_period; $i++) {
                    $labelPeriodo = $periodoActual->locale('es')->isoFormat($granularidad['format']);
                    $keyPeriodo = $periodoActual->format('Ymd');
                    $periodos[$keyPeriodo] = [
                        'label' => $labelPeriodo,
                        'value' => 0,
                        'inicio' => $periodoActual->copy()->startOfDay(),
                        'fin' => $periodoActual->copy()->endOfDay()
                    ];
                    $periodoActual->addDay();
                }
                break;
            
            case 'week':
                $inicioSemana = $this->currentDateC->copy()->startOfWeek();
                for ($i = 0; $i < $granularidad['count']; $i++) {
                    $finSemana = $inicioSemana->copy()->endOfWeek();

                    if ($inicioSemana > $this->currentDateCEnd) break;
                    if ($finSemana > $this->currentDateCEnd) $finSemana = $this->currentDateCEnd;
                    
                    $labelPeriodo = 'Sem ' . $inicioSemana->locale('es')->isoFormat('D MMM');
                    $keyPeriodo = $inicioSemana->format('Ymd');
                    $periodos[$keyPeriodo] = [
                        'label' => $labelPeriodo,
                        'value' => 0,
                        'inicio' => $inicioSemana->copy(),
                        'fin' => $finSemana->copy()
                    ];
                    $inicioSemana->addWeek();
                }

                break;
            
            case 'month':
                $mesActual = $this->currentDateC->copy()->startOfMonth();
                for ($i = 0; $i < $granularidad['count']; $i++) {
                    $finMes = $mesActual->copy()->endOfMonth();
                    if ($mesActual > $this->currentDateCEnd) break;
                    if ($finMes > $this->currentDateCEnd) $finMes = $this->currentDateCEnd;
                    
                    $labelPeriodo = $mesActual->locale('es')->isoFormat('MMM YY');
                    $keyPeriodo = $mesActual->format('Ym');
                    $periodos[$keyPeriodo] = [
                        'label' => $labelPeriodo,
                        'value' => 0,
                        'inicio' => $mesActual->copy(),
                        'fin' => $finMes->copy()
                    ];
                    $mesActual->addMonth();
                }
                break;
            
            case 'year':
                $añoActual = $this->currentDateC->copy()->startOfYear();
                for ($i = 0; $i < $granularidad['count']; $i++) {
                    $finAño = $añoActual->copy()->endOfYear();
                    if ($añoActual > $this->currentDateCEnd) break;
                    if ($finAño > $this->currentDateCEnd) $finAño = $this->currentDateCEnd;
                    
                    $labelPeriodo = $añoActual->locale('es')->isoFormat('YYYY');
                    $keyPeriodo = $añoActual->format('Y');
                    $periodos[$keyPeriodo] = [
                        'label' => $labelPeriodo,
                        'value' => 0,
                        'inicio' => $añoActual->copy(),
                        'fin' => $finAño->copy()
                    ];
                    $añoActual->addYear();
                }
                break;
        }
        
        return $periodos;
    }
    
    
    private function loadFinancialInfo($ventas,$citas,$collIds)
    {
        try{
            $metodos_citas = metodo_pago_servicio::select('cita_id', 'amount','payment_method_id', 'change','created_at')
                ->with([
                    'metodoPago' => function($q) {
                        $q->select('Payment_method','id');
                    },
                ])
                ->whereBetween('created_at', [$this->currentDateC, $this->currentDateCEnd])
                ->whereIn('cita_id', $collIds['citaIds'])
                ->get();

            $metodos_ventas = metodo_pago_venta::select('venta_id', 'amount','payment_method_id', 'change','created_at')
                ->with([
                    'metodoPago' => function($q) {
                        $q->select('Payment_method','id');
                    },
                ])
                ->whereIn('venta_id', $collIds['ventaIds'])
                ->whereBetween('created_at', [$this->currentDateC, $this->currentDateCEnd])
                ->get();

            // Datos del periodo anterior
            $metodos_citas_pasado = metodo_pago_servicio::select('cita_id', 'amount','payment_method_id', 'change','created_at')
                ->with([
                    'metodoPago' => function($q) {
                        $q->select('Payment_method','id');
                    },
                ])
                ->whereBetween('created_at', [$this->lastCurrentDateC, $this->lastCurrentDateCEnd])
                ->whereIn('cita_id', $collIds['citaIds'])
                ->get();

            $metodos_ventas_pasado = metodo_pago_venta::select('venta_id', 'amount','payment_method_id', 'change','created_at')
                ->with([
                    'metodoPago' => function($q) {
                        $q->select('Payment_method','id');
                    },
                ])
                ->whereIn('venta_id', $collIds['ventaIds'])
                ->whereBetween('created_at', [$this->lastCurrentDateC, $this->lastCurrentDateCEnd])
                ->get();
                
            $this->ingresoTotalPasado = $this->acumularMetodosPeriodoPasado([$metodos_citas_pasado, $metodos_ventas_pasado]);

            $citaPropinas = Propina::select('venta_id','cita_id', 'amount','payment_method_id','created_at')
                ->with([
                    'metodoPago' => function($q) {
                        $q->select('Payment_method','id');
                    },
                ])
                ->whereIn('cita_id', $collIds['citaIds'] ?? [])
                ->whereBetween('created_at', [$this->currentDateC, $this->currentDateCEnd])
                ->get()
                ->groupBy('cita_id');


            $ventaPropinas = Propina::select('venta_id','cita_id', 'amount','payment_method_id','created_at')
                ->with([
                    'metodoPago' => function($q) {
                        $q->select('Payment_method','id');
                    },
                ])
                ->whereIn('venta_id', $collIds['ventaIds'] ?? [])
                ->whereBetween('created_at', [$this->currentDateC, $this->currentDateCEnd])
                ->get()
                ->groupBy('venta_id');
                
            $this->nextDates = $citas->clone()
                ->where('start', '>=', $this->currentDateC)
                ->get();
                    
            $ventas_pasado = $ventas->clone()->whereBetween('created_at', [$this->lastCurrentDateC, $this->lastCurrentDateCEnd])->get();
            $citas_pasado = $citas->clone()->whereBetween('start', [$this->lastCurrentDateC, $this->lastCurrentDateCEnd])->get();
            
            $estatusArrays = $this->acumularEstatus([$ventas_pasado, $citas_pasado]);

            $ventas_actual = $ventas->clone()->whereBetween('created_at', [$this->currentDateC, $this->currentDateCEnd])->get();
            $citas_actual = $citas->clone()->whereBetween('start', [$this->currentDateC, $this->currentDateCEnd])->get();

            $response=$this->acumularMetodos(0,1,$metodos_citas);
            $response=$this->acumularMetodos($response,0,$metodos_ventas);
            $totalTips=$this->acumularPropinas($citas_actual,0,$citaPropinas);
            $this->totalTips=$this->acumularPropinas($ventas_actual,$totalTips,$ventaPropinas);
            $this->acumularTransacciones($citas_actual,1);
            $this->acumularTransacciones($ventas_actual,0);
            $this->ingresoTotal=$this->total_s+$this->total_v;
            $this->comparison_table['incomes'] = $this->calculatePercentageChange($this->ingresoTotalPasado, $this->ingresoTotal);
            $this->comparison_table['pending_dates'] = $this->calculatePercentageChange($estatusArrays['total_citas_pendientes'], $this->total_citas_pendientes);
            $this->comparison_table['dates'] = $this->calculatePercentageChange($estatusArrays['total_citas_pagadas'], $this->total_citas_pagadas);
            $this->comparison_table['sales'] = $this->calculatePercentageChange($estatusArrays['total_ventas_pagadas'], $this->total_ventas_pagadas);

            // $efectivoAjustado=$this->ajustarEfectivo($totalMethods);
            
            // if($efectivoAjustado< 0){
            //     $this->ingresoEfectivo += $efectivoAjustado; 
            // }

            $other_total = 0;
            foreach($this->ingresoTarjeta as $qty){
                $other_total += $qty;
            }

            $total_donut = $this->ingresoEfectivo + $this->ingresoCard + $this->ingresoMsi + $other_total + $this->totalTips;

            if ($total_donut == 0) {
                $total_donut = 1; // Evitar división por cero
            }
            //Arreglo asociativo para mostrar en la primer gráfica
            $dataSales = [
                'label' => ['Efectivo','Tarjeta','MSI','Propina','Otros'],
                'amount' => [number_format($this->ingresoEfectivo,2,'.',','),number_format($this->ingresoCard,2,'.',','),number_format($this->ingresoMsi,2,'.',','),number_format($this->totalTips,2,'.',','),number_format($other_total,2,'.',',')],
                'percent' => [number_format($this->ingresoEfectivo/$total_donut,2,'.',','),number_format($this->ingresoCard/$total_donut,2,'.',','),number_format($this->ingresoMsi/$total_donut,2,'.',','),number_format($this->totalTips/$total_donut,2,'.',','),number_format($other_total/$total_donut,2,'.',',')],
                'color' => ['#3DC5AB','#41B8D5','#2E8BBA','#EBD99E','#5E7391'],
            ];

            foreach($this->ingresoTarjeta as $label => $qty){
                $dataSales['label'][] = $label;
                $dataSales['amount'][] = number_format($qty,2,'.',',');
                $dataSales['percent'][] = number_format($qty/$total_donut,2,'.',',');
                $dataSales['color'][] = '#5E7391';
            }

            //Se guarda el arreglo en caso de que el usuario exporte a pdf el informe
            $this->dataSalesPdf = $dataSales;
            
            // Generar datos normalizados por período para las gráficas
            
            $dataSales = json_encode($dataSales);
            // Convertir a formato final (sin fechas de inicio/fin)
            $datos = [];
            foreach ($response['periodos'] as $periodo) {
                $datos[] = [
                    'label' => $periodo['label'],
                    'value' => $periodo['value']
                ];
            }

            $this->dataPeriodNormalizado = json_encode($datos);
            return $dataSales;
            
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 108131Informe"] );
        }
    }
    private function filtrarGastos($movimientos)
    {
        $gastosAcreditables = [];
        $gastosNoAcreditables = [];
        $gastos = [];
        foreach($movimientos as $movimiento){
            if($movimiento->type=='Acreditable'){
                $this->gastos_a+=$movimiento->total;
                $this->gastos_a_neto+=$movimiento->total-($movimiento->total*$movimiento->iva);
                $gastosAcreditables[]=$movimiento;
            }elseif($movimiento->type=='No acreditable'){
                $this->gastos_n+=$movimiento->total;
                $this->gastos_n_neto+=$movimiento->total-($movimiento->total*$movimiento->iva);
                $gastosNoAcreditables[]=$movimiento;
            }
        }
        
        $gastos= array_merge($gastosAcreditables,$gastosNoAcreditables);
        $dataExpenses = $this->cleanDataExpenses();
        $dataTypeExpensesA = $this->cleanDataExpenses();
        $dataTypeExpensesNA = $this->cleanDataExpenses();

        $dataExpenses = $this->acumularGastosArray($gastos,$dataExpenses,'payment_method');
        
        $dataTypeExpensesA = $this->acumularGastosArray($gastosAcreditables,$dataTypeExpensesA,'description');
        $dataTypeExpensesNA = $this->acumularGastosArray($gastosNoAcreditables,$dataTypeExpensesNA,'description');
        if($this->pestaña == 2){
            $dataExpenses = $this->cleanDataExpenses();
            $dataExpenses = $this->acumularGastosArray($gastosAcreditables,$dataExpenses,'payment_method');
        }elseif($this->pestaña == 3){
            $dataExpenses = $this->cleanDataExpenses();
            $dataExpenses = $this->acumularGastosArray($gastosNoAcreditables,$dataExpenses,'payment_method');
        }
        $this->calculateTotalGastos($dataExpenses);
        $this->dataExpensesFinal = $dataExpenses;
        return ['dataExpenses' => $dataExpenses, 'dataTypeExpensesA' => $dataTypeExpensesA, 'dataTypeExpensesNA' => $dataTypeExpensesNA];
    }
    private function cleanDataExpenses(){
        return [
            'label' => [],
            'qty' => [],
            'qty_neto' => []
        ];
    }
    private function calculateTotalGastos($array)
    {
        foreach($array['label'] as $index => $label){
            $this->totalGastos += $array['qty'][$index];
        }
    }
    public function changeWindow($type)
    {
        $this->pestaña = $type;
        
        $this->totalGastos=0;
        $this->useDate();
        $this->loadChartsWithNewPeriod();
    }
    private function acumularGastosArray($items,$dataExpenses,$data)
    {
        if(isset($items)){
            foreach($items as $item){
                $qty=$item->total;
                $iva=$item->iva;
                $reference = $item->{$data};
                $dataExpenses = $this->createOrUpdateArrayExpenses($reference, $dataExpenses, $qty,$iva);
            }
        }
        return $dataExpenses;
    }
    private function createOrUpdateArrayExpenses($reference, $dataExpenses, $qty, $iva)
    {
        // Buscar el índice correspondiente en 'label'
        $index = array_search($reference, $dataExpenses['label']);
        if ($index !== false) {
            $dataExpenses['qty'][$index] += floatval($qty);
            $dataExpenses['qty_neto'][$index] += floatval($qty-($iva*$qty));
        } else {
            $dataExpenses['label'][] = $reference;
            $dataExpenses['qty'][] = floatval($qty);
            $dataExpenses['qty_neto'][] = floatval($qty-($iva*$qty));
        }
        return $dataExpenses;
    }
    private function loadGastos($gastos)
    {
        try{
            $this->totalGastos = 0;
            $arrays = $this->filtrarGastos($gastos);
            $dataExpenses = $arrays['dataExpenses'];
            $this->gastos = [$arrays['dataTypeExpensesA'],$arrays['dataTypeExpensesNA']];
            $dataExpenses = json_encode($dataExpenses);
            return $dataExpenses;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 136132Informe"] );
        }
    }
    // private function loadIncomesByEmployee($items,$empleados)
    // {
    //     try{
    //         foreach($empleados as $empleado){
    //             $dataIncomes['label'][]=$empleado->first_name . ' ' . $empleado->last_name;
    //         }
    //         $employeeIndex = [];
    //         foreach ($empleados as $index => $empleado) {
    //             $employeeIndex[$empleado->id] = $index;
    //         }

    //         // Inicializar el arreglo qty con valores predeterminados
    //         $dataIncomes['qty'] = array_fill(0, count($empleados), 0);

    //         // Llenar el arreglo qty basado en las asignaciones
    //         foreach ($items as $item) {
    //             foreach($item as $asignacion){
    //                 $index = $employeeIndex[$asignacion->empleado->id];
    //                 $dataIncomes['qty'][$index] += ($asignacion->disccount_price == 0) ? $asignacion->current_price : $asignacion->disccount_price; 
    //             }
    //         }
    //         return $dataIncomes;
    //     }catch(\Throwable $th){
    //         $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 173133Informe"] );
    //     }
    // }
    private function getRandomColor()
    {
        $presets = ['#3DC5AB','#41B8D5','#2E8BBA','#5E7391','#EBD99E'];
        return $presets[array_rand($presets)];
    }
    private function loadDataEmpleados($items_p, $items_s, $empleados)
    {
        try{
            $items_v = $items_p['items_v'];
            $items_vc = $items_p['items_vc'];
            $dataEmpleados = [];
            $uniqueCustomerIds = [];
            $uniqueCustomerIdsD = [];
            foreach ($empleados as $empleado) {
                $dataEmpleados[] = [
                    'color' => $empleado->color_preset ?? $this->getRandomColor(),
                    'name' => $empleado->first_name . ' ' . $empleado->last_name,
                    'total_v' => 0,
                    'total_v_neto' => 0,
                    'total_d' => 0,
                    'total_d_neto' => 0,
                    'total_cust' => 0,
                    'duration' => 0,
                    'minutes' => 0,
                    'hours' => 0
                ];
            }

            //Calculamos los ingresos teóricos obtenidos por empleado (Incluye los que están pendientes por pagar) 
            foreach ($items_v as $asignacion) {
                $venta = $asignacion->sale;
                $index = $empleados->search(function ($item) use ($asignacion) {
                    return $item->id === $asignacion->empleado_id;
                });

                if ($index !== false) {

                    //Se  obtiene el valor sin descuentos
                    $qty=$asignacion->current_price;

                    $dataEmpleados[$index]['total_v'] +=$qty;
                    $this->total_ventas += $qty;
                    $qty_bruto = $qty;
                    $dataEmpleados[$index]['total_v_neto'] += $qty_bruto/(1+$asignacion->iva);
                    $this->total_ventas_neto += $qty_bruto/(1+$asignacion->iva);

                    $customer_id = $venta->customer_id;

                    // Verifica el Id del cliente
                    if (!in_array($customer_id, $uniqueCustomerIds)) {
                        // Incrementa el contador de clientes atendidos si no se ha contabilizado ese cliente
                        $dataEmpleados[$index]['total_cust'] += 1;
                        $this->total_cust+=1;
                        $uniqueCustomerIds[] = $customer_id;  // Se añade el id para descartarlo en un futuro
                    }
                }
            }

            //Calculamos los ingresos teóricos obtenidos por empleado (Incluye los que están pendientes por pagar) 
            if(count($items_vc)>0){
                foreach ($items_vc as $asignacion) {
                    $index = $empleados->search(function ($item) use ($asignacion) {
                        return $item->id === $asignacion->empleado_id;
                    });

                    if ($index != false) {
                        //Se  obtiene el valor sin descuentos
                        $qty=$asignacion->current_price;

                        $dataEmpleados[$index]['total_v'] +=$qty;
                        $this->total_ventas += $qty;
                        $qty_bruto = $qty;
                        $dataEmpleados[$index]['total_v_neto'] += $qty_bruto/(1+$asignacion->iva);
                        $this->total_ventas_neto += $qty_bruto/(1+$asignacion->iva);
                    }
                }
            }

            foreach ($items_s as $asignacion) {
                $customer_id = $asignacion->date->customer_id;
                $index = $empleados->search(function ($item) use ($asignacion) {
                    return $item->id === $asignacion->empleado_id;
                });

                if ($index !== false) {
                    $qty=$asignacion->current_price;

                    $dataEmpleados[$index]['total_d'] +=$qty;
                    $this->total_servicios += $qty;
                    $qty_bruto = $qty;
                    $dataEmpleados[$index]['total_d_neto'] += $qty_bruto/(1+$asignacion->iva);
                    $this->total_servicios_neto += $qty_bruto/(1+$asignacion->iva);
                    
                    // Verifica el Id del cliente
                    if (!in_array($customer_id, $uniqueCustomerIdsD)) {
                        // Increment total_cust only if it's a new customer
                        $dataEmpleados[$index]['total_cust'] += 1;
                        $this->total_cust += 1;
                        $uniqueCustomerIdsD[] = $customer_id;  // Add the customer ID to the unique array
                    }

                    $dataEmpleados[$index]['duration'] += $asignacion->duration;
                    $this->duracion_citas += $asignacion->duration;
                }
            }
            $final_index = count($dataEmpleados);
            // Incrementar los valores correspondientes al último índice
            $dataEmpleados[$final_index]['name'] = 'Total';
            $dataEmpleados[$final_index]['total_v'] = $this->total_ventas;
            $dataEmpleados[$final_index]['total_v_neto'] = $this->total_ventas_neto;
            $dataEmpleados[$final_index]['total_d'] = $this->total_servicios;
            $dataEmpleados[$final_index]['total_d_neto'] = $this->total_servicios_neto;
            $dataEmpleados[$final_index]['total_cust'] = $this->total_cust;
            $dataEmpleados[$final_index]['duration'] = $this->duracion_citas;


            //Transladar minutos a formato hrs:min
            foreach($dataEmpleados as $empleado){
                $empleado=$this->setDuration($empleado);
            }
            return $dataEmpleados;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 198134Informe"] );
        }
    }
    private function setDuration($obj)
    {
        $minutes = $obj['duration'];
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        $obj['minutes'] = $remainingMinutes;
        $obj['hours'] = $hours;
        return $obj;
    }
    
    // private function fusionarArreglos($dataIncomes1,$dataIncomes2)
    // {
    //     try{
    //         // Fusionar los arreglos
    //         $dataIncomesMerged = [
    //             'label' => array_merge($dataIncomes1['label']),
    //             'qty'   => array_map(function($qty1, $qty2) {
    //                 return $qty1 + $qty2;
    //             }, $dataIncomes1['qty'], $dataIncomes2['qty'])
    //         ];

    //         $dataIncomesMerged = json_encode($dataIncomesMerged);
    //         return $dataIncomesMerged;
    //     }catch(\Throwable $th){
    //         $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 266135Informe"] );
    //     }
    // }
    private function useDate()
    {
        try{
            $salon_id = Auth::user()->salon_id;
            $empleados = Empleado::where('salon_id',$salon_id)->get();

            $ventasQuery = venta::select('disccount','total','id','status')
                ->where('salon_id', Auth::user()->salon_id)
                ->with([
                    'details' => function($q) {
                        $q->select('venta_id', 'empleado_id','disccount_price', 'iva','current_price');
                    },
                ]);

            $ventasQuery->whereBetween('created_at', [$this->lastCurrentDateC, $this->currentDateCEnd]);
            
            // Obtener IDs de citas válidas en una sola consulta
            $ventaIds = $ventasQuery->pluck('id');
            
            $citasQuery = cita::select('disccount','total','id','status','customer_id')
                ->where('salon_id', Auth::user()->salon_id)
                ->with([
                    'details' => function($q) {
                        $q->select('cita_id', 'empleado_id','disccount_price', 'iva','current_price','start','selected_service')
                            ->with(['servicio' => function($q) {
                                $q->select('name', 'id')->with('categorias:id,name');
                            },'empleado' => function($q) {
                                $q->select('first_name', 'last_name', 'id');
                            }]);
                    },
                    'details_product' => function($q) {
                        $q->select('cita_id', 'quantity', 'empleado_id','disccount_price', 'iva','current_price');
                    },
                    'customer' => function($q) {
                        $q->select('first_name', 'last_name', 'id');
                    },
                ]);

            $citasQuery->whereBetween('start', [$this->lastCurrentDateC, $this->currentDateCEnd]);

            // Obtener IDs de citas válidas en una sola consulta
            $citaIds = $citasQuery->pluck('id');

            $gastos = gasto::where('salon_id', $salon_id)
                ->whereBetween('date', [$this->currentDateC,$this->currentDateCEnd])
                ->get();

            // Obtener todos los detalles filtrados por citas y fecha, con relaciones
            $asignaciones_servicios = Asignacion_servicio::select('cita_id', 'empleado_id','disccount_price', 'iva','current_price')
                ->with([
                    'date' => function($q) {
                        $q->select('customer_id', 'id');
                    }
                ])
                ->whereIn('cita_id', $citaIds)
                ->whereBetween('start', [$this->currentDateC, $this->currentDateCEnd])
                ->get();

            $asignaciones_venta_cita = Asignacion_venta::select('cita_id', 'empleado_id','disccount_price', 'iva','current_price')
                ->whereIn('cita_id', $citaIds)
                ->whereBetween('created_at', [$this->currentDateC, $this->currentDateCEnd])
                ->get();

            $venta_producto = Asignacion_venta::select('venta_id', 'empleado_id','disccount_price', 'iva','current_price')
                ->with([
                    'sale' => function($q) {
                        $q->select('customer_id', 'id');
                    }
                ])
                ->whereIn('venta_id', $ventaIds)
                ->whereBetween('created_at', [$this->currentDateC, $this->currentDateCEnd])
                ->get();

            $this->clientes_nuevos = cliente::where('salon_id', $salon_id)
                ->whereBetween('created_at', [$this->currentDateC, $this->currentDateCEnd])
                ->count();

            $this->clientes_nuevos_last_period = cliente::where('salon_id', $salon_id)
                ->whereBetween('created_at', [$this->lastCurrentDateC, $this->lastCurrentDateCEnd])
                ->count();

            $this->comparison_table['new_custs'] = $this->calculatePercentageChange($this->clientes_nuevos_last_period, $this->clientes_nuevos);

            $info =[
                'ventas' => $ventasQuery,
                'citas' => $citasQuery,
                'gastos' => $gastos,
                'asignaciones_servicios' => $asignaciones_servicios,
                // Arreglo dentro del arreglo para diferenciar las ventas de producto en citas y las independientes
                'asignaciones_ventas' => [
                    'items_v' => $venta_producto,
                    'items_vc' => $asignaciones_venta_cita,
                ],
                'empleados' => $empleados,
                'collIds' => [
                    'citaIds' => $citaIds,
                    'ventaIds' => $ventaIds
                ],
            ];
            $this->recalculate($info);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 283136Informe"] );
        }
    }
    private function calculatePercentageChange($last, $current)
    {
        if ($last == 0) {
            $change = $current == 0 ? 0 : 100;
        } else {
            $change = (($current - $last) / $last) * 100;
        }
        
        // Agregar símbolo y porcentaje
        $symbol = $change > 0 ? '↗ +' : ($change < 0 ? '↘ ' : '');
        return $symbol . number_format($change, 2) . '%';
    }
    private function obtenerTotales()
    {
        try{
            $total_incomes = $this->ingresoTotal;
            $total_incomes_neto = $this->ingresoTotal_neto_s+$this->ingresoTotal_neto_p;
            $total_incomes_neto_s = $this->ingresoTotal_neto_s;
            $total_incomes_neto_p = $this->ingresoTotal_neto_p;
            $gastos_a=$this->gastos_a;
            $gastos_n=$this->gastos_n;
            $gastos_a_neto=$this->gastos_a_neto;
            $gastos_n_neto=$this->gastos_n_neto;
            $total_v=$this->total_v;
            $total_s=$this->total_s;
            $comisiones=$this->comisiones;
            $total_citas_pagadas=$this->total_citas_pagadas;
            $total_ventas_pagadas=$this->total_ventas_pagadas;
            $total_citas_pendientes=$this->total_citas_pendientes;
            $clientes_nuevos=$this->clientes_nuevos;
            // $total_citas_canceladas=$this->total_citas_canceladas;
            $duracion_citas=$this->duracion_citas;
            $global_categories_counter=$this->global_categories_counter;
            $global_services_counter=$this->global_services_counter;
            $info =[
                'total_incomes' => $total_incomes,
                'total_incomes_neto' => $total_incomes_neto,
                'total_incomes_neto_p' => $total_incomes_neto_p,
                'total_incomes_neto_s' => $total_incomes_neto_s,
                'total_servicios' => $total_s,
                'total_ventas' => $total_v,
                'comisiones' => $comisiones,
                'gastos_a' => $gastos_a,
                'gastos_n' => $gastos_n,
                'gastos_a_neto' => $gastos_a_neto,
                'gastos_n_neto' => $gastos_n_neto,
                'total_citas_pagadas' => $total_citas_pagadas,
                'total_ventas_pagadas' => $total_ventas_pagadas,
                'total_citas_pendientes' => $total_citas_pendientes,
                'clientes_nuevos' => $clientes_nuevos,
                'global_categories_counter' => $global_categories_counter,
                'global_services_counter' => $global_services_counter,
                // 'total_citas_canceladas' => $total_citas_canceladas,
                'duration' => $duracion_citas,
            ];
            $info=$this->setDuration($info);
            return $info;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 314137Informe"] );
        }
    }
    private function recalculate($info)
    {
        try{
            $this->dataSales = $this->loadFinancialInfo($info['ventas'],$info['citas'],$info['collIds']);
            $this->dataExpenses = $this->loadGastos($info['gastos']);
            $this->dataEmpleados = $this->loadDataEmpleados($info['asignaciones_ventas'],$info['asignaciones_servicios'],$info['empleados']);
            $this->totales = $this->obtenerTotales();
            $this->clientesAtendidos = $this->contarClientesAtendidos([$info['asignaciones_ventas']['items_v'],$info['asignaciones_servicios']]);
            $this->obtenerPorcentajes();
            $this->dataEmpleados = json_encode($this->dataEmpleados);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 350138Informe"] );
        }
        
    }
    
    private function contarClientesAtendidos($arrays)
    {
        try{
            $qty=0;
            foreach($arrays as $movimientos){
                foreach($movimientos as $movimiento){
                    if(!empty($movimiento)){
                        $cust_id = $movimiento->date!=null ? $movimiento->date->customer_id : $movimiento->sale->customer_id;
                        // Verifica el Id del cliente
                        
                        if (!isset($this->uniqueCustomerIds[$movimiento->empleado_id])) {
                            $this->uniqueCustomerIds[$movimiento->empleado_id] = [];
                        }

                        if (!in_array($cust_id, $this->uniqueCustomerIds[$movimiento->empleado_id])) {
                            // Incrementa el contador de clientes atendidos si no se ha contabilizado ese cliente
                            $qty+=1;
                            $this->uniqueCustomerIds[$movimiento->empleado_id][] = $cust_id;  // Se añade el id para descartarlo en un futuro
                        }
                    }
                }
            }
            return $qty; 
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 516322InformeEmpleados"] );
        }
    }
    public function render()
    {  
        try{
            return view('livewire.informes.informe',['dataSales' => $this->dataSales,'dataExpenses' => $this->dataExpenses,'totales' => $this->totales, 'dataEmpleados' => $this->dataEmpleados,'dataSalesPdf' => $this->dataSalesPdf,'dataSalesP' => $this->dataSales,'dataPeriodNormalizado' => $this->dataPeriodNormalizado,'ranking_services' => $this->ranking_services,'ranking_categories' => $this->ranking_categories]);
            // return view('livewire.informes.informe',['dataSales' => $this->dataSales,'dataExpenses' => $this->dataExpenses,'totalIncomes' => $this->totalIncomes,'totales' => $this->totales, 'dataEmpleados' => $this->dataEmpleados,'dataSalesPdf' => $this->dataSalesPdf,'dataSalesP' => $this->dataSales]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 357139Informe"] );
        }
    }

    private function acumularMetodos($response,$cita,$items)
    {
        try{
            if(!is_array($response)){
                // Crear estructura de períodos vacía
                $periodos = $this->crearEstructuraPeriodos();
                $totalMethods = 0;
            }else{
                $totalMethods = $response['totalMethods'];
                $periodos = $response['periodos'];
            }
            
            foreach($items as $metodo){
                if($metodo->payment_method_id  != 4){
                    $change = $metodo->change ?? 0;
                    //Declarar el ingreso del método
                    $qty=$metodo->amount-$change;
                    
                    //Calcular las cantidades ya cobradas de p/s
                    if($cita){
                        $this->total_s+=$qty;
                    }else{
                        $this->total_v+=$qty;
                    }
                    $pm = $metodo->metodoPago->Payment_method;

                    $fechaMetodo = Carbon::parse($metodo->created_at);
                    
                    // Buscar el período correcto
                    foreach ($periodos as $key => $periodo) {
                        if ($fechaMetodo->between($periodo['inicio'], $periodo['fin'])) {
                            $periodos[$key]['value'] += $qty;
                            break;
                        }
                    }

                    //Separar los ingresos por método de pago
                    switch($pm){
                        case 'Efectivo':
                            $this->ingresoEfectivo+=$qty;
                            $totalMethods+=$qty;
                            break;
                        case 'Tarjeta':
                            $this->ingresoCard+=$qty;
                            $totalMethods+=$qty;
                            break;
                        case 'MSI':
                            $this->ingresoMsi+=$qty;
                            $totalMethods+=$qty;
                            break;
                        case 'Puntos Recompensa':
                            $this->puntosCanjeados+=$qty;
                            $totalMethods+=$qty;
                            break;
                        default:
                            $totalMethods+=$qty;
                            $reference = $pm;
                            // Agrega los nuevos elementos al array existente
                            if(isset($this->ingresoTarjeta[$reference])){
                                $this->ingresoTarjeta[$reference] += $qty;
                            }else{
                                $this->ingresoTarjeta[$reference] = $qty;
                            }
                            break;
                    }
                }
            }
            
            return ['totalMethods'=>$totalMethods,'periodos'=>$periodos];
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 500273Informe"] );
        }
    }
    private function acumularPropinas($movimientos,$totalMethods,$pagos)
    {
        try{
            if(count($pagos)>0){
                foreach($pagos as $pago){
                    foreach($pago as $propina){
                        //Declarar el ingreso del método
                        $qty=$propina->amount;
                        $totalMethods+=$qty;
                    }
                }
            }
            return $totalMethods;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 500272Informe"] );
        }
    }
    private function calculateTaxesNRanking($transaccion,$isDate)
    {
        $disccount_per_item = 0;
        $total_taxes = 0;
        $total_gross = 0;
        
        //Se divide el descuento de la transacción entre los items existentes
        if($transaccion->disccount>0){
            $items = count($transaccion->details);
            $disccount_per_item = $items>0 ? $transaccion->disccount/$items : $transaccion->disccount;
        }
        
        foreach($transaccion->details as $detail){
            //Por detalle se multiplica la cantidad descontada anteriormente por la cantidad de items o en su defecto se mantiene
            $extra_disccount = $disccount_per_item * $detail->quantity ?? 1; 
            //La cantidad descontada es restada sobre el precio total de ese detalle y se divide entre el 100% + su respectivo impuesto en %
            $qty = $detail->disccount_price > 0 ? $detail->disccount_price-$extra_disccount : $detail->current_price-$extra_disccount;
            $total_taxes+=$qty/($detail->iva+1);
            $total_gross+=$qty;

            if(!$isDate){
                continue;
            }

            $service_name = $detail->servicio->name;
            $id_service = $detail->servicio->id;
            if(isset($this->ranking_services[$id_service])){
                $this->ranking_services[$id_service]['qty'] += $qty;
                $this->ranking_services[$id_service]['counter'] += 1;
            }else{
                $this->ranking_services[$id_service] = ['qty' => $qty, 'name' => $service_name, 'counter' => 1];
            }
            
            //Ranking de categorías
            $categories = $detail->servicio->categorias;
            foreach($categories as $category){
                $category_name = $category->name;
                $id_category = $category->id;
                if(isset($this->ranking_categories[$id_category])){
                    $this->ranking_categories[$id_category]['qty'] += $qty;
                    $this->ranking_categories[$id_category]['counter'] += 1;
                }else{
                    $this->ranking_categories[$id_category] = ['qty' => $qty, 'name' => $category_name, 'counter' => 1];
                }
                $this->global_categories_counter++;
            }
            $this->global_services_counter++;
        }
        return [$total_taxes,$total_gross];
    }
    private function acumularTransacciones($transacciones,$isDate)
    {
        try{
            $iva_t=0;
            $real=0;
            foreach($transacciones as $transaccion){
                if($transaccion->status == 'Pagada' || $transaccion->status == 'Pendiente'){
                    $totales = $this->calculateTaxesNRanking($transaccion,$isDate);
                    $iva_t+=$totales[0];
                    $real+=$totales[1];
                }
                switch($transaccion->status){
                    case 'Pagada':
                        $isDate ? $this->total_citas_pagadas += 1 : $this->total_ventas_pagadas += 1;
                        break;
                    case 'Pendiente' || 'Agendada':
                        $this->total_citas_pendientes += 1;
                        break;
                    // case 'Cancelada':
                    //     $this->total_citas_canceladas += 1;
                    //     break;
                }
            }
            $iva_percent = $real > 0 ? ($isDate ? $this->total_s : $this->total_v) / $real : 0;
            $iva_t *= $iva_percent;
            $this->iva=$iva_t;
            $this->ingresoTotal_neto+=$iva_t;
            if($isDate){
                $this->ingresoTotal_neto_s = $iva_t;
            }else{
                $this->ingresoTotal_neto_p = $iva_t;
            }
            
            if(is_array($this->ranking_services)){
                // Limitar a los 20 primeros
                $ranking_services = array_slice($this->ranking_services, 0, 20);

                // Ordenar rankings por counter (descendente)
                usort($ranking_services, function($a, $b) {
                    return $b['counter'] <=> $a['counter'];
                });

                // Calcular porcentaje del contador total en ranking
                $global_services_counter = $this->global_services_counter;
                $ranking_services = array_map(function($item) use ($global_services_counter) {
                    $item['percent'] = $global_services_counter > 0 ? ($item['counter'] / $global_services_counter) * 100 : 0;
                    $item['qty'] = number_format($item['qty'], 2, '.', ',');
                    return $item;
                }, $ranking_services);

                // Convertir a JSON para su uso en gráficos
                $this->ranking_services = json_encode($ranking_services);
            }
            
            if(is_array($this->ranking_categories)){
                // Limitar a los 20 primeros
                $ranking_categories = array_slice($this->ranking_categories, 0, 20);

                usort($ranking_categories, function($a, $b) {
                    return $b['counter'] <=> $a['counter'];
                });

                $global_categories_counter = $this->global_categories_counter;
                $ranking_categories = array_map(function($item) use ($global_categories_counter) {
                    $item['percent'] = $global_categories_counter > 0 ? ($item['counter'] / $global_categories_counter) * 100 : 0;
                    $item['qty'] = number_format($item['qty'], 2, '.', ',');
                    return $item;
                }, $ranking_categories);

                // Convertir a JSON para su uso en gráficos
                $this->ranking_categories = json_encode($ranking_categories);
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 537274Informe"] );
        }
    }
    private function loadAssignments($movimientos) {
        try{
            $asignaciones=[];
            if(isset($movimientos)){
                foreach($movimientos as $movimiento){
                    if($this->is_interval==false){
                        $asignaciones[]=$movimiento->details()
                        ->whereDate('created_at',Carbon::parse($this->start)->startOfDay())
                        ->get();
                    }else{
                        $asignaciones[]=$movimiento->details()
                        ->whereBetween('created_at',[
                            Carbon::parse($this->start)->startOfDay(),
                            Carbon::parse($this->end)->endOfDay()])
                        ->get();
                    }
                }   
            }
            return $asignaciones;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 601277Informe"] );
        }
    }
    private function obtenerPorcentajes()
    {
        try{
            // Suponiendo que $totales['total_incomes'] contiene el total de ingresos

            foreach ($this->dataEmpleados as &$empleado) {
                $total_ingresos = $this->total_ventas+$this->total_servicios;

                // Calcular el valor de 'percent' para el empleado actual
                if($total_ingresos>0){
                    $percent = (($empleado['total_v'] + $empleado['total_d']) / $total_ingresos)*100;
                }else{
                    $percent= 0;
                }

                // Asignar el valor calculado a la clave 'percent' del empleado
                $empleado['percent'] = $percent;
                $empleado['label'] = $empleado['name'];
                $empleado['amount'] = $empleado['total_v_neto'] + $empleado['total_d_neto'];
            }

            // Asegurarse de desvincular la referencia al último elemento del array
            unset($empleado);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 658329Informe"] );
        }
    }
    private function loadAssignmentsSP($movimientos) {
        try{
            $asignaciones=[];
            if(isset($movimientos)){
                foreach($movimientos as $movimiento){
                    if($this->is_interval==false){
                        $asignaciones[]=$movimiento->details_product()
                        ->whereDate('created_at',Carbon::parse($this->start)->startOfDay())
                        ->get();
                    }else{
                        $asignaciones[]=$movimiento->details_product()
                        ->whereBetween('created_at',[
                            Carbon::parse($this->start)->startOfDay(),
                            Carbon::parse($this->end)->endOfDay()])
                        ->get();
                    }
                }   
            }
            return $asignaciones;
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 682330Informe"] );
        }
    }
    public function generatePdf(){
        $this->useDate();
        $pdf = Pdf::loadView('livewire.informes.informe-pdf',['dataSales' => $this->dataSales,'dataExpenses' => $this->dataExpenses,'totales' => $this->totales, 'dataEmpleados' => $this->dataEmpleados,'dataSalesPdf' => $this->dataSalesPdf,'dataSalesP' => $this->dataSales,'dataPeriodNormalizado' => $this->dataPeriodNormalizado,'ranking_services' => $this->ranking_services,'ranking_categories' => $this->ranking_categories, 'currentDate' => $this->currentDate, 'is_interval' => $this->is_interval, 'currentDateEnd' => $this->currentDateEnd, 'comparison_table' => $this->comparison_table, 'nextDates' => $this->nextDates]);
        try{
            return $pdf->download('informe_.pdf');
        }catch(\Throwable $th){
            dd($th);
        }   
    }
    public function generateExcel()
    {
        $date = $this->currentDateC;
        $date = $date->format('Y_m_d_H_i_s');
        $fileName = 'historico_' . $date . '.xlsx';
        $this->useDate();
        return Excel::download(new ReporteHistorico($this->totales,$this->dataSalesPdf,$this->dataEmpleados,$this->dataExpenses,$this->gastos),$fileName);
    }
}
