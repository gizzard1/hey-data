<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Simulador extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $customDate;

    public function mount()
    {
        if(session()->has('customDate')){
            $this->customDate = session('customDate');
        }else{
            $this->customDate = Carbon::now()->format('Y-m-d');
        }
    }

    protected $listeners = ['refresh' => '$refresh'];
    public function updatedCustomDate()
    {
        session()->put('customDate', $this->customDate);
        session()->save();
    }

    public function render()
    {
        return view('livewire.simulador');
    }
    public function returnToday()
    {
        $this->customDate = Carbon::now()->format('Y-m-d');
    }
}
