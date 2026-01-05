<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class reporteServicios implements FromView, ShouldAutoSize, WithColumnWidths, WithColumnFormatting
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $servicios=[],$cat=[],$type;

    public function __construct($servicios,$cat,$type) {
        $this->servicios = $servicios;
        $this->cat = $cat;
        $this->type = $type;
    }

    public function view(): View
    {
        return view('livewire.' . $this->type . '.exportables.' . $this->type,[
            'servicios' => $this->servicios,'cat' => $this->cat
        ]);
    }
    public function columnWidths(): array{
        return[
            'A' => 25,
        ];
    }
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER, // Aplica formato numérico estándar a la columna B
        ];
    }
}
