<?php

namespace App\Models;

use App\Models\User;
use App\Models\cliente;
use App\Models\Asignacion_servicio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class cita extends Model
{
    use HasFactory;
    protected $fillable = [
        'start',
        'end',
        'date_status',
        'remember',
        'total',
        'disccount',
        'items',
        'status',
        'generated_points',
        'customer_id',
        'user_id',
        'reference',
        'payment_method',
        'salon_id',
    ];

    public function details()
    {
        return $this->hasMany(Asignacion_servicio::class, 'cita_id');
    }
    public function details_product()
    {
        return $this->hasMany(Asignacion_venta::class, 'cita_id');
    }
    public function propinas()
    {
        return $this->hasMany(Propina::class, 'cita_id');
    }
    public function abonos()
    {
        return $this->hasMany(abono::class, 'cita_id');
    }
    public function abonoPropinas()
    {
        return $this->hasMany(abonoPropina::class, 'cita_id');
    }
    public function customer()
    {
        return $this->belongsTo(cliente::class, 'customer_id');
    }
    public function etiquetas()
    {
        return $this->belongsToMany(etiquetas_cita::class, 'etiquetas_citas_pivs');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function metodosPago()
    {
        return $this->hasMany(metodo_pago_servicio::class, 'cita_id');
    }
    public function salon()
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }
    public function ventaProductos()
    {
        return $this->belongsToMany(Asignacion_venta::class, 'asignacion_venta_citas');
    }
    public function mensajesEnviados()
    {
        return $this->hasMany(walog::class, 'cita_id');
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
                if (file_exists('storage/citas/' . $file->file)) {
                    return "storage/citas/" . $file->file;
                } else {
                    return 'storage/Image-not-found.png';
                }
            });
        }
    }
    public function scopeTransactionsBetweenDates($query, $salon_id, $start, $end)
    {
        return $query->select('id', 'status', 'start', 'created_at', 'total', 'disccount', 'customer_id', 'salon_id', 'user_id')
            ->with([
                'user:id,name',
                'metodosPago:id,cita_id,payment_method_id,tipo,reference,amount,change,created_at',
                'details_product:id,selected_item,cita_id,quantity,disccount_price,discount_type,discount_qty,current_price',
                'details_product.product:id,name',
                'details:id,selected_service,cita_id,discount_qty,disccount_price,discount_type,current_price',
                'details.servicio:id,name',
                'customer' => function ($q) {
                    $q->select(
                        'id',
                        'first_name',
                        'last_name',
                        'phone',
                        DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as nombre"),
                        DB::raw("phone as telefono"),
                    );
                },
                'metodosPago.metodoPago' => function ($q) {
                    $q->select(
                        'id',
                        'Payment_method',
                        DB::raw("Payment_method as name"),
                    );
                }
            ])
            ->where('salon_id', $salon_id)
            ->whereBetween('start', [$start, $end])
            ->orderBy('start', 'desc');
    }
    public function scopeTransactionsBetweenDatesBetweenTotal($query, $salon_id, $start, $end, $minTotal, $maxTotal)
    {
        return $query->select('id', 'status', 'start', 'created_at', 'total', 'disccount', 'customer_id', 'salon_id', 'user_id')
            ->with([
                'user:id,name',
                'metodosPago:id,cita_id,payment_method_id,tipo,reference,amount,change,created_at',
                'details_product:id,selected_item,cita_id,quantity,disccount_price,discount_type,discount_qty,current_price',
                'details_product.product:id,name',
                'details:id,selected_service,cita_id,discount_qty,disccount_price,discount_type,current_price',
                'details.servicio:id,name',
                'customer' => function ($q) {
                    $q->select(
                        'id',
                        'first_name',
                        'last_name',
                        'phone',
                        DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as nombre"),
                        DB::raw("phone as telefono"),
                    );
                },
                'metodosPago.metodoPago' => function ($q) {
                    $q->select(
                        'id',
                        'Payment_method',
                        DB::raw("Payment_method as name"),
                    );
                }
            ])
            ->whereBetween('total', [$minTotal ?? 0, $maxTotal ?? PHP_INT_MAX])
            ->where('salon_id', $salon_id)
            ->whereBetween('start', [$start, $end])
            ->orderBy('start', 'desc');
    }
    public function scopeTransactionsBetweenDatesClosing($query, $salon_id, $start, $end)
    {
        return $query->where('salon_id', $salon_id)
            ->whereBetween('updated_at', [$start, $end])
            ->where(function ($query) {
                $query->where('status', 'Pagada')
                    ->orWhere('status', 'Pendiente');
            })
            ->with([
                'abonos',
                'details.servicio',
                'metodosPago.metodoPago',
                'propinas.metodoPago',
                'customer' => function ($q) {
                    $q->select(
                        'id',
                        'first_name',
                        'last_name',
                        'phone',
                        DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as nombre"),
                        DB::raw("phone as telefono"),
                    );
                }
            ]);
    }
}
