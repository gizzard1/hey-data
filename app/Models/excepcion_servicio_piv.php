<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class excepcion_servicio_piv extends Model
{
    use HasFactory;
    protected $fillable =['comision_id','excepcion_servicio_id'];
    protected $table = 'excepcion_servicio_pivs';
}
