<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class excepcion_cat_cliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'qty',
        'type_comission',
        'categoria_cliente_id',
        'programa_recompensa_id'
    ];
    public function cat_cliente()
    {
        return $this->belongsTo(categoria_cliente::class,'categoria_cliente_id');
    }   
    public function programa_recompensa()
    {
        return $this->belongsTo(programa_recompensa::class);
    }
}
