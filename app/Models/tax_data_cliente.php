<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tax_data_cliente extends Model
{
    use HasFactory;
    protected $fillable = ['tax_data_id','cliente_id'];
    protected $table = 'tax_data_cliente';
}
