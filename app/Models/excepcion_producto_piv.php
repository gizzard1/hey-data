<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class excepcion_producto_piv extends Model
{
    use HasFactory;
    protected $fillable =['comision_id','excepcion_producto_id'];
    protected $table = 'excepcion_producto_pivs';
}
