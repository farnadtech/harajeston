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
                     $request->has('buy_now') || $request->has('sort');

        // If no filters, show home page
        if (!$hasFilters) {
            $query = Listing::query();
            
            if ($showPendingListings) {
                // نمایش حراجی‌های active, pending, و suspended
                $query->whereIn('status', ['active', 'pending', 'suspended']);
            } else {
                // نمایش active و suspended
                $query->whereIn('status', ['active', 'suspended']);
            }
            
            $listings = $query->with('seller', 'images')
                ->orderBy('ends_at', 'asc')
                ->paginate(20);
            
            return view('listings.index', compact('listings'));
        }

        // Apply filters for search results
        $query = Listing::query();
        
        if ($showPendingListings) {
            // نمایش active, completed, pending, و suspended
            $query->whereIn('status', ['active', 'completed', 'pending', 'suspended']);
        } else {
            // نمایش active, completed, و suspended
            $query->whereIn('status', ['active', 'completed', 'suspended']);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $category = \App\Models\Category::where('slug', $request->category)->first();
            if ($category) {
                // جمع‌آوری تمام ID های دسته و زیردسته‌ها (تا سطح 3)
                $categoryIds = collect([$category->id]);
                
                // اگر دسته سطح 1 است، همه فرزندان سطح 2 و 3 را اضافه کن
                if ($category->parent_id === null) {
                    $level2Children = $category->children()->pluck('id');
                    $categoryIds = $categoryIds->merge($level2Children);
                    
                    // برای هر فرزند سطح 2، فرزندان سطح 3 را هم اضافه کن
                    foreach ($level2Children as $level2Id) {
                        $level3Children = \App\Models\Category::where('parent_id', $level2Id)->pluck('id');
                        $categoryIds = $categoryIds->merge($level3Children);
                    }
                }
                // اگر دسته سطح 2 است، فرزندان سطح 3 را اضافه کن
                elseif ($category->parent_id !== null && $category->children()->count() > 0) {
                    $level3Children = $category->children()->pluck('id');
                    $categoryIds = $categoryIds->merge($level3Children);
                }
                
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Filter by tag
        if ($request->has('tag') && $request->tag) {
            $tag = trim($request->tag);
            $query->where(function($q) use ($tag) {
                $q->whereJsonContains('tags', $tag)
                  ->orWhereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", [$tag]);
            });
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

        // Sorting - pending first (starting soon), then active (ending soon), then completed
        $sort = $request->get('sort', 'ending_soon');
        switch ($sort) {
            case 'starting_soon':
                // حراجی‌های pending که زودتر شروع می‌شوند اول، بعد active که زودتر تمام می‌شوند
                $query->orderByRaw("CASE 
                    WHEN status = 'pending' THEN 0 
                    WHEN status = 'active' THEN 1 
                    ELSE 2 
                END")
                      ->orderBy('starts_at', 'asc')
                      ->orderBy('ends_at', 'asc');
                break;
            case 'ending_soon':
                $query->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                      ->orderBy('ends_at', 'asc');
                break;
            case 'price_low':
                $query->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                      ->orderBy('starting_price', 'asc');
                break;
            case 'price_high':
                $query->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                      ->orderBy('starting_price', 'desc');
                break;
            case 'newest':
                $query->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                      ->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
                      ->orderBy('ends_at', 'asc');
                break;
        }

        $listings = $query->with('seller', 'images')
            ->paginate(20)
            ->appends($request->except('page'));

        // Get available attributes for filtering (if category is selected)
        $availableAttributes = [];
        if ($request->has('category') && $request->category && isset($category)) {
            $availableAttributes = \App\Models\CategoryAttribute::where('category_id', $category->id)
                ->where('is_filterable', true)
                ->orderBy('order')
                ->get();
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
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Get counts for each status
        $counts = [
            'all' => Listing::where('seller_id', $user->id)->count(),
            'active' => Listing::where('seller_id', $user->id)->where('status', 'active')->count(),
            'pending' => Listing::where('seller_id', $user->id)->where('status', 'pending')->count(),
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
            $query->where('status', $status);
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
            })->where('status', 'completed')->count(),
        ];

        // Get listings with user's bid info
        $listings = $query->with(['category', 'images', 'seller'])
            ->withCount('bids')
            ->orderBy('ends_at', 'asc')
            ->paginate(20)
            ->appends($request->except('page'));

        // Add user's bid to each listing
        foreach ($listings as $listing) {
            $listing->my_bid = $listing->bids()->where('user_id', $user->id)->latest()->first();
        }

        return view('listings.my-bids', compact('listings', 'counts'));
    }



    /**
     * Show the form for creating a new listing
     */
    public function create()
    {
        // بررسی نقش فروشنده
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

        return redirect()
            ->route('listings.show', $listing)
            ->with('success', 'آگهی با موفقیت ایجاد شد.');
    }

    /**
     * Display the specified listing
     */
    public function show(Listing $listing)
    {
        // بررسی دسترسی برای آگهی‌های pending (هنوز شروع نشده)
        if ($listing->status === 'pending') {
            // بررسی تنظیمات ادمین - فقط تنظیمات ادمین مهم است نه فیلد show_before_start
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

        return view('listings.show', compact('listing'));
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

        return view('listings.edit', compact('listing'));
    }

    /**
     * Update the specified listing
     */
    public function update(UpdateListingRequest $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        $this->listingService->updateListing($listing, $request->validated());

        return redirect()
            ->route('listings.show', $listing)
            ->with('success', 'آگهی با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Participate in auction (pay deposit) or buy now
     */
    public function participate(ParticipateAuctionRequest $request, Listing $listing)
    {
        // Check if this is a buy now request
        if ($request->has('buy_now') && $request->buy_now == 1) {
            try {
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

                // Check wallet balance
                $wallet = auth()->user()->wallet;
                if (!$wallet || $wallet->balance < $listing->buy_now_price) {
                    return redirect()
                        ->route('listings.show', $listing)
                        ->with('error', 'موجودی کیف پول شما برای خرید فوری کافی نیست. مبلغ مورد نیاز: ' . number_format($listing->buy_now_price) . ' تومان');
                }

                // Create order for buy now
                $order = $this->orderService->createOrderFromBuyNow($listing, auth()->user());
                
                return redirect()
                    ->route('orders.show', $order)
                    ->with('success', 'خرید فوری با موفقیت انجام شد. لطفا روش ارسال را انتخاب کنید.');
                    
            } catch (\App\Exceptions\Wallet\InsufficientBalanceException $e) {
                return redirect()
                    ->route('listings.show', $listing)
                    ->with('error', 'موجودی کیف پول شما کافی نیست. لطفا ابتدا کیف پول خود را شارژ کنید.');
            } catch (\Exception $e) {
                \Log::error('Buy now error: ' . $e->getMessage());
                return redirect()
                    ->route('listings.show', $listing)
                    ->with('error', 'خطا در انجام خرید فوری. لطفا دوباره تلاش کنید.');
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
}
