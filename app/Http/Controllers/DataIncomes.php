<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataIncomes extends Controller
{
    public static function loadIncomes(Request $request)
    {
        try {

            $citas = [];
            $ventas = [];
            $propinas = [];
            $empleado_id = $request->user()->empleado->id;

            $empleado = Empleado::find($empleado_id);
            $filter_type = $request->input('filter_type');

            if (!$empleado) {
                return response()->json(['msg' => 'Sin empleado']);
            }

            $filter_type = $request->input('filter_type');

            if ($filter_type === 'custom') {
                [$start, $end] = DataTransactions::setCustomDate($request);
            } else {
                [$start, $end] = DataTransactions::getDateRange($filter_type);
            }

            $ventas = $empleado->salesIncomesBetweenDates($start, $end)->get();
            $citas = $empleado->servicesIncomesBetweenDates($start, $end)->get();
            $propinas = $empleado->tipsIncomesBetweenDates($start, $end)->get();

            return response()->json([
                'ventas' => $ventas,
                'citas' => $citas,
                'propinas' => $propinas,
                'msg' => 'Incomes loaded successfully'
            ], 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
