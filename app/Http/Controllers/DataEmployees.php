<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class DataEmployees extends Controller
{
    public static function loadEmployees(Request $request)
    {    
        try{
            $salon_id = $request->user()->salon_id;

            $employees = Empleado::select(
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'phone_number',
                    'color_preset',
                    'birth_date',
                    'visible',
                    'user_id',
                )
                ->with('user:id,name,email,role')
                ->where('salon_id', $salon_id)
                ->orderBy('first_name', 'asc')
                ->get();

            return ['employees' => $employees];
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
    public static function isUniqueEmployeeEmail(Request $request)
    {
        try{
            $email = $request->input('email');
            $user_id = $request->input('user_id') ?? $request->user()->id;

            $query = User::where('email', $email);

            if ($user_id) {
                $query->where('id', '!=', $user_id);
            }

            $exists = $query->exists();

            return ['is_unique' => !$exists];
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
    public static function updateOrCreateEmployee(Request $request)
    {
        try{
            $salon_id = $request->user()->salon_id;
            $employeeData = $request->input('employee');
            $userData = $employeeData['user'] ?? [];

            $employee = Empleado::updateOrCreate(
                [
                    'id' => $employeeData['id'] ?? null
                ],
                [
                    'first_name' => $employeeData['first_name'],
                    'last_name' => $employeeData['last_name'] ?? null,
                    'phone_number' => $employeeData['phone_number'] ?? null,
                    'color_preset' => $employeeData['color_preset'] ?? '#F0959C',
                    'birth_date' => isset($employeeData['birth_date']) ? Carbon::parse($employeeData['birth_date'])->toDateString() : null,
                    'visible' => $employeeData['visible'] ?? true,
                    'salon_id' => $salon_id,
                ]
            );

            if(!empty($userData) && isset($userData['name']) && isset($userData['email']) && isset($userData['role']) && isset($userData['password'])){
                $user = User::updateOrCreate(
                    [
                        'id' => $employee->user_id ?? null,
                    ],
                    [
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'role' => $userData['role'],
                        'password' => Hash::make($userData['password']),
                        'salon_id' => $salon_id,
                    ]
                );
                $employee->user_id = $user->id;
                $employee->save();
            }

            // Si el empleado se crea por primera vez, se crea una comisión para él
            if (!$employeeData['id']) {
                DataCommission::createCommission($employee->id);
            }

            return ['employee' => $employee, 'user' => $user ?? null];
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
}