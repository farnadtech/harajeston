<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailVerificationController extends Controller
{
    const TTL = 10; // دقیقه
    const MAX_ATTEMPTS = 3;
    const RATE_WINDOW = 10;

    /**
     * ارسال کد OTP به ایمیل
     */
    public function send(Request $request)
    {
        $user = Auth::user();

        if ($user->email_verified_at) {
            return back()->with('info', 'ایمیل شما قبلاً تایید شده است.');
        }

        // rate limit
        $recentCount = OtpCode::where('email', $user->email)
            ->where('purpose', 'email_verify')
            ->where('created_at', '>=', now()->subMinutes(self::RATE_WINDOW))
            ->count();

        if ($recentCount >= self::MAX_ATTEMPTS) {
            return back()->with('error', 'تعداد درخواست‌ها بیش از حد مجاز است. لطفاً ' . self::RATE_WINDOW . ' دقیقه صبر کنید.');
        }

        // باطل کردن کدهای قبلی
        OtpCode::where('email', $user->email)
            ->where('purpose', 'email_verify')
            ->where('used', false)
            ->update(['used' => true]);

        $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'phone'      => null,
            'email'      => $user->email,
            'code'       => $code,
            'purpose'    => 'email_verify',
            'expires_at' => now()->addMinutes(self::TTL),
            'used'       => false,
        ]);

        // ارسال ایمیل
        try {
            Mail::send([], [], function ($message) use ($user, $code) {
                $message->to($user->email, $user->name)
                    ->subject('کد تایید ایمیل - ' . config('app.name'))
                    ->html(
                        '<div dir="rtl" style="font-family:Tahoma,Arial,sans-serif;max-width:500px;margin:0 auto;padding:20px;border:1px solid #e5e7eb;border-radius:12px;">'
                        . '<h2 style="color:#1d4ed8;margin-bottom:16px;">تایید ایمیل</h2>'
                        . '<p style="color:#374151;margin-bottom:12px;">سلام ' . $user->name . ' عزیز،</p>'
                        . '<p style="color:#374151;margin-bottom:20px;">کد تایید ایمیل شما:</p>'
                        . '<div style="background:#f3f4f6;border-radius:8px;padding:16px;text-align:center;font-size:32px;font-weight:bold;letter-spacing:8px;color:#1d4ed8;">' . $code . '</div>'
                        . '<p style="color:#6b7280;margin-top:16px;font-size:13px;">این کد تا ' . self::TTL . ' دقیقه معتبر است.</p>'
                        . '</div>'
                    );
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Email OTP send error: ' . $e->getMessage());
            return back()->with('error', 'خطا در ارسال ایمیل. لطفاً دوباره تلاش کنید.');
        }

        return back()->with('email_otp_sent', true)->with('success', 'کد تایید به ایمیل ' . $user->email . ' ارسال شد.');
    }

    /**
     * تایید کد OTP ایمیل
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email_otp_code' => 'required|digits:6',
        ], [
            'email_otp_code.required' => 'کد تایید الزامی است.',
            'email_otp_code.digits'   => 'کد تایید باید ۶ رقم باشد.',
        ]);

        $user = Auth::user();

        $otp = OtpCode::where('email', $user->email)
            ->where('code', $request->email_otp_code)
            ->where('purpose', 'email_verify')
            ->where('used', false)
            ->latest()
            ->first();

        if (!$otp || $otp->expires_at->isPast()) {
            return back()->with('email_otp_sent', true)
                ->withErrors(['email_otp_code' => 'کد وارد شده نادرست یا منقضی شده است.']);
        }

        $otp->update(['used' => true]);
        $user->update(['email_verified_at' => now()]);

        // refresh کردن session کاربر
        Auth::setUser($user->fresh());

        return redirect()->back()->with('success', 'ایمیل شما با موفقیت تایید شد.');
    }
}
