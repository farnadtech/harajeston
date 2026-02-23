<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use App\Services\AuctionService;
use App\Services\DepositService;
use App\Services\ListingService;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function __construct(
        private ListingService $listingService,
        private AuctionService $auctionService,
        private DepositService $depositService
    ) {}

    public function index(Request $request)
    {
        $query = Listing::with(['seller', 'images']);

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $listings = $query->paginate(20);

        return ListingResource::collection($listings);
    }

    public function show(Listing $listing)
    {
        $listing->load(['seller', 'images', 'shippingMethods']);
        
        return new ListingResource($listing);
    }

    public function search(Request $request)
    {
        $query = Listing::with(['seller', 'images'])
            ->where('status', 'active'); // Only show active listings

        // Search by title or description
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Limit to 10 results for suggestions
        $listings = $query->limit(10)->get();

        // Format results for search suggestions
        $results = $listings->map(function ($listing) {
            return [
                'id' => $listing->id,
                'title' => $listing->title,
                'image_url' => $listing->images->first() ? url('storage/' . $listing->images->first()->file_path) : null,
                'price' => $listing->starting_price ?? $listing->buy_now_price,
                'url' => route('listings.show', $listing),
            ];
        });

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:auction,direct_sale,hybrid',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'nullable|string|max:100',
            
            // Auction fields
            'base_price' => 'required_if:type,auction,hybrid|numeric|min:0',
            'start_time' => 'required_if:type,auction,hybrid|date|after:now',
            'end_time' => 'required_if:type,auction,hybrid|date|after:start_time',
            
            // Direct sale fields
            'price' => [
                'required_if:type,direct_sale,hybrid',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    // For hybrid type, price must be greater than base_price
                    if ($request->input('type') === 'hybrid') {
                        $basePrice = $request->input('base_price');
                        if ($basePrice && $value <= $basePrice) {
                            $fail('قیمت فروش مستقیم باید بیشتر از قیمت پایه مزایده باشد.');
                        }
                    }
                },
            ],
            'stock' => 'required_if:type,direct_sale,hybrid|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
        ]);

        $listing = $this->listingService->createListing($request->user(), $validated);

        return new ListingResource($listing);
    }

    public function participate(Request $request, Listing $listing)
    {
        if (!$listing->isAuction() && !$listing->isHybrid()) {
            return response()->json([
                'message' => 'این آگهی مزایده نیست',
            ], 400);
        }

        try {
            $participation = $this->depositService->participateInAuction($request->user(), $listing);
            
            return response()->json([
                'message' => 'شما با موفقیت در مزایده شرکت کردید',
                'participation' => $participation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
