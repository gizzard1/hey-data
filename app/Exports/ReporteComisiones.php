<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ReporteComisiones implements FromView, ShouldAutoSize, WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $dataComisiones;

    public function __construct($dataComisiones) 
    {    
        $this->dataComisiones=$dataComisiones;
    }

    public function view(): View
    {
        return view('livewire.informes.reporte-comisiones',[
            'dataComisiones' => $this->dataComisiones,
        ]);
    }
    public function columnWidths(): array{
        return[
            'A' => 25,
        ];
    }
}
