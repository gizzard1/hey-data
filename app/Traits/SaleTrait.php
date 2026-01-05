<?php

namespace App\Traits;

use App\Traits\WoocommerceTrait;


trait SaleTrait
{

    use WoocommerceTrait;


    function SyncBatchStock($dataProducts)
    {
        try {
            $woocommerce = $this->getConnection();

            $result = $woocommerce->post('products/batch', $dataProducts);

            if (isset($result->update) && is_array($result->update)) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
