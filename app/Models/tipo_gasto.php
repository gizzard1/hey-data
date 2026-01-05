<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipo_gasto extends Model
{
    protected $fillable = ['name'];

    public function gastos()
    {
        return $this->hasMany(gasto::class,'tipo_id');
    }
    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }
}
