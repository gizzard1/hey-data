<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Services\MercadoPagoService;
use App\Http\Controllers\DataSalon;
use App\Http\Controllers\DataTransactions;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create($monto = null, $type = null, $isAnual = null)
    {
        if ($monto != null && $type != null) {
            // Parámetros para pago
            $parameters = [
                'monto' => $monto,
                'type' => $type,
                'isAnual' => $isAnual
            ];
        } else {
            $parameters = null;
        }
        session()->put('parameters', $parameters);
        session()->save();
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'invitation_code' => ['nullable']
        ]);

        // Validar email único para el usuario
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json(['error' => 'email_already_exists']);
        }

        $salon = DataSalon::createDefaultSalon($request->invitation_code);

        $user = User::create([
            'name' => 'Admin',
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'salon_id' => $salon->id,
        ]);

        event(new Registered($user));

        Auth::login($user);
        if (session('parameters') != null) {
            $parameters = session('parameters');
            $mercadoPagoService = new MercadoPagoService;
            $link = $mercadoPagoService->createPaymentPreference($parameters['monto'], $parameters['type'], $parameters['isAnual'], $salon->id, $request->email);

            // Accede a init_point
            $initPoint = $link->init_point;

            return redirect()->to($initPoint);
        } else {
            if (DataTransactions::castBoolean($request->fromApi)) {
                return response()->json([
                    'token' => $user->createToken($request->device_name)->plainTextToken,
                    'role' => 'admin',
                ]);
            }
            return redirect()->route('agenda');
        }
    }
}
