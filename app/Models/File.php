<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_id',
        'model_type',
        'file',
    ];

    //relationship

    public function file()
    {
        return $this->morphTo();
    }

}
