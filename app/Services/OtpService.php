<?php

namespace App\Services;

use App\Models\OtpCode;
use Illuminate\Support\Str;

class OtpService
{
    // مدت اعتبار OTP به دقیقه
    const TTL = 5;

    // حداکثر تعداد درخواست در بازه زمانی
    const MAX_ATTEMPTS = 3;
    const RATE_WINDOW  = 10; // دقیقه

    protected SmsService $sms;

    public function __construct(SmsService $sms)
    {
        $this->sms = $sms;
    }

    /**
     * تولید و ارسال کد OTP
     * @return array{success: bool, message: string}
     */
    public function send(string $phone, string $purpose = 'login'): array
    {
        // بررسی rate limit
        $recentCount = OtpCode::where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('created_at', '>=', now()->subMinutes(self::RATE_WINDOW))
            ->count();

        if ($recentCount >= self::MAX_ATTEMPTS) {
            return [
                'success' => false,
                'message' => 'تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً ' . self::RATE_WINDOW . ' دقیقه صبر کنید.',
            ];
        }

        // باطل کردن کدهای قبلی
        OtpCode::where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('used', false)
            ->update(['used' => true]);

        // تولید کد ۶ رقمی
        $code = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'phone'      => $phone,
            'code'       => $code,
            'purpose'    => $purpose,
            'expires_at' => now()->addMinutes(self::TTL),
            'used'       => false,
        ]);

        $sent = $this->sms->sendOtp($phone, $code);

        if (!$sent) {
            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک. لطفاً دوباره تلاش کنید.',
            ];
        }

        return [
            'success' => true,
            'message' => 'کد تایید به شماره ' . $phone . ' ارسال شد.',
        ];
    }

    /**
     * تایید کد OTP
     * @return array{success: bool, message: string}
     */
    public function verify(string $phone, string $code, string $purpose = 'login'): array
    {
        $otp = OtpCode::where('phone', $phone)
            ->where('code', $code)
            ->where('purpose', $purpose)
            ->where('used', false)
            ->latest()
            ->first();

        if (!$otp) {
            return ['success' => false, 'message' => 'کد وارد شده صحیح نیست.'];
        }

        if ($otp->expires_at->isPast()) {
            return ['success' => false, 'message' => 'کد وارد شده منقضی شده است.'];
        }

        // مصرف کد
        $otp->update(['used' => true]);

        return ['success' => true, 'message' => 'کد تایید شد.'];
    }

    /**
     * بررسی اینکه آیا کد معتبری برای این شماره وجود دارد (بدون مصرف)
     */
    public function hasValidOtp(string $phone, string $purpose = 'login'): bool
    {
        return OtpCode::where('phone', $phone)
            ->where('purpose', $purpose)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->exists();
    }
}
