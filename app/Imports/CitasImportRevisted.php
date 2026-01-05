<?php

namespace App\Imports;

use App\Services\RevisionImportacionMasivaService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class CitasImportRevisted implements ToCollection
{
    protected RevisionImportacionMasivaService $servicio;

    public function __construct(RevisionImportacionMasivaService $servicio)
    {
        $this->servicio = $servicio;
    }

    public function collection(Collection $rows)
    {
        $rows->shift(); // Ignora encabezado

        foreach ($rows as $row) {
            try {
                DB::beginTransaction();
                $this->servicio->procesarFila($row);
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('Error en fila de Excel: ' . $e->getMessage(), ['fila' => $row]);
            }
        }
    }
}
