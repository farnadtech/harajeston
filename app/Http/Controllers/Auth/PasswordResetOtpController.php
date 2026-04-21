<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordResetOtpController extends Controller
{
    public function __construct(protected OtpService $otpService) {}

    /** فرم فراموشی رمز عبور */
    public function showForm()
    {
        return view('auth.forgot-password');
    }

    /** مرحله ۱: ارسال OTP به شماره یا ایمیل */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ], [
            'identifier.required' => 'شماره موبایل یا ایمیل الزامی است.',
        ]);

        $identifier = $request->identifier;

        // تشخیص نوع ورودی
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
        $field   = $isEmail ? 'email' : 'phone';

        $user = User::where($field, $identifier)->first();

        if (!$user) {
            return back()->withErrors([
                'identifier' => $isEmail
                    ? 'این ایمیل در سیستم ثبت نشده است.'
                    : 'این شماره موبایل در سیستم ثبت نشده است.',
            ])->withInput();
        }

        // برای ایمیل فعلاً از شماره موبایل کاربر استفاده می‌کنیم
        // (بعداً ارسال OTP ایمیل پیاده‌سازی می‌شود)
        if ($isEmail) {
            if (empty($user->phone)) {
                return back()->withErrors([
                    'identifier' => 'این حساب شماره موبایل ندارد. لطفاً از شماره موبایل استفاده کنید.',
                ])->withInput();
            }
            $phone = $user->phone;
        } else {
            $phone = $identifier;
        }

        $result = $this->otpService->send($phone, 'reset_password');

        if (!$result['success']) {
            return back()->withErrors(['identifier' => $result['message']])->withInput();
        }

        return redirect()->route('password.otp.verify.form', [
            'identifier' => $identifier,
        ])->with('success', "کد تایید به شماره {$phone} ارسال شد.");
    }

    /** فرم تایید OTP */
    public function showVerifyForm(Request $request)
    {
        $identifier = $request->query('identifier');
        if (!$identifier) {
            return redirect()->route('password.request');
        }
        return view('auth.password-reset-otp', compact('identifier'));
    }

    /** مرحله ۲: تایید OTP و نمایش فرم رمز جدید */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'code'       => 'required|digits:6',
        ], [
            'code.required' => 'کد تایید الزامی است.',
            'code.digits'   => 'کد تایید باید ۶ رقم باشد.',
        ]);

        $identifier = $request->identifier;
        $isEmail    = filter_var($identifier, FILTER_VALIDATE_EMAIL);
        $field      = $isEmail ? 'email' : 'phone';
        $user       = User::where($field, $identifier)->first();

        if (!$user) {
            return back()->withErrors(['identifier' => 'کاربر یافت نشد.']);
        }

        $phone  = $isEmail ? $user->phone : $identifier;
        $result = $this->otpService->verify($phone, $request->code, 'reset_password');

        if (!$result['success']) {
            return back()->withErrors(['code' => $result['message']])->withInput();
        }

        // ذخیره تایید در session
        session(['password_reset_verified' => $identifier]);

        return view('auth.password-reset-otp', [
            'identifier' => $identifier,
            'verified'   => true,
        ]);
    }

    /** مرحله ۳: تغییر رمز عبور */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'identifier'            => 'required|string',
            'password'              => 'required|string|min:8',
            'password_confirmation' => 'required|string|same:password',
        ], [
            'password.required'              => 'رمز عبور الزامی است.',
            'password.min'                   => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            'password_confirmation.required' => 'تکرار رمز عبور الزامی است.',
            'password_confirmation.same'     => 'رمز عبور و تکرار آن یکسان نیستند.',
        ]);

        // بررسی session تایید
        if (session('password_reset_verified') !== $request->identifier) {
            return back()->withErrors(['identifier' => 'جلسه منقضی شده. لطفاً دوباره تلاش کنید.'])->withInput();
        }

        $identifier = $request->identifier;
        $isEmail    = filter_var($identifier, FILTER_VALIDATE_EMAIL);
        $field      = $isEmail ? 'email' : 'phone';
        $user       = User::where($field, $identifier)->first();

        if (!$user) {
            return back()->withErrors(['identifier' => 'کاربر یافت نشد.'])->withInput();
        }

        $user->update(['password' => Hash::make($request->password)]);
        session()->forget('password_reset_verified');

        return redirect()->route('login')
            ->with('status', 'رمز عبور با موفقیت تغییر یافت. اکنون می‌توانید وارد شوید.');
    }
}
