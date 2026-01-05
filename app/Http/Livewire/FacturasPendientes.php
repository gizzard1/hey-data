<?php

namespace App\Http\Livewire;

use App\Models\tax_data;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FacturasPendientes extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $facturasPendientes = [];

    public function mount()
    {
        $this->loadDefault();
    }

    protected $listeners = ['refresh' => '$refresh'];

    public function render()
    {
        return view('livewire.facturas-pendientes');
    }
    private function loadDefault()
    {
        $this->facturasPendientes = tax_data::with('citas.details', 'citas.details_product', 'ventas.details','cliente')
            ->whereHas('cliente', function ($q) {
                $q->where('salon_id', Auth::user()->salon_id);
            })
            ->where(function ($query) {
                $query->whereHas('citas', function ($q) {
                    $q->where('billing', 1);
                })->orWhereHas('ventas', function ($q) {
                    $q->where('billing', 1);
                });
            })
            ->get();
            dd($this->facturasPendientes);
    }
}
