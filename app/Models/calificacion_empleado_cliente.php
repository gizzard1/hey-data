<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class calificacion_empleado_cliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'puntaje',
        'cliente_id',
        'user_id',
        'calificado'
    ];

    function user()
    {
        return $this->belongsTo(Empleado::class,'user_id');
    }
    function cliente()
    {
        return $this->belongsTo(cliente::class,'cliente_id');
    }
}
