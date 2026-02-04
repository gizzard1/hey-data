<?php

namespace App\Http\Controllers;

use App\Http\Livewire\Agenda;
use App\Models\Asignacion_servicio;
use App\Models\cita;
use App\Http\Controllers\DataSales as DS;
use App\Models\Asignacion_venta;
use App\Models\bloqueo;
use App\Models\cliente;
use App\Models\coupon;
use App\Models\Empleado;
use App\Models\metodo_pago;
use App\Models\metodo_pago_servicio;
use App\Models\producto;
use App\Models\Propina;
use App\Models\servicio;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataResourceGrid extends Controller
{
    public static function loadEventDetails(Request $request)
    {
        try {
            $cita_id = $request->query('date_id');

            $dates = Asignacion_servicio::select(
                'id',
                'empleado_id',
                'cita_id',
                'color',
                'selected_service',
                'disccount_price',
                'discount_qty',
                'discount_type',
                'current_price',
                'base_comision',
                'iva',
                DB::raw("duration as duracionMinutos"),
                DB::raw("selected_service as servicioId"),
                DB::raw("empleado_id as empleadoId"),
                DB::raw("current_price as precio"),
                DB::raw("DATE_FORMAT(`start`, '%H:%i') as inicioServicio"),
                DB::raw("DATE_FORMAT('start', 'Y-m-d') as fecha"),
                DB::raw("DATE_FORMAT(ADDTIME(`start`, SEC_TO_TIME(duration * 60)), '%H:%i') as finServicio"),
            )->with([
                'servicio' => function ($q) {
                    $q->select(
                        'id',
                        DB::raw("name as nombre"),
                        DB::raw("gross_price as precio"),
                        DB::raw("duration as duracionMinutos"),
                    );
                },
                'empleado' => function ($q) {
                    $q->select(
                        'id',
                        DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as name"),
                        DB::raw("color_preset as color")
                    );
                },
                'date' => function ($q) {
                    $q->select(
                        'id',
                        'customer_id',
                        'total',
                        'disccount',
                        DB::raw('SUM(total) - SUM(COALESCE(`disccount`, 0)) as totalSubDiscount'),
                        DB::raw("DATE_FORMAT(`start`, '%H:%i') as startTime"),
                        DB::raw("DATE_FORMAT(`end`, '%H:%i') as endTime"),
                        DB::raw("DATE(start) as date"),
                        DB::raw("status as estado"),
                        DB::raw("customer_id as clienteId"),
                    )->with([
                        'customer' => function ($q) {
                            $q->select(
                                'id',
                                DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as nombre"),
                                DB::raw("phone as telefono"),
                            )->with([
                                'tarjetaPuntos' => function ($q) {
                                    $q->select('id', 'intern_barcode', 'balance', 'cliente_id');
                                }
                            ]);
                        },
                        'etiquetas' => function ($q) {
                            $q->select('name', 'color');
                        }
                    ]);
                },
            ])
                ->where('cita_id', $cita_id) // Filtra por la fecha del día de $currentDateC
                ->get();

            // Obtener detalles de venta asociados a la cita
            $detailsVenta = Asignacion_venta::where('cita_id', $cita_id)
                ->select(
                    'id',
                    'selected_item',
                    'cita_id',
                    'empleado_id',
                    'quantity',
                    'disccount_price',
                    'discount_qty',
                    'current_price',
                    'discount_type',
                    'disccount_price',
                    'base_comision',
                    'iva',
                )
                ->with([
                    'product' => function ($q) {
                        $q->select(
                            'id',
                            'name',
                            'description',
                            'gross_price',
                            'iva',
                            'disccount_price',
                            'cost',
                            'unit_type',
                            'stock_qty',
                            'min_stock',
                            'sku',
                            'brand_id',
                            'type_product',
                        );
                    },
                    'empleado' => function ($q) {
                        $q->select(
                            'id',
                            DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as name"),
                            DB::raw("color_preset as color")
                        );
                    },
                    'cita' => function ($q) {
                        $q->select(
                            'id',
                            'customer_id',
                            'total',
                            'disccount',
                            DB::raw('SUM(total) - SUM(COALESCE(`disccount`, 0)) as totalSubDiscount'),
                            DB::raw("DATE_FORMAT(`start`, '%H:%i') as startTime"),
                            DB::raw("DATE_FORMAT(`end`, '%H:%i') as endTime"),
                            DB::raw("DATE(start) as date"),
                            DB::raw("status as estado"),
                            DB::raw("customer_id as clienteId"),
                        )->with(['customer' => function ($q) {
                            $q->select(
                                'id',
                                DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as nombre"),
                                DB::raw("phone as telefono"),
                            )->with(['tarjetaPuntos' => function ($q) {
                                $q->select('id', 'intern_barcode', 'balance', 'cliente_id');
                            }]);
                        }]);
                    },
                ])
                ->get();

            $payed = metodo_pago_servicio::where('cita_id', $cita_id)
                ->where('payment_method_id', '!=', 4)
                ->selectRaw('SUM(amount) - SUM(COALESCE(`change`, 0)) as payed')
                ->value('payed');

            $methods = metodo_pago_servicio::where('cita_id', $cita_id)
                ->select(
                    'id',
                    'payment_method_id',
                    'tipo',
                    'reference',
                    'amount',
                    'change'
                )
                ->with([
                    'metodoPago' => function ($q) {
                        $q->select(
                            'id',
                            DB::raw("Payment_method as name"),
                        );
                    }
                ])
                ->get();

            $tips = Propina::where('cita_id', $cita_id)
                ->select(
                    'id',
                    'payment_method_id',
                    'reference',
                    'amount',
                    'empleado_id'
                )
                ->with([
                    'metodoPago' => function ($q) {
                        $q->select(
                            'id',
                            DB::raw("Payment_method as name"),
                        );
                    },
                    'empleado' => function ($q) {
                        $q->select(
                            'id',
                            DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as name"),
                            DB::raw("color_preset as color")
                        );
                    },
                ])
                ->get();

            return ['details' => $dates, 'payed' => $payed, 'methods' => $methods, 'tips' => $tips, 'detailsVenta' => $detailsVenta];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function loadDates(Request $request)
    {
        try {
            $salon_id = $request->user()->salon_id;
            $currentDate = $request->query('currentDate');
            $horas = Agenda::loadSalonTimes($request->user()->salon_id);
            $timeSlots = [];
            foreach ($horas as $index => $hora) {
                $timeSlots[$index]['time'] = $hora;
                $timeSlots[$index]['isMainHour'] = 0;
            }
            $currentDate = date('Y-m-d', strtotime($currentDate));

            $dates = Asignacion_servicio::select(
                'id',
                'empleado_id',
                'cita_id',
                'color',
                'selected_service',
                DB::raw("duration as duracionMinutos"),
                DB::raw("selected_service as servicioId"),
                DB::raw("empleado_id as empleadoId"),
                DB::raw("current_price as precio"),
                DB::raw("DATE_FORMAT(`start`, '%H:%i') as inicioServicio"),
                DB::raw("DATE_FORMAT('start', 'Y-m-d') as fecha"),
                DB::raw("DATE_FORMAT(ADDTIME(`start`, SEC_TO_TIME(duration * 60)), '%H:%i') as finServicio"),
            )->with([
                'servicio' => function ($q) {
                    $q->select(
                        'id',
                        DB::raw("name as nombre"),
                        DB::raw("gross_price as precio"),
                        DB::raw("duration as duracionMinutos"),
                    );
                },
                'empleado' => function ($q) {
                    $q->select(
                        'id',
                        DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as name"),
                        DB::raw("color_preset as color")
                    );
                },
                'date' => function ($q) {
                    $q->select(
                        'id',
                        'customer_id',
                        'description',
                        DB::raw("DATE_FORMAT(`start`, '%H:%i') as startTime"),
                        DB::raw("DATE_FORMAT(`end`, '%H:%i') as endTime"),
                        DB::raw("DATE(start) as date"),
                        DB::raw("status as estado"),
                        DB::raw("customer_id as clienteId"),
                        'total',
                    )->with(['customer' => function ($q) {
                        $q->select(
                            'id',
                            DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as nombre"),
                            DB::raw("phone as telefono"),
                        )->with([
                            'categorias' => function ($q) {
                                $q->select('name');
                            }
                        ]);
                    }])
                        ->with([
                            'etiquetas' => function ($q) {
                                $q->select('name', 'color');
                            }
                        ]);
                },
            ])->whereHas('date', function ($q) use ($currentDate, $salon_id) {
                $q->where('salon_id', $salon_id);
            })
                ->whereDate('start', $currentDate) // Filtra por la fecha del día de $currentDateC
                ->get();

            // Obtener bloqueos
            $bloqueos = bloqueo::where('salon_id', $salon_id)
                ->whereDate('start', $currentDate)
                ->select(
                    'id',
                    'empleado_id',
                    'color',
                    'description',
                    DB::raw("DATE_FORMAT(`start`, '%H:%i') as inicioServicio"),
                    DB::raw("DATE_FORMAT(`end`, '%H:%i') as finServicio"),
                    'start',
                    'end'
                )
                ->with('empleado')
                ->get();

            return ['mainTimeSlots' => $timeSlots, 'citas' => $dates, 'bloqueos' => $bloqueos];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function loadWorkersNPayment(Request $request)
    {
        try {
            $salon_id = $request->user()->salon_id;
            $data = self::loadWorkers($request);
            $data['methods'] = metodo_pago::select(
                'id',
                DB::raw("Payment_method as name")
            )
                ->where('salon_id', $salon_id)
                ->orWhere('salon_id', null)
                ->where('id', '!=', 99999)
                ->where('id', '!=', 5)
                ->where('id', '!=', 4)
                ->get();

            return $data;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    private static function getEmployees($salon_id)
    {
        try {
            return Empleado::select(
                'id',
                DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as name"),
                DB::raw("color_preset as color")
            )
                ->where('salon_id', $salon_id)
                ->get();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function loadWorkers(Request $request)
    {
        try {
            $salon_id = $request->user()->salon_id;
            $empleados = self::getEmployees($salon_id);
            return ['workers' => $empleados];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function determinateTypeOfDiscount($type)
    {
        switch ($type) {
            case '$':
                $type = 'Cantidad';
                break;
            case '%':
                $type = 'Porcentaje';
                break;
            default:
                break;
        }
        return $type;
    }
    public static function validateGiftCard(Request $request)
    {
        try {
            $pass = $request->query('pass');
            $cupon = Coupon::firstWhere('password', $pass);

            if (!$cupon) {
                return response()->json(['message' => 'not found', 'giftCard' => null]);
            }

            if ($cupon->redeemed) {
                return response()->json(['message' => 'redeemed', 'giftCard' => null]);
            }

            if ($cupon->expires_at && Carbon::parse($cupon->expires_at)->isPast()) {
                return response()->json(['message' => 'expired', 'giftCard' => null]);
            }

            return response()->json(['message' => 'ok', 'giftCard' => $cupon]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function loadBlocking(Request $request)
    {
        try {
            $blocking_id = $request->query('blocking_id');
            $blocking = bloqueo::where('id', $blocking_id)
                ->select('id', 'empleado_id', 'color', 'description', 'start', 'end')
                ->with('empleado:id,first_name,last_name,color_preset,visible')
                ->first();
            return $blocking;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function updateBlocking(Request $request)
    {
        try {
            $blockingData = $request->input('blocking');
            // Concatenar fecha con la hora para start y end
            $date = explode('T', $blockingData['date'])[0];
            $blockingData['start'] = $date . ' ' . $blockingData['start'];
            $blockingData['end'] = $date . ' ' . $blockingData['end'];

            bloqueo::updateOrCreate(
                ['id' => $blockingData['id'] ?? null], // usa null si no hay id
                [
                    'salon_id' => $request->user()->salon_id,
                    'empleado_id' => $blockingData['empleado_id'],
                    'color' => $blockingData['color'],
                    'description' => $blockingData['description'],
                    'start' => Carbon::parse($blockingData['start']),
                    'end' => Carbon::parse($blockingData['end']),
                ]
            );

            return response()->json(['message' => 'Blocking updated successfully']);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function updateDetails(Request $request)
    {
        try {
            // Obtener los datos de la cita desde la solicitud
            $details = $request->input('details');
            $date = $details['details'][0]['date'];

            // Crear o actualizar la cita
            $date_id = $date['id'] ?? cita::create([
                'salon_id' => $request->user()->salon_id,
                'start' => Carbon::parse($date['date'] . ' ' . '00:00'),
                'end' => Carbon::parse($date['date'] . ' ' . '00:00'),
                'remember' => 0,
                'total' => 0,
                'disccount' => 0,
                'generated_points' => 0,
                'customer_id' => $date['clienteId'],
                'user_id' => $request->user()->id,
            ])->id;

            // Obtener los métodos de pago asociados a la cita
            $paymentMethods = metodo_pago_servicio::where('cita_id', $date_id)->orderBy('id', 'asc')->get();

            // Crear un array para rastrear los IDs que siguen vigentes
            $keptIds = [];
            $startTimeToMinutes = 1500;
            $endTimeToMinutes = 0;
            $total_rp = 0;
            $total_date = 0;
            foreach ($details['details'] as $detail) {
                $priceOutOfDiscounts = self::determinatePriceOutOfDiscounts($detail);
                $total_date += $priceOutOfDiscounts;
                $startDetailToMinutes = self::timeToMinutes($detail['inicioServicio']);

                // Actualizar el tiempo de inicio más temprano
                if ($startDetailToMinutes < $startTimeToMinutes) {
                    $startTimeToMinutes = $startDetailToMinutes;
                }
                $endDetailToMinutes = self::timeToMinutes($detail['finServicio']);
                if ($endTimeToMinutes < $endDetailToMinutes) {
                    $endTimeToMinutes = $endDetailToMinutes;
                }
                $comisionItem = self::defineComisionService(self::generateItemToCalculateComision($detail, $detail['empleadoId']), 'servicio');
                $gen_points = self::calculateRewardPoints($detail['servicioId'], true, $priceOutOfDiscounts, $request, $date['clienteId']);
                $total_rp += $gen_points;
                $asignacion = Asignacion_servicio::updateOrCreate(
                    ['id' => $detail['id'] ?? null], // usa null si no hay id
                    [
                        'cita_id' => $date_id,
                        'selected_service' => $detail['servicioId'],
                        'empleado_id' => $detail['empleadoId'],
                        'discount_qty' => floatval($detail['discount_qty']),
                        'discount_type' => $detail['discount_type'],
                        'generated_points' => $gen_points,
                        'current_price' => $detail['precio'],
                        'disccount_price' => $detail['disccount_price'],
                        'comission' => $comisionItem['balance'],
                        'type_comision_calculated' => $comisionItem['type'],
                        'iva' => isset($detail['iva']) ? ($detail['iva'] === '8%' ? '0.08' : ($detail['iva'] === '16%' ? '0.16' : ($detail['iva'] === 'Exento' ? '0' : $detail['iva']))) : '0.16',
                        'start' => Carbon::parse($date['date'] . ' ' . $detail['inicioServicio']),
                        'color' => $detail['color'],
                        'duration' => $detail['duracionMinutos']
                    ]
                );
                // Guardamos los IDs que quedan vigentes
                $keptIds[] = $asignacion->id;
            }

            // Actualizar los detalles de ventas en la cita
            if (isset($details['detailsVenta'])) {
                // Procesar los detalles de la venta asociados a la cita
                $data_details = DS::createSaleDetails($details, $date_id, $request, false, $date['clienteId']);

                // Actualizar los totales acumulados
                $total_rp += $data_details['total_rp'];
                $total_date += $data_details['total_date'];

                // Obtener los IDs que se deben mantener
                $keptSaleIds = $data_details['keptIds'] ?? [];

                // Obtener las asignaciones actuales de la venta
                $asignacionesToDelete = Asignacion_venta::where('cita_id', $date_id)
                    ->whereNotIn('id', $keptSaleIds)
                    ->get();

                DS::updateStockAfterSale($asignacionesToDelete);
            }

            // Borrar asignaciones que ya no aparecen en la petición
            Asignacion_servicio::where('cita_id', $date_id)
                ->whereNotIn('id', $keptIds)
                ->delete();

            // Recalcular el descuento total
            $discount = self::getTotalDiscounts($paymentMethods, $total_date);

            // Actualizar la cita
            cita::where('id', [$date_id])
                ->update(
                    [
                        'start' => Carbon::parse($date['date'] . ' ' . self::minutesToTime($startTimeToMinutes)),
                        'end' => Carbon::parse($date['date'] . ' ' . self::minutesToTime($endTimeToMinutes)),
                        'generated_points' => $total_rp,
                        'status' => $date['estado'] === 'Agendada' ? 'Agendada' : self::determineDateStatus($total_date - $discount, $details['payed']),
                        'customer_id' => $date['clienteId'],
                        'total' => $total_date,
                        'disccount' => $discount,
                    ]
                );
            return response()->json(['message' => 'Appointment updated successfully']);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function getTotalDiscounts($methods, $total)
    {
        try {
            $final = 0;
            foreach ($methods as $method) {
                $qty = $method->amount - ($method->change ?? 0);
                if ($method->payment_method_id !== 4) {
                    $total -= $qty;
                } else {
                    $discount = $method->tipo === 'Porcentaje' ? $total * ($qty / 100) : $qty;
                    $final += $discount;
                    $total -= $discount;
                }
            }
            return $final;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function determineDateStatus($total, $payed)
    {
        if ($payed >= $total) {
            return 'Pagada';
        } else {
            return 'Pendiente';
        }
    }
    private static function minutesToTime(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%02d:%02d', $hours, $mins);
    }
    private static function timeToMinutes($time)
    {
        try {
            [$hours, $minutes] = explode(':', $time);
            return ((int)$hours * 60) + (int)$minutes;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function calculateRewardPoints($item_id, $is_service, $total, $request, $customer_id = null)
    {
        try {
            $is_service = filter_var($is_service, FILTER_VALIDATE_BOOLEAN);
            // Obtener el item con eager loading de relaciones
            if ($is_service) {
                $item = servicio::with('excepciones', 'categorias.excepciones')
                    ->find($item_id);
            } else {
                $item = producto::with('excepciones', 'categorias.excepciones')
                    ->find($item_id);
            }

            // Verificar si el item existe
            if ($item) {
                // Buscar excepciones asociadas al item
                $excepcion = $item->excepciones()
                    ->whereNotNull('programa_recompensa_id')
                    ->latest()
                    ->first();
                if ($excepcion) {
                    return self::getRewardPoints($excepcion, $total);
                }
            }

            // Buscar categorías y excepciones asociadas a las categorías
            $categorias = $item->categorias();

            if ($categorias) {
                $excepcion = $categorias->whereHas('excepciones', function ($query) {
                    $query->whereNotNull('programa_recompensa_id');
                })
                    ->with(['excepciones' => function ($query) {
                        $query->whereNotNull('programa_recompensa_id')->latest();
                    }])
                    ->get()
                    ->pluck('excepciones')
                    ->flatten()
                    ->first();

                if ($excepcion) {
                    return self::getRewardPoints($excepcion, $total);
                }
            }

            // Buscar excepciones del cliente si se proporciona un ID de cliente
            if ($customer_id) {
                $customer = cliente::with('excepciones', 'categorias.excepciones')->find($customer_id);
                if ($customer) {
                    $customer_points = self::calculateCustomerPoints($total, $customer);
                    if ($customer_points !== 'no_exceptions') {
                        return $customer_points;
                    }
                }
            }

            // Default: regresar la excepcion global
            $recompensa_global = $request->user()->salon->recompensaGeneral()->first();
            if ($is_service) {
                $recompensa_global->type_comission = $recompensa_global->type_comission_s;
                $recompensa_global->qty = $recompensa_global->qty_s;
            } else {
                $recompensa_global->type_comission = $recompensa_global->type_comission_p;
                $recompensa_global->qty = $recompensa_global->qty_p;
            }

            if ($recompensa_global) {
                return self::getRewardPoints($recompensa_global, $total);
            }

            // Si no hay excepciones ni categorías, retornar 0
            return 0;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    private static function calculateCustomerPoints($total, $customer)
    {

        try {
            // Buscar excepciones asociadas al cliente
            $excepcion = $customer->excepciones()
                ->latest()
                ->first();

            if ($excepcion) {
                return self::getRewardPoints($excepcion, $total);
            }

            // Buscar categorías y excepciones asociadas a las categorías
            $categoria = $customer->categorias()->latest()->first();
            if ($categoria) {
                $excepcion = $categoria->excepciones()
                    ->whereNotNull('programa_recompensa_id')
                    ->latest()
                    ->first();

                if ($excepcion) {
                    return self::getRewardPoints($excepcion, $total);
                }
            }

            // Si no hay excepciones ni categorías, retornar 0
            return 'no_exceptions';
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    private static function getRewardPoints($excepcion, $total)
    {
        try {
            switch ($excepcion->type_comission) {
                case 'percent':
                    $points = ($total * $excepcion->qty) / 100;
                    break;
                case 'qty':
                    $points = $excepcion->qty;
                    break;
                default:
                    $points = 0;
                    break;
            }
            return $points;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    public static function updateAppointment(Request $request, $type, $isDate = true)
    {
        try {
            $continuousAppointments = collect();
            $isDate = filter_var($isDate, FILTER_VALIDATE_BOOLEAN);
            // Buscar la cita o el bloqueo según el tipo
            $appointment = $isDate ? asignacion_servicio::with(['date.details'])
                ->find($request->input('appointmentId')) : bloqueo::find($request->input('appointmentId'));

            // Verificar que la cita o bloqueo pertenezca al salón del usuario autenticado
            if (($isDate && $appointment->date->salon_id !== $request->user()->salon_id) || (!$isDate && $appointment->salon_id !== $request->user()->salon_id)) {
                return response()->json(['message' => 'No autorizado'], 403);
            }

            // Actualizar start (en ambos casos es el mismo proceso)
            $dateBase = Carbon::parse($appointment->start)->format('Y-m-d');
            $appointment->start = $dateBase . ' ' . $request->input('newStart');

            // Actualizar end o duration según el tipo
            if ($isDate) {
                $newDuration = self::calculateNewDuration($appointment, $dateBase . ' ' . $request->input('newEnd'));
            } else {
                $appointment->end = $dateBase . ' ' . $request->input('newEnd');
            }

            // Identificar citas continuas
            if ($isDate) $continuousAppointments = self::identifyContinuousAppointments($appointment, $request->input('mergeQuantity'));

            // Actualizar citas continuas si existen
            if ($isDate && !$continuousAppointments->isEmpty()) {
                // Calcular nueva duración por servicio
                $duration_per_service = round($newDuration / ($continuousAppointments->count() > 0 ? $continuousAppointments->count() : 1), 0, PHP_ROUND_HALF_DOWN);
                $new_start = Carbon::parse($appointment->start);

                // Actualizar la duración y start de cada cita continua
                foreach ($continuousAppointments as $appointment) {
                    $appointment->duration = $duration_per_service;
                    $appointment->start = $new_start;
                    $appointment->save();
                    $new_start->addMinutes($duration_per_service);
                }

                $appointment->duration = $duration_per_service;
                $appointment->save();
            }

            // Actualizar empleado si el tipo es 'employee' y es diferente al actual
            if ($type == 'employee') {
                if ($appointment->empleado_id !== $request->input('employee')) {
                    // Actualizar campos en común
                    $empleado = $request->input('employee');
                    $appointment->empleado_id = $empleado;
                    $appointment->color = empleado::select('color_preset')->find($empleado)->color_preset;
                    // Recalcular comisión si es una cita
                    if ($isDate) {
                        $itemToCalculatecomision = self::generateItemToCalculateComision($appointment, $empleado);
                        $comision = self::defineComisionService($itemToCalculatecomision, 'servicio');
                        $appointment->comission = $comision['balance'];
                        $appointment->type_comision_calculated = $comision['type'];
                    }
                    // Actualizar citas continuas si existen
                    if (!$continuousAppointments->isEmpty()) {
                        foreach ($continuousAppointments as $contApp) {
                            $contApp->empleado_id = $empleado;
                            $contApp->color = empleado::select('color_preset')->find($empleado)->color_preset;
                            // Recalcular comisión
                            $itemToCalculatecomision = self::generateItemToCalculateComision($contApp, $empleado);
                            $comision = self::defineComisionService($itemToCalculatecomision, 'servicio');
                            $contApp->comission = $comision['balance'];
                            $contApp->type_comision_calculated = $comision['type'];
                            $contApp->save();
                        }
                    }
                }
            }

            // Guardar el objeto y retornar respuesta en caso de ser bloqueo
            $appointment->save();
            if (!$isDate) {
                return response()->json(['message' => 'Appointment updated successfully']);
            }

            // Recalcular start y end de la cita
            $date = $appointment->date;
            $data = self::calculateStartEndDate($date);
            $date->start = $data['start'];
            $date->end = $data['end'];
            $date->save();

            return response()->json(['message' => 'Appointment updated successfully']);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    private static function identifyContinuousAppointments($detail, $grouped)
    {
        try {
            // Ordenamos los detalles por hora de inicio
            $details_sorted = $detail->date->details
                ->sortBy(fn($d) => [$d->empleado_id, $d->start])
                ->values();
            // Obtenemos el índice del detalle actual
            $index = $details_sorted->search(fn($d) => $d->id === $detail->id);

            if ($index === false) {
                return collect(); // Por seguridad, si no se encuentra
            }

            // Tomamos desde el actual hasta los siguientes $grouped elementos
            $remaining = $details_sorted->slice($index, intval($grouped + $index))->values();

            return $remaining;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    private static function generateItemToCalculateComision($appointment, $empleado)
    {
        $item['sid'] = $appointment->selected_service ?? $appointment['servicioId'];
        $item['vendedor'] = $empleado;
        $item['base_comision'] = boolval($appointment->base_comision ?? ($appointment['base_comision'] ?? 0));
        $item['disccount_price'] = floatval($appointment->disccount_price ?? $appointment['disccount_price']);
        $item['sale_price'] = floatval($appointment->current_price ?? $appointment['precio']);
        $item['total'] = self::determinatePriceOutOfDiscounts($appointment);
        return $item;
    }
    private static function priceOrDiscount($app)
    {
        if (isset($app->disccount_price) || isset($app['disccount_price'])) {
            if (($app->disccount_price ?? $app['disccount_price']) > 0) {
                return $app->disccount_price ?? $app['disccount_price'];
            }
        }

        return $app->current_price ?? ($app['precio'] ?? $app['current_price']);
        // return $app->disccount_price ?? $app['disccount_price'] > 0 ? $app->disccount_price ?? $app['disccount_price'] : ($app->current_price ?? ($app['precio'] ?? $app['current_price']));
    }
    public static function determinatePriceOutOfDiscounts($app)
    {
        try {
            $priceOrDiscount = self::priceOrDiscount($app);
            $qty = $app['quantity'] ?? 1;
            $discount_qty = $app['discount_qty'] ?? $app->discount_qty;
            $discount_type = $app->discount_type ?? $app['discount_type'];
            $finalPriceOutOfDiscounts = $discount_qty > 0 ? ($discount_type == 'Porcentaje' ? (floatval($priceOrDiscount - (($discount_qty / 100) * $priceOrDiscount)) * $qty) : floatval(($priceOrDiscount * $qty) - $discount_qty)) : floatval($priceOrDiscount * $qty);

            return $finalPriceOutOfDiscounts;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    private static function calculateNewDuration($appointment, $end)
    {
        try {
            $diff = Carbon::parse($end)->diffInMinutes($appointment->start);
            return $diff;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    private static function calculateStartEndDate($date)
    {
        try {
            // Ordenar los detalles por el campo 'start'
            $details = $date->details->sortBy('start');

            // Obtener el primer y último elemento después de ordenar
            $newStart = $details->first()->start;
            $lastDetail = $details->last();

            // Calcular 'newEnd' sumando la duración del último detalle al 'start' del último detalle
            $lastStart = Carbon::parse($lastDetail->start);
            $newEnd = $lastStart->addMinutes($lastDetail->duration)->format('Y-m-d H:i:s');

            return [
                'start' => $newStart,
                'end' => $newEnd
            ];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    public static function defineComisionService($item)
    {
        try {
            $empleado = Empleado::with('comision.excepcion_servicio', 'comision.excepcion_cat_servicio')->find($item['vendedor']);
            $balance = 0;
            $type = 'percent';

            if (!$empleado || !$empleado->comision) {
                return ['balance' => $balance, 'type' => $type];
            }

            $base_price = self::defineBasePrice($item);
            $comision = $empleado->comision;

            // Buscar excepciones específicas
            $excepcionServicio = $comision->excepcion_servicio->firstWhere('servicio_id', $item['sid']);
            $excepcionCategoria = null;

            $servicio = servicio::with('categorias')->find($item['sid']);
            if ($servicio && $servicio->categorias) {
                foreach ($servicio->categorias as $cat) {
                    $ex = $comision->excepcion_cat_servicio->firstWhere('categoria_servicio_id', $cat->id);
                    if ($ex) {
                        $excepcionCategoria = $ex;
                        break;
                    }
                }
            }

            // Valores por defecto
            $cant = $comision->qty_s;
            $type = $comision->type_comission_s;

            // Excepciones sobreescriben si existen
            if ($excepcionCategoria) {
                $cant = $excepcionCategoria->qty;
                $type = $excepcionCategoria->type_comission;
            }

            if ($excepcionServicio) {
                $cant = $excepcionServicio->qty;
                $type = $excepcionServicio->type_comission;
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
    public static function defineBasePrice($item)
    {
        try {
            if ($item['base_comision']) {
                return $item['total'];
            } else {
                return $item['disccount_price'] ? $item['disccount_price'] : $item['sale_price'];
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function deleteDate(Request $request)
    {
        try {
            $date_id = $request->input('date_id');
            $date = cita::with('files', 'metodosPago', 'propinas', 'details.materiales', 'etiquetas', 'details_product', 'abonos', 'abonoPropinas')->find($date_id);
            if (!$date) {
                return response()->json(['message' => 'not found']);
            }
            // if($type == 'cita'){
            // self::cancelarStock();
            self::cancelarPuntos($date);
            // $respaldoData = $this->respaldarInfo();
            // $this->recuperarMensajes();
            self::deleteRelations($date);
            // }
            // elseif($type == 'bloqueo'){
            //     bloqueo::find($this->asignacion_id)->delete();  
            //     $this->dispatchBrowserEvent('cerrarBlockMenuForm');
            // }
            return response()->json(['message' => 'ok']);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function deleteBlocking(Request $request)
    {
        try {
            $blocking_id = $request->input('blocking_id');
            $blocking = bloqueo::find($blocking_id);
            if (!$blocking) {
                return response()->json(['message' => 'not found']);
            }
            $blocking->delete();
            return response()->json(['message' => 'ok']);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    private static function deleteRelations($date)
    {
        if (isset($date->files)) {
            self::deleteFiles($date->files);
        }
        self::deleteItems($date->metodosPago);
        self::deleteItems($date->propinas);
        // self::deleteItems($date->mensajesEnviados);
        foreach ($date->details as $detail) {
            if (isset($detail->materiales)) {
                self::deleteItems($detail->materiales);
            }
        }
        $date->etiquetas()->detach();
        self::deleteItems($date->details);
        self::deleteItems($date->details_product);
        self::deleteItems($date->abonos);
        self::deleteItems($date->abonoPropinas);
        $date->delete();
    }

    private static function deleteItems($relation, $metodo = false)
    {
        try {
            foreach ($relation as $item) {
                $item->delete();
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    private static function deleteFiles($files)
    {
        try {
            foreach ($files as $file) {
                $filename = 'storage/citas/' . $file->file;
                unlink($filename);
                $file->delete();
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }

    private static function cancelarPuntos($date)
    {
        try {
            if (isset($date->customer->tarjetaPuntos)) {
                $tarjeta = $date->customer->tarjetaPuntos;
                $tarjeta->balance -= $date->generated_points;
                $tarjeta->save();
            }
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
