<?php

namespace App\Http\Controllers;

use App\Models\caja_apertura;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DataCashRegister extends Controller
{
    public static function verifyOpening(Request $request)
    {
        try{
            $apertura = self::getLatestOpening($request->user()->salon_id);
            if ($apertura!=null && $apertura->caja_corte_id==null) {
                return ['open' => true];
            } else {
                return ['open' => false];
            }
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }

    public static function getCashFloat(Request $request)
    {
        try{
            $apertura = self::getLatestOpening($request->user()->salon_id);
            if ($apertura!=null && $apertura->caja_corte_id!==null) {
                $corte = $apertura->corteCaja;
                $totalCashReal = $corte->total_cash_real; 
                $efectivoCorte = $corte->total_cash;
                $propinasEfectivo = $corte->propinas_efectivo;
                $gastos = $corte->gastos;
                $cajaChica = $totalCashReal-$efectivoCorte-$propinasEfectivo+$gastos;
                return ['cash_float' => $cajaChica];
            } else {
                return ['cash_float' => null];
            }
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
    
    private static function getLatestOpening($salon_id = null)
    {
        try{
            return caja_apertura::whereHas('user', function ($query) use ($salon_id) {
                $query->where('salon_id', $salon_id);
            })
            ->latest('id')
            ->first();
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
    public static function openCashRegister(Request $request)
    {
        try{
            $salon_id = $request->user()->salon_id;
            $apertura = self::getLatestOpening($salon_id);
            if ($apertura && $apertura->caja_corte_id == null) {
                return response()->json(['msg' => 'already_open'], 400);
            }
            caja_apertura::create([
                'user_id' => $request->user()->id,
                'caja_chica' => $request->input('cash_float', 0),
            ]);
            return response()->json(['msg' => 'cash_register_opened_successfully'], 200);
        }catch(\Throwable $th){
            Log::error($th->getMessage());
            return response()->json(['msg' => 'error_opening_cash_register'], 500);
        }
    }
}
