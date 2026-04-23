<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhoneVerificationController extends Controller
{
    public function __construct(protected OtpService $otpService) {}

    public function send(Request $request)
    {
        $user = Auth::user();
        if ($user->phone_verified_at) {
            return response()->json(['success' => false, 'message' => 'شماره تلفن قبلاً تایید شده است.']);
        }
        if (!$user->phone) {
            return response()->json(['success' => false, 'message' => 'شماره تلفنی ثبت نشده است.']);
        }
        $result = $this->otpService->send($user->phone, 'phone_verify');
        if (!$result['success']) {
            $hasOtp = \App\Models\OtpCode::where('phone', $user->phone)->where('purpose', 'phone_verify')->where('used', false)->where('expires_at', '>', now())->exists();
            if ($hasOtp) return response()->json(['success' => true, 'message' => 'کد تایید ارسال شد.']);
        }
        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function verify(Request $request)
    {
        $request->validate(['phone' => 'required', 'code' => 'required|digits:6']);
        $user = Auth::user();
        if ($user->phone !== $request->phone) {
            return response()->json(['success' => false, 'message' => 'شماره تلفن معتبر نیست.']);
        }
        $result = $this->otpService->verify($request->phone, $request->code, 'phone_verify');
        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => 'کد نادرست یا منقضی شده است.']);
        }
        $user->update(['phone_verified_at' => now()]);
        Auth::setUser($user->fresh());
        return response()->json(['success' => true, 'message' => 'شماره تلفن با موفقیت تایید شد.']);
    }
}