<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class DataCategories extends Controller
{
    public static function loadCategories(Request $request)
    {
        try {
            $salon_id = $request->user()->salon_id;
            $model = 'App\\Models\\categoria_' . $request->input('type');
            $search = $request->search;

            $categories = $model::query()
                ->select('id', 'name')
                ->where('salon_id', $salon_id)
                ->where('name', 'like', "%{$search}%")
                ->orderBy('name', 'asc')
                ->paginate(50);

            return ['categories' => $categories];
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
