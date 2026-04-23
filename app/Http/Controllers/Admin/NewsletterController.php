<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class NewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::latest();

        if ($request->search) {
            $query->where('email', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
        }

        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $subscribers = $query->paginate(20)->appends($request->except('page'));
        $totalActive = NewsletterSubscriber::active()->count();
        $totalAll = NewsletterSubscriber::count();

        return view('admin.newsletter.index', compact('subscribers', 'totalActive', 'totalAll'));
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        return back()->with('success', 'مشترک حذف شد.');
    }

    public function toggleStatus(NewsletterSubscriber $subscriber)
    {
        $subscriber->update(['is_active' => !$subscriber->is_active]);
        return back()->with('success', 'وضعیت مشترک تغییر کرد.');
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
            'target'  => 'required|in:all,active',
        ]);

        $query = NewsletterSubscriber::query();
        if ($request->target === 'active') {
            $query->where('is_active', true);
        }

        $subscribers = $query->get();
        $sent = 0;
        $failed = 0;

        foreach ($subscribers as $subscriber) {
            try {
                Mail::send([], [], function ($message) use ($subscriber, $request) {
                    $message->to($subscriber->email, $subscriber->name ?? '')
                            ->subject($request->subject)
                            ->html($request->body . $this->unsubscribeFooter($subscriber));
                });
                $sent++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        return back()->with('success', "ایمیل برای {$sent} مشترک ارسال شد." . ($failed > 0 ? " {$failed} مورد ناموفق." : ''));
    }

    private function unsubscribeFooter(NewsletterSubscriber $subscriber): string
    {
        $url = url('/newsletter/unsubscribe/' . $subscriber->unsubscribe_token);
        return "<br><br><hr><p style='font-size:12px;color:#999;text-align:center;'>برای لغو اشتراک <a href='{$url}'>اینجا کلیک کنید</a></p>";
    }
}
