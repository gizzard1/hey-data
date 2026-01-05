<?php

namespace App\Traits;

use App\Models\Integration;
use Automattic\WooCommerce\Client;
use Exception;



trait WoocommerceTrait{

public function getConnection()
{
    try{
        $platform = Integration::first();

        if($platform != null && $platform->count()>0){
            
            //instancia de la api
            $woocommerce = new Client(
                $platform->url,
                $platform->key,
                $platform->secret,
                [
                    'version' => 'wc/v3',
                ]);

            //retomar la conexión
            return $woocommerce;
        }else{
            throw new Exception('No hay credenciales de integración a Woocommerce');
        }
    }   
    catch(\Throwable $th){
        throw $th;
    }
}   
}