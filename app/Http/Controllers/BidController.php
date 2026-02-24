<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceBidRequest;
use App\Models\Listing;
use App\Services\BidService;

class BidController extends Controller
{
    public function __construct(
        protected BidService $bidService
    ) {}

    /**
     * Place a bid on an auction
     */
    public function store(PlaceBidRequest $request)
    {
        $listing = null;
        
        try {
            $listing = Listing::findOrFail($request->listing_id);
            
            $this->bidService->placeBid(
                auth()->user(),
                $listing,
                $request->validated()['amount']
            );

            return redirect()
                ->route('listings.show', $listing->slug)
                ->with('bid_success', 'پیشنهاد شما با موفقیت ثبت شد.');
        } catch (\Exception $e) {
            // If listing was found, redirect to it, otherwise redirect to home
            if ($listing) {
                return redirect()
                    ->route('listings.show', $listing->slug)
                    ->with('bid_error', $e->getMessage());
            }
            
            return redirect()
                ->route('home')
                ->with('bid_error', 'خطا در ثبت پیشنهاد: ' . $e->getMessage());
        }
    }
}
