<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;
    protected $fillable = [
        'qty',
        'cost',
        'iva',
        'producto_id',
        'user_id',
        'salon_id',
        'marca_id',
        'description',
        'folio_fiscal',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function producto()
    {
        return $this->belongsTo(producto::class);
    }
    public function marca()
    {
        return $this->belongsTo(marca::class);
    }
    public function scopeTransactionsBetweenDates($query, $salon_id, $start, $end)
    {
        return $query->with('marca', 'producto', 'user')
            ->whereHas('user', function ($query) use ($salon_id) {
                $query->where('salon_id', $salon_id);
            })
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->updated_at;
            });
    }
}
