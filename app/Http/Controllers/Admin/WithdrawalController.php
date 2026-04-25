<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Traits\CsvExportTrait;
use App\Models\WithdrawalRequest;
use App\Models\Notification;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    use CsvExportTrait;
    public function __construct(protected WalletService $walletService) {}

    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $query = WithdrawalRequest::with('user')->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->paginate(20);
        $pendingCount = WithdrawalRequest::pending()->count();

        return view('admin.withdrawals.index', compact('requests', 'pendingCount', 'status'));
    }

    public function approve(WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'این درخواست قبلاً بررسی شده است.');
        }

        $user = $withdrawal->user;
        $wallet = $user->wallet;

        if ($wallet->balance < $withdrawal->amount) {
            return back()->with('error', 'موجودی کاربر کافی نیست.');
        }

        try {
            $this->walletService->deduct(
                $user,
                $withdrawal->amount,
                'برداشت از حساب - تایید شده توسط ادمین'
            );

            $withdrawal->update([
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            // Database notification
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'type'    => 'withdrawal_approved',
                'title'   => 'درخواست برداشت تایید شد',
                'message' => 'درخواست برداشت ' . number_format($withdrawal->amount) . ' تومان شما تایید شد و مبلغ از کیف پول کسر گردید.',
                'icon'    => 'check_circle',
                'color'   => 'green',
                'link'    => route('wallet.show'),
                'is_read' => false,
            ]);

            // SMS/Email via NotificationDispatcher
            $dispatcher = app(\App\Services\NotificationDispatcher::class);
            $dispatcher->dispatch('withdrawal_approved', $user, [
                'amount' => number_format($withdrawal->amount),
            ]);

            return back()->with('success', 'درخواست برداشت تایید و مبلغ از کیف پول کسر شد.');
        } catch (\Exception $e) {
            return back()->with('error', 'خطا: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, WithdrawalRequest $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'این درخواست قبلاً بررسی شده است.');
        }

        $request->validate([
            'reject_reason' => 'required|string|max:500',
        ], [
            'reject_reason.required' => 'دلیل رد درخواست الزامی است.',
        ]);

        $withdrawal->update([
            'status'        => 'rejected',
            'reject_reason' => $request->reject_reason,
            'reviewed_by'   => auth()->id(),
            'reviewed_at'   => now(),
        ]);

        $user = $withdrawal->user;

        // Database notification
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'type'    => 'withdrawal_rejected',
            'title'   => 'درخواست برداشت رد شد',
            'message' => 'درخواست برداشت ' . number_format($withdrawal->amount) . ' تومان شما رد شد. دلیل: ' . $request->reject_reason,
            'icon'    => 'cancel',
            'color'   => 'red',
            'link'    => route('wallet.show'),
            'is_read' => false,
        ]);

        // SMS/Email via NotificationDispatcher
        $dispatcher = app(\App\Services\NotificationDispatcher::class);
        $dispatcher->dispatch('withdrawal_rejected', $user, [
            'amount'        => number_format($withdrawal->amount),
            'reject_reason' => $request->reject_reason,
        ]);

        return back()->with('success', 'درخواست برداشت رد شد.');
    }

    public function export(Request $request)
    {
        $statusLabels = ['pending' => 'در انتظار', 'approved' => 'تایید شده', 'rejected' => 'رد شده'];
        $query = WithdrawalRequest::with('user');
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $items = $query->latest()->get();

        $rows = $items->map(fn($w) => [
            $w->id, $w->user->name ?? '', $w->user->email ?? '',
            $w->full_name, $w->bank_name, $w->card_number,
            number_format($w->amount),
            $statusLabels[$w->status] ?? $w->status,
            $this->jalali($w->created_at),
            $this->jalali($w->reviewed_at),
        ]);

        return $this->csvResponse('withdrawals-' . date('Y-m-d') . '.csv', [
            'شناسه', 'کاربر', 'ایمیل', 'نام صاحب حساب', 'بانک', 'شماره کارت',
            'مبلغ (تومان)', 'وضعیت', 'تاریخ درخواست', 'تاریخ بررسی',
        ], $rows);
    }
}
