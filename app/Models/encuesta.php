<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class encuesta extends Model
{
    use HasFactory;
    protected $fillable = [
        'salon_id',
    ];
    function salon()
    {
        return $this->belongsTo(salon::class,'salon_id');
    }
    public function preguntas(){
        return $this->hasMany(pregunta::class);
    }
}
