<?php

namespace App\Services;

use App\Imports\CitasImportRevisted;
use App\Models\cita;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class RevisionImportacionMasivaService
{
    public array $clientesNoEncontrados = [];
    public array $serviciosNoEncontrados = [];

    public $prevDate=null;
    public $itemSelected;
    public function importar(string $rutaArchivo)
    {
        $importador = new CitasImportRevisted($this);
        Excel::import($importador, $rutaArchivo);
    }
    public function procesarFila($row)
    {
        try{
            Log::info($row);

            // Ignorar filas vacías o con celdas en blanco (por ejemplo, solo contienen null o strings vacíos)
            if ($row->filter(fn($value) => !is_null($value) && $value !== '')->isEmpty()) {
                return;
            }
            
            $allDates = cita::where('salon_id',5)
                ->where('id','>',value: 2865)
                ->whereBetween('start',['2021-03-09 00:00:00','2026-03-13 23:59:59'])
                ->get();

            foreach($allDates as $date){
                    Log::info($date);
                    $this->deleteRelations($date);
                }
        }catch(\Throwable $th){
            Log::error('Error en fila de Excel: ' . $th, ['fila' => $row]);
        }

    }
    private function deleteRelations($date)
    {
        $this->deleteItems($date->metodosPago);
        $this->deleteItems($date->propinas);
        $this->deleteItems($date->details_product);
        $date->etiquetas()->detach();
        $this->deleteItems($date->details);
        $date->delete();
    }

    private function deleteItems($relation)
    {
        foreach($relation as $item){
            $item->delete();
        }
    }

}
