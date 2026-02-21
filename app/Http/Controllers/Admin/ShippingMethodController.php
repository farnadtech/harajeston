<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateShippingMethodRequest;
use App\Models\ShippingMethod;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    public function __construct(
        protected ShippingService $shippingService
    ) {
        $this->middleware('admin');
    }

    /**
     * List all shipping methods
     */
    public function index()
    {
        $shippingMethods = ShippingMethod::orderBy('created_at', 'desc')->get();

        return view('admin.shipping-methods.index', compact('shippingMethods'));
    }

    /**
     * Show form to create shipping method
     */
    public function create()
    {
        return view('admin.shipping-methods.create');
    }

    /**
     * Store new shipping method
     */
    public function store(CreateShippingMethodRequest $request)
    {
        $this->shippingService->createShippingMethod(auth()->user(), $request->validated());

        return redirect()
            ->route('admin.shipping-methods.index')
            ->with('success', 'روش ارسال با موفقیت ایجاد شد.');
    }

    /**
     * Show form to edit shipping method
     */
    public function edit(ShippingMethod $shippingMethod)
    {
        return view('admin.shipping-methods.edit', compact('shippingMethod'));
    }

    /**
     * Update shipping method
     */
    public function update(Request $request, ShippingMethod $shippingMethod)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $shippingMethod->update($request->all());

        return redirect()
            ->route('admin.shipping-methods.index')
            ->with('success', 'روش ارسال با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Deactivate shipping method
     */
    public function destroy(ShippingMethod $shippingMethod)
    {
        $shippingMethod->update(['is_active' => false]);

        return redirect()
            ->route('admin.shipping-methods.index')
            ->with('success', 'روش ارسال غیرفعال شد.');
    }

    /**
     * Toggle shipping method active status
     */
    public function toggle(Request $request, ShippingMethod $shippingMethod)
    {
        $shippingMethod->update([
            'is_active' => $request->input('is_active', !$shippingMethod->is_active)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'وضعیت روش ارسال با موفقیت تغییر کرد.'
        ]);
    }
}
