<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class recompensas_cat_servicio extends Model
{
    use HasFactory;
    protected $fillable = ['programa_id','services_category_id'];
    protected $table = 'recompensas_cat_servicios';
}
