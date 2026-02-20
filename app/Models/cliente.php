<?php

namespace App\Models;

use App\Models\categoria_cliente;
use App\Models\tarjetas_punto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class cliente extends Model
{
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email', 'phone', 'birth_date', 'description', 'want_custom_messages', 'want_offers', 'sexo', 'postcode', 'procedencia_id', 'salon_id'];


    public function salon()
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }
    public function procedencia()
    {
        return $this->belongsTo(procedencia::class, 'procedencia_id');
    }
    public function datosFacturacion()
    {
        return $this->hasMany(tax_data::class);
    }

    public function compras()
    {
        return $this->hasMany(venta::class, 'customer_id');
    }

    public function calificaciones()
    {
        return $this->hasMany(calificacion_empleado_cliente::class, 'cliente_id');
    }
    public function reviews()
    {
        return $this->hasMany(calificacion_cliente_empleado::class, 'cliente_id');
    }
    public function materiales()
    {
        return $this->hasMany(Material::class, 'cliente_id');
    }
    public function respuestas()
    {
        return $this->hasMany(respuesta::class, 'cliente_id');
    }
    public function citas()
    {
        return $this->hasMany(cita::class, 'customer_id');
    }
    public function categorias()
    {
        return $this->belongsToMany(categoria_cliente::class, 'categoria_clientes_pivs');
    }

    function tarjetaPuntos()
    {
        return $this->hasOne(tarjetas_punto::class, 'cliente_id');
    }

    function excepciones()
    {
        return $this->hasMany(excepcion_cliente::class);
    }
    public function scopeVisits(Builder $query)
    {
        return $query->withCount('citas', 'compras');
    }
    public function scopeVisitsBetween(Builder $query, $fecha_inicio = null, $fecha_fin = null)
    {
        $inicio = $fecha_inicio ?? request('start_date');
        $fin = $fecha_fin ?? request('end_date');

        return $query->withCount([
            'citas' => function (Builder $q) use ($inicio, $fin) {
                $q->whereBetween('start', [$inicio, $fin]);
            },
            'compras' => function (Builder $q) use ($inicio, $fin) {
                $q->whereBetween('created_at', [$inicio, $fin]);
            }
        ]);
    }
    public function scopeOrderByBirthdayProximity(Builder $query, $direction = 'asc')
    {
        $today = now();
        $salonId = Auth::user()->salon->id;

        $query->whereNotNull('birth_date')
            ->where('salon_id', $salonId)
            ->select('*')
            ->selectRaw(
                'CASE 
                    WHEN (DATE_FORMAT(birth_date, "%m-%d") >= DATE_FORMAT(?, "%m-%d")) THEN DATEDIFF(DATE_FORMAT(CONCAT(YEAR(NOW()), "-", DATE_FORMAT(birth_date, "%m-%d")), "%Y-%m-%d"), NOW())
                    ELSE DATEDIFF(DATE_FORMAT(CONCAT(YEAR(NOW()) + 1, "-", DATE_FORMAT(birth_date, "%m-%d")), "%Y-%m-%d"), NOW())
                END AS birthday_proximity',
                [$today]
            )
            ->orderBy('birthday_proximity', $direction);

        return $query;
    }
    public function top10ServicesConsumed()
    {
        return $this->hasManyThrough(Asignacion_servicio::class, cita::class, 'customer_id', 'cita_id')
            ->select('selected_service', DB::raw('COUNT(*) as times_consumed, SUM(current_price - disccount_price) as total_spent'))
            ->with('servicio:id,name')
            ->groupBy('selected_service', 'citas.customer_id')
            ->orderByDesc('times_consumed')
            ->limit(10);
    }
    public function top10ServicesCategoriesConsumed()
    {
        return $this->hasManyThrough(Asignacion_servicio::class, cita::class, 'customer_id', 'cita_id')
            ->join('categoria_servicios_pivs', 'categoria_servicios_pivs.servicio_id', '=', 'asignacion_servicios.selected_service')
            ->join('categoria_servicios', 'categoria_servicios.id', '=', 'categoria_servicios_pivs.categoria_servicio_id')
            ->select('categoria_servicios.name as category_name', DB::raw('COUNT(*) as times_consumed, SUM(asignacion_servicios.current_price - asignacion_servicios.disccount_price) as total_spent'))
            ->groupBy('categoria_servicios.name', 'citas.customer_id')
            ->orderByDesc('times_consumed')
            ->limit(10);
    }
}
