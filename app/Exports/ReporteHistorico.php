<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ReporteHistorico implements FromView, ShouldAutoSize, WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $totales, $dataSalesExcel, $dataEmpleados, $dataExpenses, $dataTypeExpenses;

    public function __construct($totales,$dataSalesExcel,$dataEmpleados,$dataExpenses,$dataTypeExpenses) {
        $this->totales = $totales;
        $this->dataSalesExcel = $dataSalesExcel;
        $this->dataEmpleados = $dataEmpleados;
        $this->dataExpenses = json_decode($dataExpenses,true);
        $this->dataTypeExpenses = $dataTypeExpenses;
    }

    public function view(): View
    {
        return view('livewire.informes.informe-historico',[
            'totales' => $this->totales,
            'dataSalesExcel' => $this->dataSalesExcel,
            'dataEmpleados' => $this->dataEmpleados,
            'dataExpenses' => $this->dataExpenses,
            'dataTypeExpenses' => $this->createNewDataTypeExpenses(),
        ]);
    }
    public function columnWidths(): array{
        return[
            'A' => 25,
        ];
    }
    private function createNewDataTypeExpenses()
    {
        $data = $this->dataTypeExpenses;
        $newData = $this->cleanArray();

        // Mapeamos los labels de "Acreditable" con su índice
        foreach ($data[0]['label'] as $index => $label) {
            $newData['label'][$label] = $label;
            $newData['qty_a'][$label] = $data[0]['qty'][$index];
            $newData['qty_neto_a'][$label] = $data[0]['qty_neto'][$index];
            $newData['qty_na'][$label] = 0; // Inicializamos en 0 por si no está en NA
            $newData['qty_neto_na'][$label] = 0;
        }

        // Ahora agregamos los "No Acreditables", reusando los labels si existen
        foreach ($data[1]['label'] as $index => $label) {
            if (!isset($newData['label'][$label])) {
                $newData['label'][$label] = $label;
                $newData['qty_a'][$label] = 0; // Si no existe en A, inicializamos en 0
                $newData['qty_neto_a'][$label] = 0;
            }
            $newData['qty_na'][$label] = $data[1]['qty'][$index];
            $newData['qty_neto_na'][$label] = $data[1]['qty_neto'][$index];
        }
        return $newData;
    }
    private function cleanArray()
    {        
        return [
            'label' => [],
            'qty_a' => [],
            'qty_neto_a' => [],
            'qty_na' => [],
            'qty_neto_na' => []
        ];
    }
}
