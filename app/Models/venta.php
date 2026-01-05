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
        return $this->belongsTo(Salon::class,'salon_id');
    }
    function details()
    {
        return $this->hasMany(Asignacion_venta::class,'venta_id');
    }
    function propinas()
    {
        return $this->hasMany(Propina::class,'venta_id');
    }
    function customer()
    {
        return $this->belongsTo(cliente::class,'customer_id');
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }
    public function metodosPago()
    {
        return $this->hasMany(metodo_pago_venta::class,'venta_id');
    }    
    function mensajesEnviados()
    {
        return $this->hasMany(walog::class,'venta_id');
    }
    public function abonos()
    {
        return $this->hasMany(abono::class,'venta_id');
    }
    public function abonoPropinas()
    {
        return $this->hasMany(abonoPropina::class,'venta_id');
    }

}
