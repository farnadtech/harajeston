<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryPageController extends Controller
{
    public function index()
    {
        $categories = Category::active()
            ->parents()
            ->ordered()
            ->with(['children' => function ($q) {
                $q->active()->ordered()->with(['children' => function ($q2) {
                    $q2->active()->ordered();
                }]);
            }])
            ->withCount('listings')
            ->get();

        return view('categories.index', compact('categories'));
    }
}
