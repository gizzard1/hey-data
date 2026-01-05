<?php

namespace App\Http\Livewire;

use App\Models\cliente;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class HistoricoCliente extends Component
{
    use WithPagination;
    public $pestaña,$infoDates,$infoSales,$customerSelected,$start,$end,$currentDate,$currentDateEnd,$is_interval,$allTimes;

    public $rankingP = [], $rankingS = [], $rankingPD = [];

    public $totalS=0,$totalP=0;
    protected $paginationTheme = 'bootstrap';

    public function mount($search,$pestaña)
    {
        $this->pestaña = $pestaña;
        $this->customerSelected = cliente::with('compras','citas')->find($search);
        $this->setAllTimes();
    }
    public function setAllTimes()
    {
        try{
            $this->allTimes = true;
            $this->is_interval = false;
            $this->changeWindow($this->pestaña);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 204361Cliente"] );
        }
    }
    protected $listeners = ['refresh' => '$refresh','windowCust'=>'changeWindow','datesSelected' => 'setDatesFromPeriod','dateSelected' => 'setDate'];

    public function render()
    {
        return view('livewire.historico-cliente');
    }

    public function changeWindow($window,$start=null,$end=null)
    {
        $this->pestaña = $window;
        switch($window){
            case 1:
                $infoDates = $start && $end ? $this->customerSelected->citas()->whereBetween('start', [$start, $end])->orderBy('start','desc')->get() : $this->customerSelected->citas()->orderBy('start','desc')->get();
                $infoSales = $start && $end ? $this->customerSelected->compras()->whereBetween('created_at', [$start, $end])->get() : $this->customerSelected->compras()->get();
                break;
            case 2:
                $infoDates = $start && $end ? $this->customerSelected->citas()->where('status','Pendiente')->whereBetween('start', [$start, $end])->orderBy('start','desc')->get() : $this->customerSelected->citas()->where('status','Pendiente')->orderBy('start','desc')->get();
                $infoSales = $start && $end ? $this->customerSelected->compras()->where('status','Pendiente')->whereBetween('created_at', [$start, $end])->get() : $this->customerSelected->compras()->where('status','Pendiente')->get();
                break;
            case 3:
                $infoDates = $start && $end ? $this->customerSelected->citas()->where('status','Pagada')->whereBetween('start', [$start, $end])->orderBy('start','desc')->get() : $this->customerSelected->citas()->where('status','Pagada')->orderBy('start','desc')->get();
                $infoSales = $start && $end ? $this->customerSelected->compras()->where('status','Pagada')->whereBetween('created_at', [$start, $end])->get() : $this->customerSelected->compras()->where('status','Pagada')->get();
                break;
            case 4:
                $infoDates = $start && $end ? $this->customerSelected->citas()->where('status','Cancelada')->whereBetween('start', [$start, $end])->orderBy('start','desc')->get() : $this->customerSelected->citas()->where('status','Cancelada')->orderBy('start','desc')->get();
                $infoSales = $start && $end ? $this->customerSelected->compras()->where('status','Cancelada')->whereBetween('created_at', [$start, $end])->get() : $this->customerSelected->compras()->where('status','Cancelada')->get();
                break;
            case 5:
                $infoDates = $start && $end ? $this->customerSelected->citas()->where('status','Agendada')->whereBetween('start', [$start, $end])->orderBy('start','desc')->get() : $this->customerSelected->citas()->where('status','Agendada')->orderBy('start','desc')->get();
                $infoSales = [];
                break;
        }
        $this->infoDates = $infoDates;
        $this->infoSales = $infoSales;

        $this->rankingS = $this->calculateRanking($infoDates);
        $this->rankingPD = $this->calculateRanking($infoDates,'details_product');
        $this->rankingP = $this->calculateRanking($infoSales);

        $this->rankingP = [
            'total' => ($this->rankingP['total'] ?? 0) + ($this->rankingPD['total'] ?? 0),
            'items' => array_unique(array_merge($this->rankingP['items'] ?? [], $this->rankingPD['items'] ?? []))
        ];

        $total = $this->rankingS['total'] + $this->rankingP['total'];
        $this->totalS = $total > 0 ? ($this->rankingS['total']*100)/$total : 0;
        $this->totalP = $total > 0 ? ($this->rankingP['total']*100)/$total : 0;
    }
    public function calculateRanking($data,$detail_name='details')
    {
        try{
            $total = 0;
            $items = [];
            foreach($data as $mov){
                foreach($mov->{$detail_name} as $detail){
                    // Calcular el total considerando descuentos
                    $price = $detail->disccount_price > 0 ? $detail->disccount_price : $detail->current_price;
                    $extra_disc = $detail->discount_qty > 0 ? ($detail->discount_type == 'Porcentaje' ? ($price * ($detail->discount_qty / 100)) : $detail->discount_qty) : 0;
                    $total += ($price-$extra_disc) * ($detail->quantity ?? 1);

                    // Obtener IDs únicos de productos/servicios
                    $item_id = $detail->selected_service ?? $detail->selected_item;
                    if(!in_array($item_id,$items)){
                        $items[] = $item_id;
                    }
                }
            }
            return [
                'total' => $total,
                'items' => $items
            ];
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 50129Clientes"] );
            return [];
        }
    }
    public function setDatesFromPeriod($selectedDates)
    {
        try{
            $this->allTimes = false;
            $this->is_interval=true;
            if (count($selectedDates) >= 2) {
                // Actualizar las fechas según la lógica que necesites
                $currentDateC = Carbon::parse($selectedDates[0]);
                $currentDateCEnd = Carbon::parse($selectedDates[1]);
            }elseif(count($selectedDates)==1){
                // Actualizar las fechas según la lógica que necesites
                $currentDateC = Carbon::parse($selectedDates[0])->startOfDay();
                $currentDateCEnd = $currentDateC->copy()->endOfDay();
            }
            $this->currentDate=$currentDateC->copy()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->currentDateEnd=$currentDateCEnd->copy()->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->changeWindow($this->pestaña,$currentDateC,$currentDateCEnd);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 30127Clientes"] );
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
        $this->setDatesFromPeriod([Carbon::now()->subDay()]);
    }
    public function setWeek()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 379141Clientes"] );
        }
    }
    public function setMonth()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 395142Clientes"] );
        }
    }
    public function setYear()
    {
        try{
            $this->setDatesFromPeriod([Carbon::now()->startOfYear(),Carbon::now()->endOfYear()]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 411143Clientes"] );
        }
    }
    public function setDate($selectedDate)
    {
        try{
            $this->setDatesFromPeriod([Carbon::parse($selectedDate[0])]);
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 53128Clientes"] );
        }
    }
}
