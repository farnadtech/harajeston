<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ListingComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = ListingComment::with(['listing', 'user', 'parent'])
            ->where('type', 'question') // Only questions
            ->whereNull('parent_id') // Only show parent questions
            ->latest();

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            // Default to pending
            $query->where('status', 'pending');
        }

        $comments = $query->paginate(20);
        $pendingCount = ListingComment::pending()->where('type', 'question')->whereNull('parent_id')->count();

        return view('admin.comments.index', compact('comments', 'pendingCount'));
    }

    public function approve($id)
    {
        $comment = ListingComment::findOrFail($id);
        $comment->approve(auth()->user());
        
        // Update listing rating if comment has rating
        if ($comment->hasRating()) {
            $comment->listing->updateRating();
        }

        return back()->with('success', $comment->type === 'comment' ? 'نظر تایید شد' : 'پرسش تایید شد');
    }

    public function reject($id)
    {
        $comment = ListingComment::findOrFail($id);
        $hadRating = $comment->hasRating();
        $listing = $comment->listing;
        
        $comment->reject();
        
        // Update listing rating if comment had rating
        if ($hadRating) {
            $listing->updateRating();
        }

        return back()->with('success', $comment->type === 'comment' ? 'نظر رد شد' : 'پرسش رد شد');
    }

    public function destroy($id)
    {
        $comment = ListingComment::findOrFail($id);
        $hadRating = $comment->hasRating();
        $listing = $comment->listing;
        
        $comment->delete();
        
        // Update listing rating if comment had rating
        if ($hadRating) {
            $listing->updateRating();
        }

        return back()->with('success', 'حذف شد');
    }
}
