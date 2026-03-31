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
    public $currentDate, $currentDateEnd, $is_interval = false;
    public $start, $currentDateC, $end, $currentDateCEnd;

    public $citasFilter, $ventasFilter, $propinasFilter;
    public $total = 0;
    private $dataGanancias;
    private $salon_id;
    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->empleado = Auth::user()->empleado ?? null;
        $this->activateCheckers();

        if (session()->has('selectedDates')) {
            $this->setDatesFromPeriod(session('selectedDates'));
        } else {
            $this->loadFecha();
        }
    }

    protected $listeners = [
        'refresh' => '$refresh',
        'datesSelected' => 'setDatesFromPeriod',
        'prevDay',
        'dateSelected' => 'setDate'
    ];

    public function aplicarFiltros()
    {
        $this->useDate();
    }
    private function activateCheckers()
    {
        try {
            $this->ventasFilter = true;
            $this->citasFilter = true;
            $this->propinasFilter = true;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 42369MisGanancias"]);
        }
    }
    public function render()
    {
        return view('livewire.mis-ganancias', ['dataGanancias' => $this->dataGanancias]);
    }

    private function loadFecha()
    {
        try {
            $this->setDatesFromPeriod([Carbon::now()]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 57369MisGanancias"]);
        }
    }

    #Función que establece un día anterior 
    public function prevDay()
    {
        try {
            $this->setDatesFromPeriod([Carbon::now()->subDay()]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 84130Informe"]);
        }
    }
    public function setDatesFromPeriod($selectedDates)
    {
        try {
            session()->put('selectedDates', $selectedDates);
            session()->save();
            $this->is_interval = true;
            if (count($selectedDates) >= 2) {
                // Actualizar las fechas según la lógica que necesites
                $currentDateC = Carbon::parse($selectedDates[0]);
                $currentDateCEnd = Carbon::parse($selectedDates[1])->endOfDay();
            } elseif (count($selectedDates) == 1) {
                // Actualizar las fechas según la lógica que necesites
                $currentDateC = Carbon::parse($selectedDates[0])->startOfDay();
                $currentDateCEnd = $currentDateC->copy()->endOfDay();
            }
            $this->currentDateC = Carbon::parse($currentDateC);
            $this->currentDateCEnd = Carbon::parse($currentDateCEnd);
            $this->currentDate = $this->currentDateC->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->start = $this->currentDateC->toDateString();
            $this->currentDateEnd = $this->currentDateCEnd->locale('es')->isoFormat('dddd, D MMMM YYYY');
            $this->end = $this->currentDateCEnd->toDateString();

            $this->useDate();
            $this->loadDatesWithNewPeriod();
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 30127MisGanancias"]);
        }
    }
    public function returnToday()
    {
        $this->is_interval = false;
        $this->loadFecha();
    }
    public function returnYesterday()
    {
        $this->prevDay();
    }
    public function setWeek()
    {
        try {
            $this->setDatesFromPeriod([Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 379141MisGanancias"]);
        }
    }
    public function setMonth()
    {
        try {
            $this->setDatesFromPeriod([Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 395142MisGanancias"]);
        }
    }
    public function setYear()
    {
        try {
            $this->setDatesFromPeriod([Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 411143MisGanancias"]);
        }
    }
    public function setDate($selectedDate)
    {
        try {
            $this->setDatesFromPeriod([Carbon::parse($selectedDate[0])]);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 53128MisGanancias"]);
        }
    }
    #función que actualiza las gráficas con la nueva fecha
    private function loadDatesWithNewPeriod()
    {
        try {
            $this->emit('dateUpdated-movimientos', $this->currentDate, $this->currentDateEnd);
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 192369MisGanancias"]);
        }
    }
    private function useDate()
    {
        try {
            $this->clear();
            $ingresosCitas = [];
            $ingresosVentas = [];
            $propinas = [];
            $totalComissions = 0;
            if ($this->ventasFilter) {
                $ingresosVentas = $this->empleado->salesIncomesBetweenDates($this->currentDateC, $this->currentDateCEnd)->get();
            }
            if ($this->citasFilter) {
                $ingresosCitas = $this->empleado->servicesIncomesBetweenDates($this->currentDateC, $this->currentDateCEnd)->get();
            }
            if ($this->propinasFilter) {
                $propinas = $this->empleado->tipsIncomesBetweenDates($this->currentDateC, $this->currentDateCEnd)->get();
            }

            $totalComissions = $this->recalculate($ingresosCitas, 1);
            $totalComissions += $this->recalculate($ingresosVentas, 1);
            $totalPropinas = $this->recalculate($propinas, 0);

            $info = [
                'ingresosCitas' => $ingresosCitas,
                'ingresosVentas' => $ingresosVentas,
                'propinas' => $propinas,
                'total' => $this->total,
                'totalPropinas' => $totalPropinas,
                'totalComissions' => $totalComissions,
            ];

            $this->dataGanancias = $info;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 200369MisGanancias"]);
        }
    }
    private function recalculate($info, $type)
    {
        try {
            $balance = 0;
            foreach ($info as $item) {
                if ($type) {
                    $balance += $item->comission;
                    $this->total += $item->comission;
                } else {
                    $balance += $item->amount;
                    $this->total += $item->amount;
                }
            }
            return $balance;
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 274369MisGanancias"]);
        }
    }
    private function clear()
    {
        $this->total = 0;
    }
}
