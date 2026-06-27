<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asignacion_venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'current_price',
        'comission',
        'iva',
        'venta_id',
        'cita_id',
        'selected_item',
        'empleado_id',
        'generated_points',
        'disccount_price',
        'discount_qty',
        'discount_type',
        'type_comision_calculated',
        'base_comision'
    ];
    function product()
    {
        return $this->belongsTo(producto::class, 'selected_item');
    }
    function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
    public function sale()
    {
        return $this->belongsTo(venta::class, 'venta_id');
    }
    public function cita()
    {
        return $this->belongsTo(cita::class, 'cita_id');
    }
    public function giftCard()
    {
        return $this->hasOne(coupon::class, 'asignacion_venta_id');
    }
    public function scopeReportOnAppointmentBetweenDates($query, $citaIds, $start, $end)
    {
        return $query->select('cita_id', 'empleado_id', 'disccount_price', 'iva', 'current_price', 'created_at')
            ->whereIn('cita_id', $citaIds)
            ->whereBetween('created_at', [$start, $end]);
    }
    public function scopeReportBetweenDates($query, $ventaIds, $start, $end)
    {
        return $query->select('venta_id', 'empleado_id', 'disccount_price', 'iva', 'current_price', 'created_at')
            ->with([
                'sale' => function ($q) {
                    $q->select('customer_id', 'id');
                }
            ])
            ->whereIn('venta_id', $ventaIds)
            ->whereBetween('created_at', [$start, $end]);
    }
}
