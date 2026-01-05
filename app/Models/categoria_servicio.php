<?php

namespace App\Models;

use App\Models\servicio;
use App\Models\recompensas_servicio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class categoria_servicio extends Model
{
    use HasFactory;
    protected $fillable = ['name','salon_id'];

    public function servicio()
    {
        return $this->belongsToMany(servicio::class,'categoria_servicios_pivs');
    }
    
    function excepciones()
    {
        return $this->hasMany(excepcion_cat_servicio::class);
    }
    public function RecompensasServicio()
    {
        return $this->belongsToMany(recompensas_servicio::class,'recompensas_cat_servicios');
    }
    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }
}
