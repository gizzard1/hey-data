<?php

namespace App\Models;

use App\Models\categoria_servicio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class recompensas_servicio extends Model
{
    use HasFactory;
    protected $fillable=[
        'to_all',
        'percent',
        'salon_id'
    ];
    public function categorias()
    {
        return $this->belongsToMany(categoria_servicio::class,'recompensas_cat_servicios');
    }
    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }
}
