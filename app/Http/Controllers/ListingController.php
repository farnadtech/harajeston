<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Http\Requests\ParticipateAuctionRequest;
use App\Models\Listing;
use App\Services\ListingService;
use App\Services\DepositService;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function __construct(
        protected ListingService $listingService,
        protected DepositService $depositService
    ) {}

    /**
     * Display a listing of listings with filters
     */
    public function index(Request $request)
    {
        // بررسی تنظیمات ادمین برای نمایش حراجی‌های pending
        $showPendingListings = \App\Models\SiteSetting::get('default_show_before_start', false);
        
        // Check if any filter is applied
        $hasFilters = $request->has('category') || $request->has('tag') || 
                     $request->has('search') || $request->has('seller_id') || 
                     $request->has('buy_now') || $request->has('sort') ||
                     $request->has('category_ids');

        // If no filters, show home page
        if (!$hasFilters) {
            $query = Listing::query();
            
            if ($showPendingListings) {
                // نمایش حراجی‌های active، ended، completed و pending که تایید شده‌اند
                $query->where(function($q) {
                    $q->whereIn('status', ['active', 'suspended', 'ended', 'completed'])
                      ->orWhere(function($q2) {
                          $q2->where('status', 'pending')->whereNotNull('approved_at');
                      });
                });
            } else {
                // نمایش active، suspended، ended، completed
                $query->whereIn('status', ['active', 'suspended', 'ended', 'completed']);
            }
            
            $listings = $query->with('seller', 'images')
                ->withCount('bids')
                ->orderBy('ends_at', 'asc')
                ->get(); // همه رو می‌گیریم، فیلتر در view انجام میشه

            return view('listings.index', compact('listings'));
        }

        // Apply filters for search results
        $query = Listing::query();
        
        if ($showPendingListings) {
            // نمایش active, completed, pending که تایید شده‌اند، و suspended
            $query->where(function($q) {
                $q->whereIn('status', ['active', 'completed', 'suspended'])
                  ->orWhere(function($q2) {
                      $q2->where('status', 'pending')->whereNotNull('approved_at');
                  });
            });
        } else {
            // نمایش active, completed, و suspended
            $query->whereIn('status', ['active', 'completed', 'suspended']);
        }

        // Filter by category (single slug یا multiple IDs)
        if ($request->has('category_ids') && is_array($request->category_ids)) {
            // چند دسته‌بندی با ID
            $multiCatIds = array_map('intval', $request->category_ids);
            $allCatIds = collect($multiCatIds);
            foreach ($multiCatIds as $catId) {
                $children = \App\Models\Category::where('parent_id', $catId)->pluck('id');
                $allCatIds = $allCatIds->merge($children);
                foreach ($children as $childId) {
                    $grandChildren = \App\Models\Category::where('parent_id', $childId)->pluck('id');
                    $allCatIds = $allCatIds->merge($grandChildren);
                }
            }
            $query->whereIn('category_id', $allCatIds->unique()->values());
        } elseif ($request->has('category') && $request->category) {
            $category = \App\Models\Category::where('slug', $request->category)->first();
            if ($category) {
                // جمع‌آوری تمام ID های دسته و زیردسته‌ها (تا سطح 3)
                $categoryIds = collect([$category->id]);
                
                if ($category->parent_id === null) {
                    $level2Children = $category->children()->pluck('id');
                    $categoryIds = $categoryIds->merge($level2Children);
                    foreach ($level2Children as $level2Id) {
                        $level3Children = \App\Models\Category::where('parent_id', $level2Id)->pluck('id');
                        $categoryIds = $categoryIds->merge($level3Children);
                    }
                } elseif ($category->parent_id !== null && $category->children()->count() > 0) {
                    $level3Children = $category->children()->pluck('id');
                    $categoryIds = $categoryIds->merge($level3Children);
                }
                
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Filter by tag
        if ($request->has('tag') && $request->tag) {
            $tag = trim($request->tag);
            // تگ‌ها به صورت Unicode escaped در دیتابیس ذخیره می‌شن
            // باید با PHP فیلتر کنیم
            $query->whereRaw("1=1"); // placeholder - will filter in PHP
            $listings = $query->with('seller', 'images')
                ->withCount('bids')
                ->get()
                ->filter(function($listing) use ($tag) {
                    $tags = $listing->tags ?? [];
                    if (is_string($tags)) {
                        $tags = json_decode($tags, true) ?? [];
                    }
                    return in_array($tag, $tags);
                });
            
            // Paginate manually
            $page = $request->get('page', 1);
            $perPage = 20;
            $total = $listings->count();
            $items = $listings->slice(($page - 1) * $perPage, $perPage)->values();
            
            $listings = new \Illuminate\Pagination\LengthAwarePaginator(
                $items, $total, $perPage, $page,
                ['path' => $request->url(), 'query' => $request->except('page')]
            );
            
            // Get available attributes
            $availableAttributes = collect();
            if ($items->isNotEmpty()) {
                $catIds = $items->pluck('category_id')->unique()->values()->toArray();
                if (!empty($catIds)) {
                    // همه ID های مرتبط: خود + والدها + فرزندها
                    $allAttrCatIds = collect($catIds);
                    foreach ($catIds as $cid) {
                        $cat = \App\Models\Category::find($cid);
                        if ($cat && $cat->parent_id) {
                            $allAttrCatIds->push($cat->parent_id);
                            $parent = \App\Models\Category::find($cat->parent_id);
                            if ($parent && $parent->parent_id) {
                                $allAttrCatIds->push($parent->parent_id);
                            }
                        }
                        $children = \App\Models\Category::where('parent_id', $cid)->pluck('id');
                        $allAttrCatIds = $allAttrCatIds->merge($children);
                        foreach ($children as $childId) {
                            $allAttrCatIds = $allAttrCatIds->merge(
                                \App\Models\Category::where('parent_id', $childId)->pluck('id')
                            );
                        }
                    }
                    $availableAttributes = \App\Models\CategoryAttribute::whereIn('category_id', $allAttrCatIds->unique()->values()->toArray())
                        ->where('is_filterable', true)->orderBy('order')->get();
                }
            }
            
            return view('listings.search', compact('listings', 'request', 'availableAttributes'));
        }

        // Filter by seller
        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        // Filter by buy now availability
        if ($request->has('buy_now') && $request->buy_now) {
            $query->whereNotNull('buy_now_price')->where('buy_now_price', '>', 0);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by attributes
        if ($request->has('attr') && is_array($request->attr)) {
            foreach ($request->attr as $attributeId => $value) {
                if (!empty($value)) {
                    if (is_array($value)) {
                        // Range filter (for numbers)
                        if (!empty($value['min']) || !empty($value['max'])) {
                            $query->whereHas('attributeValues', function ($q) use ($attributeId, $value) {
                                $q->where('category_attribute_id', $attributeId);
                                if (!empty($value['min'])) {
                                    $q->where('value', '>=', $value['min']);
                                }
                                if (!empty($value['max'])) {
                                    $q->where('value', '<=', $value['max']);
                                }
                            });
                        }
                    } else {
                        // Exact match filter
                        $query->whereHas('attributeValues', function ($q) use ($attributeId, $value) {
                            $q->where('category_attribute_id', $attributeId)
                              ->where('value', $value);
                        });
                    }
                }
            }
        }

        // Sorting - active اول، بعد ended/completed، بعد بقیه
        // آگهی‌های active که ends_at گذشته مثل ended رفتار می‌کنن
        $activeFirst = "CASE 
            WHEN status = 'active' AND (ends_at IS NULL OR ends_at > NOW()) THEN 0 
            WHEN status IN ('ended','completed') OR (status = 'active' AND ends_at <= NOW()) THEN 1 
            ELSE 2 
        END";
        $sort = $request->get('sort', 'ending_soon');
        switch ($sort) {
            case 'starting_soon':
                $query->orderByRaw("CASE 
                    WHEN status = 'pending' THEN 0 
                    WHEN status = 'active' THEN 1 
                    ELSE 2 
                END")
                      ->orderBy('starts_at', 'asc')
                      ->orderBy('ends_at', 'asc');
                break;
            case 'ending_soon':
                $query->orderByRaw($activeFirst)->orderBy('ends_at', 'asc');
                break;
            case 'newest':
                $query->orderByRaw($activeFirst)->orderBy('created_at', 'desc');
                break;
            case 'most_bids':
                $query->orderByRaw($activeFirst)->orderBy('bids_count', 'desc');
                break;
            case 'highest_price':
            case 'price_high':
                $query->orderByRaw($activeFirst)
                      ->orderByRaw('COALESCE((SELECT MAX(amount) FROM bids WHERE bids.listing_id = listings.id), starting_price) DESC');
                break;
            case 'lowest_price':
            case 'price_low':
                $query->orderByRaw($activeFirst)
                      ->orderByRaw('COALESCE((SELECT MAX(amount) FROM bids WHERE bids.listing_id = listings.id), starting_price) ASC');
                break;
            default:
                $query->orderByRaw($activeFirst)->orderBy('ends_at', 'asc');
                break;
        }

        $listings = $query->with('seller', 'images')
            ->withCount('bids')
            ->paginate(20)
            ->appends($request->except('page'));

        // Get available attributes for filtering
        $availableAttributes = collect();

        // تابع کمکی برای گرفتن همه ID های مرتبط (خود + والدها + فرزندها)
        $getAllRelatedCatIds = function(array $ids) {
            $all = collect($ids);
            foreach ($ids as $id) {
                // والدها
                $cat = \App\Models\Category::find($id);
                if ($cat && $cat->parent_id) {
                    $all->push($cat->parent_id);
                    $parent = \App\Models\Category::find($cat->parent_id);
                    if ($parent && $parent->parent_id) {
                        $all->push($parent->parent_id);
                    }
                }
                // فرزندها
                $children = \App\Models\Category::where('parent_id', $id)->pluck('id')->toArray();
                $all = $all->merge($children);
                foreach ($children as $childId) {
                    $grandChildren = \App\Models\Category::where('parent_id', $childId)->pluck('id')->toArray();
                    $all = $all->merge($grandChildren);
                }
            }
            return $all->unique()->values()->toArray();
        };

        // تابع کمکی برای گرفتن فقط زیردسته‌ها (بدون والد)
        $getAllCatIds = function(array $ids) use ($getAllRelatedCatIds) {
            return $getAllRelatedCatIds($ids);
        };

        if ($request->has('category_ids') && is_array($request->category_ids)) {
            $multiCatIds = array_map('intval', $request->category_ids);
            $allIds = $getAllRelatedCatIds($multiCatIds);
            $availableAttributes = \App\Models\CategoryAttribute::whereIn('category_id', $allIds)
                ->where('is_filterable', true)->orderBy('order')->get();
        } elseif ($request->has('category') && $request->category && isset($category)) {
            $allIds = $getAllRelatedCatIds([$category->id]);
            $availableAttributes = \App\Models\CategoryAttribute::whereIn('category_id', $allIds)
                ->where('is_filterable', true)->orderBy('order')->get();
        } elseif ($listings->isNotEmpty()) {
            $listingItems = method_exists($listings, 'getCollection') ? $listings->getCollection() : $listings;
            $catIds = $listingItems->pluck('category_id')->unique()->values()->toArray();
            if (!empty($catIds)) {
                $allIds = $getAllRelatedCatIds($catIds);
                $availableAttributes = \App\Models\CategoryAttribute::whereIn('category_id', $allIds)
                    ->where('is_filterable', true)->orderBy('order')->get();
            }
        }

        // Return search results view
        return view('listings.search', compact('listings', 'request', 'availableAttributes'));
    }
    /**
     * Show seller's own listings
     */
    public function myListings(Request $request)
    {
        $user = auth()->user();

        // Get status filter
        $status = $request->get('status', 'all');

        // Build query
        $query = Listing::where('seller_id', $user->id);

        // Apply status filter
        if ($status === 'needs_approval') {
            // آگهی‌هایی که واقعاً منتظر تایید ادمین هستند
            $query->where('status', 'pending')->whereNull('approved_at');
        } elseif ($status === 'pending') {
            // آگهی‌هایی که تایید شده‌اند ولی هنوز شروع نشده‌اند
            $query->where('status', 'pending')->whereNotNull('approved_at');
        } elseif ($status !== 'all') {
            $query->where('status', $status);
        }

        // Get counts for each status
        $counts = [
            'all' => Listing::where('seller_id', $user->id)->count(),
            'active' => Listing::where('seller_id', $user->id)->where('status', 'active')->count(),
            'needs_approval' => Listing::where('seller_id', $user->id)->where('status', 'pending')->whereNull('approved_at')->count(),
            'pending_start' => Listing::where('seller_id', $user->id)->where('status', 'pending')->whereNotNull('approved_at')->count(),
            'completed' => Listing::where('seller_id', $user->id)->where('status', 'completed')->count(),
            'rejected' => Listing::where('seller_id', $user->id)->where('status', 'rejected')->count(),
        ];

        // Get listings with pagination
        $listings = $query->with(['category', 'images'])
            ->withCount('bids')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->except('page'));

        return view('listings.my-listings', compact('listings', 'counts'));
    }

    /**
     * Display listings where user has placed bids
     */
    public function myBids(Request $request)
    {
        $user = auth()->user();

        // Get status filter
        $status = $request->get('status', 'all');

        // Build query - get listings where user has bids
        $query = Listing::whereHas('bids', function($q) use ($user) {
            $q->where('user_id', $user->id);
        });

        // Apply status filter
        if ($status !== 'all') {
            if ($status === 'completed') {
                // تمام شده شامل هم ended و هم completed
                $query->whereIn('status', ['ended', 'completed']);
            } else {
                $query->where('status', $status);
            }
        }

        // Get counts for each status
        $counts = [
            'all' => Listing::whereHas('bids', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'active' => Listing::whereHas('bids', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('status', 'active')->count(),
            'completed' => Listing::whereHas('bids', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->whereIn('status', ['ended', 'completed'])->count(),
        ];

        // Get listings with user's bid info
        $listings = $query->with(['category', 'images', 'seller'])
            ->withCount('bids')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->appends($request->except('page'));

        // Add user's bid and current highest bid to each listing
        foreach ($listings as $listing) {
            $listing->my_bid = $listing->bids()->where('user_id', $user->id)->orderBy('amount', 'desc')->first();
            $listing->current_highest_bid = $listing->bids()->max('amount') ?? $listing->starting_price;
        }

        return view('listings.my-bids', compact('listings', 'counts'));
    }



    /**
     * Show the form for creating a new listing
     */
    public function create()
    {
        // بررسی احراز هویت
        if (!auth()->user()->isVerified()) {
            return redirect()->route('dashboard')
                ->with('error', 'برای ایجاد حراجی باید ابتدا شماره تلفن یا ایمیل خود را تایید کنید.');
        }
        if (!auth()->user()->isSeller()) {
            return redirect()->route('dashboard')
                ->with('error', 'فقط فروشندگان می‌توانند حراجی ایجاد کنند.');
        }

        // بررسی وضعیت فروشنده
        if (!auth()->user()->isSellerActive()) {
            return redirect()->route('dashboard')
                ->with('error', 'حساب فروشندگی شما هنوز تایید نشده است.');
        }

        return view('listings.create');
    }

    /**
     * Store a newly created listing
     */
    public function store(CreateListingRequest $request)
    {
        $listing = $this->listingService->createListing(
            auth()->user(),
            $request->validated()
        );

        $requiresApproval = \App\Models\SiteSetting::get('require_listing_approval', false);
        
        $message = $requiresApproval 
            ? 'آگهی با موفقیت ایجاد شد و برای تایید ادمین ارسال گردید.'
            : 'آگهی با موفقیت ایجاد شد.';

        return redirect()
            ->route('listings.show', $listing)
            ->with('success', $message);
    }

    /**
     * Display the specified listing
     */
    public function show(Listing $listing)
    {
        // بررسی دسترسی برای آگهی‌های pending (هنوز شروع نشده)
        if ($listing->status === 'pending') {
            // اگر approved_at خالیه، یعنی منتظر تایید ادمینه
            if (!$listing->approved_at) {
                // فقط ادمین و صاحب آگهی می‌تونن ببینن
                if (!auth()->check() || 
                    (auth()->user()->role !== 'admin' && auth()->id() !== $listing->seller_id)) {
                    abort(404);
                }
            } else {
                // تایید شده ولی هنوز شروع نشده - بررسی تنظیمات ادمین
                $showPendingListings = \App\Models\SiteSetting::get('default_show_before_start', false);
                
                if (!$showPendingListings) {
                    // اگر تنظیمات ادمین غیرفعال است، فقط ادمین و صاحب آگهی می‌تونن ببینن
                    if (!auth()->check() || 
                        (auth()->user()->role !== 'admin' && auth()->id() !== $listing->seller_id)) {
                        abort(404);
                    }
                }
                // اگر تنظیمات ادمین فعال است، همه می‌تونن ببینن
            }
        }

        // Increment view count (فقط برای آگهی‌های فعال)
        if ($listing->status === 'active') {
            $listing->increment('views');
        }
        
        $listing->load([
            'seller' => function($query) {
                $query->withCount(['sellerOrders as successful_sales' => function($q) {
                    $q->where('status', 'delivered');
                }]);
            },
            'seller.store',
            'images', 
            'bids.user', 
            'shippingMethods', 
            'participations', 
            'attributeValues.attribute',
            'comments' => function($query) {
                $query->approved()
                      ->parentOnly()
                      ->with(['user', 'replies' => function($q) {
                          $q->approved()->with('user');
                      }])
                      ->latest();
            }
        ]);

        // Calculate amounts for finalization modal
        $totalAmount = null;
        $depositAmount = null;
        $remainingAmount = null;
        
        if ($listing->status === 'ended' && auth()->check() && $listing->current_winner_id === auth()->id()) {
            $winningBid = $listing->bids()
                ->where('user_id', auth()->id())
                ->orderBy('amount', 'desc')
                ->first();
            
            if ($winningBid) {
                $totalAmount = $winningBid->amount;
                $depositAmount = $listing->required_deposit;
                $remainingAmount = $totalAmount - $depositAmount;
            }
        }

        return view('listings.show', compact('listing', 'totalAmount', 'depositAmount', 'remainingAmount'));
    }

    /**
     * Show the form for editing the specified listing
     */
    public function edit(Listing $listing)
    {
        $this->authorize('update', $listing);

        // Load relationships with fresh data
        $listing->load([
            'category.attributes', 
            'shippingMethods'
        ]);
        
        // Force reload attributeValues
        $listing->load('attributeValues');

        // Get site settings for auction duration
        $forceDuration = \App\Models\SiteSetting::get('force_auction_duration', false);
        $durationDays = \App\Models\SiteSetting::get('auction_duration_days', 7);

        return view('listings.edit', compact('listing', 'forceDuration', 'durationDays'));
    }

    /**
     * Update the specified listing
     */
    public function update(UpdateListingRequest $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        $requiresApproval = \App\Models\SiteSetting::get('require_listing_approval', false);
        $hasActiveBids = $listing->hasActiveBids();
        $wasSuspended = $listing->status === 'suspended';
        $wasActive = $listing->status === 'active';
        
        $this->listingService->updateListing($listing, $request->validated());

        // Determine message based on what happened
        if ($wasActive && $hasActiveBids) {
            $message = 'فقط توضیحات و روش‌های ارسال به‌روزرسانی شدند. سایر فیلدها به دلیل وجود پیشنهاد فعال قابل تغییر نیستند.';
        } elseif ($requiresApproval && !auth()->user()->isAdmin() && ($wasActive || $listing->status === 'pending')) {
            $message = 'تغییرات شما ثبت شد و برای تایید ادمین ارسال گردید. آگهی فعلی بدون تغییر باقی می‌ماند تا ادمین تغییرات را تایید کند.';
        } elseif ($wasSuspended) {
            $message = 'آگهی با موفقیت به‌روزرسانی شد و برای تایید مجدد ارسال گردید.';
        } else {
            $message = 'آگهی با موفقیت به‌روزرسانی شد.';
        }

        return redirect()
            ->route('listings.show', $listing)
            ->with('success', $message);
    }

    /**
     * Participate in auction (pay deposit) or buy now
     */
    public function participate(ParticipateAuctionRequest $request, Listing $listing)
    {
        // بررسی احراز هویت
        if (!auth()->user()->isVerified()) {
            return redirect()->route('listings.show', $listing)
                ->with('error', 'برای شرکت در حراجی باید ابتدا شماره تلفن یا ایمیل خود را تایید کنید. به داشبورد خود مراجعه کنید.');
        }

        // Check if this is a buy now request
        if ($request->has('buy_now') && $request->buy_now == 1) {
            // Validate buy now is available
            if (!$listing->buy_now_price || $listing->buy_now_price <= 0) {
                return redirect()
                    ->route('listings.show', $listing)
                    ->with('error', 'خرید فوری برای این حراجی فعال نیست.');
            }

            // Check if listing is active
            if (!$listing->isActive()) {
                return redirect()
                    ->route('listings.show', $listing)
                    ->with('error', 'این حراجی فعال نیست.');
            }
            
            // Validate shipping method
            $shippingMethodId = $request->input('shipping_method_id');
            if (!$shippingMethodId) {
                return redirect()
                    ->route('listings.show', $listing)
                    ->with('error', 'لطفا روش ارسال را انتخاب کنید.');
            }
            
            // Get shipping method with pivot data from listing
            $shippingMethod = $listing->shippingMethods()
                ->where('shipping_method_id', $shippingMethodId)
                ->first();
                
            if (!$shippingMethod) {
                return redirect()
                    ->route('listings.show', $listing)
                    ->with('error', 'روش ارسال انتخاب شده معتبر نیست.');
            }

            try {
                $order = \DB::transaction(function() use ($listing, $shippingMethod, $shippingMethodId) {
                    // Check if user has already participated (has deposit blocked)
                    $participation = \App\Models\AuctionParticipation::where('listing_id', $listing->id)
                        ->where('user_id', auth()->id())
                        ->where('deposit_status', 'paid')
                        ->first();
                    
                    $wallet = auth()->user()->wallet;
                    $buyNowPrice = $listing->buy_now_price;
                    
                    // Calculate shipping cost from base_cost + custom_cost_adjustment
                    $shippingCost = $shippingMethod->base_cost + ($shippingMethod->pivot->custom_cost_adjustment ?? 0);
                    $totalAmount = $buyNowPrice + $shippingCost;
                    
                    if ($participation) {
                        // User has already bid, so deposit is blocked
                        // Unfreeze the deposit and freeze the full amount (buy_now + shipping)
                        $depositAmount = $participation->deposit_amount;
                        $amountToPay = $totalAmount - $depositAmount;
                        
                        // Check if user has enough balance for the difference
                        if (!$wallet || $wallet->balance < $amountToPay) {
                            throw new \Exception('موجودی کیف پول شما برای خرید فوری کافی نیست. مبلغ مورد نیاز: ' . number_format($amountToPay) . ' تومان (اختلاف قیمت خرید فوری + هزینه ارسال و سپرده بلاک شده)');
                        }
                        
                        // Unfreeze the deposit first - safe check
                        $actualFrozen = min($depositAmount, max(0, $wallet->frozen));
                        $wallet->frozen -= $actualFrozen;
                        $wallet->balance += $actualFrozen;
                        
                        // Now freeze the full amount (buy_now + shipping)
                        $wallet->balance -= $totalAmount;
                        $wallet->frozen += $totalAmount;
                        $wallet->save();
                        
                        // Record unfreeze transaction
                        \App\Models\WalletTransaction::create([
                            'wallet_id' => $wallet->id,
                            'user_id' => auth()->id(),
                            'type' => 'release_deposit',
                            'amount' => $depositAmount,
                            'final_amount' => $depositAmount,
                            'balance_before' => $wallet->balance + $totalAmount - $depositAmount,
                            'balance_after' => $wallet->balance + $totalAmount,
                            'frozen_before' => $wallet->frozen - $totalAmount + $depositAmount,
                            'frozen_after' => $wallet->frozen - $totalAmount,
                            'reference_type' => \App\Models\Listing::class,
                            'reference_id' => $listing->id,
                            'status' => 'completed',
                            'description' => sprintf('آزادسازی سپرده برای خرید فوری: %s', $listing->title),
                        ]);
                        
                        // Record freeze transaction for buy now + shipping
                        \App\Models\WalletTransaction::create([
                            'wallet_id' => $wallet->id,
                            'user_id' => auth()->id(),
                            'type' => 'freeze_deposit',
                            'amount' => $totalAmount,
                            'final_amount' => $totalAmount,
                            'balance_before' => $wallet->balance + $totalAmount,
                            'balance_after' => $wallet->balance,
                            'frozen_before' => $wallet->frozen - $totalAmount,
                            'frozen_after' => $wallet->frozen,
                            'reference_type' => \App\Models\Listing::class,
                            'reference_id' => $listing->id,
                            'status' => 'completed',
                            'description' => sprintf('بلاک مبلغ خرید فوری + هزینه ارسال: %s', $listing->title),
                        ]);
                    } else {
                        // User hasn't bid yet, charge full amount (buy_now + shipping)
                        if (!$wallet || $wallet->balance < $totalAmount) {
                            throw new \Exception('موجودی کیف پول شما برای خرید فوری کافی نیست. مبلغ مورد نیاز: ' . number_format($totalAmount) . ' تومان (قیمت خرید فوری + هزینه ارسال)');
                        }
                        
                        // Freeze the full amount
                        $wallet->balance -= $totalAmount;
                        $wallet->frozen += $totalAmount;
                        $wallet->save();
                        
                        \App\Models\WalletTransaction::create([
                            'wallet_id' => $wallet->id,
                            'user_id' => auth()->id(),
                            'type' => 'freeze_deposit',
                            'amount' => $totalAmount,
                            'final_amount' => $totalAmount,
                            'balance_before' => $wallet->balance + $totalAmount,
                            'balance_after' => $wallet->balance,
                            'frozen_before' => $wallet->frozen - $totalAmount,
                            'frozen_after' => $wallet->frozen,
                            'reference_type' => \App\Models\Listing::class,
                            'reference_id' => $listing->id,
                            'status' => 'completed',
                            'description' => sprintf('بلاک مبلغ خرید فوری + هزینه ارسال: %s', $listing->title),
                        ]);
                    }

                    // Create order with pending status (waiting for shipping address)
                    $order = \App\Models\Order::create([
                        'order_number' => 'ORD-' . strtoupper(uniqid()),
                        'buyer_id' => auth()->id(),
                        'seller_id' => $listing->seller_id,
                        'status' => 'pending',
                        'subtotal' => $buyNowPrice,
                        'shipping_cost' => $shippingCost,
                        'total' => $totalAmount,
                        'shipping_method_id' => $shippingMethodId,
                    ]);

                    // Create order item
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'listing_id' => $listing->id,
                        'quantity' => 1,
                        'price_snapshot' => $buyNowPrice,
                        'subtotal' => $buyNowPrice,
                    ]);

                    // Update listing status
                    $listing->status = 'completed';
                    $listing->current_winner_id = auth()->id();
                    $listing->save();

                    return $order;
                });

                // Send notifications (outside transaction)
                try {
                    // Notify buyer - custom notification
                    \App\Models\Notification::create([
                        'user_id' => auth()->id(),
                        'type' => 'order_placed',
                        'title' => 'خرید فوری موفق',
                        'message' => sprintf('خرید فوری "%s" با موفقیت انجام شد. لطفاً آدرس ارسال را وارد کنید.', $listing->title),
                        'icon' => 'shopping_bag',
                        'color' => 'green',
                        'link' => route('orders.show', $order->id),
                        'is_read' => false,
                    ]);
                    
                    // Notify seller about buy now - custom notification
                    \App\Models\Notification::create([
                        'user_id' => $listing->seller_id,
                        'type' => 'buy_now_completed',
                        'title' => 'خرید فوری انجام شد',
                        'message' => sprintf('حراجی "%s" با خرید فوری به پایان رسید. خریدار در حال وارد کردن آدرس ارسال است.', $listing->title),
                        'icon' => 'local_offer',
                        'color' => 'orange',
                        'link' => route('orders.show', $order->id),
                        'is_read' => false,
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Failed to send notification: ' . $e->getMessage());
                }
                
                return redirect()
                    ->route('orders.show', $order)
                    ->with('success', 'خرید فوری با موفقیت انجام شد. لطفا آدرس ارسال را وارد کنید.');
                    
            } catch (\Exception $e) {
                \Log::error('Buy now error: ' . $e->getMessage());
                return redirect()
                    ->route('listings.show', $listing)
                    ->with('error', $e->getMessage());
            }
        }

        // Regular auction participation (deposit)
        try {
            $this->depositService->participateInAuction(auth()->user(), $listing);

            return redirect()
                ->route('listings.show', $listing)
                ->with('success', 'شما با موفقیت در مزایده شرکت کردید.');
        } catch (\Exception $e) {
            return redirect()
                ->route('listings.show', $listing)
                ->with('error', 'خطا در شرکت در مزایده: ' . $e->getMessage());
        }
    }

    /**
     * Finalize auction - winner pays and order is created
     */
    public function finalize(Request $request, Listing $listing)
    {
        try {
            $auctionService = app(\App\Services\AuctionService::class);
            $order = $auctionService->finalizeAuction($listing, auth()->user());
            
            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'خرید با موفقیت تکمیل شد! سفارش شما ثبت گردید.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
