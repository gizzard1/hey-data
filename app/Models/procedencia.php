<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class procedencia extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','salon_id'
    ];

    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }
    public function clientes()
    {
        return $this->hasMany(cliente::class,'procedencia_id');
    }    

}
