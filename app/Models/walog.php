<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class walog extends Model
{
    use HasFactory;
    protected $fillable = ['uid','sent','answered','cita_id','venta_id','type'];

    
    public function date()
    {
        return $this->belongsTo(cita::class,'cita_id');
    }
    public function sale()
    {
        return $this->belongsTo(venta::class,'venta_id');
    }
}
