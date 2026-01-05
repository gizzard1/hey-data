<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoria_gastos_piv extends Model
{
    protected $fillable = ['categoria_gasto_id','gasto_id'];
    protected $table = 'categoria_gastos_pivs';
}
