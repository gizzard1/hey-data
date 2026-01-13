<?php

namespace App\Http\Controllers;

use App\Models\cita;
use App\Models\cliente;
use App\Models\coupon;
use App\Models\metodo_pago_servicio;
use App\Models\Propina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\DataResourceGrid as DRG;
use App\Models\metodo_pago_venta;
use App\Models\Salon;
use App\Models\venta;

class DataPayment extends Controller
{
    private static function createOrUpdateSaleMethod($sale,$tipo,$item)
    {
        try{
            return metodo_pago_venta::updateOrCreate(
                ['id' => $item['id'] ?? null], // usa null si no hay id
                [
                    'venta_id' => $sale->id,
                    'payment_method_id'=>$item['payment_method_id'],
                    'tipo'=>$tipo,
                    'reference'=>$item['reference'],
                    'amount'=>floatval($item['amount']),
                ]
            );
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
    private static function createOrUpdateDateMethod($date,$tipo,$item)
    {
        try{
            return metodo_pago_servicio::updateOrCreate(
                ['id' => $item['id'] ?? null], // usa null si no hay id
                [
                    'cita_id' => $date['id'],
                    'payment_method_id'=>$item['payment_method_id'],
                    'tipo'=>$tipo,
                    'reference'=>$item['reference'],
                    'amount'=>floatval($item['amount']),
                ]
            );
        }
        catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
    public static function updateMethods(Request $request)
    {
        try{
            $sale_id = null;
            $data = $request->input('methods');
            
            if (isset($data['detailsVenta'])) {
                $sale_id = DataSales::updateOrCreateSale($request, false);
            }
            
            $mov = $sale_id ? venta::find($sale_id) : $data['details'][0]['date'];
            // Crear un array para rastrear los IDs que siguen vigentes
            $keptMethodsIds = [];
            $keptTipsIds = [];
            $total_date = $mov['total'] ?? $mov->total;
            $total_methods = 0;
            foreach($data['methods'] as $item){
                $tipo = isset($item['tipo']) ? DRG::determinateTypeOfDiscount($item['tipo']) : 'Cantidad';
                // Guardamos los IDs que quedan vigentes
                $metodo = $sale_id ? self::createOrUpdateSaleMethod($mov,$tipo,$item) : self::createOrUpdateDateMethod($mov,$tipo,$item);
                $keptMethodsIds[] = $metodo->id;
                $total_methods += $item['payment_method_id'] === 4 ? 0 : floatval($item['amount']);
            }
            foreach($data['tips'] as $item){
                $propina = Propina::updateOrCreate(
                    ['id' => $item['id'] ?? null], // usa null si no hay id
                    [
                        'cita_id' => $sale_id ? null : $mov['id'],
                        'venta_id' => $sale_id ? $mov->id : null,
                        'payment_method_id'=>$item['payment_method_id'],
                        'reference'=>$item['reference'],
                        'amount'=>floatval($item['amount']),
                        'empleado_id'=>$item['empleado']['id'],
                    ]
                );
                // Guardamos los IDs que quedan vigentes
                $keptTipsIds[] = $propina->id;
            }
            $paymentMethods = $sale_id ? metodo_pago_venta::where('venta_id',$mov->id)->orderBy('id','asc')->get() : metodo_pago_servicio::where('cita_id',$mov['id'])->orderBy('id','asc')->get();
            $total_discount = DRG::getTotalDiscounts($paymentMethods,$total_date);
            $customer = cliente::with('tarjetaPuntos')->find($sale_id ? $mov->customer_id : $mov['clienteId']);
            //metodos de pago
            foreach($paymentMethods as $method) {
                if($method->payment_method_id==5 && $method->updated_at == $method->created_at){
                    $customer->tarjetaPuntos->balance -= $method->amount;
                    $customer->tarjetaPuntos->save();
                }

                if($method->payment_method_id==1){
                    $remaining = $total_discount+$total_methods-$total_date;
                    $method->change = $remaining > 0 ? $remaining : 0;
                }
                
                if($method->payment_method_id==99999){
                    $gc = coupon::firstWhere('password',$method->reference);
                    $gc->redeemed = 1;
                    $gc->save();
                }
                
                $method->save();
            }
            // Borrar métodos de pago y propinas que ya no aparecen en la petición
            $sale_id ? self::deleteSaleMethods($keptMethodsIds, $keptTipsIds, $mov) : self::deleteDateMethods($keptMethodsIds, $keptTipsIds, $mov);

            // Actualizar el status y descuento de la venta o cita
            $status = $sale_id ? self::updateStatusNDiscountSale($mov,$total_date,$total_methods,$total_discount) : self::updateStatusNDiscountDate($mov,$total_date,$total_methods,$total_discount);
            
            // Si la cita o venta queda como Pagada, retornar datos adicionales
            if ($status === 'Pagada'){
                $data = [
                    'mov_id' => $sale_id ? $mov->id : $mov['id'],
                    'business_data' => $sale_id ? $mov->salon->only(['name','file']) : cita::find($mov['id'])->salon->only(['name','file']),
                    'user_data' => $sale_id ? $mov->user->only(['name','role','email']) : cita::find($mov['id'])->user->only(['name','role','email']),
                ];
                return response()->json(['message' => 'Appointment updated successfully','data' => $data]);
            }

            return response()->json(['message' => 'Appointment updated successfully']);
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
    private static function updateStatusNDiscountSale($mov,$total_date,$total_methods,$total_discount)
    {
        try{
            $status = DRG::determineDateStatus($total_date,$total_methods+$total_discount);
            venta::where('id',[$mov->id])
                ->update([
                        'status'=>$status,
                        'disccount'=>$total_discount,
                    ]
                );
            return $status;
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
    private static function updateStatusNDiscountDate($mov,$total_date,$total_methods,$total_discount)
    {
        try{
            $status = DRG::determineDateStatus($total_date,$total_methods+$total_discount);
            cita::where('id',[$mov['id']])
                ->update([
                        'status'=>$status,
                        'disccount'=>$total_discount,
                    ]
                );
            return $status;
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
    private static function deleteDateMethods($keptMethodsIds, $keptTipsIds, $date)
    {
        try{
            metodo_pago_servicio::where('cita_id', $date['id'])
                ->whereNotIn('id', $keptMethodsIds)
                ->delete();

            Propina::where('cita_id', $date['id'])
                ->whereNotIn('id', $keptTipsIds)
                ->delete();
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
    private static function deleteSaleMethods($keptMethodsIds, $keptTipsIds, $sale)
    {
        try{
            metodo_pago_venta::where('venta_id', $sale->id)
                ->whereNotIn('id', $keptMethodsIds)
                ->delete();

            Propina::where('venta_id', $sale->id)
                ->whereNotIn('id', $keptTipsIds)
                ->delete();
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
}

