<?php

namespace App\Models;

use App\Models\Comision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empleado extends Model
{

    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'birth_date',
        'is_active',
        'salon_id',
        'user_id',
        'color_preset'
    ];

    function propinas()
    {
        return $this->hasMany(Propina::class,'empleado_id');
    }
    function patrones()
    {
        return $this->hasMany(empleado_huella::class,'empleado_id');
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }    
    function comision()
    {
        return $this->hasOne(Comision::class);
    }
    public function asignacionesServicios()
    {
        return $this->hasMany(Asignacion_servicio::class, 'empleado_id');
    }
    public function asignacionesProductos()
    {
        return $this->hasMany(Asignacion_venta::class, 'empleado_id');
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
                if(file_exists('storage/empleados/' . $this->salon_id . '/' . $file->file)){
                    return "storage/empleados" . $this->salon_id . $file->file;
                }else{
                    return 'storage/Image-not-found.png';
                }
            });
        }
    }
}
