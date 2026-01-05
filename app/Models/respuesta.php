<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class respuesta extends Model
{
    use HasFactory;
    protected $fillable = [
        'eleccion',
        'cliente_id',
        'pregunta_id',
    ];
    function cliente()
    {
        return $this->belongsTo(cliente::class,'cliente_id');
    }
    function pregunta()
    {
        return $this->belongsTo(pregunta::class,'pregunta_id');
    }
}
