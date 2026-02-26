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
     * Process auction ending - identify winner and release losers' deposits
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
            
            // Get all bids ordered by amount DESC
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
            
            // Get winner (highest bidder)
            $winner = $bids->first();
            
            // Get deposit amount from settings
            $depositPercentage = (float) \App\Models\SiteSetting::get('auction_deposit_percentage', 20);
            $depositAmount = (int) (($listing->base_price ?? $listing->starting_price) * ($depositPercentage / 100));
            
            // Release deposits for all losers
            foreach ($bids->skip(1) as $bid) {
                $user = $bid->user;
                
                // بررسی تنظیمات کارمزد بازندگان
                $loserFeeEnabled = \App\Models\SiteSetting::get('loser_fee_enabled', false);
                $loserFeePercentage = (float) \App\Models\SiteSetting::get('loser_fee_percentage', 0);
                
                if ($loserFeeEnabled && $loserFeePercentage > 0 && $depositAmount > 0) {
                    // محاسبه کارمزد
                    $fee = (int) ($depositAmount * ($loserFeePercentage / 100));
                    $refundAmount = $depositAmount - $fee;
                    
                    // کسر کارمزد از موجودی مسدود
                    $wallet = $user->wallet;
                    $wallet->frozen -= $fee;
                    $wallet->save();
                    
                    \App\Models\WalletTransaction::create([
                        'wallet_id' => $wallet->id,
                        'user_id' => $user->id,
                        'type' => 'deduct_frozen',
                        'amount' => $fee,
                        'final_amount' => $fee,
                        'balance_before' => $wallet->balance,
                        'balance_after' => $wallet->balance,
                        'frozen_before' => $wallet->frozen + $fee,
                        'frozen_after' => $wallet->frozen,
                        'reference_type' => \App\Models\Listing::class,
                        'reference_id' => $listing->id,
                        'status' => 'completed',
                        'description' => sprintf('کارمزد بازنده حراجی: %s', $listing->title),
                    ]);
                    
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
                        $wallet->frozen -= $refundAmount;
                        $wallet->balance += $refundAmount;
                        $wallet->save();
                        
                        \App\Models\WalletTransaction::create([
                            'wallet_id' => $wallet->id,
                            'user_id' => $user->id,
                            'type' => 'release_deposit',
                            'amount' => $refundAmount,
                            'final_amount' => $refundAmount,
                            'balance_before' => $wallet->balance - $refundAmount,
                            'balance_after' => $wallet->balance,
                            'frozen_before' => $wallet->frozen + $refundAmount,
                            'frozen_after' => $wallet->frozen,
                            'reference_type' => \App\Models\Listing::class,
                            'reference_id' => $listing->id,
                            'status' => 'completed',
                            'description' => sprintf('بازگشت سپرده (پس از کسر کارمزد): %s', $listing->title),
                        ]);
                    }
                } else if ($depositAmount > 0) {
                    // آزادسازی کامل سپرده بدون کارمزد
                    $this->walletService->releaseDeposit(
                        $user,
                        $depositAmount,
                        $listing
                    );
                }
            }
            
            // Update auction status to 'ended'
            $listing->status = 'ended';
            $listing->current_winner_id = $winner->user_id;
            
            // Set finalization deadline from settings
            $deadlineHours = (int) \App\Models\SiteSetting::get('auction_payment_deadline_hours', 24);
            $listing->finalization_deadline = now()->addHours($deadlineHours);
            
            $listing->save();
            
            // Send notification to winner
            $notificationService = app(\App\Services\NotificationService::class);
            $notificationService->notifyAuctionWon($listing, $winner->user, $winner->amount);
        });
    }
    
    /**
     * Handle finalization timeout - forfeit deposit and mark as failed
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
            
            // Get deposit amount from settings
            $depositPercentage = (float) \App\Models\SiteSetting::get('auction_deposit_percentage', 20);
            $depositAmount = (int) (($listing->base_price ?? $listing->starting_price) * ($depositPercentage / 100));
            
            if ($depositAmount > 0) {
                // تنظیمات تقسیم سپرده ضبط شده
                $forfeitToSite = (float) \App\Models\SiteSetting::get('forfeit_to_site_percentage', 100);
                $forfeitToSeller = 100 - $forfeitToSite;
                
                $siteAmount = (int) ($depositAmount * ($forfeitToSite / 100));
                $sellerAmount = $depositAmount - $siteAmount;
                
                // Forfeit current winner's deposit
                $wallet = $currentWinner->wallet;
                $wallet->frozen -= $depositAmount;
                $wallet->save();
                
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $currentWinner->id,
                    'type' => 'deduct_frozen',
                    'amount' => $depositAmount,
                    'final_amount' => $depositAmount,
                    'balance_before' => $wallet->balance,
                    'balance_after' => $wallet->balance,
                    'frozen_before' => $wallet->frozen + $depositAmount,
                    'frozen_after' => $wallet->frozen,
                    'reference_type' => \App\Models\Listing::class,
                    'reference_id' => $listing->id,
                    'status' => 'completed',
                    'description' => sprintf('ضبط سپرده به دلیل عدم پرداخت: %s', $listing->title),
                ]);
                
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
            }
            
            // Mark auction as failed
            $listing->status = 'failed';
            $listing->current_winner_id = null;
            $listing->finalization_deadline = null;
            $listing->save();
            
            // Send notification to seller
            $notificationService = app(\App\Services\NotificationService::class);
            // TODO: Add notifyAuctionFailed method
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
            
            // Get deposit amount from settings
            $depositPercentage = (float) \App\Models\SiteSetting::get('auction_deposit_percentage', 20);
            $depositAmount = (int) (($listing->base_price ?? $listing->starting_price) * ($depositPercentage / 100));
            $remainingAmount = $totalAmount - $depositAmount;
            
            // Convert deposit to payment (no change in frozen, just record the conversion)
            if ($depositAmount > 0) {
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
                    'status' => 'completed',
                    'description' => sprintf('تبدیل سپرده به پرداخت: %s', $listing->title),
                ]);
            }
            
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
                    'status' => 'completed',
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
            
            // Release frozen amount from buyer and record as payment
            $buyerWallet->frozen -= $totalAmount;
            $buyerWallet->save();
            
            // Record buyer payment transaction
            \App\Models\WalletTransaction::create([
                'wallet_id' => $buyerWallet->id,
                'user_id' => $order->buyer_id,
                'type' => 'withdrawal',
                'amount' => $totalAmount,
                'final_amount' => $totalAmount,
                'balance_before' => $buyerWallet->balance,
                'balance_after' => $buyerWallet->balance,
                'frozen_before' => $buyerWallet->frozen + $totalAmount,
                'frozen_after' => $buyerWallet->frozen,
                'reference_type' => \App\Models\Order::class,
                'reference_id' => $order->id,
                'status' => 'completed',
                'description' => sprintf('پرداخت سفارش #%s', $order->order_number),
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
            
            // Transfer seller amount to seller (this creates a deposit transaction)
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

    /**
     * Finalize auction with shipping details
     */
    public function finalizeAuctionWithShipping(Listing $listing, User $winner, array $shippingData): \App\Models\Order
    {
        return DB::transaction(function () use ($listing, $winner, $shippingData) {
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
            
            // Get deposit amount from settings
            $depositPercentage = (float) \App\Models\SiteSetting::get('auction_deposit_percentage', 20);
            $depositAmount = (int) (($listing->base_price ?? $listing->starting_price) * ($depositPercentage / 100));
            $remainingAmount = $totalAmount - $depositAmount;
            
            // Get shipping cost
            $shippingMethod = \App\Models\ShippingMethod::findOrFail($shippingData['shipping_method_id']);
            $shippingCost = $shippingMethod->cost;
            
            // Check if listing has custom shipping cost
            $listingShipping = $listing->shippingMethods()
                ->where('shipping_method_id', $shippingMethod->id)
                ->first();
            
            if ($listingShipping && $listingShipping->pivot->custom_cost_adjustment) {
                $shippingCost += $listingShipping->pivot->custom_cost_adjustment;
            }
            
            $finalTotal = $totalAmount + $shippingCost;
            $amountToFreeze = $remainingAmount + $shippingCost;
            
            // Convert deposit to payment (no change in frozen, just record the conversion)
            if ($depositAmount > 0) {
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
                    'status' => 'completed',
                    'description' => sprintf('تبدیل سپرده به پرداخت: %s', $listing->title),
                ]);
            }
            
            // Freeze remaining amount + shipping cost
            if ($amountToFreeze > 0) {
                $wallet = $winner->wallet;
                
                if ($wallet->balance < $amountToFreeze) {
                    throw new \Exception('موجودی کیف پول کافی نیست. مبلغ مورد نیاز: ' . number_format($amountToFreeze) . ' تومان');
                }
                
                // Freeze the amount
                $wallet->balance -= $amountToFreeze;
                $wallet->frozen += $amountToFreeze;
                $wallet->save();
                
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'user_id' => $winner->id,
                    'type' => 'freeze_deposit',
                    'amount' => $amountToFreeze,
                    'final_amount' => $amountToFreeze,
                    'balance_before' => $wallet->balance + $amountToFreeze,
                    'balance_after' => $wallet->balance,
                    'frozen_before' => $wallet->frozen - $amountToFreeze,
                    'frozen_after' => $wallet->frozen,
                    'reference_type' => \App\Models\Listing::class,
                    'reference_id' => $listing->id,
                    'status' => 'completed',
                    'description' => sprintf('بلاک مبلغ باقیمانده + هزینه ارسال: %s', $listing->title),
                ]);
            }
            
            // Create order - set to processing since payment is complete
            $order = \App\Models\Order::create([
                'buyer_id' => $winner->id,
                'seller_id' => $listing->seller_id,
                'subtotal' => $totalAmount,
                'shipping_cost' => $shippingCost,
                'total' => $finalTotal,
                'status' => 'processing',
                'shipping_method_id' => $shippingMethod->id,
                'shipping_address' => $shippingData['shipping_address'],
                'shipping_city' => $shippingData['shipping_city'],
                'shipping_postal_code' => $shippingData['shipping_postal_code'],
                'shipping_phone' => $shippingData['shipping_phone'],
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

}

