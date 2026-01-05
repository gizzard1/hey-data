<?php

namespace App\Models;

use App\Models\marca;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'type_product',
        'status',
        'visibility',
        'gross_price',
        'disccount_price',
        'cost',
        'stock_status',
        'manage_stock',
        'stock_qty',
        'min_stock',
        'brand_id',
        'platform_id',
        'salon_id',
        'iva',
        'unit_type'
    ];
    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }
    function marca()
    {
        return $this->belongsTo(marca::class,'brand_id');
    }

    function excepciones()
    {
        return $this->hasMany(excepcion_producto::class);
    }
    function asignaciones()
    {
        return $this->hasMany(Asignacion_venta::class,'selected_item');
    }
    function entradas()
    {
        return $this->hasMany(Entrada::class);
    }
    function salidas()
    {
        return $this->hasMany(Material::class);
    }

    public function categorias()
    {
        return $this->belongsToMany(categoria_producto::class,'categoria_productos_pivs');
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
                if(file_exists('storage/productos/' . $file->file)){
                    return "storage/productos/" . $file->file;
                }else{
                    return 'storage/Image-not-found.png';
                }
            });
        }
    }

}
