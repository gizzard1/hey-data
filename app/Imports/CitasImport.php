<?php

namespace App\Imports;

use App\Services\ImportacionMasivaService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class CitasImport implements ToCollection
{
    protected ImportacionMasivaService $servicio;

    public function __construct(ImportacionMasivaService $servicio)
    {
        $this->servicio = $servicio;
    }

    public function collection(Collection $rows)
    {
        $rows->shift(); // quitar encabezado

        $bloques = $rows->chunk(1000); // divide en bloques de 1000

        foreach ($bloques as $bloqueIndex => $bloque) {
            try {
                DB::beginTransaction();

                foreach ($bloque as $fila) {
                    if ($fila->filter(fn($v) => !is_null($v) && $v !== '')->isEmpty()) {
                        continue;
                    }

                    $this->servicio->procesarFila($fila);
                }

                DB::commit();
                logger()->info("Bloque #$bloqueIndex procesado exitosamente.");

            } catch (\Throwable $e) {
                DB::rollBack();
                logger()->error("Error en bloque #$bloqueIndex: " . $e->getMessage());
                // Aquí puedes exportar también el bloque completo con error si lo deseas
            }
            $this->servicio->exportarErrores();
        }
    }
}
