<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryCommission;
use Illuminate\Http\Request;

class CategoryCommissionController extends Controller
{
    public function index()
    {
        $categories = Category::with(['categoryCommission', 'parent'])
            ->orderBy('parent_id')
            ->orderBy('order')
            ->get();

        return view('admin.category-commissions.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:fixed,percentage',
            'fixed_amount' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        CategoryCommission::updateOrCreate(
            ['category_id' => $validated['category_id']],
            [
                'type' => $validated['type'],
                'fixed_amount' => $validated['type'] === 'fixed' ? $validated['fixed_amount'] : null,
                'percentage' => $validated['type'] === 'percentage' ? $validated['percentage'] : null,
            ]
        );

        return redirect()->route('admin.category-commissions.index')
            ->with('success', 'کمیسیون دسته‌بندی با موفقیت ذخیره شد.');
    }

    public function destroy($id)
    {
        $commission = CategoryCommission::findOrFail($id);
        $commission->delete();

        return redirect()->route('admin.category-commissions.index')
            ->with('success', 'کمیسیون دسته‌بندی با موفقیت حذف شد.');
    }
}
