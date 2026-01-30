<?php

namespace App\Http\Controllers;

use App\Models\marca;
use App\Models\servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataServices extends Controller
{
    public static function loadServices(Request $request)
    {    
        try{
            $salon_id = $request->user()->salon_id;
            $servicios = servicio::select('id','name','description','gross_price','disccount_price','reward_points','duration','iva',
                DB::raw("name as nombre"),
                DB::raw("gross_price as precio"),
                DB::raw("duration as duracionMinutos"),
            )
                ->where('salon_id', $salon_id)
                ->where('visibility','visible')
                ->where('name', '!=', 'Servicio eliminado')
                ->with('categorias:id,name', 'marca:id,name', 'files:id,model_id,file,model_type')
                ->get();
            return ['servicios' => $servicios];
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
    public static function loadSuppliers(Request $request)
    {    
        try{
            $salon_id = $request->user()->salon_id;
            $marcas = marca::select('id','name','contact_name','phone_number','rfc','email')
                ->where('salon_id', $salon_id)
                ->where('name', '!=', 'Marca eliminada')
                ->get();
            return ['marcas' => $marcas];
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
    
    public function storeService(Request $request)
    {
        try{
            $salon_id = $request->user()->salon_id;
            $service = $request->input('service');
            
            $newService = servicio::updateOrCreate(
                [
                    'id' => $service['id'] ?? null
                ],
                [
                    'name' => $service['name'],
                    'iva'=>isset($service['iva']) ? ($service['iva'] === '8%' ? '0.08' : ($service['iva'] === '16%' ? '0.16' : ($service['iva'] === 'Exento' ? '0' : $service['iva']))) : '0.16',
                    'description' => $service['description'],
                    'gross_price' => $service['gross_price'],
                    'disccount_price' => $service['disccount_price'],
                    'duration' => $service['duration'],
                    'salon_id' => $salon_id,
                ]
            );

            return $newService;
        }catch(\Throwable $th){
            Log::error($th->getMessage());
        }
    }
}

