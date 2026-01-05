<?php

namespace App\Models;

use App\Models\producto;
use App\Models\excepcion_cat_producto;
use App\Models\recompensas_producto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class categoria_producto extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'platform_id'];
    

    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }
    public function image() 
    {
        return $this->morphOne(Image::class, 'model');
    }

    //accessors
    public function getPictureAttribute()
    {
        $img = $this->image;

        if($img != null){
            if(file_exists('storage/categoria_productos/' . $img->file))
                return 'storage/categoria_productos/' . $img->file;
            else
                return 'storage/Image-not-found.png'; 
        }
        
        return 'storage/no-image.jpg';
    }

    function excepciones()
    {
        return $this->hasMany(excepcion_cat_producto::class);
    }
    public function productos()
    {
        return $this->belongsToMany(Producto::class,'categoria_productos_pivs');
    }
    public function RecompensasProducto()
    {
        return $this->belongsToMany(recompensas_producto::class,'recompensas_cat_productos');
    }
    
}
