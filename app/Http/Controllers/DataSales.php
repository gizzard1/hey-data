<?php

namespace App\Http\Controllers;

use App\Models\metodo_pago_venta;
use App\Models\venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Empleado;
use App\Http\Controllers\DataResourceGrid as DRG;
use App\Http\Controllers\DataMaterials as DM;
use App\Models\Asignacion_venta;
use App\Models\producto;
use App\Models\Propina;
use Illuminate\Support\Facades\DB;

class DataSales extends Controller
{
    public static function updateOrCreateSale(Request $request, $SaleOnly = true)
    {
        try {
            // Obtener los datos de la venta desde la solicitud
            $details = $request->input('details') ?? $request->input('methods');
            $sale = isset($details['sale']) ? $details['sale'] : $details['detailsVenta'][0]['sale'];

            // Crear o actualizar la venta
            $sale_id = $sale['id'] ?? venta::create([
                'salon_id' => $request->user()->salon_id,
                'total' => 0,
                'disccount' => 0,
                'generated_points' => 0,
                'customer_id' => $sale['customer_id'],
                'user_id' => $request->user()->id,
                'items' => 0,
            ])->id;

            // Obtener los métodos de pago asociados a la venta
            $paymentMethods = metodo_pago_venta::where('venta_id', $sale_id)->orderBy('id', 'asc')->get();

            // Crear o actualizar los detalles de la venta
            $data_details = self::createSaleDetails($details, $sale_id, $request, true, $sale['customer_id']);

            // Obtener los totales calculados
            $total_rp = $data_details['total_rp'];
            $total_date = $data_details['total_date'];
            $total_items = $data_details['total_items'];
            $keptIds = $data_details['keptIds'];

            // Obtener las asignaciones actuales de la venta
            $asignacionesToDelete = Asignacion_venta::where('venta_id', $sale_id)
                ->whereNotIn('id', $keptIds)
                ->get();

            // Actualizar el stock de los productos eliminados
            self::updateStockAfterSale($asignacionesToDelete);

            // Recalcular el descuento total
            $discount = DRG::getTotalDiscounts($paymentMethods, $total_date);

            // Actualizar la venta
            venta::where('id', [$sale_id])
                ->update(
                    [
                        'generated_points' => $total_rp,
                        'status' => DRG::determineDateStatus($total_date - $discount, $details['payed']),
                        'total' => $total_date,
                        'disccount' => $discount,
                        'items' => $total_items,
                    ]
                );

            // Retornar la respuesta según el contexto
            if ($SaleOnly) {
                return response()->json(['message' => 'Sale updated successfully']);
            } else {
                return $sale_id;
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function updateStockAfterSale($asignacionesToDelete)
    {
        try {
            // Actualizar el stock de los productos antes de eliminar las asignaciones
            foreach ($asignacionesToDelete as $asignacion) {
                $product = producto::find($asignacion->selected_item);
                if ($product) {
                    $product->stock_qty += $asignacion->quantity;
                    $product->save();
                }
            }

            // Borrar asignaciones que ya no aparecen en la petición
            $asignacionesToDelete->each->delete();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function createSaleDetails($details, $mov_id, $request, $belongsToSale, $customer_id = null)
    {
        try {
            // Crear un array para rastrear los IDs que siguen vigentes
            $keptIds = [];
            $total_rp = 0;
            $total_date = 0;
            $total_items = 0;
            foreach ($details['detailsVenta'] as $detail) {
                if (!isset($detail['selected_item'])) continue;
                $priceOutOfDiscounts = DRG::determinatePriceOutOfDiscounts($detail);
                $total_date += $priceOutOfDiscounts;
                $comisionItem = self::defineComisionProduct(self::generateItemToCalculateComision($detail, $detail['empleado_id']));
                $gen_points = DRG::calculateRewardPoints($detail['selected_item'], false, $priceOutOfDiscounts, $request, $customer_id);
                $total_rp += $gen_points * $detail['quantity'];
                $total_items += $detail['quantity'];

                $qty_before_update = 0;
                if (isset($detail['id'])) {
                    $existingAsignacion = Asignacion_venta::find($detail['id']);
                    if ($existingAsignacion) {
                        $qty_before_update = $existingAsignacion->quantity;
                    }
                }

                $asignacion = Asignacion_venta::updateOrCreate(
                    ['id' => $detail['id'] ?? null], // usa null si no hay id
                    [
                        'quantity' => $detail['quantity'],
                        'current_price' => $detail['current_price'],
                        'comission' => $comisionItem['balance'],
                        'iva' => isset($detail['iva']) ? ($detail['iva'] === '8%' ? '0.08' : ($detail['iva'] === '16%' ? '0.16' : ($detail['iva'] === 'Exento' ? '0' : $detail['iva']))) : '0.16',
                        'venta_id' => $belongsToSale ? $mov_id : null,
                        'cita_id' => $belongsToSale ? null : $mov_id,
                        'selected_item' => $detail['selected_item'],
                        'empleado_id' => $detail['empleado_id'],
                        'generated_points' => $gen_points,
                        'disccount_price' => $detail['disccount_price'],
                        'discount_qty' => $detail['discount_qty'],
                        'discount_type' => $detail['discount_type'],
                        'type_comision_calculated' => $comisionItem['type'],
                    ]
                );
                // Actualizar el stock del producto
                $product = producto::find($detail['selected_item']);
                $product->stock_qty -= $detail['quantity'] - $qty_before_update;
                $product->save();

                // Guardamos los IDs que quedan vigentes
                $keptIds[] = $asignacion->id;
            }
            return [
                'total_rp' => $total_rp,
                'total_date' => $total_date,
                'total_items' => $total_items,
                'keptIds' => $keptIds
            ];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function generateItemToCalculateComision($sale, $empleado)
    {
        $item['pid'] = $sale->selected_item ?? $sale['selected_item'];
        $item['vendedor'] = $empleado;
        $item['base_comision'] = boolval($sale->base_comision ?? ($sale['base_comision'] ?? 0));
        $item['disccount_price'] = floatval($sale->disccount_price ?? $sale['disccount_price']);
        $item['sale_price'] = floatval($sale->current_price ?? $sale['current_price']);
        $item['total'] = DRG::determinatePriceOutOfDiscounts($sale);
        return $item;
    }


    public static function defineComisionProduct($item)
    {
        try {
            $empleado = Empleado::with('comision.excepcion_producto', 'comision.excepcion_cat_producto')->find($item['vendedor']);
            $balance = 0;
            $type = 'percent';

            if (!$empleado || !$empleado->comision) {
                return ['balance' => $balance, 'type' => $type];
            }

            $base_price = DRG::defineBasePrice($item);
            $comision = $empleado->comision;

            // Buscar excepciones específicas
            $excepcionProducto = $comision->excepcion_producto->firstWhere('producto_id', $item['pid']);
            $excepcionCategoria = null;

            $producto = producto::with('categorias')->find($item['pid']);
            if ($producto && $producto->categorias) {
                foreach ($producto->categorias as $cat) {
                    $ex = $comision->excepcion_cat_producto->firstWhere('categoria_producto_id', $cat->id);
                    if ($ex) {
                        $excepcionCategoria = $ex;
                        break;
                    }
                }
            }

            // Valores por defecto
            $cant = $comision->qty_p;
            $type = $comision->type_comission_p;

            // Excepciones sobreescriben si existen
            if ($excepcionCategoria) {
                $cant = $excepcionCategoria->qty;
                $type = $excepcionCategoria->type_comission;
            }

            if ($excepcionProducto) {
                $cant = $excepcionProducto->qty;
                $type = $excepcionProducto->type_comission;
            }

            // Cálculo de la comisión
            if ($type === 'percent') {
                $balance = ($cant / 100) * $base_price;
            } elseif ($type === 'qty') {
                $balance = $cant;
            }

            return ['balance' => $balance, 'type' => $type];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function loadSale(Request $request)
    {
        try {
            $sale_id = $request->query('sale_id');
            $sale = venta::select(
                'id',
                'customer_id',
                'status',
                'total',
                'disccount',
                DB::raw('total - COALESCE(`disccount`, 0) as totalSubDiscount'),
                'created_at',
                'updated_at'
            )->with(['customer' => function ($q) {
                $q->select(
                    'id',
                    DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as nombre"),
                    DB::raw("phone as telefono"),
                )->with(['tarjetaPuntos' => function ($q) {
                    $q->select('id', 'intern_barcode', 'balance', 'cliente_id');
                }]);
            }, 'details' => function ($q) {
                $q->select(
                    'id',
                    'selected_item',
                    'venta_id',
                    'empleado_id',
                    'quantity',
                    'discount_qty',
                    'discount_type',
                    'current_price',
                    'disccount_price',
                    'generated_points',
                    'base_comision',
                    'iva',
                )->with([
                    'product:id,name,description,gross_price,iva,disccount_price,unit_type,sku',
                    'empleado' => function ($q) {
                        $q->select(
                            'id',
                            DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as name"),
                            DB::raw("color_preset as color")
                        );
                    }
                ]);
            }, 'metodosPago' => function ($q) {
                $q->select(
                    'id',
                    'venta_id',
                    'payment_method_id',
                    'reference',
                    'amount',
                    'tipo',
                    'change',
                    'created_at'
                )->with([
                    'metodoPago' => function ($q) {
                        $q->select('id', DB::raw("Payment_method as name"));
                    }
                ]);
            }])->find($sale_id);

            $tips = Propina::select('id', 'payment_method_id', 'reference', 'amount', 'empleado_id')->with([
                'empleado' => function ($q) {
                    $q->select(
                        'id',
                        DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as name"),
                        DB::raw("color_preset as color")
                    );
                },
                'metodoPago' => function ($q) {
                    $q->select('id', DB::raw("Payment_method as name"));
                }
            ])->where('venta_id', $sale_id)->get();

            if (!$sale) {
                return response()->json(['message' => 'Sale not found'], 404);
            }
            return response()->json(['sale' => $sale, 'tips' => $tips]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function deleteSale(Request $request)
    {
        try {
            $sale_id = $request->input('sale_id');
            $itemSelected = venta::with('details.product', 'metodosPago', 'customer.tarjetaPuntos')->find($sale_id);
            $hasGiftCardsRedeemed = isset($itemSelected->details) &&$itemSelected->details->contains(fn($detail) => optional($detail->giftCard)->redeemed);
            $sameSalon = $itemSelected->salon_id === $request->user()->salon_id;
            if ($hasGiftCardsRedeemed) {
                return response()->json(['message' => 'giftcard_redeemed'], 400);
            }
            if (!$sameSalon) {
                return response()->json(['message' => 'salon_not_allowed'], 404);
            }
            if(isset($itemSelected->details)){
                self::cancelarStock($itemSelected);
            }
            DRG::cancelarPuntos($itemSelected);
            // $this->recuperarMensajes();
            DRG::deleteRelations($itemSelected);
            return response()->json(['message' => 'Sale deleted successfully']);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function cancelarStock($details)
    {
        foreach ($details as $detail) {
            if ($detail->product) {
                DM::updateStock($detail->selected_item, -$detail->quantity);
            }
        }
    }
}
