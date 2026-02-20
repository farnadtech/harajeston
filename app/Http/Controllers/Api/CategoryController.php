<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function getAttributes(Category $category): JsonResponse
    {
        $attributes = $category->attributes()
            ->ordered()
            ->get()
            ->map(function ($attr) {
                return [
                    'id' => $attr->id,
                    'name' => $attr->name,
                    'type' => $attr->type,
                    'options' => $attr->options,
                    'is_required' => $attr->is_required,
                    'is_filterable' => $attr->is_filterable,
                ];
            });

        return response()->json([
            'attributes' => $attributes,
        ]);
    }
}
