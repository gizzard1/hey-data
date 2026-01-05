<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class metodo_pago extends Model
{
    use HasFactory;
    protected $fillable = [
        'Payment_method','salon_id'
    ];
    
    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }
    public function metodoServicios()
    {
        return $this->hasMany(metodo_pago_servicio::class,'payment_method_id');
    }
    public function metodoVentas()
    {
        return $this->hasMany(metodo_pago_venta::class,'payment_method_id');
    }
}