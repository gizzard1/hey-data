<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $fillable = [
        'qty',
        'sale_price',
        'salon_id',
        'user_id',
        'asignacion_id',
        'producto_id',
        'empleado_id',
        'cliente_id',
    ];
    public function asignacion()
    {
        return $this->belongsTo(Asignacion_servicio::class,'asignacion_id');
    }
    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }
    public function producto()
    {
        return $this->belongsTo(producto::class,'producto_id');
    }
    public function customer()
    {
        return $this->belongsTo(cliente::class,'cliente_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    function empleado()
    {
        return $this->belongsTo(Empleado::class,'empleado_id');
    }

}
