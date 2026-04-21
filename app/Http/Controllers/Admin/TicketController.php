<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['creator', 'recipient', 'listing', 'messages'])
            ->latest('last_reply_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('ticket_number', 'like', '%' . $request->search . '%');
            });
        }

        $tickets = $query->paginate(20);
        $openCount  = Ticket::where('status', 'open')->count();
        $totalCount = Ticket::count();

        return view('admin.tickets.index', compact('tickets', 'openCount', 'totalCount'));
    }

    public function create()
    {
        $users    = User::where('role', '!=', 'admin')->orderBy('name')->get(['id', 'name', 'role', 'seller_status']);
        $listings = collect(); // خالی - با AJAX پر می‌شه
        return view('admin.tickets.create', compact('users', 'listings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'listing_id'   => 'required|exists:listings,id',
            'subject'      => 'required|string|max:255',
            'message'      => 'required|string|max:5000',
            'priority'     => 'in:low,normal,high',
        ]);

        $listing   = Listing::findOrFail($request->listing_id);
        $recipient = User::findOrFail($request->recipient_id);

        // نوع تیکت بر اساس نقش گیرنده - ادمین فرستنده است
        $type = $recipient->isSeller() ? 'admin_to_seller' : 'admin_to_buyer';

        $ticket = Ticket::create([
            'creator_id'    => Auth::id(),
            'recipient_id'  => $recipient->id,
            'listing_id'    => $listing->id,
            'subject'       => $request->subject,
            'type'          => $type,
            'priority'      => $request->priority ?? 'normal',
            'last_reply_at' => now(),
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(),
            'message'   => $request->message,
        ]);

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'تیکت با موفقیت ایجاد شد.');
    }

    /**
     * حراجی‌های مرتبط با یک کاربر (AJAX)
     */
    public function listingsForUser(Request $request)
    {
        $user = User::findOrFail($request->user_id);

        if ($user->isSeller()) {
            // فروشنده: حراجی‌هایی که خودش ساخته
            $listings = Listing::where('seller_id', $user->id)
                ->orderByDesc('created_at')
                ->get(['id', 'title']);
        } else {
            // خریدار: حراجی‌هایی که order داره (برنده شده)
            $listings = Listing::whereHas('orderItems.order', function ($q) use ($user) {
                $q->where('buyer_id', $user->id);
            })->orderByDesc('created_at')->get(['id', 'title']);
        }

        return response()->json($listings);
    }

    public function show(Ticket $ticket)
    {
        $ticket->messages()->where('is_read', false)->update(['is_read' => true]);
        $ticket->load(['messages.user', 'listing', 'creator', 'recipient']);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate(['message' => 'required|string|max:5000']);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id'   => Auth::id(),
            'message'   => $request->message,
        ]);

        $ticket->update([
            'status'        => 'answered',
            'last_reply_at' => now(),
        ]);

        return back()->with('success', 'پیام ادمین ارسال شد.');
    }

    public function close(Ticket $ticket)
    {
        $ticket->update(['status' => 'closed']);
        return back()->with('success', 'تیکت بسته شد.');
    }

    public function reopen(Ticket $ticket)
    {
        $ticket->update(['status' => 'open']);
        return back()->with('success', 'تیکت مجدداً باز شد.');
    }
}
