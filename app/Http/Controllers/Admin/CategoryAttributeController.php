<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryAttribute;
use Illuminate\Http\Request;

class CategoryAttributeController extends Controller
{
    public function index(Category $category)
    {
        // فقط زیردسته‌ها میتونن ویژگی داشته باشن
        if ($category->isParent()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'فقط زیردسته‌ها می‌توانند ویژگی داشته باشند.');
        }

        $attributes = $category->attributes()->ordered()->get();
        
        return view('admin.category-attributes.index', compact('category', 'attributes'));
    }

    public function create(Category $category)
    {
        if ($category->isParent()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'فقط زیردسته‌ها می‌توانند ویژگی داشته باشند.');
        }

        return view('admin.category-attributes.create', compact('category'));
    }

    public function store(Request $request, Category $category)
    {
        if ($category->isParent()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'فقط زیردسته‌ها می‌توانند ویژگی داشته باشند.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:select,text,number',
            'options' => 'nullable|string',
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'order' => 'integer',
        ]);

        // تبدیل options از string به array
        if ($validated['type'] === 'select' && !empty($validated['options'])) {
            $options = array_map('trim', explode(',', $validated['options']));
            $validated['options'] = $options;
        } else {
            $validated['options'] = null;
        }

        $category->attributes()->create($validated);

        return redirect()->route('admin.category-attributes.index', $category)
            ->with('success', 'ویژگی با موفقیت اضافه شد.');
    }

    public function edit(Category $category, CategoryAttribute $attribute)
    {
        if ($attribute->category_id !== $category->id) {
            abort(404);
        }

        return view('admin.category-attributes.edit', compact('category', 'attribute'));
    }

    public function update(Request $request, Category $category, CategoryAttribute $attribute)
    {
        if ($attribute->category_id !== $category->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:select,text,number',
            'options' => 'nullable|string',
            'is_required' => 'boolean',
            'is_filterable' => 'boolean',
            'order' => 'integer',
        ]);

        // تبدیل options از string به array
        if ($validated['type'] === 'select' && !empty($validated['options'])) {
            $options = array_map('trim', explode(',', $validated['options']));
            $validated['options'] = $options;
        } else {
            $validated['options'] = null;
        }

        $attribute->update($validated);

        return redirect()->route('admin.category-attributes.index', $category)
            ->with('success', 'ویژگی با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(Category $category, CategoryAttribute $attribute)
    {
        if ($attribute->category_id !== $category->id) {
            abort(404);
        }

        $attribute->delete();

        return redirect()->route('admin.category-attributes.index', $category)
            ->with('success', 'ویژگی با موفقیت حذف شد.');
    }
}
