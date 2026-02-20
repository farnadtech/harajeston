<?php

namespace App\View\Components;

use App\Models\Category;
use Illuminate\View\Component;

class CategoryMegamenu extends Component
{
    public $categories;

    public function __construct()
    {
        $this->categories = Category::active()
            ->parents()
            ->ordered()
            ->with(['children' => function ($query) {
                $query->active()->ordered();
            }])
            ->get();
    }

    public function render()
    {
        return view('components.category-megamenu');
    }
}
