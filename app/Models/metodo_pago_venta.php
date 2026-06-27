<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class metodo_pago_venta extends Model
{
    use HasFactory;
    protected $fillable = ['payment_method_id', 'reference', 'amount', 'venta_id', 'tipo'];

    public function ventas()
    {
        return $this->belongsTo(venta::class, 'venta_id');
    }
    public function metodoPago()
    {
        return $this->belongsTo(metodo_pago::class, 'payment_method_id');
    }
    public function scopeReportBetweenDates($query, $ventaIds, $start, $end)
    {
        return $query->select('venta_id', 'amount', 'payment_method_id', 'change', 'created_at')
            ->with([
                'metodoPago' => function ($q) {
                    $q->select('Payment_method', 'id');
                },
            ])
            ->whereIn('venta_id', $ventaIds)
            ->whereBetween('created_at', [$start, $end]);
    }
}
