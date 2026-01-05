<?php

namespace App\Models;

use App\Models\categoria_producto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class excepcion_cat_producto extends Model
{
    use HasFactory;
    protected $fillable = [
        'qty',
        'type_comission',
        'categoria_producto_id',
        'programa_recompensa_id',
        'comision_id'
    ];
    function cat_producto()
    {
        return $this->belongsTo(categoria_producto::class,'categoria_producto_id');
    }
    function comision()
    {
        return $this->belongsTo(Comision::class);
    }
}
