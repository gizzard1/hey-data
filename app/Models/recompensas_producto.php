<?php

namespace App\Models;

use App\Models\categoria_producto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class recompensas_producto extends Model
{
    use HasFactory;
    protected $fillable = [
        'to_all',
        'percent',
        'salon_id'
    ];
    public function categorias()
    {
        return $this->belongsToMany(categoria_producto::class,'recompensas_cat_productos');
    }
    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }
}
