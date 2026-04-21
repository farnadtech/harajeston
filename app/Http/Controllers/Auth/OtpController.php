<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpController extends Controller
{
    public function __construct(protected OtpService $otpService) {}

    // ─── Login با OTP ────────────────────────────────────────────

    /** فرم ورود با OTP */
    public function loginForm()
    {
        return view('auth.otp-login');
    }

    /** ارسال کد به شماره موبایل (مرحله اول ورود) */
    public function sendLoginOtp(Request $request)
    {
        // بررسی فعال بودن OTP
        if (!\App\Models\SiteSetting::get('otp_enabled', true)) {
            return redirect()->route('login')->with('info', 'سیستم OTP غیرفعال است.');
        }

        $request->validate([
            'phone' => ['required', 'regex:/^09[0-9]{9}$/'],
        ], [
            'phone.required' => 'شماره موبایل الزامی است.',
            'phone.regex'    => 'شماره موبایل باید ۱۱ رقمی و با ۰۹ شروع شود.',
        ]);

        $phone = $request->phone;

        // بررسی وجود کاربر
        if (!User::where('phone', $phone)->exists()) {
            return back()->withErrors(['phone' => 'این شماره موبایل در سیستم ثبت نشده است.'])->withInput();
        }

        $result = $this->otpService->send($phone, 'login');

        if (!$result['success']) {
            return back()->withErrors(['phone' => $result['message']])->withInput();
        }

        return redirect()->route('otp.login.verify.form', ['phone' => $phone])
            ->with('success', $result['message']);
    }

    /** فرم تایید کد OTP ورود */
    public function verifyLoginForm(Request $request)
    {
        $phone = $request->query('phone');
        if (!$phone) {
            return redirect()->route('otp.login');
        }
        return view('auth.otp-verify', ['phone' => $phone, 'purpose' => 'login']);
    }

    /** تایید کد و ورود کاربر */
    public function verifyLogin(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^09[0-9]{9}$/'],
            'code'  => ['required', 'digits:6'],
        ], [
            'code.required' => 'کد تایید الزامی است.',
            'code.digits'   => 'کد تایید باید ۶ رقم باشد.',
        ]);

        $result = $this->otpService->verify($request->phone, $request->code, 'login');

        if (!$result['success']) {
            return back()->withErrors(['code' => $result['message']])->withInput();
        }

        $user = User::where('phone', $request->phone)->firstOrFail();
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    // ─── Register با OTP ─────────────────────────────────────────

    /** ارسال کد تایید در حین ثبت‌نام (AJAX) */
    public function sendRegisterOtp(Request $request)
    {
        try {
            if (!\App\Models\SiteSetting::get('otp_enabled', true)) {
                return response()->json(['success' => false, 'message' => 'سیستم OTP غیرفعال است.'], 422);
            }

            $request->validate([
                'phone' => ['required', 'regex:/^09[0-9]{9}$/', 'unique:users,phone'],
            ], [
                'phone.required' => 'شماره موبایل الزامی است.',
                'phone.regex'    => 'شماره موبایل باید ۱۱ رقمی و با ۰۹ شروع شود.',
                'phone.unique'   => 'این شماره موبایل قبلاً ثبت شده است.',
            ]);

            $result = $this->otpService->send($request->phone, 'register');

            // اگر SMS ارسال شد (حتی اگر success=false از gateway) کد در DB ذخیره شده
            // پس پاپ‌آپ رو نشون بده
            if (!$result['success']) {
                // بررسی اینکه آیا کد در DB ذخیره شده (یعنی SMS رفته ولی gateway خطا داد)
                $hasOtp = \App\Models\OtpCode::where('phone', $request->phone)
                    ->where('purpose', 'register')
                    ->where('used', false)
                    ->where('expires_at', '>', now())
                    ->exists();

                if ($hasOtp) {
                    // کد ذخیره شده، پاپ‌آپ رو نشون بده
                    return response()->json(['success' => true, 'message' => 'کد تایید ارسال شد.'], 200);
                }
            }

            return response()->json($result, $result['success'] ? 200 : 422);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('sendRegisterOtp error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'خطای سرور. لطفاً دوباره تلاش کنید.'], 500);
        }
    }

    /** تکمیل ثبت‌نام پس از تایید OTP */
    public function completeRegister(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'phone'                 => ['required', 'regex:/^09[0-9]{9}$/', 'unique:users,phone'],
            'email'                 => 'required|email|max:255|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'terms'                 => 'required|accepted',
            'otp_code'              => [\App\Models\SiteSetting::get('otp_enabled', true) ? 'required' : 'nullable', 'digits:6'],
        ], [
            'name.required'         => 'نام و نام خانوادگی الزامی است.',
            'phone.required'        => 'شماره موبایل الزامی است.',
            'phone.regex'           => 'شماره موبایل باید ۱۱ رقمی و با ۰۹ شروع شود.',
            'phone.unique'          => 'این شماره موبایل قبلاً ثبت شده است.',
            'email.required'        => 'ایمیل الزامی است.',
            'email.email'           => 'فرمت ایمیل صحیح نیست.',
            'email.unique'          => 'این ایمیل قبلاً ثبت شده است.',
            'password.required'     => 'رمز عبور الزامی است.',
            'password.min'          => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
            'password.confirmed'    => 'تکرار رمز عبور مطابقت ندارد.',
            'terms.accepted'        => 'باید قوانین و مقررات را بپذیرید.',
            'otp_code.required'     => 'کد تایید پیامکی الزامی است.',
            'otp_code.digits'       => 'کد تایید باید ۶ رقم باشد.',
        ]);

        // تایید OTP فقط اگر فعال باشد
        if (\App\Models\SiteSetting::get('otp_enabled', true)) {
            $otpResult = $this->otpService->verify($validated['phone'], $validated['otp_code'] ?? '', 'register');
            if (!$otpResult['success']) {
                return back()->withErrors(['otp_code' => $otpResult['message']])->withInput();
            }
        }

        $user = User::create([
            'name'          => $validated['name'],
            'phone'         => $validated['phone'],
            'email'         => $validated['email'],
            'password'      => bcrypt($validated['password']),
            'role'          => 'buyer',
            'seller_status' => 'none',
        ]);

        Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'frozen'  => 0,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    /** ارسال مجدد کد (resend) */
    public function resend(Request $request)
    {
        $request->validate([
            'phone'   => ['required', 'regex:/^09[0-9]{9}$/'],
            'purpose' => ['required', 'in:login,register'],
        ]);

        $result = $this->otpService->send($request->phone, $request->purpose);

        if ($request->expectsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return back()->withErrors(['phone' => $result['message']]);
        }

        return back()->with('success', $result['message']);
    }
}
