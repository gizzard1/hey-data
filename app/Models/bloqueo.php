<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bloqueo extends Model
{
    use HasFactory;
    protected $fillable = [
        'empleado_id',
        'salon_id',
        'description',
        'color',
        'start',
        'end',
    ];
    
    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }
    function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
