<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class metodo_pago_gasto extends Model
{
    use HasFactory;
    protected $fillable = ['Payment_method','reference','amount','gasto_id'];
    
    public function gasto()
    {
        return $this->belongsTo(gasto::class, 'gasto_id');
    }
}
