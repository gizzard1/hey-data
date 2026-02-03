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
    public static function updatePassword(Request $request)
    {
        try{
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6',
            ]);

            $user = $request->user();

            if(!Hash::check($request->current_password,$user->password)){
                return response()->json(['error' => 'current_password is incorrect'], 200);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json(['message' => 'password updated']);
        }catch(\Throwable $th){
            Log::error($th->getMessage());
            return response()->json(['error' => 'error updating password'], 200);
        }
    }
}

