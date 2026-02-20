<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BidResource;
use App\Models\Listing;
use App\Services\BidService;
use Illuminate\Http\Request;

class BidController extends Controller
{
    public function __construct(
        private BidService $bidService
    ) {}

    public function store(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
        ], [
            'amount.required' => 'مبلغ پیشنهاد الزامی است',
            'amount.numeric' => 'مبلغ پیشنهاد باید عدد باشد',
            'amount.min' => 'مبلغ پیشنهاد نمی‌تواند منفی باشد',
        ]);

        try {
            $bid = $this->bidService->placeBid($request->user(), $listing, $validated['amount']);
            
            return new BidResource($bid);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
