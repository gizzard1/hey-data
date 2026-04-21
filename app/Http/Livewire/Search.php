<?php

namespace App\Http\Livewire;

use App\Models\cliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Http\Livewire\Productos;
use App\Http\Livewire\Servicios;
use App\Models\producto;
use App\Models\servicio;

class Search extends Component
{
    public $queryGlobal, $sugerencias = [];

    protected $listeners = [
        'loadSearchBox',
        'searchGlobal'
    ];
    public static function loadSearchBox($module)
    {
        session()->put('modulo_actual', $module);
        session()->save();
    }
    public function render()
    {
        if (!session('modulo_actual')) {
            return view('livewire.search.search', ['queryGlobal' => $this->queryGlobal]);
        } else {
            return view('livewire.search.suggestions');
        }
    }
    public function searchGlobal($type = null)
    {
        if ($type == null) {
            if (session()->has('modulo_actual')) {
                $type = session('modulo_actual');
            }
        }
        switch ($type) {
            case 1:
                // Redirigir a la ruta 'productos' con el parámetro de búsqueda
                return redirect()->route('productos', ['pestaña' => 1, 'search' => $this->queryGlobal]);
            case 2:
                // Redirigir a la ruta 'servicios' con el parámetro de búsqueda
                return redirect()->route('servicios', ['search' => $this->queryGlobal]);
            case 3:
                // Redirigir a la ruta 'empleados' con el parámetro de búsqueda
                return redirect()->route('empleados', ['search' => $this->queryGlobal]);
            case 4:
                // Redirigir a la ruta 'clientes' con el parámetro de búsqueda
                return redirect()->route('clientes', ['search' => $this->queryGlobal, 'custId' => 0]);
            case 5:
                // Redirigir a la ruta 'marcas' con el parámetro de búsqueda
                return redirect()->route('marcas', ['search' => $this->queryGlobal]);
        }
    }
    public function selectedItem($item)
    {
        $module = session('modulo_actual');
        switch ($module) {
            case 1:
                $this->emit('selectedProductToEdit', $item);
                break;
            case 2:
                $this->emit('selectedServiceToEdit', $item);
                break;
            case 3:
                break;
            case 4:
                $this->emit('selectedItemToEdit', $item);
                break;
            case 5:
                break;
            default:
                break;
        }
    }
    public function updatedQueryGlobal()
    {
        try {
            if (!session()->has('modulo_actual')) {
                return;
            }
            $q = $this->queryGlobal;
            switch (session('modulo_actual')) {
                case 1:
                    $query = producto::basicQuery();
                    $this->sugerencias = Productos::searchProduct($query, $q)->orderBy('name', 'asc')->get();
                    break;
                case 2:
                    $query = servicio::basicQuery();
                    $this->sugerencias = Servicios::searchService($query, $q)->orderBy('name', 'asc')->get();
                    break;
                case 4:
                    $this->sugerencias = cliente::where(function ($query) {
                        $words = preg_split('/\s+/', trim($this->queryGlobal));

                        foreach ($words as $word) {
                            $query->where(function ($q) use ($word) {
                                $q->where('first_name', 'like', "%{$word}%")
                                    ->orWhere('last_name', 'like', "%{$word}%")
                                    ->orWhere(DB::raw("CONCAT_WS(' ', TRIM(first_name), TRIM(last_name))"), 'like', "%{$word}%")
                                    ->orWhere('email', 'like', "%{$word}%")
                                    ->orWhere('phone', 'like', "%{$word}%")
                                    ->orWhereRaw("SOUNDEX(first_name) = SOUNDEX(?)", [$word])
                                    ->orWhereRaw("SOUNDEX(last_name) = SOUNDEX(?)", [$word]);
                            });
                        }
                    })
                        ->where('salon_id', Auth::user()->salon->id)
                        ->orderByRaw("
                    CASE
                        WHEN first_name LIKE '{$q}%' THEN 1
                        WHEN last_name LIKE '{$q}%' THEN 2
                        WHEN phone LIKE '{$q}%' THEN 3
                        WHEN first_name LIKE '%{$q}%' THEN 4
                        WHEN last_name LIKE '%{$q}%' THEN 5
                        WHEN phone LIKE '%{$q}%' THEN 6
                        ELSE 7
                    END
                ")
                        ->get();
                    break;
                default:
                    $this->sugerencias = [];
                    break;
            }
        } catch (\Throwable $th) {
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "Código de error: 2097CartView"]);
        }
    }
}
