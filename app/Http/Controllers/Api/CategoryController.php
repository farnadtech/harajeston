<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function getStructure(): JsonResponse
    {
        $categories = Category::getMenuStructure();
        return response()->json($categories);
    }
    
    public function getAttributes(Category $category): JsonResponse
    {
        // دریافت ویژگی‌های خود دسته
        $attributes = $category->attributes()
            ->ordered()
            ->get();
        
        // اگر دسته ویژگی نداره، از والدش بگیر (ارث‌بری)
        if ($attributes->isEmpty() && $category->parent) {
            $attributes = $category->parent->attributes()
                ->ordered()
                ->get();
        }
        
        $attributes = $attributes->map(function ($attr) {
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
    
    public function getPath(Category $category): JsonResponse
    {
        $path = [];
        $current = $category;
        
        while ($current) {
            array_unshift($path, [
                'id' => $current->id,
                'name' => $current->name,
                'children' => $current->children()->active()->ordered()->get()->map(function($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'children' => $child->children()->active()->ordered()->get()->map(function($grand) {
                            return [
                                'id' => $grand->id,
                                'name' => $grand->name,
                            ];
                        })
                    ];
                })
            ]);
            $current = $current->parent;
        }
        
        return response()->json(['path' => $path]);
    }
}
