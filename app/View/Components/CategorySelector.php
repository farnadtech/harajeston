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
        
        // استفاده از متد getMenuStructure که تا سطح 3 لود می‌کنه
        $this->categories = Category::getMenuStructure();
    }

    public function render()
    {
        return view('components.category-selector');
    }
}
