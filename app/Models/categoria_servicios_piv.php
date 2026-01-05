<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoria_servicios_piv extends Model
{
    use HasFactory;
    protected $fillable = ['comision_id','producto_id'];
    protected $table = 'categoria_servicios_pivs';

}
