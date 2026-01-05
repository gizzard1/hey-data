<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class excepcion_cat_servicio_piv extends Model
{
    use HasFactory;
    protected $fillable =['comision_id','excepcion_cat_servicio_id'];
    protected $table = 'excepcion_cat_servicio_pivs';
}
