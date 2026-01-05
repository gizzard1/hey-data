<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RevisionImportacionMasivaService;

class ImportarCitasRevisionCommand extends Command
{
    protected $signature = 'importar:citas {archivo : Ruta al archivo Excel}';
    protected $description = 'Importa citas desde un archivo Excel y guarda los datos en la base de datos';

    public function handle()
    {
        $ruta = $this->argument('archivo');

        if (!file_exists($ruta)) {
            $this->error("El archivo no existe en la ruta: $ruta");
            return Command::FAILURE;
        }

        $importador = new RevisionImportacionMasivaService();
        $importador->importar($ruta);

        $this->info('Importación finalizada.');
        return Command::SUCCESS;
    }
}
