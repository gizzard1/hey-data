<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ReportePropinas implements FromView, ShouldAutoSize, WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $dataPropinas,$terminales;

    public function __construct($terminales, $dataPropinas) 
    {    
        $this->terminales=$terminales;
        $this->dataPropinas=$dataPropinas;
    }

    public function view(): View
    {
        return view('livewire.informes.actividad-empleados',[
            'dataPropinas' => $this->dataPropinas,
            'terminales' => $this->terminales,
        ]);
    }
    public function columnWidths(): array{
        return[
            'A' => 25,
        ];
    }
}
