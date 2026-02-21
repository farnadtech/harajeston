<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\ListingComment;
use Illuminate\Http\Request;

class ListingCommentController extends Controller
{
    public function store(Request $request, Listing $listing)
    {
        $request->validate([
            'content' => 'required|string|min:10|max:1000',
            'type' => 'required|in:comment,question',
            'rating' => 'nullable|integer|min:1|max:5',
            'parent_id' => 'nullable|exists:listing_comments,id',
        ]);

        // Check if replying to a comment, user must be the seller
        if ($request->parent_id) {
            $parentComment = ListingComment::findOrFail($request->parent_id);
            
            if (auth()->id() !== $listing->seller_id) {
                return back()->with('error', 'فقط فروشنده می‌تواند به نظرات و پرسش‌ها پاسخ دهد');
            }
            
            // Seller replies are auto-approved and don't have ratings
            $status = 'approved';
            $rating = null;
        } else {
            // Check if user already rated this listing (only for comments with rating)
            if ($request->type === 'comment' && $request->rating) {
                $existingRating = ListingComment::where('listing_id', $listing->id)
                    ->where('user_id', auth()->id())
                    ->whereNotNull('rating')
                    ->whereNull('parent_id')
                    ->exists();
                
                if ($existingRating) {
                    return back()->with('error', 'شما قبلا به این محصول امتیاز داده‌اید');
                }
            }
            
            // New comments/questions need approval
            $status = 'pending';
            $rating = $request->rating;
        }

        $comment = ListingComment::create([
            'listing_id' => $listing->id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'type' => $request->type,
            'content' => $request->content,
            'rating' => $rating,
            'status' => $status,
            'approved_at' => $status === 'approved' ? now() : null,
        ]);

        if ($status === 'pending') {
            return back()->with('success', $request->type === 'comment' ? 'نظر شما پس از تایید مدیر منتشر خواهد شد' : 'پرسش شما پس از تایید مدیر منتشر خواهد شد');
        }

        return back()->with('success', 'پاسخ شما با موفقیت ثبت شد');
    }
}
