<?php

namespace App\Http\Controllers;

use App\Models\Comision;
use Illuminate\Support\Facades\Log;

class DataCommission extends Controller
{
    public static function createCommission($employee_id)
    {
        try {
            Comision::create([
                'empleado_id' => $employee_id
            ]);
        } catch (\Throwable $th) {
            Log::error('Error al crear comisión para empleado ID: ' . $employee_id . ' - ' . $th->getMessage());
        }
    }
}
