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
        // Check if any filter is applied
        $hasFilters = $request->has('category') || $request->has('tag') || 
                     $request->has('search') || $request->has('seller_id') || 
                     $request->has('buy_now') || $request->has('sort');

        // If no filters, show home page
        if (!$hasFilters) {
            $listings = Listing::query()
                ->where('status', 'active')
                ->with('seller', 'images')
                ->orderBy('ends_at', 'asc')
                ->paginate(20);
            
            return view('listings.index', compact('listings'));
        }

        // Apply filters for search results - include both active and completed
        $query = Listing::query()->whereIn('status', ['active', 'completed']);

        // Filter by category
        if ($request->has('category') && $request->category) {
            $category = \App\Models\Category::where('slug', $request->category)->first();
            if ($category) {
                // اگر دسته اصلی است، همه زیردسته‌ها را هم نمایش بده
                if ($category->isParent()) {
                    $categoryIds = $category->children()->pluck('id')->push($category->id);
                    $query->whereIn('category_id', $categoryIds);
                } else {
                    $query->where('category_id', $category->id);
                }
            }
        }

        // Filter by tag
        if ($request->has('tag') && $request->tag) {
            $tag = trim($request->tag);
            $query->whereRaw("JSON_SEARCH(tags, 'one', ?) IS NOT NULL", [$tag]);
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

        // Sorting - active first, then completed by end date
        $sort = $request->get('sort', 'ending_soon');
        switch ($sort) {
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

        // Return search results view
        return view('listings.search', compact('listings', 'request'));
    }

    /**
     * Show the form for creating a new listing
     */
    public function create()
    {
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
        // بررسی دسترسی برای آگهی‌های تعلیق شده
        if ($listing->status === 'suspended') {
            // فقط ادمین و صاحب آگهی می‌تونن ببینن
            if (!auth()->check() || 
                (auth()->user()->role !== 'admin' && auth()->id() !== $listing->seller_id)) {
                abort(404);
            }
        }

        // Increment view count
        $listing->increment('views');
        
        $listing->load([
            'seller', 
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

        return view('listings.edit', compact('listing'));
    }

    /**
     * Update the specified listing
     */
    public function update(UpdateListingRequest $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        $listing->update($request->validated());

        return redirect()
            ->route('listings.show', $listing)
            ->with('success', 'آگهی با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Participate in auction (pay deposit)
     */
    public function participate(ParticipateAuctionRequest $request, Listing $listing)
    {
        $this->depositService->participateInAuction(auth()->user(), $listing);

        return redirect()
            ->route('listings.show', $listing)
            ->with('success', 'شما با موفقیت در مزایده شرکت کردید.');
    }
}
