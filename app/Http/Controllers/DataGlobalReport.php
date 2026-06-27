<?php

namespace App\Http\Controllers;

use App\Http\Livewire\Informe;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DataGlobalReport extends Controller
{
    public static function loadGlobalReport(Request $request)
    {
        try {
            $filter_type = $request->input('filter_type');
            if ($filter_type === 'custom') {
                [$start, $end] = DataTransactions::setCustomDate($request);
            } else {
                [$start, $end] = DataTransactions::getDateRange($filter_type);
            }

            $salon_id = $request->user()->salon_id;
            $report = Informe::getGlobalReportData($salon_id, $start, $end);

            return response()->json($report);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());

            return response()->json(['error' => 'error loading global report'], 500);
        }
    }
}
