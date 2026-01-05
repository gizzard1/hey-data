<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class marca extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','phone_number','contact_name','email','salon_id'
    ];

    protected $table = 'marcas';
    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }

    public function products()
    {
        return $this->hasMany(Producto::class,'brand_id');
    }
    public function compras()
    {
        return $this->hasMany(Entrada::class);
    }
    public function gastos()
    {
        return $this->hasMany(gasto::class);
    }

}
