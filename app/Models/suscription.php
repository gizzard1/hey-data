<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class suscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'period',
        'plan',
        'email',
        'gross_price',
        'salon_id',
    ];
    public function salon()
    {
        return $this->belongsTo(Salon::class,'salon_id');
    }

}
