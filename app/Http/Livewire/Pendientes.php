<?php

namespace App\Http\Livewire;

use App\Models\cita;
use App\Models\venta;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Pendientes extends Component
{
    use WithPagination;
    public $transacciones = [], $type;

    protected $paginationTheme = 'bootstrap';


    protected $listeners = ['refresh' => '$refresh'];

    public $billed = false, $no_payed = true, $no_billed = false;

    public function mount()
    {
        $responses = $this->recuperarSesion(['billed', 'no_payed', 'no_billed']);
        if (!in_array('ok', $responses)) {
            $this->selectFilters(false, false, true);
        }
    }
    private function recuperarSesion($keys)
    {
        $responses = [];
        foreach ($keys as $key) {
            if (session()->has($key)) {
                $this->{$key} = session($key);
                $responses[] = 'ok';
            } else {
                $this->{$key} = new Collection;
                $responses[] = 'nope';
            }
        }
        return $responses;
    }
    private function saveSession($key)
    {
        session()->put($key, $this->{$key});
        session()->save();
    }

    public function selectFilters($billed, $no_billed, $no_payed)
    {
        $this->billed = $billed;
        $this->no_payed = $no_payed;
        $this->no_billed = $no_billed;

        $this->saveSession('billed');
        $this->saveSession('no_payed');
        $this->saveSession('no_billed');
    }

    public function cobrar($mov_id)
    {
        if ($this->type === 1) {
            return redirect()->route('ventas', ['venta_id' => $mov_id]);
        } elseif ($this->type === 0) {
            return redirect()->route('citas', ['action' => 2, 'pestaña' => 1, 'cita_id' => $mov_id]);
        }
    }
    public function render()
    {
        $this->transacciones = $this->getDataMov($this->type);
        $this->transacciones = $this->setPendingQty($this->transacciones);
        return view('livewire.pendientes.pendientes', ['transacciones' => $this->transacciones]);
    }
    private function getDataMov($type)
    {
        $dataNoPayed = new Collection;
        $dataBilled = new Collection;
        $dataNoBilled = new Collection;
        $futureData = new Collection;
        $sid = Auth::user()->salon_id;
        $modelQuery = $type ? venta::select('id', 'created_at', 'disccount', 'total', 'updated_at', 'status', 'billing', 'customer_id', 'user_id', 'salon_id')
            ->with([
                'metodosPago' => function ($q) {
                    $q->select('payment_method_id', 'venta_id', 'id', 'amount');
                },
                'customer' => function ($q) {
                    $q->select('first_name', 'id', 'last_name');
                },
            ]) : cita::select('id', 'created_at', 'disccount', 'total', 'updated_at', 'status', 'billing', 'customer_id', 'user_id', 'salon_id')
            ->with([
                'metodosPago' => function ($q) {
                    $q->select('payment_method_id', 'cita_id', 'id', 'amount');
                },
                'customer' => function ($q) {
                    $q->select('first_name', 'id', 'last_name');
                },
            ]);
        if ($this->billed) {
            $dataBilled = $this->getDataBilled(clone $modelQuery, $sid);
        }
        if ($this->no_payed && $this->no_billed && $this->type == 0) {
            $futureData = $this->getFutureData(clone $modelQuery, $sid);
            return [$dataBilled, $dataNoBilled, $dataNoPayed, $futureData];
        }
        if ($this->no_payed) {
            $dataNoPayed = $this->getDataNoPayed(clone $modelQuery, $sid);
        }
        if ($this->no_billed) {
            $dataNoBilled = $this->getDataNoBilled(clone $modelQuery, $sid);
        }
        return [$dataBilled, $dataNoBilled, $dataNoPayed];
    }
    private function getDataBilled($modelName, $salon_id)
    {
        return $modelName
            ->where('salon_id', $salon_id)
            ->where('billing', 2)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    private function getDataNoBilled($modelName, $salon_id)
    {
        return $modelName
            ->where('salon_id', $salon_id)
            ->where('billing', 1)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    private function getDataNoPayed($modelName, $salon_id)
    {
        return $modelName
            ->where('salon_id', $salon_id)
            ->where(function ($query) {
                $query->where('status', 'Pendiente')
                    ->orWhere('status', 'Agendada');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
    private function getFutureData($modelName, $salon_id)
    {
        return $modelName
            ->where('salon_id', $salon_id)
            ->where('start', '>', now())
            ->where('status', '!=', 'Cancelada')
            ->orderBy('created_at', 'desc')
            ->get();
    }
    private function setPendingQty($transacciones)
    {
        foreach ($transacciones as $movimientos) {
            foreach ($movimientos as $movimiento) {
                $data = $this->totalMethods($movimiento->metodosPago, $movimiento->total - $movimiento->disccount);
                $movimiento->pendiente = $data['restante'];
                $movimiento->recibido = $data['recibido'];
            }
        }
        return $transacciones;
    }

    private function totalMethods($methods, $restante)
    {
        try {
            $recibido = 0;
            foreach ($methods as $method) {
                if ($method->payment_method_id !== 4) {
                    $recibido += $method->amount;
                }
            }
            return [
                'restante' => $restante - $recibido,
                'recibido' => $recibido
            ];
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 1082369InformeMovimientos"]);
        }
    }
}
