<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pregunta extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'encuesta_id',
    ];
    function respuestas()
    {
        return $this->hasMany(respuesta::class,'pregunta_id');
    }
    function encuesta()
    {
        return $this->belongsTo(encuesta::class,'encuesta_id');
    }
}
