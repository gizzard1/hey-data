<?php

namespace App\Models;

use App\Models\Empleado;
use App\Models\excepcion_producto;
use App\Models\excepcion_servicio;
use App\Models\excepcion_cat_producto;
use App\Models\excepcion_cat_servicio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comision extends Model
{
    use HasFactory;
    protected $fillable = [
        'empleado_id'
    ];
    function excepcion_producto()
    {
        return $this->hasMany(excepcion_producto::class);
    }
    function excepcion_cat_producto()
    {
        return $this->hasMany(excepcion_cat_producto::class);
    }
    function excepcion_cat_servicio()
    {
        return $this->hasMany(excepcion_cat_servicio::class);
    }
    function excepcion_servicio()
    {
        return $this->hasMany(excepcion_servicio::class);
    }
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }   
}
