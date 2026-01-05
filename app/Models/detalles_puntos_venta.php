<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class detalles_puntos_venta extends Model
{
    use HasFactory;
    protected $fillable = ['cliente_id','venta_id'];
    protected $table = 'detalles_puntos_ventas';
}
