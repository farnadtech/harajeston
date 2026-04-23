<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'name'  => 'nullable|string|max:100',
        ]);

        $existing = NewsletterSubscriber::where('email', $request->email)->first();

        if ($existing) {
            if (!$existing->is_active) {
                $existing->update(['is_active' => true]);
                return response()->json(['message' => 'با موفقیت مجدداً عضو شدید.', 'success' => true]);
            }
            return response()->json(['message' => 'این ایمیل قبلاً ثبت شده است.', 'success' => false], 422);
        }

        NewsletterSubscriber::create([
            'email' => $request->email,
            'name'  => $request->name,
        ]);

        return response()->json(['message' => 'با موفقیت عضو خبرنامه شدید.', 'success' => true]);
    }

    public function unsubscribe(string $token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (!$subscriber) {
            return view('newsletter.unsubscribe', ['success' => false]);
        }

        $subscriber->update(['is_active' => false]);
        return view('newsletter.unsubscribe', ['success' => true]);
    }
}
