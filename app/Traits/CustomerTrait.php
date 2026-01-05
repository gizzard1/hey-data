<?php


namespace App\Traits;

use App\Traits\WoocommerceTrait;

trait CustomerTrait
{

    use WoocommerceTrait;


    function createOrUpdateCustomer($customer, $isCreating = true)
    {

        $woocommerce = $this->getConnection();


        $billingDelivery = $customer->deliveries()->where('type', 'billing')->first();
        $shippingDelivery = $customer->deliveries()->where('type', 'shipping')->first();

        $data = [
            'email' => $customer->email ?? 'momo@momo.com',
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'username' => '',
            'billing' => [
                'first_name' => $billingDelivery->first_name ?? '',
                'last_name' => $billingDelivery->last_name ?? '',
                'company' =>  $billingDelivery->company ?? '',
                'address_1' => $billingDelivery->primary_address ?? '',
                'address_2' => $billingDelivery->secondary_address ?? '',
                'city' => $billingDelivery->city ?? '',
                'state' => $billingDelivery->state ?? '',
                'postcode' => $billingDelivery->postcode ?? '',
                'country' => $billingDelivery->country ?? 'MX',
                'email' => $billingDelivery->email ?? $customer->email,
                'phone' => $billingDelivery->phone ?? ''
            ],
            'shipping' => [
                'first_name' => $shippingDelivery->first_name ?? '',
                'last_name' => $shippingDelivery->last_name ?? '',
                'company' =>  $shippingDelivery->company ?? '',
                'address_1' => $shippingDelivery->primary_address ?? '',
                'address_2' => $shippingDelivery->secondary_address ?? '',
                'city' => $shippingDelivery->city ?? '',
                'state' => $shippingDelivery->state ?? 'MX',
                'postcode' => $shippingDelivery->postcode ?? '',
                'country' => $shippingDelivery->country ?? '',
            ]
        ];

        if ($isCreating) {
            $result = $woocommerce->post('customers', $data);
            $customer->platform_id = $result->id;
            $customer->save();
        } else {
            if ($customer->platform_id) {
                $woocommerce->put("customers/{$customer->platform_id}", $data);
            } else {
                $result = $woocommerce->post('customers', $data);
                $customer->platform_id = $result->id;
                $customer->save();
            }
        }

        return true;
    }
    function deleteCustomer($id)
    {
        $woocommerce = $this->getConnection();

        $params = [
            'force' => true
        ];

        $result = $woocommerce->delete("customers/{$id}", $params);

        return $result->id;
    }
}
