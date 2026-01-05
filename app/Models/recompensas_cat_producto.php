<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recompensas_cat_producto extends Model
{
    use HasFactory;
    protected $fillable = ['recompensas_producto_id','categoria_productos_id'];
    protected $table = 'recompensas_cat_productos';
}
