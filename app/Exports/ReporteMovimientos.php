<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ReporteMovimientos implements FromView, ShouldAutoSize, WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $dataMovimientos,$type,$itemSelected;

    public function __construct($dataMovimientos,$type,$itemSelected) {
        $this->dataMovimientos = $dataMovimientos;
        $this->type = $type;
        $this->itemSelected = $itemSelected;
    }

    public function view(): View
    {
        return view('livewire.informes.informe-movimientos',[
            'dataMovimientos' => $this->dataMovimientos,
            'type' => $this->type,
            'itemSelected' => $this->itemSelected,
        ]);
    }
    public function columnWidths(): array{
        return[
            'A' => 25,
        ];
    }
}
