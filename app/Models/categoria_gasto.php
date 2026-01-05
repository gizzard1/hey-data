<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoria_gasto extends Model
{
    protected $fillable = ['name'];

    public function gastos()
    {
        return $this->hasMany(gasto::class,'categoria_id');
    }
    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }
}