<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Services\AuctionService;
use App\Services\WalletService;

class ListingController extends Controller
{
    public function __construct(
        protected AuctionService $auctionService,
        protected WalletService $walletService
    ) {
        $this->middleware('admin');
    }

    /**
     * List all listings with admin view
     */
    public function index()
    {
        $query = Listing::with('seller', 'images');

        // Search
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('id', $search);
            });
        }

        // Filter by type
        if (request()->filled('type')) {
            $query->where('type', request('type'));
        }

        // Filter by status
        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        // Filter by seller
        if (request()->filled('seller')) {
            $query->whereHas('seller', function($q) {
                $q->where('name', 'like', '%' . request('seller') . '%');
            });
        }

        $listings = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.listings.index', compact('listings'));
    }

    /**
     * Show detailed listing view
     */
    public function show(Listing $listing)
    {
        $listing->load([
            'store.user',
            'images',
            'bids.user',
            'participations.user'
        ]);

        $activityLogs = \App\Models\AdminActionLog::where('listing_id', $listing->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.listings.show', compact('listing', 'activityLogs'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $categories = \App\Models\Category::getMenuStructure();
        return view('admin.listings.create', compact('categories'));
    }

    /**
     * Store new listing created by admin
     */
    public function store()
    {
        $validated = request()->validate([
            'seller_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    $category = \App\Models\Category::find($value);
                    if ($category && $category->hasChildren()) {
                        $fail('فقط دسته‌های نهایی (بدون زیردسته) قابل انتخاب هستند.');
                    }
                },
            ],
            'condition' => 'required|string',
            'starting_price' => 'required|numeric|min:0',
            'buy_now_price' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'bid_increment' => 'nullable|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'auto_extend' => 'nullable|boolean',
            'tags' => 'nullable|string',
            'status' => 'required|in:pending,active,suspended',
            'attributes' => 'nullable|array',
            'attributes.*' => 'nullable|string|max:255',
        ]);

        // Process tags
        if (isset($validated['tags']) && !empty($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
            $tags = array_filter($tags);
            $tags = array_slice($tags, 0, 5);
            $validated['tags'] = array_values($tags);
        } else {
            $validated['tags'] = [];
        }

        $listing = Listing::create([
            'seller_id' => $validated['seller_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'],
            'condition' => $validated['condition'],
            'starting_price' => $validated['starting_price'],
            'current_price' => $validated['starting_price'],
            'buy_now_price' => $validated['buy_now_price'] ?? null,
            'deposit_amount' => $validated['deposit_amount'] ?? 0,
            'bid_increment' => $validated['bid_increment'] ?? 10000,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'auto_extend' => $validated['auto_extend'] ?? false,
            'tags' => $validated['tags'],
            'status' => $validated['status'],
        ]);

        // ذخیره ویژگی‌ها
        if (isset($validated['attributes']) && is_array($validated['attributes'])) {
            foreach ($validated['attributes'] as $attributeId => $value) {
                if (!empty($value)) {
                    $listing->attributeValues()->create([
                        'category_attribute_id' => $attributeId,
                        'value' => $value,
                    ]);
                }
            }
        }

        \App\Models\AdminActionLog::create([
            'listing_id' => $listing->id,
            'admin_id' => auth()->id(),
            'action' => 'create',
            'description' => 'حراجی توسط ادمین ایجاد شد',
            'icon' => 'add_circle'
        ]);

        return redirect()->route('admin.listings.manage', $listing)
            ->with('success', 'حراجی با موفقیت ایجاد شد.');
    }

    /**
     * Redirect edit to manage page
     */
    public function edit(Listing $listing)
    {
        return redirect()->route('admin.listings.manage', $listing);
    }

    /**
     * Cancel listing (admin override)
     */
    public function cancel(Listing $listing)
    {
        // Release all deposits if auction
        if ($listing->type === 'auction' && $listing->status === 'active') {
            $participations = $listing->participations;
            
            foreach ($participations as $participation) {
                $this->walletService->releaseDeposit(
                    $participation->user,
                    $listing->required_deposit,
                    $listing
                );
            }
        }

        $listing->status = 'cancelled';
        $listing->save();

        return redirect()
            ->route('admin.listings.show', $listing)
            ->with('success', 'آگهی با موفقیت لغو شد.');
    }

    /**
     * Manually release deposit
     */
    public function releaseDeposit(Listing $listing, int $userId)
    {
        $user = \App\Models\User::findOrFail($userId);

        $this->walletService->releaseDeposit(
            $user,
            $listing->required_deposit,
            $listing
        );

        return redirect()
            ->route('admin.listings.show', $listing)
            ->with('success', 'سپرده با موفقیت آزاد شد.');
    }

    /**
     * Manage auction page with full controls
     */
    public function manage(Listing $listing)
    {
        $listing->load([
            'store.user',
            'images',
            'bids.user',
            'participations.user'
        ]);

        $activityLogs = \App\Models\AdminActionLog::where('listing_id', $listing->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $categories = \App\Models\Category::getMenuStructure();

        return view('admin.listings.manage', compact('listing', 'activityLogs', 'categories'));
    }

    public function updateSettings(Listing $listing)
    {
        $validated = request()->validate([
            'starting_price' => 'nullable|numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:0',
            'bid_increment' => 'nullable|numeric|min:0',
            'buy_now_price' => 'nullable|numeric|min:0',
            'deposit_amount' => 'nullable|numeric|min:0',
            'ends_at' => 'nullable|date',
            'auto_extend' => 'nullable|boolean'
        ]);

        $listing->update($validated);
        return response()->json(['success' => true]);
    }

    public function endEarly(Listing $listing)
    {
        if ($listing->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'فقط مزایده‌های فعال قابل پایان هستند']);
        }

        // First call endAuction (which checks for active status), then update
        $this->auctionService->endAuction($listing);
        
        // Refresh the listing to get updated data
        $listing->refresh();

        return response()->json(['success' => true]);
    }

    public function suspend(Listing $listing)
    {
        $listing->update([
            'status' => 'suspended',
            'suspension_reason' => request()->input('reason')
        ]);

        \App\Models\AdminActionLog::create([
            'listing_id' => $listing->id,
            'admin_id' => auth()->id(),
            'action' => 'suspend',
            'description' => request()->input('reason'),
            'icon' => 'block'
        ]);

        return response()->json(['success' => true]);
    }

    public function activate(Listing $listing)
    {
        $listing->update(['status' => 'active']);

        \App\Models\AdminActionLog::create([
            'listing_id' => $listing->id,
            'admin_id' => auth()->id(),
            'action' => 'activate',
            'description' => 'مزایده فعال شد',
            'icon' => 'check_circle'
        ]);

        return response()->json(['success' => true]);
    }

    public function updateTags(Listing $listing)
    {
        $listing->update(['tags' => request()->input('tags', [])]);
        return response()->json(['success' => true]);
    }

    public function getBids(Listing $listing)
    {
        $bids = $listing->bids()->with('user')->orderBy('created_at', 'desc')->get();
        return response()->json(['bids' => $bids]);
    }

    public function update(Listing $listing)
    {
        $validated = request()->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $category = \App\Models\Category::find($value);
                        if ($category && $category->hasChildren()) {
                            $fail('فقط دسته‌های نهایی (بدون زیردسته) قابل انتخاب هستند.');
                        }
                    }
                },
            ],
            'condition' => 'nullable|string',
            'tags' => 'nullable|string',
            'attributes' => 'nullable|array',
            'attributes.*' => 'nullable|string|max:255',
        ]);

        // Process tags: split by comma, trim, limit to 5
        if (isset($validated['tags']) && !empty($validated['tags'])) {
            $tags = array_map('trim', explode(',', $validated['tags']));
            $tags = array_filter($tags); // Remove empty values
            $tags = array_slice($tags, 0, 5); // Limit to 5 tags
            $validated['tags'] = array_values($tags); // Re-index array
        } else {
            $validated['tags'] = [];
        }

        $listing->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category_id' => $validated['category_id'] ?? null,
            'condition' => $validated['condition'] ?? null,
            'tags' => $validated['tags'],
        ]);

        // به‌روزرسانی ویژگی‌ها
        if (isset($validated['attributes']) && is_array($validated['attributes'])) {
            // حذف ویژگی‌های قبلی
            $listing->attributeValues()->delete();
            
            // اضافه کردن ویژگی‌های جدید
            foreach ($validated['attributes'] as $attributeId => $value) {
                if (!empty($value)) {
                    $listing->attributeValues()->create([
                        'category_attribute_id' => $attributeId,
                        'value' => $value,
                    ]);
                }
            }
        }

        \App\Models\AdminActionLog::create([
            'listing_id' => $listing->id,
            'admin_id' => auth()->id(),
            'action' => 'update',
            'description' => 'جزئیات آگهی به‌روزرسانی شد',
            'icon' => 'edit'
        ]);

        return response()->json(['success' => true]);
    }

    public function uploadImage(Listing $listing)
    {
        // Check if listing already has 8 images
        if ($listing->images()->count() >= 8) {
            return response()->json([
                'success' => false,
                'message' => 'حداکثر 8 تصویر برای هر محصول مجاز است'
            ], 400);
        }

        request()->validate([
            'image' => 'required|image|max:2048'
        ]);

        $file = request()->file('image');
        $path = $file->store('listings', 'public');

        $listing->images()->create([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'display_order' => $listing->images()->max('display_order') + 1
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteImage(Listing $listing, $imageId)
    {
        $image = $listing->images()->findOrFail($imageId);
        
        // Delete file from storage
        \Storage::disk('public')->delete($image->file_path);
        
        // Delete record
        $image->delete();

        return response()->json(['success' => true]);
    }
}
