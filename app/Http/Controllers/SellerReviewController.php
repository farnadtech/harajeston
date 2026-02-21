<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SellerReview;
use Illuminate\Http\Request;

class SellerReviewController extends Controller
{
    public function create(Order $order)
    {
        // Check if user is the buyer of this order
        if (auth()->id() !== $order->buyer_id) {
            abort(403, 'شما مجاز به ثبت نظر برای این سفارش نیستید');
        }

        // Check if order is completed
        if ($order->status !== 'completed') {
            return back()->with('error', 'فقط می‌توانید برای سفارشات تکمیل شده نظر ثبت کنید');
        }

        // Check if already reviewed
        $existingReview = SellerReview::where('order_id', $order->id)
            ->where('buyer_id', auth()->id())
            ->exists();

        if ($existingReview) {
            return back()->with('error', 'شما قبلا برای این سفارش نظر ثبت کرده‌اید');
        }

        return view('seller-reviews.create', compact('order'));
    }

    public function store(Request $request, Order $order)
    {
        // Check if user is the buyer of this order
        if (auth()->id() !== $order->buyer_id) {
            abort(403);
        }

        // Check if order is completed
        if ($order->status !== 'completed') {
            return back()->with('error', 'فقط می‌توانید برای سفارشات تکمیل شده نظر ثبت کنید');
        }

        // Check if already reviewed
        $existingReview = SellerReview::where('order_id', $order->id)
            ->where('buyer_id', auth()->id())
            ->exists();

        if ($existingReview) {
            return back()->with('error', 'شما قبلا برای این سفارش نظر ثبت کرده‌اید');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:10|max:1000',
        ]);

        SellerReview::create([
            'seller_id' => $order->seller_id,
            'buyer_id' => auth()->id(),
            'order_id' => $order->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending',
        ]);

        return redirect()->route('orders.show', $order)
            ->with('success', 'نظر شما پس از تایید مدیر منتشر خواهد شد');
    }
}
