<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class caja_corte extends Model
{
    use HasFactory;
    protected $fillable = [
        'description',
        'total_bruto',
        'total_neto',
        'total_cash',
        'total_NF',
        'total_points',
        'tips',
        'comissions',
        'user_id',
        'ganancia',
        'total_ventas',
        'total_servicios',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    function apertura()
    {
        return $this->hasOne(caja_apertura::class, 'caja_apertura_id');
    }
    public function metodosZettle()
    {
        return $this->hasMany(metodo_pago_corte::class, 'caja_corte_id');
    }
    public function metodosZettlePropina()
    {
        return $this->hasMany(metodo_propina_corte::class, 'caja_corte_id');
    }
    public function scopeTransactionsBetweenDates($query, $salon_id, $start, $end)
    {
        return $query->with('user')
            ->whereHas('user', function ($query) use ($salon_id) {
                $query->where('salon_id', $salon_id);
            })
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function scopeTransactionsBetweenDatesBetweenTotal($query, $salon_id, $start, $end, $minTotal, $maxTotal)
    {
        return $query->with('user')
                ->whereBetween('ganancia', [$minTotal ?? 0, $maxTotal ?? PHP_INT_MAX])
            ->whereHas('user', function ($query) use ($salon_id) {
                $query->where('salon_id', $salon_id);
            })
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc');
    }
}
