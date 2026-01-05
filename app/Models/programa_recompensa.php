<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class programa_recompensa extends Model
{
    use HasFactory;
    public function excepcion_producto()
    {
        return $this->hasMany(excepcion_producto::class);
    }
    public function excepcion_cat_producto()
    {
        return $this->hasMany(excepcion_cat_producto::class);
    }
    public function excepcion_cat_servicio()
    {
        return $this->hasMany(excepcion_cat_servicio::class);
    }
    public function excepcion_servicio()
    {
        return $this->hasMany(excepcion_servicio::class);
    }
    public function excepcion_cat_cliente()
    {
        return $this->hasMany(excepcion_cat_cliente::class);
    }
    public function excepcion_cliente()
    {
        return $this->hasMany(excepcion_cliente::class);
    }
    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }   
}
