<?php

namespace App\Traits;

use App\Traits\WoocommerceTrait;

use Illuminate\Support\Facades\Log;


trait CategoryTrait {

    use WoocommerceTrait;

    /*  Por defecto, la API de WooCommerce devuelve un máximo de 10 elementos por página. 
        Si deseas obtener más de 10 categorías, puedes utilizar el parámetro per_page para especificar cuántos 
        elementos deseas obtener por página (hasta un máximo de 100). 
        También puedes utilizar el parámetro page para paginar los resultados y obtener todas las categorías.
    */

    public function getCategories() // 10 per page
    {

        $woocommerce = $this->getConnection();

        return $woocommerce->get('products/categories');
    }

    public function getAllCategories()
    {
        $woocommerce = $this->getConnection();
        $page = 1;
        $categories = [];

        while (true) {
            // Obtener una página de categorías
            $response = $woocommerce->get('products/categories', ['per_page' => 10, 'page' => $page]);

            if (empty($response)) {
                // No hay más categorías, salir del bucle
                break;
            }

            // Agregar las categorías a la lista
            $categories = array_merge($categories, $response);

            // Ir a la siguiente página
            $page++;
        }

        return $categories;
    }



    // /*
    // En este ejemplo, se utiliza un bucle while para obtener todas las páginas de productos en WooCommerce. 
    // En cada iteración del bucle, se obtiene una página de productos utilizando el método get de la conexión a WooCommerce 
    // y se especifican los parámetros per_page y page. Luego, se verifica si la respuesta está vacía, 
    // lo que indica que no hay más productos. Si la respuesta no está vacía, se agregan los productos a la lista 
    // y se incrementa el número de página para obtener la siguiente página en la siguiente iteración del bucle. 
    // De esta manera, puedes obtener todos los productos en WooCommerce utilizando paginación. 
    // */
    // public function getProducts()
    // {
    //     $woocommerce = $this->getConnection();
    //     $page = 1;
    //     $products = [];

    //     while (true) {
    //         // Obtener una página de productos
    //         $response = $woocommerce->get('products', ['per_page' => 100, 'page' => $page]);

    //         if (empty($response)) {
    //             // No hay más productos, salir del bucle
    //             break;
    //         }

    //         // Agregar los productos a la lista
    //         $products = array_merge($products, $response);

    //         // Ir a la siguiente página
    //         $page++;
    //     }

    //     return $products;
    // }


    public function createCategory($name)
    {
        try {
            $woocommerce = $this->getConnection();

            $data = ['name' => $name];

            $result = $woocommerce->post('products/categories', $data);

            Log::info(json_encode($result));

            return $result->id;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function updateCategory($category)
    {
        try {
            $woocommerce = $this->getConnection();

            $data = [
                'name' => $category->name
            ];

            $result = $woocommerce->put("products/categories/{$category->platform_id}", $data);

            return $result->id;
            //

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function findOrCreateCategoryByName($category)
    {
        try {
            $woocommerce = $this->getConnection();

            $params = [
                'search' => $category->name
            ];

            $result = $woocommerce->get('products/categories', $params);

            Log::info(json_encode($result));

            if (!empty($result)) {
                $category->platform_id = $result[0]->id;
                $category->save();
            } else {
                $result = $this->createCategory($category->name);
                $category->platform_id = $result;
                $category->save();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function deleteCategory($id)
    {
        try {
            $woocommerce = $this->getConnection();

            $params = [
                'force' => true
            ];

            $result = $woocommerce->delete("products/categories/{$id}", $params);

            Log::info(json_encode($result));

            return $result->id;
        } catch (\Throwable $th) {
            throw $th;
        }
        
    }
}
