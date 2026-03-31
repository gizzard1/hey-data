<?php

namespace App\Models;

use App\Models\Comision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Empleado extends Model
{

    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'birth_date',
        'is_active',
        'salon_id',
        'user_id',
        'color_preset'
    ];

    function propinas()
    {
        return $this->hasMany(Propina::class, 'empleado_id');
    }
    function patrones()
    {
        return $this->hasMany(empleado_huella::class, 'empleado_id');
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    function comision()
    {
        return $this->hasOne(Comision::class, 'empleado_id');
    }
    public function asignacionesServicios()
    {
        return $this->hasMany(Asignacion_servicio::class, 'empleado_id');
    }
    public function asignacionesProductos()
    {
        return $this->hasMany(Asignacion_venta::class, 'empleado_id');
    }
    public function files()
    {
        return $this->morphMany(File::class, 'model');
    }

    public function latestImage()
    {
        //recent file
        return $this->morphOne(File::class, 'model')->latestOfMany();
    }

    public function getPhotosAttribute()
    {
        if (count($this->files)) {
            return $this->files->map(function ($file) {
                if (file_exists('storage/empleados/' . $this->salon_id . '/' . $file->file)) {
                    return "storage/empleados" . $this->salon_id . $file->file;
                } else {
                    return 'storage/Image-not-found.png';
                }
            });
        }
    }
    // Métodos de instancia para obtener ingresos entre fechas
    public function salesIncomesBetweenDates($start, $end)
    {
        return $this->asignacionesProductos()
            ->select(
                'id',
                'comission',
                'selected_item',
                'cita_id',
                'venta_id',
                'disccount_price',
                'current_price',
                'discount_qty',
                'discount_type',
                'empleado_id',
                'created_at',
                DB::raw("
                    CASE
                        WHEN discount_qty IS NOT NULL AND discount_qty > 0 THEN
                            CASE
                                WHEN discount_type = 'Porcentaje' THEN
                                    (
                                        CASE
                                            WHEN disccount_price IS NOT NULL AND disccount_price > 0
                                                THEN disccount_price
                                                ELSE current_price
                                        END
                                    )
                                    - (
                                        (
                                            CASE
                                                WHEN disccount_price IS NOT NULL AND disccount_price > 0
                                                    THEN disccount_price
                                                    ELSE current_price
                                            END
                                        ) * (discount_qty / 100)
                                    )
                                ELSE
                                    (
                                        CASE
                                            WHEN disccount_price IS NOT NULL AND disccount_price > 0
                                                THEN disccount_price
                                                ELSE current_price
                                        END
                                    )
                                    - discount_qty
                            END
                        ELSE
                            (
                                CASE
                                    WHEN disccount_price IS NOT NULL AND disccount_price > 0
                                        THEN disccount_price
                                        ELSE current_price
                                END
                            )
                    END AS final_price
                ")
            )
            ->with([
                'cita' => function ($q) {
                    $q->select('id', 'customer_id')->with([
                        'customer' => function ($q) {
                            $q->select(
                                'id',
                                DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as nombre"),
                                'first_name',
                                'last_name'
                            );
                        }
                    ]);
                },
                'sale' => function ($q) {
                    $q->select('id', 'customer_id')->with([
                        'customer' => function ($q) {
                            $q->select(
                                'id',
                                DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as nombre"),
                                'first_name',
                                'last_name'
                            );
                        }
                    ]);
                },
                'product:id,name',
            ])
            ->whereBetween('created_at', [$start, $end])
            ->where('comission', '>', 0)
            ->orderBy('created_at', 'desc');
    }

    public function servicesIncomesBetweenDates($start, $end)
    {
        return $this->asignacionesServicios()
            ->select(
                'id',
                'comission',
                'selected_service',
                'cita_id',
                'disccount_price',
                'current_price',
                'discount_qty',
                'discount_type',
                'empleado_id',
                'created_at',
                DB::raw("
                    CASE
                        WHEN discount_qty IS NOT NULL AND discount_qty > 0 THEN
                            CASE
                                WHEN discount_type = 'Porcentaje' THEN
                                    (
                                        CASE
                                            WHEN disccount_price IS NOT NULL AND disccount_price > 0
                                                THEN disccount_price
                                                ELSE current_price
                                        END
                                    )
                                    - (
                                        (
                                            CASE
                                                WHEN disccount_price IS NOT NULL AND disccount_price > 0
                                                    THEN disccount_price
                                                    ELSE current_price
                                            END
                                        ) * (discount_qty / 100)
                                    )
                                ELSE
                                    (
                                        CASE
                                            WHEN disccount_price IS NOT NULL AND disccount_price > 0
                                                THEN disccount_price
                                                ELSE current_price
                                        END
                                    )
                                    - discount_qty
                            END
                        ELSE
                            (
                                CASE
                                    WHEN disccount_price IS NOT NULL AND disccount_price > 0
                                        THEN disccount_price
                                        ELSE current_price
                                END
                            )
                    END AS final_price
                ")
            )
            ->with([
                'date' => function ($q) {
                    $q->select(
                        'id',
                        'customer_id',
                    )->with([
                        'customer' => function ($q) {
                            $q->select(
                                'id',
                                DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as nombre"),
                                'first_name',
                                'last_name'
                            );
                        }
                    ]);
                },
                'servicio:id,name',
            ])
            ->whereBetween('start', [$start, $end])
            ->where('comission', '>', 0)
            ->orderBy('start', 'desc');
    }

    public function tipsIncomesBetweenDates($start, $end)
    {
        return $this->propinas()
            ->select(
                'id',
                'amount',
                'cita_id',
                'venta_id',
                'created_at',
                'payment_method_id',
            )
            ->with([
                'cita' => function ($q) {
                    $q->select(
                        'id',
                        'customer_id',
                        'total',
                        'disccount',
                        DB::raw('total - COALESCE(`disccount`, 0) as final_price'),
                    )->with([
                        'customer' => function ($q) {
                            $q->select(
                                'id',
                                DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as nombre"),
                                'first_name',
                                'last_name'
                            );
                        }
                    ]);
                },
                'venta' => function ($q) {
                    $q->select(
                        'id',
                        'customer_id',
                        'total',
                        'disccount',
                        DB::raw('total - COALESCE(`disccount`, 0) as final_price'),
                    )->with([
                        'customer' => function ($q) {
                            $q->select(
                                'id',
                                DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as nombre"),
                                'first_name',
                                'last_name'
                            );
                        }
                    ]);
                },
                'metodoPago:id,Payment_method'
            ])
            ->whereBetween('created_at', [$start, $end])
            ->where('amount', '>', 0)
            ->orderBy('created_at', 'desc');
    }
}
