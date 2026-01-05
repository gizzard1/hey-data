<?php

namespace App\Models;

use App\Models\Comision;
use App\Models\categoria_servicio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class excepcion_cat_servicio extends Model
{
    use HasFactory;
    protected $fillable = [
        'qty',
        'type_comission',
        'categoria_servicio_id',
        'programa_recompensa_id',
        'comision_id'
    ];
    
    function cat_servicio()
    {
        return $this->belongsTo(categoria_servicio::class,'categoria_servicio_id');
    }
    function comision()
    {
        return $this->belongsTo(Comision::class);
    }
}
