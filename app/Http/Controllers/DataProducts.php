<?php

namespace App\Http\Controllers;

use App\Models\producto;
use Illuminate\Http\Request;
use App\Http\Controllers\DataFiles as DF;
use Illuminate\Support\Facades\Log;

class DataProducts extends Controller
{
    public static function loadProducts(Request $request)
    {
        try {
            $salon_id = $request->user()->salon_id;
            $search = $request->search;
            $productos = producto::query()
                ->select(
                    'id',
                    'name',
                    'description',
                    'gross_price',
                    'iva',
                    'disccount_price',
                    'cost',
                    'unit_type',
                    'stock_qty',
                    'min_stock',
                    'sku',
                    'brand_id',
                    'type_product',
                )
                ->where('salon_id', $salon_id)
                ->where('visibility', 'visible')
                ->where('name', '!=', 'Producto eliminado')
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                })
                ->with('categorias:id,name', 'marca:id,name', 'files:id,model_id,file,model_type')
                ->orderBy('name', 'asc')
                ->paginate(50);

            return ['productos' => $productos];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
    public static function storeProduct(Request $request)
    {
        try {
            $salon_id = $request->user()->salon_id;
            $product = $request->input('product');
            $iva = isset($product['iva']) ? ($product['iva'] === '8%' ? '0.08' : ($product['iva'] === '16%' ? '0.16' : ($product['iva'] === 'Exento' ? '0' : $product['iva']))) : '0.16';

            $newProduct = producto::updateOrCreate(
                [
                    'id' => $product['id'] ?? null
                ],
                [
                    'name' => $product['name'],
                    'description' => $product['description'] ?? null,
                    'gross_price' => $product['gross_price'],
                    'disccount_price' => $product['disccount_price'] ?? null,
                    'cost' => $product['cost'] ?? null,
                    'iva' => $iva,
                    'unit_type' => $product['unit_type'] ?? 'Unidad',
                    'sku' => $product['sku'] ?? null,
                    'stock_qty' => $product['stock_qty'] ?? 0,
                    'min_stock' => $product['min_stock'] ?? 0,
                    'brand_id' => $product['brand_id'] ?? null,
                    'type_product' => $product['type_product'] === 'uso' ? 'variable' : 'simple',
                    'salon_id' => $salon_id,
                ]
            );

            // Guardar imágenes
            $msg = DF::updateOrCreateFile($product['files'] ?? [],$newProduct->id);

            return ['product' => $newProduct, 'msg' => $msg];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
