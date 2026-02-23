<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * نمایش فرم درخواست فروشندگی
     */
    public function create()
    {
        $user = auth()->user();

        // اگر قبلا درخواست داده یا فروشنده است
        if ($user->hasRequestedSeller()) {
            return redirect()->route('dashboard')
                ->with('info', 'شما قبلاً درخواست فروشندگی داده‌اید.');
        }

        return view('seller-request.create');
    }

    /**
     * ثبت درخواست فروشندگی
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // بررسی اینکه قبلا درخواست نداده باشد
        if ($user->hasRequestedSeller()) {
            return redirect()->route('dashboard')
                ->with('error', 'شما قبلاً درخواست فروشندگی داده‌اید.');
        }

        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_description' => 'required|string|max:1000',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'national_id' => [
                'required',
                'string',
                'max:10',
                'min:10',
                function ($attribute, $value, $fail) {
                    // Check if national_id already exists in seller_request_data
                    $exists = \App\Models\User::whereNotNull('seller_request_data')
                        ->where('seller_request_data->national_id', $value)
                        ->exists();
                    
                    if ($exists) {
                        $fail('این کد ملی قبلاً ثبت شده است.');
                    }
                },
            ],
            'bank_account' => 'required|string|max:50',
            'bank_name' => 'required|string|max:100',
        ], [
            'store_name.required' => 'نام فروشگاه الزامی است.',
            'store_description.required' => 'توضیحات فروشگاه الزامی است.',
            'phone.required' => 'شماره تلفن الزامی است.',
            'national_id.required' => 'کد ملی الزامی است.',
            'national_id.min' => 'کد ملی باید ۱۰ رقم باشد.',
            'national_id.max' => 'کد ملی باید ۱۰ رقم باشد.',
            'bank_account.required' => 'شماره حساب الزامی است.',
            'bank_name.required' => 'نام بانک الزامی است.',
        ]);

        DB::beginTransaction();
        try {
            // ذخیره اطلاعات درخواست
            $user->update([
                'seller_status' => 'pending',
                'seller_requested_at' => now(),
                'seller_request_data' => $validated,
            ]);

            // بررسی تنظیمات: آیا نیاز به تایید دستی است؟
            $requireApproval = SiteSetting::get('require_seller_approval', true);

            if (!$requireApproval) {
                // تایید خودکار
                $this->approveSeller($user, $validated);
            }

            DB::commit();

            if ($requireApproval) {
                return redirect()->route('dashboard')
                    ->with('success', 'درخواست شما با موفقیت ثبت شد و در انتظار تایید مدیریت است.');
            } else {
                return redirect()->route('dashboard')
                    ->with('success', 'تبریک! حساب فروشندگی شما فعال شد.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'خطا در ثبت درخواست: ' . $e->getMessage());
        }
    }

    /**
     * تایید خودکار فروشنده
     */
    protected function approveSeller($user, $data)
    {
        // تغییر نقش به seller
        $user->update([
            'role' => 'seller',
            'seller_status' => 'active',
            'seller_approved_at' => now(),
        ]);

        // ایجاد فروشگاه
        Store::create([
            'user_id' => $user->id,
            'store_name' => $data['store_name'],
            'slug' => \Str::slug($data['store_name']),
            'description' => $data['store_description'],
        ]);
    }

    /**
     * نمایش وضعیت درخواست
     */
    public function status()
    {
        $user = auth()->user();

        if (!$user->hasRequestedSeller()) {
            return redirect()->route('seller-request.create');
        }

        return view('seller-request.status', compact('user'));
    }
}
