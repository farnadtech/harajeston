<?php

namespace App\Services;

use App\Models\User;
use App\Models\Listing;
use App\Models\Bid;
use App\Exceptions\Auction\AuctionNotActiveException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuctionService
{
    public function __construct(
        protected WalletService $walletService,
        protected CommissionService $commissionService
    ) {}
    
    /**
     * Create new auction with auto-calculated deposit (10%)
     * 
     * @param User $seller
     * @param array $data
     * @return Listing
     */
    public function createAuction(User $seller, array $data): Listing
    {
        // Validate time constraints
        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);
        
        if ($endTime->lte($startTime)) {
            throw new \InvalidArgumentException('زمان پایان باید بعد از زمان شروع باشد.');
        }
        
        if ($startTime->lt(now())) {
            throw new \InvalidArgumentException('زمان شروع نمی‌تواند در گذشته باشد.');
        }
        
        // Calculate deposit based on site settings
        $deposit = $this->commissionService->calculateDeposit($data['base_price']);
        
        return Listing::create([
            'seller_id' => $seller->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category' => $data['category'] ?? null,
            'type' => 'auction',
            'base_price' => $data['base_price'],
            'required_deposit' => $deposit,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'pending',
        ]);
    }
    
    /**
     * Start auction when start_time is reached
     * 
     * @param Listing $listing
     * @return void
     */
    public function startAuction(Listing $listing): void
    {
        if ($listing->status !== 'pending') {
            throw new AuctionNotActiveException($listing->id, $listing->status);
        }
        
        $listing->status = 'active';
        $listing->save();
        
        // Send notification to seller
        $listing->seller->notify(new \App\Notifications\AuctionStartedNotification($listing));
    }
    
    /**
     * Process auction ending - identify top 3 bidders and release others
     * 
     * @param Listing $listing
     * @return void
     */
    public function endAuction(Listing $listing): void
    {
        DB::transaction(function () use ($listing) {
            $listing = Listing::where('id', $listing->id)
                ->lockForUpdate()
                ->first();
            
            if ($listing->status !== 'active') {
                throw new AuctionNotActiveException($listing->id, $listing->status);
            }
            
            // Skip if required_deposit is not set
            if (!$listing->required_deposit) {
                \Illuminate\Support\Facades\Log::warning('ProcessAuctionEnding: Skipping auction ' . $listing->id . ' - no required_deposit set');
                return;
            }
            
            // Get all bids ordered by amount DESC, grouped by user (highest bid per user)
            $bids = Bid::where('listing_id', $listing->id)
                ->orderBy('amount', 'desc')
                ->orderBy('created_at', 'asc') // Earlier bid wins in case of tie
                ->get()
                ->unique('user_id') // Get highest bid per user
                ->values();
            
            if ($bids->isEmpty()) {
                // No bids - mark as failed
                $listing->status = 'failed';
                $listing->save();
                return;
            }
            
            // Identify top 3 bidders
            $top3 = $bids->take(3);
            $others = $bids->skip(3);
            
            // Release deposits for non-top-3 bidders
            foreach ($others as $bid) {
                // بررسی تنظیمات کارمزد بازندگان
                $loserFeeEnabled = \App\Models\SiteSetting::get('loser_fee_enabled', false);
                $loserFeePercentage = (float) \App\Models\SiteSetting::get('loser_fee_percentage', 0);
                
                if ($loserFeeEnabled && $loserFeePercentage > 0) {
                    // محاسبه کارمزد
                    $fee = (int) ($listing->required_deposit * ($loserFeePercentage / 100));
                    $refundAmount = $listing->required_deposit - $fee;
                    
                    // کسر کارمزد از موجودی مسدود
                    $this->walletService->deductFrozenAmount(
                        $bid->user,
                        $fee,
                        sprintf('کارمزد شرکت در مزایده: %s', $listing->title),
                        $listing
                    );
                    
                    // واریز کارمزد به کیف پول سایت
                    $siteUser = User::find(1);
                    if ($siteUser) {
                        $this->walletService->addFunds(
                            $siteUser,
                            $fee,
                            sprintf('کارمزد بازنده مزایده: %s', $listing->title)
                        );
                    }
                    
                    // آزادسازی مابقی سپرده
                    if ($refundAmount > 0) {
                        $wallet = $bid->user->wallet;
                        $wallet->frozen -= $refundAmount;
                        $wallet->balance += $refundAmount;
                        $wallet->save();
                        
                        \App\Models\WalletTransaction::create([
                            'wallet_id' => $wallet->id,
                            'user_id' => $bid->user->id,
                            'type' => 'release_deposit',
                            'amount' => $refundAmount,
                            'final_amount' => $refundAmount,
                            'balance_before' => $wallet->balance - $refundAmount,
                            'balance_after' => $wallet->balance,
                            'frozen_before' => $wallet->frozen + $refundAmount,
                            'frozen_after' => $wallet->frozen,
                            'reference_type' => \App\Models\Listing::class,
                            'reference_id' => $listing->id,
                            'description' => sprintf('بازگشت سپرده (پس از کسر کارمزد): %s', $listing->title),
                        ]);
                    }
                } else {
                    // آزادسازی کامل سپرده بدون کارمزد
                    $this->walletService->releaseDeposit(
                        $bid->user,
                        $listing->required_deposit,
                        $listing
                    );
                }
            }
            
            // Update auction status to 'ended'
            $listing->status = 'ended';
            
            // Set rank 1 as current winner with 48-hour deadline
            $winner = $top3->first();
            $listing->current_winner_id = $winner->user_id;
            $listing->finalization_deadline = now()->addHours(48);
            
            $listing->save();
            
            // Send notification to winner
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifyAuctionWon($listing, $winner->user, $winner->amount);
            
            // Release deposits for rank 2 and 3 (they are not winners)
            foreach ($top3->skip(1) as $bid) {
                $loserFeeEnabled = \App\Models\SiteSetting::get('loser_fee_enabled', false);
                $loserFeePercentage = (float) \App\Models\SiteSetting::get('loser_fee_percentage', 0);
                
                if ($loserFeeEnabled && $loserFeePercentage > 0) {
                    $fee = (int) ($listing->required_deposit * ($loserFeePercentage / 100));
                    $refundAmount = $listing->required_deposit - $fee;
                    
                    $this->walletService->deductFrozenAmount(
                        $bid->user,
                        $fee,
                        sprintf('کارمزد شرکت در مزایده: %s', $listing->title),
                        $listing
                    );
                    
                    $siteUser = User::find(1);
                    if ($siteUser) {
                        $this->walletService->addFunds(
                            $siteUser,
                            $fee,
                            sprintf('کارمزد بازنده مزایده: %s', $listing->title)
                        );
                    }
                    
                    if ($refundAmount > 0) {
                        $wallet = $bid->user->wallet;
                        $wallet->frozen -= $refundAmount;
                        $wallet->balance += $refundAmount;
                        $wallet->save();
                        
                        \App\Models\WalletTransaction::create([
                            'wallet_id' => $wallet->id,
                            'user_id' => $bid->user->id,
                            'type' => 'release_deposit',
                            'amount' => $refundAmount,
                            'final_amount' => $refundAmount,
                            'balance_before' => $wallet->balance - $refundAmount,
                            'balance_after' => $wallet->balance,
                            'frozen_before' => $wallet->frozen + $refundAmount,
                            'frozen_after' => $wallet->frozen,
                            'reference_type' => \App\Models\Listing::class,
                            'reference_id' => $listing->id,
                            'description' => sprintf('بازگشت سپرده (پس از کسر کارمزد): %s', $listing->title),
                        ]);
                    }
                } else {
                    $this->walletService->releaseDeposit(
                        $bid->user,
                        $listing->required_deposit,
                        $listing
                    );
                }
            }
        });
    }
    
    /**
     * Handle finalization timeout - cascade to next bidder
     * 
     * @param Listing $listing
     * @return void
     */
    public function handleFinalizationTimeout(Listing $listing): void
    {
        DB::transaction(function () use ($listing) {
            $listing = Listing::where('id', $listing->id)
                ->lockForUpdate()
                ->first();
            
            if ($listing->status !== 'ended') {
                return; // Already processed
            }
            
            $currentWinner = User::find($listing->current_winner_id);
            
            // تنظیمات تقسیم سپرده ضبط شده
            $forfeitToSite = (float) \App\Models\SiteSetting::get('forfeit_to_site_percentage', 100);
            $forfeitToSeller = 100 - $forfeitToSite;
            
            $siteAmount = (int) ($listing->required_deposit * ($forfeitToSite / 100));
            $sellerAmount = $listing->required_deposit - $siteAmount;
            
            // Forfeit current winner's deposit
            $this->walletService->deductFrozenAmount(
                $currentWinner,
                $listing->required_deposit,
                sprintf('ضبط سپرده به دلیل عدم پرداخت در مهلت مقرر: %s', $listing->title),
                $listing
            );
            
            // واریز سهم سایت
            if ($siteAmount > 0) {
                $siteUser = User::find(1);
                if ($siteUser) {
                    $this->walletService->addFunds(
                        $siteUser,
                        $siteAmount,
                        sprintf('سهم سایت از سپرده ضبط شده: %s', $listing->title)
                    );
                }
            }
            
            // واریز سهم فروشنده
            if ($sellerAmount > 0) {
                $this->walletService->addFunds(
                    $listing->seller,
                    $sellerAmount,
                    sprintf('سهم فروشنده از سپرده ضبط شده: %s', $listing->title)
                );
            }
            
            // Find next ranked bidder (top 3)
            $top3Bids = Bid::where('listing_id', $listing->id)
                ->orderBy('amount', 'desc')
                ->orderBy('created_at', 'asc')
                ->get()
                ->unique('user_id')
                ->take(3);
            
            // Find current winner's rank
            $currentRank = $top3Bids->search(function ($bid) use ($listing) {
                return $bid->user_id === $listing->current_winner_id;
            });
            
            // Get next bidder
            $nextBidder = $top3Bids->get($currentRank + 1);
            
            if ($nextBidder) {
                // Set next bidder as current winner with new 48-hour deadline
                $listing->current_winner_id = $nextBidder->user_id;
                $listing->finalization_deadline = now()->addHours(48);
                $listing->save();
                
                // TODO: Send notification to new winner
            } else {
                // No more bidders - mark auction as failed
                $listing->status = 'failed';
                $listing->current_winner_id = null;
                $listing->finalization_deadline = null;
                $listing->save();
                
                // Release remaining deposits (if any)
                foreach ($top3Bids as $bid) {
                    if ($bid->user_id !== $currentWinner->id) {
                        $this->walletService->releaseDeposit(
                            $bid->user,
                            $listing->required_deposit,
                            $listing
                        );
                    }
                }
                
                // TODO: Send notification to seller
            }
        });
    }
    
    /**
     * Cancel auction by admin
     * Release all deposits and set status to failed
     * 
     * @param Listing $listing
     * @param User $admin
     * @param string $reason
     * @return void
     */
    public function cancelAuctionByAdmin(Listing $listing, User $admin, string $reason): void
    {
        DB::transaction(function () use ($listing, $admin, $reason) {
            $listing = Listing::where('id', $listing->id)
                ->lockForUpdate()
                ->first();
            
            // Get all participants
            $participants = $listing->participations()
                ->where('deposit_status', 'paid')
                ->with('user')
                ->get();
            
            // Release all deposits
            foreach ($participants as $participation) {
                $this->walletService->releaseDeposit(
                    $participation->user,
                    $listing->required_deposit,
                    $listing
                );
                
                $participation->update(['deposit_status' => 'released']);
            }
            
            // Update listing status
            $listing->update([
                'status' => 'failed',
            ]);
            
            // Log admin action
            $adminService = app(\App\Services\AdminService::class);
            $adminService->logAction(
                $admin,
                'cancel_auction',
                $listing,
                [
                    'participants_count' => $participants->count(),
                    'deposits_released' => $participants->count() * $listing->required_deposit,
                ],
                $reason
            );
        });
    }

    /**
     * Finalize auction - winner pays remaining amount and order is created
     */
    public function finalizeAuction(Listing $listing, User $winner): \App\Models\Order
    {
        return DB::transaction(function () use ($listing, $winner) {
            $listing = Listing::where('id', $listing->id)
                ->lockForUpdate()
                ->first();
            
            if ($listing->status !== 'ended') {
                throw new \Exception('حراجی در وضعیت مناسب برای نهایی‌سازی نیست');
            }
            
            if ($listing->current_winner_id !== $winner->id) {
                throw new \Exception('شما برنده این حراجی نیستید');
            }
            
            // Get winning bid
            $winningBid = Bid::where('listing_id', $listing->id)
                ->where('user_id', $winner->id)
                ->orderBy('amount', 'desc')
                ->first();
            
            if (!$winningBid) {
                throw new \Exception('پیشنهاد برنده یافت نشد');
            }
            
            $totalAmount = $winningBid->amount;
            $depositAmount = $listing->required_deposit;
            $remainingAmount = $totalAmount - $depositAmount;
            
            // Convert deposit to payment (no change in frozen, just record the conversion)
            \App\Models\WalletTransaction::create([
                'wallet_id' => $winner->wallet->id,
                'user_id' => $winner->id,
                'type' => 'auction_payment',
                'amount' => $depositAmount,
                'final_amount' => $depositAmount,
                'balance_before' => $winner->wallet->balance,
                'balance_after' => $winner->wallet->balance,
                'frozen_before' => $winner->wallet->frozen,
                'frozen_after' => $winner->wallet->frozen,
                'reference_type' => \App\Models\Listing::class,
                'reference_id' => $listing->id,
                'description' => sprintf('تبدیل سپرده به پرداخت: %s', $listing->title),
            ]);
            
            // Freeze remaining amount (deposit is already frozen)
            if ($remainingAmount > 0) {
                $wallet = $winner->wallet;
                
                if ($wallet->balance < $remainingAmount) {
                    throw new \Exception('موجودی کیف پول کافی نیست');
                }
                
                // Freeze the remaining amount
                $wallet->balance -= $remainingAmount;
                $wallet->frozen += $remainingAmount;
                $wallet->save();
                
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $winner->id,
                    'type' => 'freeze_deposit',
                    'amount' => $remainingAmount,
                    'final_amount' => $remainingAmount,
                    'balance_before' => $wallet->balance + $remainingAmount,
                    'balance_after' => $wallet->balance,
                    'frozen_before' => $wallet->frozen - $remainingAmount,
                    'frozen_after' => $wallet->frozen,
                    'reference_type' => \App\Models\Listing::class,
                    'reference_id' => $listing->id,
                    'description' => sprintf('بلاک مبلغ باقیمانده حراجی: %s', $listing->title),
                ]);
            }
            
            // Create order
            $order = \App\Models\Order::create([
                'buyer_id' => $winner->id,
                'seller_id' => $listing->seller_id,
                'subtotal' => $totalAmount,
                'shipping_cost' => 0,
                'total' => $totalAmount,
                'status' => 'pending',
            ]);
            
            // Create order item
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'listing_id' => $listing->id,
                'quantity' => 1,
                'price_snapshot' => $totalAmount,
                'subtotal' => $totalAmount,
            ]);
            
            // Update listing status
            $listing->status = 'completed';
            $listing->save();
            
            // Send notifications
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifyNewOrder($order);
            
            return $order;
        });
    }

    /**
     * Release payment to seller after delivery confirmation
     * Called when order status changes to 'delivered'
     */
    public function releasePaymentToSeller(\App\Models\Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order = \App\Models\Order::where('id', $order->id)
                ->lockForUpdate()
                ->first();
            
            // Get the listing
            $orderItem = $order->items()->first();
            if (!$orderItem || !$orderItem->listing) {
                throw new \Exception('آیتم سفارش یافت نشد');
            }
            
            $listing = $orderItem->listing;
            $totalAmount = $order->total;
            
            // Calculate commission
            $commission = $this->commissionService->calculateCommission($totalAmount, $listing->category_id);
            $sellerAmount = $totalAmount - $commission;
            
            // Get buyer wallet
            $buyerWallet = $order->buyer->wallet;
            
            // Release frozen amount from buyer
            $buyerWallet->frozen -= $totalAmount;
            $buyerWallet->save();
            
            \App\Models\WalletTransaction::create([
                'wallet_id' => $buyerWallet->id,
                'user_id' => $order->buyer_id,
                'type' => 'deduct_frozen',
                'amount' => $totalAmount,
                'final_amount' => $totalAmount,
                'balance_before' => $buyerWallet->balance,
                'balance_after' => $buyerWallet->balance,
                'frozen_before' => $buyerWallet->frozen + $totalAmount,
                'frozen_after' => $buyerWallet->frozen,
                'reference_type' => \App\Models\Order::class,
                'reference_id' => $order->id,
                'description' => sprintf('کسر مبلغ بلاک شده برای سفارش #%s', $order->order_number),
            ]);
            
            // Transfer commission to site
            $siteUser = User::find(1);
            if ($siteUser && $commission > 0) {
                $this->walletService->addFunds(
                    $siteUser,
                    $commission,
                    sprintf('کمیسیون سفارش #%s', $order->order_number)
                );
            }
            
            // Transfer seller amount to seller
            if ($sellerAmount > 0) {
                $this->walletService->addFunds(
                    $order->seller,
                    $sellerAmount,
                    sprintf('فروش محصول - سفارش #%s', $order->order_number)
                );
            }
            
            // Update listing status
            $listing->status = 'completed';
            $listing->save();
            
            // Mark payment as released
            $order->payment_released_at = now();
            $order->save();
        });
    }
}
