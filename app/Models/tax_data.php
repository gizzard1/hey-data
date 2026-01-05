<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tax_data extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'tax_system',
        'rfc',
        'postcode',
        'email',
        'phone',
    ];

    public function cliente()
    {
        return $this->belongsTo(cliente::class);
    }
    public function ventas()
    {
        return $this->hasMany(venta::class);
    }
    public function citas()
    {
        return $this->hasMany(cita::class);
    }
    
}
