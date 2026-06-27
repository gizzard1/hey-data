<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propina extends Model
{
    use HasFactory;
    protected $fillable = ['payment_method_id', 'reference', 'amount', 'venta_id', 'cita_id', 'empleado_id'];

    public function venta()
    {
        return $this->belongsTo(venta::class, 'venta_id');
    }
    public function cita()
    {
        return $this->belongsTo(cita::class, 'cita_id');
    }
    public function empleado()
    {
        return $this->belongsTo(empleado::class, 'empleado_id');
    }
    public function metodoPago()
    {
        return $this->belongsTo(metodo_pago::class, 'payment_method_id');
    }
    public function scopeReportAppointmentsBetweenDates($query, $citaIds, $start, $end)
    {
        return $query->select('venta_id', 'cita_id', 'amount', 'payment_method_id', 'created_at')
            ->with([
                'metodoPago' => function ($q) {
                    $q->select('Payment_method', 'id');
                },
            ])
            ->whereIn('cita_id', $citaIds ?? [])
            ->whereBetween('created_at', [$start, $end]);
    }
    public function scopeReportSalesBetweenDates($query, $ventaIds, $start, $end)
    {
        return $query->select('venta_id', 'cita_id', 'amount', 'payment_method_id', 'created_at')
            ->with([
                'metodoPago' => function ($q) {
                    $q->select('Payment_method', 'id');
                },
            ])
            ->whereIn('venta_id', $ventaIds ?? [])
            ->whereBetween('created_at', [$start, $end]);
    }
}
