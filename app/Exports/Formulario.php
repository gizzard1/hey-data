<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Formulario implements FromView, ShouldAutoSize, WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public $encuesta,$preguntas,$clientes=[];

    public function __construct($encuesta) {
        $this->encuesta = $encuesta;
        $this->preguntas = $encuesta->preguntas;
        foreach($this->preguntas as $pregunta){
            foreach($pregunta->respuestas as $respuesta){
                if(!in_array($respuesta->cliente,$this->clientes)){
                    $this->clientes[] = $respuesta->cliente;
                }
            }
        }
    }

    public function view(): View
    {
        return view('livewire.clientes.exportables.formulario',[
            'encuesta' => $this->encuesta,'clientes' => $this->clientes, 'preguntas' => $this->preguntas
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
            'C' => NumberFormat::FORMAT_NUMBER, // Establece el formato numérico para la columna C
        ];
    }
}
