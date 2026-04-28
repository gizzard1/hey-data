<?php

namespace App\Http\Controllers;

use App\Models\abono;
use App\Models\abonoPropina;
use App\Models\caja_apertura;
use App\Models\caja_corte;
use App\Models\cita;
use App\Models\gasto;
use App\Models\venta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataCashRegister extends Controller
{
    public static function verifyOpening(Request $request)
    {
        try {
            $apertura = self::getLatestOpening($request->user()->salon_id);
            if ($apertura != null && $apertura->caja_corte_id == null) {
                return ['open' => true];
            } else {
                return ['open' => false];
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    public static function getCashFloat(Request $request)
    {
        try {
            $apertura = self::getLatestOpening($request->user()->salon_id);
            if ($apertura != null && $apertura->caja_corte_id !== null) {
                $corte = $apertura->corteCaja;
                $totalCashReal = $corte->total_cash_real;
                $efectivoCorte = $corte->total_cash;
                $propinasEfectivo = $corte->propinas_efectivo;
                $gastos = $corte->gastos;
                $cajaChica = $totalCashReal - $efectivoCorte - $propinasEfectivo + $gastos;
                return ['cash_float' => $cajaChica];
            } else {
                return ['cash_float' => null];
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    private static function getLatestOpening($salon_id = null)
    {
        try {
            return caja_apertura::whereHas('user', function ($query) use ($salon_id) {
                $query->where('salon_id', $salon_id);
            })
                ->latest('id')
                ->first();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function openCashRegister(Request $request)
    {
        try {
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
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['msg' => 'error_opening_cash_register'], 500);
        }
    }
    private static function storePendingPayments($cartPendingMethods, $cartPendingPropinas)
    {
        foreach ($cartPendingMethods as $pendingMethod) {
            abono::create([
                'venta_id' => $pendingMethod['venta_id'] ?? null,
                'cita_id' => $pendingMethod['cita_id'] ?? null,
                'payed_qty' => $pendingMethod['payed_qty'],
                'total_debt' => $pendingMethod['total_debt'],
                'taxes_payed' => $pendingMethod['taxes_payed'],
                'payment_method_id' => $pendingMethod['payment_method_id'],
            ]);
        }
        foreach ($cartPendingPropinas as $pendingMethod) {
            abonoPropina::create([
                'venta_id' => $pendingMethod['venta_id'] ?? null,
                'cita_id' => $pendingMethod['cita_id'] ?? null,
                'payed_qty' => $pendingMethod['payed_qty'],
                'payment_method_id' => $pendingMethod['payment_method_id'],
            ]);
        }
    }

    public static function storeCashRegister(Request $request)
    {
        try {
            $closing_data = $request->input('closing_data');
            $real_cash = $request->input('real_cash');
            $description = $request->input('description');

            $cartPendingMethods = collect($closing_data['cartPendingMethods']);
            $cartPendingPropinas = collect($closing_data['cartPendingPropinas']);

            $opening = self::getLatestOpening($request->user()->salon_id);
            if (!$opening) {
                return response()->json(['msg' => 'no_opening_found'], 400);
            }

            DB::beginTransaction();

            self::storePendingPayments($cartPendingMethods, $cartPendingPropinas);

            $closing = caja_corte::updateOrCreate(
                ['id' => $closing_data['corte_id'] ?? null],
                [
                    'description' => $description,
                    'total_bruto' => $closing_data['totalCorte'],
                    'total_neto' => $closing_data['totalCorte_neto'],
                    'ganancia' => $closing_data['totalCorte_neto'] - $closing_data['puntosCorte'],
                    'total_ventas' => $closing_data['ventasCorte'],
                    'total_servicios' => $closing_data['serviciosCorte'],
                    'total_cash' => $closing_data['efectivoCorte'],
                    'total_cash_real' => $real_cash,
                    'total_tarjeta' => $closing_data['cardCorte'],
                    'total_tarjeta_real' => 0,
                    'total_NF' => $closing_data['msiCorte'],
                    'total_NF_real' => 0,
                    'total_points' => $closing_data['puntosCorte'],
                    'tips' => $closing_data['tipsCorte'],
                    'propinas_efectivo' => $closing_data['propinasEfectivo'],
                    'propinas_efectivo_real' => 0,
                    'propinas_banorte' => 0,
                    'propinas_banorte_real' => 0,
                    'propinas_tarjeta' => 0,
                    'propinas_tarjeta_real' => 0,
                    'comissions' => $closing_data['comissionsCorte'],
                    'user_id' => $request->user()->id,
                    'caja_chica' => $closing_data['caja_chica'],
                    'caja_chica_real' => 0,
                    'gastos' => $closing_data['gastos_qty'],
                ]
            );

            $opening->caja_corte_id = $closing->id;
            $opening->save();

            DB::commit();
            return response()->json(['msg' => 'cash_register_closed_successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return response()->json(['msg' => 'error_closing_cash_register'], 500);
        }
    }

    public static function closeCashRegister(Request $request)
    {
        try {
            $closingData = self::getClosingData($request);

            if (!$closingData) {
                return response()->json(['msg' => 'No opening found'], 400);
            }

            return response()->json([
                'msg' => 'closing_data_retrieved_successfully',
                'data' => $closingData,
            ], 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json(['msg' => 'error_retrieving_closing_data'], 500);
        }
    }

    private static function getClosingData(Request $request)
    {
        try {
            $now = Carbon::now()->format('Y-m-d H:i:s');
            $salon_id = $request->user()->salon_id;
            $apertura = self::getLatestOpening($salon_id);

            if (!$apertura) {
                return null;
            }

            $aperturaTime = $apertura->created_at->format('Y-m-d H:i:s');
            $caja_chica = $apertura->caja_chica;

            $gastos = gasto::floatCashBetweenDates($salon_id, $aperturaTime, $now);
            $gastos_qty = $gastos->sum('total');
            $gastos_collection = $gastos->get();

            $ventas = venta::transactionsBetweenDatesClosing($salon_id, $aperturaTime, $now)->get();
            $citas = cita::transactionsBetweenDatesClosing($salon_id, $aperturaTime, $now)->get();

            // Inicializar acumuladores
            $acumuladores = [
                'efectivoCorte' => 0,
                'propinasEfectivo' => 0,
                'totalTips' => 0,
                'totalCorte_neto' => 0,
                'comissionsCorte' => 0,
                'ventasCorte' => 0,
                'serviciosCorte' => 0,
                'cardCorte' => 0,
                'msiCorte' => 0,
                'puntosCorte' => 0,
                'tipsCorte' => 0,
                'totalCorte' => 0,
                'cartPendingMethods' => collect(),
                'cartPendingPropinas' => collect(),
            ];

            // Procesar ventas y citas
            $acumuladores = self::acumularTransacciones($ventas, 0, $acumuladores);
            $acumuladores = self::acumularTransacciones($citas, 1, $acumuladores);

            return array_merge($acumuladores, [
                'caja_chica' => $caja_chica,
                'gastos_qty' => $gastos_qty,
                'gastos' => $gastos_collection,
                'ventas' => $ventas,
                'citas' => $citas,
                'apertura_id' => $apertura->id,
            ]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return null;
        }
    }

    private static function acumularTransacciones($transacciones, $isDate, $acumuladores)
    {
        try {
            foreach ($transacciones as $transaccion) {
                $methods_data = self::acumularMetodos($transaccion->metodosPago, $transaccion->status == 'Pendiente', 0, $isDate, $acumuladores);
                $acumuladores['totalTips'] += self::acumularMetodos($transaccion->propinas, $transaccion->status == 'Pendiente', 1, $isDate, $acumuladores)['totalTips'];

                $total_taxes = self::calculateTaxes($transaccion, $acumuladores);
                $acumuladores['comissionsCorte'] = $total_taxes['comissions'];

                if ($transaccion->status == 'Pendiente') {
                    $total_transaccion = $transaccion->total - $transaccion->discount;
                    $methods_data = $methods_data['total_methods'];

                    // Actualizar cartPendingMethods con las nuevas claves
                    $acumuladores['cartPendingMethods'] = $acumuladores['cartPendingMethods']->map(function ($item) use ($transaccion, $isDate, $total_transaccion, $total_taxes) {
                        $key = $isDate ? 'cita_id' : 'venta_id';
                        if ($item[$key] === $transaccion->id) {
                            $percent_payed = $item['payed_qty'] / $total_transaccion;
                            $item['total_debt'] = $total_transaccion;
                            $item['taxes_payed'] = $percent_payed * $total_taxes['taxes'];
                        }
                        return $item;
                    });

                    $percent_payed = $methods_data / $total_transaccion;
                    $total_taxes['taxes'] = $percent_payed * $total_taxes['taxes'];
                } else {
                    $methods_data = $methods_data['total_methods'];
                }

                // Procesar abonos previos
                $metodos_prev = [];
                $abonos = $transaccion->abonos()->orderBy('payed_qty', 'asc')->get();
                foreach ($abonos as $abono) {
                    if (!in_array($abono->payment_method_id, $metodos_prev)) {
                        $acumuladores['totalCorte_neto'] += $abono->taxes_payed;
                        $acumuladores['efectivoCorte'] += $abono->payed_qty;
                        $metodos_prev[] = $abono->payment_method_id;
                    }
                }

                // Procesar abonos de propinas
                $metodos_prev = [];
                $abonos_propina = $transaccion->abonoPropinas()->orderBy('created_at', 'desc')->get();
                foreach ($abonos_propina as $abono) {
                    if (!in_array($abono->payment_method_id, $metodos_prev)) {
                        $acumuladores['propinasEfectivo'] += $abono->payed_qty;
                        $acumuladores['totalTips'] += $abono->payed_qty;
                        $metodos_prev[] = $abono->payment_method_id;
                    }
                }

                $acumuladores['totalCorte_neto'] += $total_taxes['taxes'];
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }

        return $acumuladores;
    }

    private static function acumularMetodos($metodos, $isPending, $type, $isDate, &$acumuladores)
    {
        try {
            $total_methods = 0;

            foreach ($metodos as $metodo) {
                if ($metodo->amount == 0) {
                    continue;
                }

                $qty = $metodo->amount - $metodo->change;
                $total_methods += $qty;

                if ($isPending) {
                    if ($isDate) {
                        $coll = [
                            'payment_method_id' => $metodo->payment_method_id,
                            'payed_qty' => - ($qty),
                            'cita_id' => $metodo->cita_id,
                        ];
                    } else {
                        $coll = [
                            'payment_method_id' => $metodo->payment_method_id,
                            'payed_qty' => - ($qty),
                            'venta_id' => $metodo->venta_id,
                        ];
                    }

                    if ($type) {
                        $acumuladores['cartPendingPropinas']->push($coll);
                    } else {
                        $acumuladores['cartPendingMethods']->push($coll);
                    }
                }

                // Sumar por método
                if ($metodo->payment_method_id === 1) {
                    if (!$type) {
                        $acumuladores['efectivoCorte'] += $qty;
                    } else {
                        $acumuladores['propinasEfectivo'] += $qty;
                    }
                }
            }

            return [
                'total_methods' => $total_methods,
                'totalTips' => 0,
            ];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return ['total_methods' => 0, 'totalTips' => 0];
        }
    }

    private static function calculateTaxes($transaccion, &$acumuladores)
    {
        try {
            $disccount_per_item = 0;
            $total_taxes = 0;
            $comissions = 0;

            if ($transaccion->disccount > 0) {
                $items = count($transaccion->details);
                $disccount_per_item = $items > 0 ? $transaccion->disccount / $items : $transaccion->disccount;
            }

            foreach ($transaccion->details as $detail) {
                $extra_disccount = $disccount_per_item * ($detail->quantity ?? 1);

                if ($detail->disccount_price > 0) {
                    $total_taxes += ($detail->disccount_price - $extra_disccount) / ($detail->iva + 1);
                } else {
                    $total_taxes += ($detail->current_price - $extra_disccount) / ($detail->iva + 1);
                }

                $comissions += $detail->comission;
            }

            return [
                'taxes' => $total_taxes,
                'comissions' => $comissions,
            ];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return ['taxes' => 0, 'comissions' => 0];
        }
    }
}
