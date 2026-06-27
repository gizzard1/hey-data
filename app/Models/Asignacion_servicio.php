<?php

namespace App\Models;

use App\Models\cita;
use App\Models\Empleado;
use App\Models\producto;
use App\Models\servicio;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asignacion_servicio extends Model
{
    use HasFactory;
    protected $fillable = [
        'selected_service',
        'cita_id',
        'empleado_id',
        'disccount_percent',
        'generated_points',
        'current_price',
        'disccount_price',
        'comission',
        'iva',
        'start',
        'selected',
        'duration',
        'type_comision_calculated',
        'base_comision',
        'discount_qty',
        'discount_type',
        'color'
    ];
    function servicio()
    {
        return $this->belongsTo(servicio::class, 'selected_service');
    }
    function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
    function date()
    {
        return $this->belongsTo(cita::class, 'cita_id');
    }
    function materiales()
    {
        return $this->hasMany(Material::class, 'asignacion_id');
    }
    public function scopeReportBetweenDates($query, $citaIds, $start, $end)
    {
        return $query->select('cita_id', 'empleado_id', 'disccount_price', 'iva', 'current_price', 'start')
            ->with([
                'date' => function ($q) {
                    $q->select('customer_id', 'id');
                }
            ])
            ->whereIn('cita_id', $citaIds)
            ->whereBetween('start', [$start, $end]);
    }
}
