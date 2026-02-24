<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function index()
    {
        $gateways = PaymentGateway::orderBy('sort_order')->get();
        return view('admin.payment-gateways.index', compact('gateways'));
    }

    public function edit(PaymentGateway $gateway)
    {
        return view('admin.payment-gateways.edit', compact('gateway'));
    }

    public function update(Request $request, PaymentGateway $gateway)
    {
        $validated = $request->validate([
            'is_active' => 'boolean',
            'sandbox_mode' => 'boolean',
            'credentials' => 'array',
            'sort_order' => 'integer|min:0',
        ]);

        // اگر checkbox ها ارسال نشده باشند، false قرار می‌دهیم
        $validated['is_active'] = $request->has('is_active');
        $validated['sandbox_mode'] = $request->has('sandbox_mode');

        $gateway->update($validated);

        return redirect()
            ->route('admin.payment-gateways.index')
            ->with('success', 'درگاه پرداخت با موفقیت به‌روزرسانی شد');
    }

    public function toggle(PaymentGateway $gateway)
    {
        $gateway->update(['is_active' => !$gateway->is_active]);

        return back()->with('success', 'وضعیت درگاه تغییر کرد');
    }
}
