<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class caja_apertura extends Model
{
    use HasFactory;
    protected $fillable = [
        'caja_corte_id',
        'caja_chica'
    ];
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }   
    public function corteCaja()
    {
        return $this->belongsTo(caja_corte::class,'caja_corte_id');
    } 
}
