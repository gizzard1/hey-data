<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categoria_productos_piv extends Model
{
    use HasFactory;
    protected $fillable = ['categoria_producto_id','producto_id'];
    protected $table = 'categoria_productos_pivs';
}
