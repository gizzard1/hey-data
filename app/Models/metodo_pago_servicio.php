<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class metodo_pago_servicio extends Model
{
    use HasFactory;
    protected $fillable = ['payment_method_id', 'reference', 'amount', 'cita_id', 'tipo'];

    public function cita()
    {
        return $this->belongsTo(cita::class, 'cita_id');
    }
    public function metodoPago()
    {
        return $this->belongsTo(metodo_pago::class, 'payment_method_id');
    }
    public function scopeReportBetweenDates($query, $citaIds, $start, $end)
    {
        return $query->select('cita_id', 'amount', 'payment_method_id', 'change', 'created_at')
            ->with([
                'metodoPago' => function ($q) {
                    $q->select('Payment_method', 'id');
                },
            ])
            ->whereBetween('created_at', [$start, $end])
            ->whereIn('cita_id', $citaIds);
    }
}
