<?php

namespace App\View\Components;

use App\Models\Category;
use Illuminate\View\Component;

class CategorySelector extends Component
{
    public $categories;
    public $selected;
    public $name;
    public $label;

    public function __construct($selected = null, $name = 'category_id', $label = 'دسته‌بندی')
    {
        $this->selected = $selected;
        $this->name = $name;
        $this->label = $label;
        
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
        return view('components.category-selector');
    }
}
