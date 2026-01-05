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

class DataPayment extends Controller
{
    public static function updateMethods(Request $request)
    {
        try{
            $data = $request->input('methods');
            $date = $data['details'][0]['date'];
            // Crear un array para rastrear los IDs que siguen vigentes
            $keptMethodsIds = [];
            $keptTipsIds = [];
            $total_date = $date['total'];
            $total_methods = 0;
            foreach($data['methods'] as $item){
                $tipo = isset($item['tipo']) ? DRG::determinateTypeOfDiscount($item['tipo']) : 'Cantidad';
                $metodo = metodo_pago_servicio::updateOrCreate(
                    ['id' => $item['id'] ?? null], // usa null si no hay id
                    [
                        'cita_id' => $date['id'],
                        'payment_method_id'=>$item['payment_method_id'],
                        'tipo'=>$tipo,
                        'reference'=>$item['reference'],
                        'amount'=>floatval($item['amount']),
                    ]
                );
                // Guardamos los IDs que quedan vigentes
                $keptMethodsIds[] = $metodo->id;
                $total_methods += $item['payment_method_id'] === 4 ? 0 : floatval($item['amount']);
            }
            foreach($data['tips'] as $item){
                $propina = Propina::updateOrCreate(
                    ['id' => $item['id'] ?? null], // usa null si no hay id
                    [
                        'cita_id' => $date['id'],
                        'payment_method_id'=>$item['payment_method_id'],
                        'reference'=>$item['reference'],
                        'amount'=>floatval($item['amount']),
                        'empleado_id'=>$item['empleado']['id'],
                    ]
                );
                // Guardamos los IDs que quedan vigentes
                $keptTipsIds[] = $propina->id;
            }
            $paymentMethods = metodo_pago_servicio::where('cita_id',$date['id'])->orderBy('id','asc')->get();
            $total_discount = DRG::getTotalDiscounts($paymentMethods,$total_date);
            $customer = cliente::with('tarjetaPuntos')->find($date['clienteId']);
            //metodos de pago
            foreach($paymentMethods as $method) {
                if($method->payment_method_id==5 && $method->updated_at == $method->created_at){
                    $customer->tarjetaPuntos->balance -= $method->amount;
                    $customer->tarjetaPuntos->save();
                }

                if($method->payment_method_id==1){
                    $remaining = $total_discount+$total_methods-$total_date;
                    $method->change = $remaining;
                }
                
                if($method->payment_method_id==99999){
                    $gc = coupon::firstWhere('password',$method->reference);
                    $gc->redeemed = 1;
                    $gc->save();
                }
                
                $method->save();
            }
            // Borrar métodos de pago y propinas que ya no aparecen en la petición
            metodo_pago_servicio::where('cita_id', $date['id'])
                ->whereNotIn('id', $keptMethodsIds)
                ->delete();

            Propina::where('cita_id', $date['id'])
                ->whereNotIn('id', $keptTipsIds)
                ->delete();

            cita::where('id',[$date['id']])
                ->update([
                        'status'=>DRG::determineDateStatus($total_date,$total_methods+$total_discount),
                        'disccount'=>$total_discount,
                    ]
                );
            return response()->json(['message' => 'Appointment updated successfully']);
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
}

