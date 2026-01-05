<?php

namespace App\Models;

use App\Models\User;
use App\Models\cliente;
use App\Models\Asignacion_servicio;
use App\Models\metodo_pago;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class cita extends Model
{
    use HasFactory;
    protected $fillable = [
        'start',
        'end',
        'date_status',
        'remember',
        'total',
        'disccount',
        'items',
        'status',
        'generated_points',
        'customer_id',
        'user_id',
        'reference',
        'payment_method',
        'salon_id',
    ];

    public function details()
    {
        return $this->hasMany(Asignacion_servicio::class,'cita_id');
    }
    public function details_product()
    {
        return $this->hasMany(Asignacion_venta::class,'cita_id');
    }
    public function propinas()
    {
        return $this->hasMany(Propina::class,'cita_id');
    }
    public function abonos()
    {
        return $this->hasMany(abono::class,'cita_id');
    }
    public function abonoPropinas()
    {
        return $this->hasMany(abonoPropina::class,'cita_id');
    }
    public function customer()
    {
        return $this->belongsTo(cliente::class,'customer_id');
    }
    public function etiquetas()
    {
        return $this->belongsToMany(etiquetas_cita::class,'etiquetas_citas_pivs');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function metodosPago()
    {
        return $this->hasMany(metodo_pago_servicio::class,'cita_id');
    }
    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }
    public function ventaProductos()
    {
        return $this->belongsToMany(Asignacion_venta::class,'asignacion_venta_citas');
    }    
    public function mensajesEnviados()
    {
        return $this->hasMany(walog::class,'cita_id');
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
                if(file_exists('storage/citas/' . $file->file)){
                    return "storage/citas/" . $file->file;
                }else{
                    return 'storage/Image-not-found.png';
                }
            });
        }
    }
}
