<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class calificacion_cliente_empleado extends Model
{
    use HasFactory;
    protected $fillable = [
        'puntaje',
        'cliente_id',
        'empleado_id',
        'comentario'
    ];

    function cliente()
    {
        return $this->belongsTo(cliente::class,'cliente_id');
    }
}
