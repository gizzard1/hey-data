<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignacion_venta_cita extends Model
{
    use HasFactory;
    protected $fillable = ['asignacion_venta_id','cita_id'];
    protected $table = 'asignacion_venta_citas';

}
