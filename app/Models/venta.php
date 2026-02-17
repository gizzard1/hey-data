<?php

namespace App\Models;

use App\Models\User;
use App\Models\cliente;
use App\Models\Asignacion_venta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class venta extends Model
{
    use HasFactory;
    protected $fillable = [
        'total',
        'disccount',
        'items',
        'status',
        'customer_id',
        'user_id',
        'reference',
        'payment_method',
        'salon_id',
        'generated_points'
    ];

    public function salon()
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }
    function details()
    {
        return $this->hasMany(Asignacion_venta::class, 'venta_id');
    }
    function propinas()
    {
        return $this->hasMany(Propina::class, 'venta_id');
    }
    function customer()
    {
        return $this->belongsTo(cliente::class, 'customer_id');
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }
    public function metodosPago()
    {
        return $this->hasMany(metodo_pago_venta::class, 'venta_id');
    }
    function mensajesEnviados()
    {
        return $this->hasMany(walog::class, 'venta_id');
    }
    public function abonos()
    {
        return $this->hasMany(abono::class, 'venta_id');
    }
    public function abonoPropinas()
    {
        return $this->hasMany(abonoPropina::class, 'venta_id');
    }
    public function scopeTransactionsBetweenDates($query, $salon_id, $start, $end)
    {
        return $query->select('id', 'status', 'created_at', 'total', 'disccount','customer_id', 'salon_id', 'user_id')
            ->where('salon_id', $salon_id)
            ->with('user:id,name', 'customer:id,first_name,last_name', 'metodosPago:id,venta_id,payment_method_id,tipo,reference,amount,change,created_at', 'metodosPago.metodoPago:id,Payment_method','details:id,selected_item,venta_id,quantity,disccount_price,discount_type,discount_qty,current_price','details.product:id,name')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc');
    }
    public function scopeTransactionsBetweenDatesBetweenTotal($query, $salon_id, $start, $end, $minTotal, $maxTotal)
    {
        return $query->select('id', 'status', 'created_at', 'total', 'disccount','customer_id', 'salon_id', 'user_id')
            ->where('salon_id', $salon_id)
            ->whereBetween('total', [$minTotal ?? 0, $maxTotal ?? PHP_INT_MAX])
            ->with('user:id,name', 'customer:id,first_name,last_name', 'metodosPago:id,venta_id,payment_method_id,tipo,reference,amount,change,created_at', 'metodosPago.metodoPago:id,Payment_method','details:id,selected_item,venta_id,quantity,disccount_price,discount_type,discount_qty,current_price','details.product:id,name')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc');
    }
}
