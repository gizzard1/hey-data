<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class metodo_pago_venta extends Model
{
    use HasFactory;
    protected $fillable = ['payment_method_id','reference','amount','venta_id','tipo'];
    
    public function ventas()
    {
        return $this->belongsTo(venta::class,'venta_id');
    }
    public function metodoPago()
    {
        return $this->belongsTo(metodo_pago::class,'payment_method_id');
    }
}
