<?php

namespace App\Http\Controllers;

use App\Models\metodo_pago_venta;
use App\Models\venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Empleado;
use App\Http\Controllers\DataResourceGrid as DRG;
use App\Models\Asignacion_venta;
use App\Models\producto;

class DataSales extends Controller
{
    public static function updateOrCreateSale(Request $request, $SaleOnly = true)
    {
        try{
            $details = $request->input('details') ?? $request->input('methods');
            $sale = $details['detailsVenta'][0]['sale'];
            $sale_id = $sale['id'] ?? venta::create([
                'salon_id' => $request->user()->salon_id,
                'total' => 0,
                'disccount' => 0,
                'generated_points' => 0,
                'customer_id' => $sale['customer_id'],
                'user_id' => $request->user()->id,
                'items' => 0,
            ])->id;

            $paymentMethods = metodo_pago_venta::where('venta_id',$sale_id)->orderBy('id','asc')->get();

            // Crear un array para rastrear los IDs que siguen vigentes
            $keptIds = [];
            $total_rp = 0;
            $total_date = 0;
            $total_items = 0;
            foreach($details['detailsVenta'] as $detail){
                if(!isset($detail['selected_item'])) continue;
                $priceOutOfDiscounts = DRG::determinatePriceOutOfDiscounts($detail);
                $total_date += $priceOutOfDiscounts * $detail['quantity'];
                $comisionItem = self::defineComisionProduct(self::generateItemToCalculateComision($detail,$detail['empleado_id']));
                $gen_points = DRG::calculateRewardPoints($detail['selected_item'],false,$priceOutOfDiscounts);
                $total_rp += $gen_points * $detail['quantity'];
                $total_items += $detail['quantity'];
                $asignacion = Asignacion_venta::updateOrCreate(
                    ['id' => $detail['id'] ?? null], // usa null si no hay id
                    [
                        'quantity' => $detail['quantity'],
                        'current_price' => $detail['current_price'],
                        'comission' => $comisionItem['balance'],
                        'iva' => isset($detail['iva']) ? ($detail['iva'] === '8%' ? '0.08' : ($detail['iva'] === '16%' ? '0.16' : ($detail['iva'] === 'Exento' ? '0' : $detail['iva']))) : '0.16',
                        'venta_id' => $sale_id,
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
                $product->stock_qty -= $detail['quantity'];
                $product->save();
                // Guardamos los IDs que quedan vigentes
                $keptIds[] = $asignacion->id;
            }

            // Borrar asignaciones que ya no aparecen en la petición
            Asignacion_venta::where('venta_id', $sale_id)
                ->whereNotIn('id', $keptIds)
                ->delete();
            $discount = DRG::getTotalDiscounts($paymentMethods,$total_date);

            venta::where('id',[$sale_id])
                ->update([
                        'generated_points'=>$total_rp,
                        'status'=>DRG::determineDateStatus($total_date-$discount,$details['payed']),
                        'total'=>$total_date,
                        'disccount'=>$discount,
                        'items'=>$total_items,
                    ]
                );
            if($SaleOnly){
                return response()->json(['message' => 'Sale updated successfully']);
            }else{
                return $sale_id;
            }
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
    
    private static function generateItemToCalculateComision($sale,$empleado)
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
        try{
            $empleado = Empleado::with('comision.excepcion_producto','comision.excepcion_cat_producto')->find($item['vendedor']);
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
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
}

