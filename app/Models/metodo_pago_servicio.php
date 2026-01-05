<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class metodo_pago_servicio extends Model
{
    use HasFactory;
    protected $fillable = ['payment_method_id','reference','amount','cita_id','tipo'];
    
    public function cita()
    {
        return $this->belongsTo(cita::class,'cita_id');
    }
    public function metodoPago()
    {
        return $this->belongsTo(metodo_pago::class,'payment_method_id');
    }
}
