<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\Listing;
use App\Models\Order;
use App\Models\AuctionParticipation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * لیست تیکتهای کاربر جاری
     */
    public function index()
    {
        $user = Auth::user();

        $tickets = Ticket::where('creator_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->with(['listing', 'creator', 'recipient', 'messages'])
            ->latest('last_reply_at')
            ->paginate(15);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * فرم ایجاد تیکت جدید
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $preselectedListingId = $request->query('listing_id');

        // حراجیهایی که کاربر میتواند برای آنها تیکت بزند
        $eligibleListings = $this->getEligibleListings($user);

        $preselectedListing = null;
        if ($preselectedListingId) {
            $preselectedListing = $eligibleListings->firstWhere('id', $preselectedListingId);
        }

        return view('tickets.create', compact('eligibleListings', 'preselectedListing'));
    }

    /**
     * ذخیره تیکت جدید
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'subject'    => 'required|string|max:255',
            'message'    => 'required|string|max:5000',
            'type'       => 'required|in:buyer_to_seller,buyer_to_admin,seller_to_buyer,seller_to_admin',
        ]);

        $listing = Listing::findOrFail($request->listing_id);

        // بررسی مجاز بودن ارسال تیکت
        $this->authorizeTicketCreation($user, $listing, $request->type);

        // تعیین گیرنده
        $recipientId = $this->resolveRecipient($user, $listing, $request->type);

        $ticket = Ticket::create([
            'creator_id'   => $user->id,
            'recipient_id' => $recipientId,
            'listing_id'   => $listing->id,
            'subject'      => $request->subject,
            'type'         => $request->type,
            'priority'     => $request->priority ?? 'normal',
            'last_reply_at' => now(),
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $user->id,
            'message'   => $request->message,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'تیکت با موفقیت ارسال شد.');
    }

    /**
     * نمایش تیکت و پیامهای آن
     */
    public function show(Ticket $ticket)
    {
        $user = Auth::user();

        // فقط طرفین تیکت یا ادمین میتوانند ببینند
        if ($user->role !== 'admin' && $ticket->creator_id !== $user->id && $ticket->recipient_id !== $user->id) {
            abort(403);
        }

        // علامتگذاری پیامهای خوانده نشده
        $ticket->messages()
            ->where('user_id', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $ticket->load(['messages.user', 'listing', 'creator', 'recipient']);

        return view('tickets.show', compact('ticket'));
    }

    /**
     * ارسال پیام جدید در تیکت
     */
    public function reply(Request $request, Ticket $ticket)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $ticket->creator_id !== $user->id && $ticket->recipient_id !== $user->id) {
            abort(403);
        }

        if ($ticket->isClosed()) {
            return back()->with('error', 'این تیکت بسته شده است.');
        }

        $request->validate(['message' => 'required|string|max:5000']);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $user->id,
            'message'   => $request->message,
        ]);

        $ticket->update([
            'status'       => 'answered',
            'last_reply_at' => now(),
        ]);

        return back()->with('success', 'پیام ارسال شد.');
    }

    /**
     * بستن تیکت
     */
    public function close(Ticket $ticket)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $ticket->creator_id !== $user->id) {
            abort(403);
        }

        $ticket->update(['status' => 'closed']);

        return back()->with('success', 'تیکت بسته شد.');
    }

    //  Private Helpers 

    /**
     * حراجیهایی که کاربر مجاز به ارسال تیکت برای آنهاست
     * (شرکت کرده و برنده شده - خرید فوری یا پیشنهاد عادی)
     */
    private function getEligibleListings($user)
    {
        if ($user->role === 'admin') {
            return Listing::select('id', 'title', 'seller_id', 'status')->get();
        }

        if ($user->isSeller()) {
            // فروشنده: حراجیهایی که خودش ایجاد کرده و برندهای دارند
            return Listing::where('seller_id', $user->id)
                ->whereNotNull('current_winner_id')
                ->select('id', 'title', 'seller_id', 'current_winner_id', 'status')
                ->get();
        }

        // خریدار: حراجی‌هایی که برنده شده (order وجود دارد)
        return Listing::whereHas('orderItems.order', function ($q) use ($user) {
            $q->where('buyer_id', $user->id);
        })
        ->select('id', 'title', 'seller_id', 'current_winner_id', 'status')
        ->get();
    }

    private function authorizeTicketCreation($user, $listing, $type): void
    {
        if ($user->role === 'admin') return;

        // خریدار به فروشنده یا ادمین
        if (in_array($type, ['buyer_to_seller', 'buyer_to_admin'])) {
            $hasOrder = Order::where('buyer_id', $user->id)
                ->whereHas('items', fn($q) => $q->where('listing_id', $listing->id))
                ->exists();
            if (!$hasOrder) {
                abort(403, 'شما برنده این حراجی نشده‌اید.');
            }
        }

        // فروشنده به خریدار یا ادمین
        if (in_array($type, ['seller_to_buyer', 'seller_to_admin'])) {
            if ($listing->seller_id !== $user->id) {
                abort(403, 'این حراجی متعلق به شما نیست.');
            }
            if ($type === 'seller_to_buyer' && is_null($listing->current_winner_id)) {
                abort(403, 'این حراجی هنوز برندهای ندارد.');
            }
        }
    }

    private function resolveRecipient($user, $listing, $type): ?int
    {
        return match($type) {
            'buyer_to_seller'  => $listing->seller_id,
            'seller_to_buyer'  => $listing->current_winner_id,
            'buyer_to_admin',
            'seller_to_admin'  => null, // null = ادمین
            default            => null,
        };
    }
}
