<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class excepcion_cliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'qty',
        'type_comission',
        'cliente_id',
        'programa_recompensa_id'
    ];
    public function cliente()
    {
        return $this->belongsTo(cliente::class,'cliente_id');
    }   
    public function programa_recompensa()
    {
        return $this->belongsTo(programa_recompensa::class);
    }
}
