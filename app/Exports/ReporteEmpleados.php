<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ReporteEmpleados implements FromView, ShouldAutoSize, WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $dataComisiones,$dataPropinas,$terminales,$dataClientes,$dataLog;

    public function __construct($dataComisiones,$terminales,$dataPropinas,$dataClientes,$dataLog) 
    {    
        $this->dataComisiones=$dataComisiones;
        $this->terminales=$terminales;
        $this->dataPropinas=$dataPropinas;
        $this->dataClientes=$dataClientes;
        $this->dataLog=$dataLog;
    }

    public function view(): View
    {
        return view('livewire.informes.actividad-empleados',[
            'dataComisiones' => $this->dataComisiones,
            'terminales' => $this->terminales,
            'dataPropinas' => $this->dataPropinas,
            'dataClientes' => $this->dataClientes,
            'dataLog' => $this->dataLog,
        ]);
    }
    public function columnWidths(): array{
        return[
            'A' => 25,
        ];
    }
}
