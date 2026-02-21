<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SellerController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * لیست تمام فروشندگان و درخواست‌ها
     */
    public function index(Request $request)
    {
        $query = User::query();

        // فیلتر بر اساس وضعیت
        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->where('seller_status', $status);
        } else {
            // نمایش همه به جز none
            $query->where('seller_status', '!=', 'none');
        }

        // جستجو
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        $sellers = $query->with('store')
            ->orderBy('seller_requested_at', 'desc')
            ->paginate(20);

        $stats = [
            'pending' => User::sellerPending()->count(),
            'active' => User::sellerActive()->count(),
            'suspended' => User::sellerSuspended()->count(),
            'rejected' => User::where('seller_status', 'rejected')->count(),
        ];

        return view('admin.sellers.index', compact('sellers', 'stats', 'status'));
    }

    /**
     * نمایش جزئیات فروشنده
     */
    public function show(User $seller)
    {
        $seller->load(['store', 'sellerReviews']);

        $stats = [
            'total_listings' => $seller->listings()->count(),
            'active_listings' => $seller->listings()->where('status', 'active')->count(),
            'completed_sales' => $seller->sellerOrders()->where('status', 'completed')->count(),
            'total_revenue' => $seller->sellerOrders()->where('status', 'completed')->sum('total'),
        ];

        // لیست آگهی‌ها
        $listings = $seller->listings()
            ->with(['category', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.sellers.show', compact('seller', 'stats', 'listings'));
    }

    /**
     * تایید درخواست فروشندگی
     */
    public function approve(User $seller)
    {
        if ($seller->seller_status !== 'pending') {
            return back()->with('error', 'این درخواست قابل تایید نیست.');
        }

        DB::beginTransaction();
        try {
            $data = $seller->seller_request_data;

            // تغییر نقش به seller
            $seller->update([
                'role' => 'seller',
                'seller_status' => 'active',
                'seller_approved_at' => now(),
            ]);

            // ایجاد فروشگاه اگر وجود نداره
            if (!$seller->store) {
                Store::create([
                    'user_id' => $seller->id,
                    'name' => $data['store_name'],
                    'slug' => \Str::slug($data['store_name']),
                    'description' => $data['store_description'] ?? '',
                    'phone' => $data['phone'] ?? '',
                    'address' => $data['address'] ?? null,
                ]);
            }

            DB::commit();

            return back()->with('success', 'فروشنده با موفقیت تایید شد.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'خطا در تایید فروشنده: ' . $e->getMessage());
        }
    }

    /**
     * رد درخواست فروشندگی
     */
    public function reject(Request $request, User $seller)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $seller->update([
            'seller_status' => 'rejected',
            'seller_rejection_reason' => $request->reason,
        ]);

        return back()->with('success', 'درخواست رد شد.');
    }

    /**
     * تعلیق فروشنده
     */
    public function suspend(Request $request, User $seller)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $seller->update([
            'seller_status' => 'suspended',
            'seller_rejection_reason' => $request->reason,
        ]);

        // غیرفعال کردن تمام آگهی‌های فعال
        $seller->listings()->where('status', 'active')->update([
            'status' => 'suspended',
            'suspension_reason' => 'تعلیق فروشنده توسط مدیریت',
        ]);

        return back()->with('success', 'فروشنده تعلیق شد.');
    }

    /**
     * فعال‌سازی مجدد فروشنده
     */
    public function activate(User $seller)
    {
        if (!in_array($seller->seller_status, ['suspended', 'rejected'])) {
            return back()->with('error', 'این فروشنده قابل فعال‌سازی نیست.');
        }

        DB::beginTransaction();
        try {
            $seller->update([
                'seller_status' => 'active',
                'seller_rejection_reason' => null,
            ]);

            // فعال‌سازی مجدد آگهی‌های تعلیق شده (فقط اگر قبلا به خاطر تعلیق فروشنده suspend شده بودن)
            $seller->listings()
                ->where('status', 'suspended')
                ->where('suspension_reason', 'تعلیق فروشنده توسط مدیریت')
                ->update([
                    'status' => 'active',
                    'suspension_reason' => null,
                ]);

            DB::commit();

            return back()->with('success', 'فروشنده فعال شد.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'خطا: ' . $e->getMessage());
        }
    }

    /**
     * حذف فروشنده (تبدیل به خریدار)
     */
    public function destroy(User $seller)
    {
        DB::beginTransaction();
        try {
            // بررسی آگهی‌های فعال
            $activeListings = $seller->listings()->where('status', 'active')->count();
            if ($activeListings > 0) {
                return back()->with('error', 'این فروشنده دارای آگهی فعال است. ابتدا آگهی‌ها را مدیریت کنید.');
            }

            // تبدیل به خریدار
            $seller->update([
                'role' => 'buyer',
                'seller_status' => 'none',
                'seller_requested_at' => null,
                'seller_approved_at' => null,
                'seller_rejection_reason' => null,
                'seller_request_data' => null,
            ]);

            // حذف فروشگاه
            if ($seller->store) {
                $seller->store->delete();
            }

            DB::commit();

            return redirect()->route('admin.sellers.index')
                ->with('success', 'فروشنده به خریدار تبدیل شد.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'خطا: ' . $e->getMessage());
        }
    }
}
