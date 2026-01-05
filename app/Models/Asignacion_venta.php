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
        return $this->belongsTo(producto::class,'selected_item');
    }
    function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
    public function sale()
    {
        return $this->belongsTo(venta::class,'venta_id');
    }
    public function cita()
    {
        return $this->belongsTo(cita::class,'cita_id');
    }
    public function giftCard()
    {
        return $this->hasOne(coupon::class,'asignacion_venta_id');
    }
}
