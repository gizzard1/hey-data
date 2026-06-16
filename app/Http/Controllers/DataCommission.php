<?php

namespace App\Http\Controllers;

use App\Models\Comision;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DataCommission extends Controller
{
    public static function createCommission($employee_id)
    {
        try {
            Comision::create([
                'empleado_id' => $employee_id
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    private static function updateGlobalCommission($id, $isService, $newCommission)
    {
        try {
            $qty_key = $isService ? 'qty_s' : 'qty_p';
            $type_key = $isService ? 'type_comission_s' : 'type_comission_p';
            $commission = Comision::find($id);
            if (!$commission) {
                return null;
            }
            $commission->$qty_key = $newCommission['qty'];
            $commission->$type_key = $newCommission['type_comission'];
            $commission->save();
            return $commission;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    private static function getExceptionByType($type)
    {
        $model = null;
        $relation_key = null;
        switch ($type) {
            case '0':
                $model = 'App\\Models\\excepcion_servicio';
                $relation_key = 'servicio_id';
                break;
            case '1':
                $model = 'App\\Models\\excepcion_cat_servicio';
                $relation_key = 'categoria_servicio_id';
                break;
            case '2':
                $model = 'App\\Models\\excepcion_producto';
                $relation_key = 'producto_id';
                break;
            case '3':
                $model = 'App\\Models\\excepcion_cat_producto';
                $relation_key = 'categoria_producto_id';
                break;
        }
        return [$model, $relation_key];
    }
    private static function updateOrCreateException($type, $newCommission, $comision_id)
    {
        try {
            [$model, $relation_key] = self::getExceptionByType($type);
            $updated = $model::updateOrCreate(
                [
                    'id' => $newCommission['id'] ?? null,
                ],
                [
                    'qty' => $newCommission['qty'],
                    'type_comission' => $newCommission['type_comission'],
                    $relation_key => $newCommission[$relation_key],
                    'comision_id' => $comision_id
                ]
            );
            return $model::with(
                $relation_key === 'servicio_id' ? 'servicio' : ($relation_key === 'categoria_servicio_id' ? 'cat_servicio' : ($relation_key === 'producto_id' ? 'producto' : 'cat_producto'))
            )->find($updated->id);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function createOrUpdateCommission(Request $request)
    {
        try {
            $commissions = $request->input('commissions');
            $type = $request->input('type');

            $newCommission = $commissions[0];
            $oldCommissionId = $commissions[1];

            if ($newCommission['id'] === 'global-service' || $newCommission['id'] === 'global-product') {
                $isService = $newCommission['id'] === 'global-service';
                $exception = self::updateGlobalCommission($oldCommissionId, $isService, $newCommission);
            } else {
                $exception = self::updateOrCreateException($type, $newCommission, $oldCommissionId);
            }

            if ($exception) {
                return json_encode(['success' => true, 'commission' => $exception]);
            } else {
                return json_encode(['success' => false, 'message' => 'failed_update']);
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function deleteCommission(Request $request)
    {
        try {
            $type = $request->input('type');
            $id = $request->id;

            [$model, ] = self::getExceptionByType($type);
            $record = $model::find($id);
            if ($record) {
                $record->delete();
                return json_encode(['success' => true]);
            } else {
                return json_encode(['success' => false, 'message' => 'record_not_found']);
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
