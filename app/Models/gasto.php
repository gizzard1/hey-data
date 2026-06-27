<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class gasto extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'type',
        'note',
        'payment_method',
        'folio_fiscal',
        'total',
        'iva',
        'status',
        'salon_id',
        'marca_id',
        'categoria_id',
        'tipo_id',
        'date',
    ];
    public function marca()
    {
        return $this->belongsTo(marca::class, 'marca_id');
    }
    public function salon()
    {
        return $this->belongsTo(Salon::class, 'salon_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function categoria()
    {
        return $this->belongsTo(categoria_gasto::class);
    }
    public function tipo()
    {
        return $this->belongsTo(tipo_gasto::class);
    }
    public function files()
    {
        return $this->morphMany(File::class, 'model');
    }

    public function latestImage()
    {
        //recent file
        return $this->morphOne(File::class, 'model')->latestOfMany();
    }
    public function getPhotosAttribute()
    {
        if (count($this->files)) {
            return $this->files->map(function ($file) {
                if (file_exists('storage/gastos/' . $file->file)) {
                    return "storage/gastos/" . $file->file;
                } else {
                    return 'storage/Image-not-found.png';
                }
            });
        }
    }
    public function scopeFloatCashBetweenDates($query, $salon_id, $start, $end)
    {
        return $query->select('id', 'total', 'payment_method', 'status', 'updated_at', 'date', 'created_at', 'salon_id', 'user_id')
            ->with('user:id,name')
            ->where('salon_id', $salon_id)
            ->where('payment_method', 'Caja chica')
            ->whereBetween('date', [$start, $end]);
    }
    public function scopeReportBetweenDates($query, $salon_id, $start, $end)
    {
        return $query->where('salon_id', $salon_id)
            ->whereBetween('date', [$start, $end]);
    }
}
