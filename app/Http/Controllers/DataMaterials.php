<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataMaterials extends Controller
{
    public static function createMaterials(Request $request)
    {
        $detalles = $request->input('details', []);
        DB::beginTransaction();
        try {
            foreach ($detalles['materiales'] as $material) {
                $material['salon_id'] = $request->user()->salon_id;
                $material['user_id'] = $request->user()->id;
                self::updateMaterial((array)$material);
            }
            DB::commit();
            return response()->json(['message' => 'ok'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return response()->json(['message' => 'error'], 500);
        }
    }
    public static function updateMaterial($material)
    {
        try {
            // Guardar la cantidad de material para materiales existentes
            $qty_before_update = Material::find($material['id'] ?? 0)->qty ?? 0;

            $new_mat = Material::updateOrCreate(
                ['id' => $material['id'] ?? null],
                [
                    'asignacion_id' => $material['asignacion_id'] ?? null,
                    'producto_id' => $material['producto']['id'],
                    'qty' => $material['qty'] ?? 1,
                    'sale_price' => $material['sale_price'] ?? 0,
                    'salon_id' => $material['salon_id'] ?? null,
                    'user_id' => $material['user_id'] ?? null,
                    'empleado_id' => isset($material['empleado']) ? $material['empleado']['id'] : null,
                    'cliente_id' => isset($material['customer']) ? $material['customer']['id'] : null,
                ]
            );

            self::updateStock($material['producto']['id'], $material['qty'] - $qty_before_update);
            return $new_mat->id;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function deleteMaterials($materials)
    {
        foreach ($materials as $materialToDelete) {
            self::deleteMaterial($materialToDelete->id);
        }
    }
    public static function deleteMaterial($material_id)
    {
        try {
            $material = Material::find($material_id);
            if ($material) {
                // Restaurar el stock antes de eliminar el material
                self::updateStock($material->producto_id, -$material->qty);
                $material->delete();
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function updateStock($producto_id, $qty)
    {
        try {
            producto::find($producto_id)->decrement('stock_qty', $qty);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
