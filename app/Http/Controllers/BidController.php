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
    public function store(PlaceBidRequest $request, Listing $listing)
    {
        $this->bidService->placeBid(
            auth()->user(),
            $listing,
            $request->validated()['amount']
        );

        return redirect()
            ->route('listings.show', $listing)
            ->with('success', 'پیشنهاد شما با موفقیت ثبت شد.');
    }
}
