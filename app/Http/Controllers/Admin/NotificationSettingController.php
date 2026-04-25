<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotificationSettingController extends Controller
{
    public function index()
    {
        NotificationSetting::seedDefaults();
        $settings = NotificationSetting::orderBy('recipient')->orderBy('event_label')->get();

        // Attach sample/vars from static definition
        $events = NotificationSetting::getEvents();
        foreach ($settings as $setting) {
            $def = $events[$setting->event_key] ?? [];
            $setting->sms_pattern_text = $def['sms_pattern_text'] ?? null;
            $setting->sms_vars         = $def['sms_vars'] ?? [];
        }

        return view('admin.notification-settings.index', compact('settings'));
    }

    public function test(\App\Models\NotificationSetting $setting, \Illuminate\Http\Request $request)
    {
        $admin = auth()->user();

        // Use admin_test_phone setting, fallback to admin's own phone
        $testPhone = \App\Models\SiteSetting::get('admin_test_phone', $admin->phone ?? '');

        if (!$testPhone) {
            return response()->json([
                'success' => false,
                'message' => 'شماره موبایل ادمین برای تست تنظیم نشده است. لطفاً ابتدا شماره را وارد کنید.',
            ]);
        }

        if (!$setting->via_sms) {
            return response()->json([
                'success' => false,
                'message' => 'پیامک برای این رویداد فعال نیست.',
            ]);
        }

        if (!$setting->sms_pattern_id) {
            return response()->json([
                'success' => false,
                'message' => 'شناسه پترن پیامک برای این رویداد تنظیم نشده است.',
            ]);
        }

        $sms = app(\App\Services\SmsService::class);
        $result = $sms->sendByPatternId($testPhone, $setting->sms_pattern_id, [
            'order_number'  => 'TEST-001',
            'buyer_name'    => 'تست خریدار',
            'seller_name'   => 'تست فروشنده',
            'amount'        => '100,000',
            'listing_title' => 'محصول تست',
        ]);

        return response()->json([
            'success' => (bool) $result,
            'message' => $result
                ? 'پیامک تست با موفقیت به ' . $testPhone . ' ارسال شد.'
                : 'ارسال پیامک تست ناموفق بود. لطفاً تنظیمات دروازه پیامک را بررسی کنید.',
        ]);
    }

    public function updateAdminPhone(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'admin_test_phone' => 'required|string|max:20',
            'admin_test_email' => 'nullable|email|max:100',
        ], [
            'admin_test_phone.required' => 'شماره موبایل الزامی است.',
            'admin_test_email.email'    => 'فرمت ایمیل صحیح نیست.',
        ]);

        \App\Models\SiteSetting::set('admin_test_phone', $request->admin_test_phone, 'string', 'شماره موبایل ادمین برای دریافت پیامک تست');

        if ($request->filled('admin_test_email')) {
            \App\Models\SiteSetting::set('admin_test_email', $request->admin_test_email, 'string', 'ایمیل ادمین برای دریافت ایمیل تست');
        }

        return back()->with('success', 'اطلاعات تماس ادمین ذخیره شد.');
    }

    public function update(Request $request)
    {
        $rows = $request->input('settings', []);

        foreach ($rows as $id => $data) {
            $setting = NotificationSetting::find($id);
            if (!$setting) continue;

            $setting->update([
                'via_database'   => isset($data['via_database']),
                'via_sms'        => isset($data['via_sms']),
                'via_email'      => isset($data['via_email']),
                'sms_pattern_id' => $data['sms_pattern_id'] ?? null,
                'email_subject'  => $data['email_subject'] ?? null,
                'email_body'     => $data['email_body'] ?? null,
            ]);

            Cache::forget("notif_setting_{$setting->event_key}");
        }

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'ذخیره شد']);
        }
        return back()->with('success', 'تنظیمات اعلان‌ها ذخیره شد.');
    }

    public function testEmail(\App\Models\NotificationSetting $setting)
    {
        $admin = auth()->user();

        $testEmail = \App\Models\SiteSetting::get('admin_test_email', $admin->email ?? '');

        if (!$testEmail) {
            return response()->json([
                'success' => false,
                'message' => 'ایمیل ادمین برای تست تنظیم نشده است.',
            ]);
        }

        if (!$setting->via_email) {
            return response()->json([
                'success' => false,
                'message' => 'ایمیل برای این رویداد فعال نیست.',
            ]);
        }

        $subject = $setting->email_subject ?: $setting->event_label . ' - تست';
        $body    = $setting->email_body    ?: 'این یک ایمیل تست برای رویداد «' . $setting->event_label . '» می‌باشد.';

        // Replace sample vars
        $sampleData = [
            '{order_number}'   => 'TEST-001',
            '{buyer_name}'     => 'تست خریدار',
            '{seller_name}'    => 'تست فروشنده',
            '{amount}'         => '100,000',
            '{listing_title}'  => 'محصول تست',
            '{tracking_number}'=> 'TRK-12345',
        ];
        $body = str_replace(array_keys($sampleData), array_values($sampleData), $body);

        $siteName = config('app.name', 'سایت');
        $siteUrl  = config('app.url', '');

        try {
            \Illuminate\Support\Facades\Mail::send(
                'emails.notification',
                compact('subject', 'body', 'siteName', 'siteUrl'),
                function ($m) use ($testEmail, $subject) {
                    $m->to($testEmail)->subject($subject);
                }
            );
            return response()->json([
                'success' => true,
                'message' => 'ایمیل تست با موفقیت به ' . $testEmail . ' ارسال شد.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'ارسال ایمیل ناموفق بود: ' . $e->getMessage(),
            ]);
        }
    }
}
