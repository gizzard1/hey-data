<?php
namespace App\Services;
use MercadoPago\Payment;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Illuminate\Support\Facades\Log;

class MercadoPagoService{

    public function __construct()
    {
        $this->authenticate();
    }

    protected function authenticate()
    {
        // Getting the access token from .env file (create your own function)
        $mpAccessToken = config('mercadopago.access_token');
        // Set the token the SDK's config
        MercadoPagoConfig::setAccessToken($mpAccessToken);
        // (Optional) Set the runtime enviroment to LOCAL if you want to test on localhost
        // Default value is set to SERVER
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
    }
    // Function that will return a request object to be sent to Mercado Pago API
    public function crearMPreferencia($items, $payer)
    {
        $paymentMethods = [
            // Define métodos de pago específicos permitidos
            "excluded_payment_methods" => [], // Sin métodos excluidos
            "excluded_payment_types" => [
                ["id" => "ticket"] // Excluir otros tipos de pago si es necesario
            ],
            "installments" => 12, // Cantidad máxima de cuotas
            "default_installments" => 1
        ];

        $backUrls = array(
            'success' => route('agenda'), // Redirige a la agenda en caso de éxito
            'failure' => route('agenda')
        );

        $request = [
            "items" => $items,
            "payer" => $payer,
            "payment_methods" => $paymentMethods,
            "back_urls" => $backUrls,
            "statement_descriptor" => "NAME_DISPLAYED_IN_USER_BILLING",
            "expires" => false,
            "auto_return" => 'approved',
        ];

        return $request;
    }
    public function createPaymentPreference($monto,$type,$isAnual,$salon_id,$email)
    {
        // Fill the data about the product(s) being pruchased
        $product = array(
            "id" => $type,
            "title" => $type,
            "description" => $isAnual ? 'anual':'mensual',
            "currency_id" => "MXN",
            "quantity" => 1,
            "unit_price" => intval($monto),
        );

        // Mount the array of products that will integrate the purchase amount
        $items = array($product);


        $payer = array(
            "name" => $salon_id,
            "surname" => 'default',
            "email" => $email,
        );

        // Create the request object to be sent to the API when the preference is created
        $request = $this->crearMPreferencia($items, $payer);


        // Instantiate a new Preference Client
        $client = new PreferenceClient();

        try {
            // Send the request that will create the new preference for user's checkout flow
            $preference = $client->create($request);

            // Useful props you could use from this object is 'init_point' (URL to Checkout Pro) or the 'id'
            return $preference;
        }catch (MPApiException $error) {
            // Return null or handle the error as needed
            return null;
        }
    }
    public function getPayment($id)
    {
        // Obtén el token de acceso desde el archivo de configuración .env
        $accessToken = config('mercadopago.access_token');
        $url = 'https://api.mercadopago.com/v1/payments/' . intval($id);
        
        // Inicializa cURL
        $ch = curl_init();
        
        // Configura las opciones de cURL
        curl_setopt($ch, CURLOPT_URL, $url); // Define la URL de la solicitud
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Devuelve el resultado en lugar de imprimirlo
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . $accessToken
        ]);
        
        // Ejecuta la solicitud y obtiene la respuesta
        $response = curl_exec($ch);
        
        // Manejo de errores
        if (curl_errno($ch)) {
            // Log del error si hay un problema con cURL
            Log::error("cURL error: " . curl_error($ch));
            curl_close($ch);
            return null;
        }
        
        // Cierra la conexión de cURL
        curl_close($ch);
        
        // Decodifica la respuesta JSON
        $responseData = json_decode($response, true);
        
        return $responseData; // Retorna la respuesta como array o la puedes manipular según necesites
    }

}