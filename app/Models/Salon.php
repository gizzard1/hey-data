<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salon extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone',
        'email',
        'webPage',
        'facebook',
        'instagram',
        'tiktok',
        'youtube',
        'start',
        'end',
        'simulador',

    ];
    public function recompensaGeneral()
    {
        return $this->hasOne(programa_recompensa::class);
    }
    public function users(){
        return $this->hasMany(User::class);
    }
    public function encuestas(){
        return $this->hasMany(encuesta::class);
    }
    public function walogs(){
        return $this->hasMany(walog::class);
    }
    public function recompensasServicios(){
        return $this->hasMany(recompensas_servicio::class);
    }
    public function categoriaServicios(){
        return $this->hasMany(categoria_servicio::class);
    }
    public function categoriaGastos(){
        return $this->hasMany(categoria_gasto::class);
    }
    public function tipoGastos(){
        return $this->hasMany(tipo_gasto::class);
    }
    public function etiquetas(){
        return $this->hasMany(etiquetas_cita::class);
    }
    public function citas(){
        return $this->hasMany(cita::class);
    }
    public function servicios(){
        return $this->hasMany(servicio::class);
    }
    public function gastos(){
        return $this->hasMany(gasto::class);
    }
    public function categoriasClientes(){
        return $this->hasMany(categoria_cliente::class);
    }
    public function materiales(){
        return $this->hasMany(material::class);
    }
    public function empleados(){
        return $this->hasMany(Empleado::class);
    }
    public function clientes(){
        return $this->hasMany(cliente::class);
    }
    public function ventas(){
        return $this->hasMany(venta::class);
    }
    public function categoriasProductos(){
        return $this->hasMany(categoria_producto::class);
    }
    public function productos(){
        return $this->hasMany(producto::class);
    }
    public function marcas(){
        return $this->hasMany(marca::class);
    }
    public function recompensasProductos(){
        return $this->hasMany(recompensas_producto::class);
    }
    public function metodosPago(){
        return $this->hasMany(metodo_pago::class);
    }
    public function procedencias(){
        return $this->hasMany(procedencia::class);
    }
    public function suscripciones(){
        return $this->hasMany(suscription::class);
    }

    public function getPictureAttribute()
    {
        $img = $this->file;

        if($img != null){
            if(file_exists('storage/salon/' . $img->file))
                return 'storage/salon/' . $img->file;
            else
                return 'storage/Image-not-found.png'; 
        }
    }
    public function file()
    {
        return $this->morphOne(File::class, 'model');
    }

    
}
