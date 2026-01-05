<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;
    protected $fillable = [
        'qty',
        'cost',
        'iva',
        'producto_id',
        'user_id',
        'salon_id',
        'marca_id',
        'description',
        'folio_fiscal',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }   
    public function producto()
    {
        return $this->belongsTo(producto::class);
    }
    public function marca()
    {
        return $this->belongsTo(marca::class);
    }
}
