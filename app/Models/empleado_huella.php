<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class empleado_huella extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'empleado_id',
        'pair_type',
        'average',
    ];
    function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
