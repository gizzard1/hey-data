<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class coupon extends Model
{
    use HasFactory;
    protected $fillable = [
        'value_amount',
        'expires_at',
        'acumulable',
        'password',
        'asignacion_venta_id',
    ];
    function asignaciones()
    {
        return $this->belongsTo(Asignacion_venta::class,'asignacion_venta_id');
    }
    function canjeosProductos()
    {
        return $this->hasMany(venta::class,'selected_item');
    }
    function canjeosServicios()
    {
        return $this->hasMany(cita::class,'selected_item');
    }
}
