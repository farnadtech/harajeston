<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MelipayamakSetting;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsGatewayController extends Controller
{
    public function index()
    {
        $settings = MelipayamakSetting::get();
        return view('admin.sms-gateways.index', compact('settings'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100',
        ], [
            'username.required' => 'نام کاربری الزامی است.',
        ]);

        MelipayamakSetting::updateOrCreate(
            ['id' => 1],
            [
                'username'    => $request->username,
                'password'    => $request->filled('password') ? $request->password : (MelipayamakSetting::get()?->password ?? ''),
                'api_key'     => $request->filled('api_key') ? $request->api_key : (MelipayamakSetting::get()?->api_key ?? ''),
                'from_number' => $request->from_number ?? '',
                'body_id'     => $request->body_id ?? '',
                'is_active'   => true,
            ]
        );

        app()->forgetInstance(SmsService::class);

        return back()->with('success', 'تنظیمات ملی پیامک با موفقیت ذخیره شد.');
    }

    public function test(Request $request)
    {
        $request->validate([
            'test_phone' => ['required', 'regex:/^09[0-9]{9}$/'],
        ], [
            'test_phone.required' => 'شماره موبایل الزامی است.',
            'test_phone.regex'    => 'شماره موبایل باید ۱۱ رقمی و با ۰۹ شروع شود.',
        ]);

        app()->forgetInstance(SmsService::class);
        $smsService = app(SmsService::class);

        // تست با SendOtp — نیازی به شماره فرستنده ندارد
        $testCode = '12345';
        $result   = $smsService->sendOtpWithLog($request->test_phone, $testCode);

        return back()
            ->with($result['success'] ? 'test_success' : 'test_error', $result['message'])
            ->with('test_response', $result['response']);
    }
}
