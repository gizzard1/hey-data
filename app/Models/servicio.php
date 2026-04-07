<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class servicio extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'iva',
        'gross_price',
        'disccount_price',
        'duration',
        'brand_id',
        'reward_points',
        'salon_id'
    ];
    public function categorias()
    {
        return $this->belongsToMany(categoria_servicio::class,'categoria_servicios_pivs');
    }
    function marca()
    {
        return $this->belongsTo(marca::class,'brand_id');
    }

    function excepciones()
    {
        return $this->hasMany(excepcion_servicio::class);
    }
    function asignaciones()
    {
        return $this->hasMany(Asignacion_servicio::class,'selected_service');
    }
    public function ventas()
    {
        return $this->hasManyThrough(Asignacion_servicio::class, Cita::class)
                    ->whereIn('citas.status', ['Pagada', 'Pendiente']);
    }
    public function ventasUnicas()
    {
        return $this->hasMany(Asignacion_servicio::class, 'selected_service')
            ->whereHas('date', function ($query) {
                $query->whereIn('status', ['Pagada', 'Pendiente']);
            })
            ->select('cita_id', 'selected_service')
            ->groupBy('cita_id', 'selected_service'); // Agrupar por cita y servicio
    }


    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }

    
    public function files()
    {
        return $this->morphMany(File::class,'model');
    }

    public function latestImage()
    {
        //recent file
        return $this->morphOne(File::class,'model')->latestOfMany();
    }
    public function getPhotosAttribute()
    {
        if (count($this->files)) {
            return $this->files->map(function ($file) {
                if(file_exists('storage/servicios/' . $file->file)){
                    return "storage/servicios/" . $file->file;
                }else{
                    return 'storage/Image-not-found.png';
                }
            });
        }
    }
    public function scopeBasicQuery()
    {
        return servicio::where('salon_id', Auth()->user()->salon->id)
            ->where('visibility', 'visible')
            ->where('name', '!=', 'Servicio eliminado');
    }
}
