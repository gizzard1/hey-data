<?php

namespace App\Http\Controllers;

use App\Models\caja_apertura;
use App\Models\caja_corte;
use App\Models\cita;
use App\Models\Entrada;
use App\Models\Material;
use App\Models\venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataTransactions extends Controller
{
    public static function loadTransactions(Request $request)
    {
        try {
            $citas = [];
            $ventas = [];
            $aperturas = [];
            $cortes = [];
            $usos = [];
            $entradas = [];
            $salon_id = $request->user()->salon_id;
            $filters = $request->input('filters', [
                'ventasFilter' => true,
                'citasFilter' => true,
                'aperturasFilter' => true,
                'cortesFilter' => true,
                'usosFilter' => true,
                'entradasFilter' => true,
                'dateFilter' => 'today',
            ]);
            $filter_type = $filters['dateFilter'];
            if($filter_type === 'custom'){
                $start = $request->input('start_date');
                $end = $request->input('end_date');
            } else {
                [$start, $end] = self::getDateRange($filter_type);
            }
            if ($filters['ventasFilter']) {
                $ventas = venta::transactionsBetweenDates($salon_id, $start, $end);
            }
            if ($filters['citasFilter']) {
                $citas = cita::transactionsBetweenDates($salon_id, $start, $end);
            }
            if ($filters['aperturasFilter']) {
                $aperturas = caja_apertura::transactionsBetweenDates($salon_id, $start, $end);
            }
            if ($filters['cortesFilter']) {
                $cortes = caja_corte::transactionsBetweenDates($salon_id, $start, $end);
            }
            if ($filters['usosFilter']) {
                $usos = Material::transactionsBetweenDates($salon_id, $start, $end);
            }
            if ($filters['entradasFilter']) {
                $entradas = Entrada::transactionsBetweenDates($salon_id, $start, $end);
            }

            $info = [
                'openingCashes' => $aperturas,
                'dates' => $citas,
                'closingCashes' => $cortes,
                'sales' => $ventas,
                'entries' => $entradas,
                'materials' => $usos,
            ];

            return response()->json([$info]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['error' => 'error loading transactions'], 200);
        }
    }
    private static function getDateRange($filter_type)
    {
        $today = now()->startOfDay();
        switch ($filter_type) {
            case 'today':
                return [$today, $today->copy()->endOfDay()];
            case 'yesterday':
                $yesterday = $today->copy()->subDay();
                return [$yesterday, $yesterday->copy()->endOfDay()];
            case 'week':
                return [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()];
            case 'month':
                return [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()];
            case 'year':
                return [$today->copy()->startOfYear(), $today->copy()->endOfYear()];
            default:
                return [$today, $today->copy()->endOfDay()];
        }
    }
}