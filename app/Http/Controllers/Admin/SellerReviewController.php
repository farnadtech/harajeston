<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerReview;
use Illuminate\Http\Request;

class SellerReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = SellerReview::with(['seller', 'buyer', 'order'])
            ->latest();

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            // Default to pending
            $query->where('status', 'pending');
        }

        $reviews = $query->paginate(20);
        $pendingCount = SellerReview::pending()->count();

        return view('admin.seller-reviews.index', compact('reviews', 'pendingCount'));
    }

    public function approve($id)
    {
        $review = SellerReview::findOrFail($id);
        $review->approve(auth()->user());
        
        // Update seller rating
        $review->seller->updateSellerRating();

        return back()->with('success', 'نظر تایید شد');
    }

    public function reject($id)
    {
        $review = SellerReview::findOrFail($id);
        $review->reject();

        return back()->with('success', 'نظر رد شد');
    }

    public function destroy($id)
    {
        $review = SellerReview::findOrFail($id);
        $seller = $review->seller;
        
        $review->delete();
        
        // Update seller rating
        $seller->updateSellerRating();

        return back()->with('success', 'حذف شد');
    }
}
