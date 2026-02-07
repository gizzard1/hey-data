<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $fillable = [
        'qty',
        'sale_price',
        'salon_id',
        'user_id',
        'asignacion_id',
        'producto_id',
        'empleado_id',
        'cliente_id',
    ];
    public function asignacion()
    {
        return $this->belongsTo(Asignacion_servicio::class, 'asignacion_id');
    }
    public function salon()
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }
    public function producto()
    {
        return $this->belongsTo(producto::class, 'producto_id');
    }
    public function customer()
    {
        return $this->belongsTo(cliente::class, 'cliente_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
    public function scopeTransactionsBetweenDates($query, $salon_id, $start, $end)
    {
        return $query->with('asignacion.date', 'producto', 'customer', 'user', 'empleado')
            ->whereHas('user', function ($query) use ($salon_id) {
                $query->where('salon_id', $salon_id);
            })
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->asignacion_id  ?? $item->created_at->format('Y-m-d H:i:s');
            });
    }
}
