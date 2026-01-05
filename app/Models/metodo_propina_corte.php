<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class metodo_propina_corte extends Model
{
    use HasFactory;
    protected $fillable = ['payment_method','qty','caja_corte_id','salon_id'];
    
    public function corte()
    {
        return $this->belongsTo(caja_corte::class,'caja_corte_id');
    }
}
