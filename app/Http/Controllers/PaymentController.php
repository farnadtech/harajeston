<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Services\AuctionService;

class PaymentController extends Controller
{
    public function __construct(
        protected AuctionService $auctionService
    ) {}

    /**
     * Complete winner payment for auction
     */
    public function complete(Listing $listing)
    {
        $this->auctionService->completeWinnerPayment($listing, auth()->user());

        return redirect()
            ->route('listings.show', $listing)
            ->with('success', 'پرداخت با موفقیت انجام شد.');
    }
}
