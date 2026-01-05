<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propina extends Model
{
    use HasFactory;
    protected $fillable = ['payment_method_id','reference','amount','venta_id','cita_id','empleado_id'];
    
    public function venta()
    {
        return $this->belongsTo(venta::class,'venta_id');
    }
    public function cita()
    {
        return $this->belongsTo(cita::class,'cita_id');
    }
    public function empleado()
    {
        return $this->belongsTo(empleado::class,'empleado_id');
    }
    public function metodoPago()
    {
        return $this->belongsTo(metodo_pago::class,'payment_method_id');
    }

}
