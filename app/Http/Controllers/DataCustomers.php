<?php

namespace App\Http\Controllers;

use App\Models\cliente;
use App\Models\procedencia;
use App\Models\tarjetas_punto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Svg\Tag\Rect;

class DataCustomers extends Controller
{
    public static function loadCustomers(Request $request)
    {
        try {
            $salon_id = $request->user()->salon_id;
            $search = $request->search;
            $clientes = cliente::query()
                ->select(
                    'id',
                    DB::raw("CONCAT(COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as nombre"),
                    'email',
                    DB::raw("phone as telefono"),
                    DB::raw("CONCAT(UPPER(LEFT(COALESCE(first_name, ''), 1)),UPPER(LEFT(COALESCE(last_name, ''), 1))) as iniciales")
                )
                ->where('salon_id', $salon_id)
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere(DB::raw("CONCAT_WS(' ', TRIM(first_name), TRIM(last_name))"), 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
                })
                ->orderBy('first_name', 'asc')
                ->paginate(50);
            return ['clientes' => $clientes];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function loadCustomer(Request $request)
    {
        try {
            $cust_id = $request->query('cust_id');
            $cliente = cliente::select(
                'id',
                'email',
                'want_custom_messages',
                'procedencia_id',
                DB::raw('first_name as nombre'),
                DB::raw('last_name as apellidos'),
                DB::raw("CONCAT(UPPER(LEFT(COALESCE(first_name, ''), 1)),UPPER(LEFT(COALESCE(last_name, ''), 1))) as iniciales"),
                DB::raw("phone as telefono"),
                DB::raw("sexo as genero"),
                DB::raw("postcode as codigo_postal"),
                DB::raw("birth_date as fecha_nacimiento")
            )
                ->with([
                    'procedencia' => function ($q) {
                        $q->select('id', 'name');
                    },
                    'tarjetaPuntos' => function ($q) {
                        $q->select('id', 'intern_barcode', 'balance', 'cliente_id');
                    },
                    'calificaciones' => function ($q) {
                        $q->select('id', 'puntaje', 'cliente_id');
                    },
                    'categorias' => function ($q) {
                        $q->select('categoria_clientes.id', 'categoria_clientes.name');
                    },
                ])
                ->withAvg('calificaciones', 'puntaje')
                ->find($cust_id);
            return $cliente;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function validateRewardPoints(Request $request)
    {
        try {
            $cust_id = $request->query('cust_id');
            $rc = tarjetas_punto::firstWhere('cliente_id', $cust_id);

            if (!$rc) {
                return response()->json(['message' => 'not found', 'card' => null]);
            }

            if ($rc->balance <= 0) {
                return response()->json(['message' => 'insufficient funds', 'card' => null]);
            }

            return response()->json(['message' => 'ok', 'card' => $rc]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function createCardCust(Request $request)
    {
        try {
            $cust_id = $request->input('cust_id');

            $existingCard = tarjetas_punto::firstWhere('cliente_id', $cust_id);
            if ($existingCard) {
                return response()->json(['message' => 'Card already exists for this customer', 'card' => $existingCard]);
            }

            $newCard = new tarjetas_punto();
            $newCard->cliente_id = $cust_id;
            $newCard->balance = 0;
            $newCard->save();

            return response()->json(['message' => 'ok', 'card' => $newCard]);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function loadOrigins(Request $request)
    {
        try {
            $salon_id = $request->user()->salon_id;
            $data['origins'] = procedencia::select(
                'id',
                'name'
            )
                ->where('salon_id', $salon_id)
                ->orWhere('salon_id', null)
                ->get();

            return $data;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public function storeClient(Request $request)
    {
        try {
            $salon_id = $request->user()->salon_id;
            $cliente = $request->input('cliente');

            $cliente['genero'] = isset($cliente['genero']) && $cliente['genero'] !== "Otro" ? $cliente['genero'] : "noBinario";

            $newClient = cliente::updateOrCreate(
                [
                    'id' => $cliente['id'] ?? null
                ],
                [
                    'first_name' => $cliente['nombre'],
                    'last_name' => $cliente['apellidos'] ?? null,
                    'email' => $cliente['email'] ?? null,
                    'phone' => $cliente['telefono'] ?? null,
                    'sexo' => $cliente['genero'] ?? null,
                    'postcode' => $cliente['codigo_postal'] ?? null,
                    'birth_date' => isset($cliente['fecha_nacimiento']) ? Carbon::parse($cliente['fecha_nacimiento'])->toDateString() : null,
                    'procedencia_id' => $cliente['procedencia']['id'] ?? null,
                    'salon_id' => $salon_id
                ]
            );

            $sendClient = [
                'nombre' => $newClient->first_name . ' ' . $newClient->last_name,
                'email' => $newClient->email,
                'telefono' => $newClient->phone,
                'iniciales' => strtoupper(substr($newClient->first_name, 0, 1) . substr($newClient->last_name, 0, 1)),
                'apellidos' => $newClient->last_name,
                'genero' => $newClient->sexo,
                'codigo_postal' => $newClient->postcode,
                'fecha_nacimiento' => $newClient->birth_date,
                'procedencia' => [
                    'id' => $newClient->procedencia_id,
                    'name' => $newClient->procedencia ? $newClient->procedencia->name : null
                ],
            ];

            return $sendClient;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
