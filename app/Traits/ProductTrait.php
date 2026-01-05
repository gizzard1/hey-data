<?php


namespace App\Traits;

use App\Traits\WoocommerceTrait;

trait ProductTrait
{

    use WoocommerceTrait;


    function createProduct($product)
    {

        $woocommerce = $this->getConnection();

        $categories = $product->categorias()->get()->map(function ($category) {
            return ['id' => $category->platform_id];
        })->toArray();


        $data = [
            'sku' => $product->sku,
            'name' => $product->name,
            'description' => $product->description,
            'type' => $product->type_product,
            'status' => $product->status,
            'visibility' => $product->visibility,
            'price' => $product->gross_price,
            'regular_price' => $product->gross_price,
            'sale_price' => $product->disccount_price ? $product->disccount_price : null,
            'stock_status' => $product->stock_status,
            'manage_stock' => $product->manage_stock,
            'stock_quantity' => $product->stock_qty,
            'low_stock' => $product->min_stock,
            'categories' => $product->categories, 
        ];

        $result = $woocommerce->post('products', $data);

        $product->platform_id = $result->id;
        $product->save();

        return true;
    }

    function updateProduct($product)
    {
        $woocommerce = $this->getConnection();

        $categories = $product->categorias()->get()->map(function ($category) {
            return ['id' => $category->platform_id];
        })->toArray();


        $data = [
            'sku' => $product->sku,
            'name' => $product->name,
            'description' => $product->description,
            'type' => $product->type_product,
            'status' => $product->status,
            'visibility' => $product->visibility,
            'price' => $product->gross_price,
            'regular_price' => $product->gross_price,
            'sale_price' => $product->disccount_price,
            'stock_status' => $product->stock_status,
            'manage_stock' => $product->manage_stock,
            'stock_quantity' => $product->stock_qty,
            'low_stock' => $product->min_stock,
            'categories' => $categories, 
        ];


        $result = $woocommerce->put("products/{$product->platform_id}", $data);

        $product->platform_id = $result->id;
        $product->save();

        return true;
    }

    function deleteProduct($id, $force = false)
    {
        $woocommerce = $this->getConnection();

        $orders = $woocommerce->get('orders', ['per_page' => 1, 'product' => $id]);

        if (!empty($orders)) {
            return false;
        } else {

            $woocommerce->delete("products/{$id}", ['force' => $force]);

            return true;
        }
    }


    function findOrCreateProductByName($product)
    {
        try{
            $woocommerce = $this->getConnection();
    
    
            $params = ['search' => $product->name];
    
    
            $result = $woocommerce->get('products', $params);
    
            if (!empty($result)) {
                $product->platform_id = $result[0]->id;
                $product->save();
                $this->updateProduct($product);
            } else {
                $this->createProduct($product);
            }
        }catch(\Throwable $th){
            $this->dispatchBrowserEvent('noty-error', ['msg' =>  "EXCEPCIÓN: \n {$th->getMessage()}"] );
        }
    }
}
