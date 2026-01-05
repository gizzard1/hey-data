<?php

namespace App\Models;

use App\Models\servicio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class excepcion_servicio extends Model
{
    use HasFactory;
    protected $fillable = [
        'qty',
        'type_comission',
        'servicio_id',
        'programa_recompensa_id',
        'comision_id'
    ];
    function comision()
    {
        return $this->belongsTo(Comision::class);
    }
    function servicio()
    {
        return $this->belongsTo(servicio::class);
    }
}
