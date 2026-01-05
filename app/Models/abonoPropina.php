<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class abonoPropina extends Model
{
    use HasFactory;
    protected $fillable = [
        'cita_id',
        'venta_id',
        'payed_qty',
        'payment_method_id',
    ];
    public function venta()
    {
        return $this->belongsTo(venta::class,'venta_id');
    }
    public function cita()
    {
        return $this->belongsTo(cita::class,'cita_id');
    }
    public function metodoPago()
    {
        return $this->belongsTo(metodo_pago::class,'payment_method_id');
    }
}
