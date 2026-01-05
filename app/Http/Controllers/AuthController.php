<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public static function login(Request $request)
    {
        try{
            
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'device_name' => 'required',
            ]);

            $user = User::where('email',$request->email)->first();

            if(!$user || !Hash::check($request->password,$user->password)){
                return response()->json(['error' => 'Credenciales']);
            }

            return response()->json([
                'token' => $user->createToken($request->device_name)->plainTextToken
            ]);
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
}

