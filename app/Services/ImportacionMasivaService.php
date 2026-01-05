<?php

namespace App\Services;

use App\Exports\ErroresExport;
use App\Imports\CitasImport;
use App\Models\Asignacion_servicio;
use App\Models\cita;
use App\Models\cliente;
use App\Models\Empleado;
use App\Models\etiquetas_cita;
use App\Models\metodo_pago;
use App\Models\metodo_pago_servicio;
use App\Models\servicio;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ImportacionMasivaService
{
    public array $clientesNoEncontrados = [];
    public array $serviciosNoEncontrados = [];

    public $prevCust=null;
    public function importar(string $rutaArchivo)
    {
        $importador = new CitasImport($this);
        Excel::import($importador, $rutaArchivo);
    }
    protected function convertirFechaExcel($valor)
    {
        if (is_numeric($valor)) {
            return \Carbon\Carbon::instance(
                \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($valor)
            );
        }

        // Asegura que no se intente parsear texto no-fecha
        try {
            return \Carbon\Carbon::parse($valor);
        } catch (\Exception $e) {
            throw new \Exception("Fecha inválida: '$valor'");
        }
    }

    public function procesarFila($row)
    {
        try{
            Log::info($row);

            // Ignorar filas vacías o con celdas en blanco (por ejemplo, solo contienen null o strings vacíos)
            if ($row->filter(fn($value) => !is_null($value) && $value !== '')->isEmpty()) {
                return;
            }

            $cita = new cita();
            $asignacion = new Asignacion_servicio();
            $cita->customer_id = $row[3];

            $pagado = $row[10] ?? 0;
            $sugerido = $row[9] ?? 0;
            // 2. Crear cita
            $citaPrev = cita::find($row[22]);

            if(!$citaPrev){
                $cita->id = $row[22];
                $cita->start = $this->convertirFechaExcel($row[1]);
                $cita->remember = 0;
                $cita->generated_points = 0;
                $cita->status = (string) $row[11];
                $cita->user_id = 8;
                $cita->salon_id = 5;
                $cita->disccount = 0;
                $cita->total = $pagado < $sugerido ? $sugerido : $pagado;
                $cita->created_at = $this->convertirFechaExcel($row[15]);
                $cita->updated_at = $this->convertirFechaExcel($row[16]);
                $cita->description = $row[21-1];
                $cita->end_real = $this->convertirFechaExcel($row[19-1]);
            }else{
                $cita = $citaPrev;
                $cita->total += $pagado < $sugerido ? $sugerido : $pagado;
            }

            $cita->end = $this->convertirFechaExcel($row[2]);
            // 3. Asignar servicio
            $asignacion->selected_service = $row[7];

            $asignacion->empleado_id = $row[0];
            $asignacion->duration = $row[8];
            $asignacion->start = $this->convertirFechaExcel($row[1]);
            $asignacion->discount_qty = $pagado < $sugerido ? $pagado : 0;
            $asignacion->current_price = $pagado < $sugerido ? $sugerido : $pagado;
            $asignacion->discount_type = 'Cantidad';
            $asignacion->iva = 0.16;
            $asignacion->generated_points = 0;
            $asignacion->comission = $this->calcularComision($asignacion->selected_service,$asignacion->empleado_id,$asignacion->current_price,$asignacion->iva); // puedes definir lógica real

            // 5. Guardar relaciones
            $cita->save();
            $asignacion->cita_id = $cita->id;
            $asignacion->save();

            if((string) $row[11] == 'Pagada'){
                $metodo_pago = new metodo_pago_servicio;
                // 4. Método de pago
                $metodo = metodo_pago::firstOrCreate(
                    ['Payment_method' => $row[13-1]],
                    ['salon_id' => 5]
                );

                $metodo_pago->amount = $pagado;
                $metodo_pago->updated_at = $this->convertirFechaExcel($row[17-1]);
                $metodo_pago->created_at = $this->convertirFechaExcel($row[19-1]);
                $metodo_pago->payment_method_id = $metodo->id;
                $metodo_pago->cita_id = $cita->id;
                $metodo_pago->save();
            }

            // 6. Etiquetas
            if (!empty($row[14])) {
                $etiquetas = explode(',', $row[14]);
                foreach ($etiquetas as $nombreEtiqueta) {
                    $etiqueta = etiquetas_cita::firstOrCreate(['name' => trim($nombreEtiqueta),'salon_id'=>5]);
                    $cita->etiquetas()->syncWithoutDetaching([$etiqueta->id]);
                }
            }

            // 7. Tiempos
            $asignacion->created_at = $this->convertirFechaExcel($row[15]);
            $asignacion->updated_at = $this->convertirFechaExcel($row[16]);
            $asignacion->save();
        }catch(\Throwable $th){
            Log::error('Error en fila de Excel: ' . $th, ['fila' => $row]);
        }

    }

    public function buscarServicioPorNombre(string $nombre): ?servicio
    {
        $servicio = 99999;
        // lógica para buscar por nombre, eliminar última palabra si no encuentra
        $palabras = explode(' ', $nombre);
        while (!empty($palabras)) {
            $servicio = servicio::where('name', implode(' ', $palabras))->first();
            if ($servicio) return $servicio;
            array_pop($palabras);
        }
        return $servicio;
    }
    private function calcularComision($sid, $empleado_id, $gross_price, $iva)
    {
        $empleado = Empleado::with('comision.excepcion_servicio','comision.excepcion_cat_servicio')->find($empleado_id);
        $balance = 0;

        if (!$empleado || !$empleado->comision) {
            return $balance;
        }

        $precioSinIva = $gross_price - ($gross_price * $iva);
        $comision = $empleado->comision;

        // Buscar excepciones específicas
        $excepcionServicio = $comision->excepcion_servicio->firstWhere('servicio_id', $sid);
        $excepcionCategoria = null;

        $servicio = servicio::with('categorias')->find($sid);
        if ($servicio && $servicio->categorias) {
            foreach ($servicio->categorias as $cat) {
                $ex = $comision->excepcion_servicio->firstWhere('categoria_servicio_id', $cat->id);
                if ($ex) {
                    $excepcionCategoria = $ex;
                    break;
                }
            }
        }

        // Valores por defecto
        $cant = $comision->qty_s;
        $type = $comision->type_comission_s;

        // Excepciones sobreescriben si existen
        if ($excepcionCategoria) {
            $cant = $excepcionCategoria->qty;
            $type = $excepcionCategoria->type_comission;
        }

        if ($excepcionServicio) {
            $cant = $excepcionServicio->qty;
            $type = $excepcionServicio->type_comission;
        }

        // Cálculo de la comisión
        if ($type === 'percent') {
            $balance = ($cant / 100) * $precioSinIva;
        } elseif ($type === 'qty') {
            $balance = $cant;
        }

        return $balance;
    }

    public function exportarErrores()
    {
        if (!empty($this->clientesNoEncontrados)) {
            Excel::store(
                new ErroresExport($this->clientesNoEncontrados, 'ClientesNoEncontrados'),
                'errores/clientes_no_encontrados.xlsx',
                'local'
            );
        }

        if (!empty($this->serviciosNoEncontrados)) {
            Excel::store(
                new ErroresExport($this->serviciosNoEncontrados, 'ServiciosNoEncontrados'),
                'errores/servicios_no_encontrados.xlsx',
                'local'
            );
        }
    }

}
