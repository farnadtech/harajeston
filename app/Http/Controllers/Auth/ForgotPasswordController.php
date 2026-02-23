<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'لطفا ایمیل خود را وارد کنید.',
            'email.email' => 'فرمت ایمیل صحیح نیست.',
            'email.exists' => 'این ایمیل در سیستم ثبت نشده است.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'لینک بازیابی رمز عبور به ایمیل شما ارسال شد.')
            : back()->withErrors(['email' => 'خطا در ارسال ایمیل. لطفا دوباره تلاش کنید.']);
    }
}
