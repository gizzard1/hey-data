<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ReporteGastos implements FromView, ShouldAutoSize, WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $gastos,$pdf;

    public function __construct($gastos,$pdf) {
        $this->gastos = $gastos;
        $this->pdf = $pdf;
    }

    public function view(): View
    {
        return view('livewire.informes.informe-gastos',[
            'gastos' => $this->gastos, 'pdf' => $this->pdf
        ]);
    }
    public function columnWidths(): array{
        return[
            'A' => 10,
        ];
    }
}