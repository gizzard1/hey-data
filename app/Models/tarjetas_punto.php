<?php

namespace App\Models;

use App\Models\cliente;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class tarjetas_punto extends Model
{
    protected $fillable = [ 'intern_barcode','balance','cliente_id'];
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(cliente::class,'cliente_id');
    }   
    use HasFactory;
}
