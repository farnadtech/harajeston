<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Listing;
use App\Exceptions\Wallet\InsufficientBalanceException;
use App\Exceptions\Wallet\WalletNotFoundException;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Create wallet for user
     */
    public function createWallet(User $user): Wallet
    {
        return Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
            'frozen' => 0,
        ]);
    }

    /**
     * Freeze deposit amount for auction participation
     * 
     * @param User $user
     * @param float $amount
     * @param Listing $listing
     * @return bool
     * @throws InsufficientBalanceException
     * @throws WalletNotFoundException
     */
    public function freezeDeposit(User $user, float $amount, Listing $listing): bool
    {
        return DB::transaction(function () use ($user, $amount, $listing) {
            $wallet = Wallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();
            
            if (!$wallet) {
                throw new WalletNotFoundException($user->id);
            }
            
            if ($wallet->balance < $amount) {
                throw new InsufficientBalanceException($amount, $wallet->balance);
            }
            
            $beforeBalance = $wallet->balance;
            $beforeFrozen = $wallet->frozen;
            
            $wallet->balance -= $amount;
            $wallet->frozen += $amount;
            $wallet->save();
            
            // Record transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'freeze_deposit',
                'amount' => $amount,
                'balance_before' => $beforeBalance,
                'balance_after' => $wallet->balance,
                'frozen_before' => $beforeFrozen,
                'frozen_after' => $wallet->frozen,
                'reference_type' => Listing::class,
                'reference_id' => $listing->id,
                'description' => sprintf('مسدود سازی سپرده برای مزایده: %s', $listing->title),
            ]);
            
            return true;
        });
    }
    
    /**
     * Release frozen deposit back to available balance
     * 
     * @param User $user
     * @param float $amount
     * @param Listing $listing
     * @return bool
     * @throws WalletNotFoundException
     */
    public function releaseDeposit(User $user, float $amount, Listing $listing): bool
    {
        return DB::transaction(function () use ($user, $amount, $listing) {
            $wallet = Wallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();
            
            if (!$wallet) {
                throw new WalletNotFoundException($user->id);
            }
            
            $beforeBalance = $wallet->balance;
            $beforeFrozen = $wallet->frozen;
            
            $wallet->frozen -= $amount;
            $wallet->balance += $amount;
            $wallet->save();
            
            // Record transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'release_deposit',
                'amount' => $amount,
                'balance_before' => $beforeBalance,
                'balance_after' => $wallet->balance,
                'frozen_before' => $beforeFrozen,
                'frozen_after' => $wallet->frozen,
                'reference_type' => Listing::class,
                'reference_id' => $listing->id,
                'description' => sprintf('آزادسازی سپرده مزایده: %s', $listing->title),
            ]);
            
            return true;
        });
    }
    
    /**
     * Deduct frozen deposit (forfeit or apply to purchase)
     * 
     * @param User $user
     * @param float $amount
     * @param string $reason
     * @param Listing|null $listing
     * @return bool
     * @throws WalletNotFoundException
     */
    public function deductFrozenAmount(User $user, float $amount, string $reason, Listing $listing = null): bool
    {
        return DB::transaction(function () use ($user, $amount, $reason, $listing) {
            $wallet = Wallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();
            
            if (!$wallet) {
                throw new WalletNotFoundException($user->id);
            }
            
            $beforeBalance = $wallet->balance;
            $beforeFrozen = $wallet->frozen;
            
            $wallet->frozen -= $amount;
            $wallet->save();
            
            // Record transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deduct_frozen',
                'amount' => $amount,
                'balance_before' => $beforeBalance,
                'balance_after' => $wallet->balance,
                'frozen_before' => $beforeFrozen,
                'frozen_after' => $wallet->frozen,
                'reference_type' => $listing ? Listing::class : null,
                'reference_id' => $listing?->id,
                'description' => $reason,
            ]);
            
            return true;
        });
    }
    
    /**
     * Add funds to wallet
     * 
     * @param User $user
     * @param float $amount
     * @param string $source
     * @return bool
     * @throws WalletNotFoundException
     */
    public function addFunds(User $user, float $amount, string $source): bool
    {
        return DB::transaction(function () use ($user, $amount, $source) {
            $wallet = Wallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();
            
            if (!$wallet) {
                throw new WalletNotFoundException($user->id);
            }
            
            $beforeBalance = $wallet->balance;
            $beforeFrozen = $wallet->frozen;
            
            $wallet->balance += $amount;
            $wallet->save();
            
            // Record transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $amount,
                'balance_before' => $beforeBalance,
                'balance_after' => $wallet->balance,
                'frozen_before' => $beforeFrozen,
                'frozen_after' => $wallet->frozen,
                'description' => sprintf('افزایش موجودی: %s', $source),
            ]);
            
            return true;
        });
    }
    
    /**
     * Transfer funds between users
     * 
     * @param User $from
     * @param User $to
     * @param float $amount
     * @param string $reason
     * @param Listing|null $listing
     * @return bool
     * @throws InsufficientBalanceException
     * @throws WalletNotFoundException
     */
    public function transfer(User $from, User $to, float $amount, string $reason, Listing $listing = null): bool
    {
        return DB::transaction(function () use ($from, $to, $amount, $reason, $listing) {
            // Lock both wallets to prevent race conditions
            $fromWallet = Wallet::where('user_id', $from->id)
                ->lockForUpdate()
                ->first();
            
            $toWallet = Wallet::where('user_id', $to->id)
                ->lockForUpdate()
                ->first();
            
            if (!$fromWallet) {
                throw new WalletNotFoundException($from->id);
            }
            
            if (!$toWallet) {
                throw new WalletNotFoundException($to->id);
            }
            
            if ($fromWallet->balance < $amount) {
                throw new InsufficientBalanceException($amount, $fromWallet->balance);
            }
            
            // Deduct from sender
            $fromBeforeBalance = $fromWallet->balance;
            $fromBeforeFrozen = $fromWallet->frozen;
            $fromWallet->balance -= $amount;
            $fromWallet->save();
            
            // Add to receiver
            $toBeforeBalance = $toWallet->balance;
            $toBeforeFrozen = $toWallet->frozen;
            $toWallet->balance += $amount;
            $toWallet->save();
            
            // Record sender transaction
            WalletTransaction::create([
                'wallet_id' => $fromWallet->id,
                'type' => 'transfer_out',
                'amount' => $amount,
                'balance_before' => $fromBeforeBalance,
                'balance_after' => $fromWallet->balance,
                'frozen_before' => $fromBeforeFrozen,
                'frozen_after' => $fromWallet->frozen,
                'reference_type' => $listing ? Listing::class : null,
                'reference_id' => $listing?->id,
                'description' => sprintf('انتقال وجه به کاربر %s: %s', $to->name, $reason),
            ]);
            
            // Record receiver transaction
            WalletTransaction::create([
                'wallet_id' => $toWallet->id,
                'type' => 'transfer_in',
                'amount' => $amount,
                'balance_before' => $toBeforeBalance,
                'balance_after' => $toWallet->balance,
                'frozen_before' => $toBeforeFrozen,
                'frozen_after' => $toWallet->frozen,
                'reference_type' => $listing ? Listing::class : null,
                'reference_id' => $listing?->id,
                'description' => sprintf('دریافت وجه از کاربر %s: %s', $from->name, $reason),
            ]);
            
            return true;
        });
    }
    
    /**
     * Deduct amount from available balance (for purchases)
     * 
     * @param User $user
     * @param float $amount
     * @param string $reason
     * @param mixed $reference
     * @return bool
     * @throws InsufficientBalanceException
     * @throws WalletNotFoundException
     */
    public function deduct(User $user, float $amount, string $reason, $reference = null): bool
    {
        return DB::transaction(function () use ($user, $amount, $reason, $reference) {
            $wallet = Wallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();
            
            if (!$wallet) {
                throw new WalletNotFoundException($user->id);
            }
            
            if ($wallet->balance < $amount) {
                throw new InsufficientBalanceException($amount, $wallet->balance);
            }
            
            $beforeBalance = $wallet->balance;
            $beforeFrozen = $wallet->frozen;
            
            $wallet->balance -= $amount;
            $wallet->save();
            
            // Record transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'purchase',
                'amount' => $amount,
                'balance_before' => $beforeBalance,
                'balance_after' => $wallet->balance,
                'frozen_before' => $beforeFrozen,
                'frozen_after' => $wallet->frozen,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->id,
                'description' => $reason,
            ]);
            
            return true;
        });
    }
    
    /**
     * Refund amount to user (for order cancellations)
     * 
     * @param User $user
     * @param float $amount
     * @param string $reason
     * @param mixed $reference
     * @return bool
     * @throws WalletNotFoundException
     */
    public function refund(User $user, float $amount, string $reason, $reference = null): bool
    {
        return DB::transaction(function () use ($user, $amount, $reason, $reference) {
            $wallet = Wallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();
            
            if (!$wallet) {
                throw new WalletNotFoundException($user->id);
            }
            
            $beforeBalance = $wallet->balance;
            $beforeFrozen = $wallet->frozen;
            
            $wallet->balance += $amount;
            $wallet->save();
            
            // Record transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'refund',
                'amount' => $amount,
                'balance_before' => $beforeBalance,
                'balance_after' => $wallet->balance,
                'frozen_before' => $beforeFrozen,
                'frozen_after' => $wallet->frozen,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference?->id,
                'description' => $reason,
            ]);
            
            return true;
        });
    }
    
    /**
     * Get transaction history for user
     * Ordered by created_at DESC
     * 
     * @param User $user
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTransactionHistory(User $user, array $filters = [])
    {
        $query = WalletTransaction::where('wallet_id', $user->wallet->id)
            ->orderBy('created_at', 'desc');
        
        // Filter by transaction type
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        // Filter by date range
        if (isset($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }
        
        if (isset($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }
        
        return $query->paginate($filters['per_page'] ?? 50);
    }
    
    /**
     * Export transaction history to CSV
     * 
     * @param User $user
     * @param array $filters
     * @return string CSV content
     */
    public function exportTransactionsToCsv(User $user, array $filters = [])
    {
        $query = WalletTransaction::where('wallet_id', $user->wallet->id)
            ->orderBy('created_at', 'desc');
        
        // Apply same filters as getTransactionHistory
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (isset($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }
        
        if (isset($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }
        
        $transactions = $query->get();
        
        // Generate CSV
        $csv = "شناسه,نوع تراکنش,مبلغ,موجودی قبل,موجودی بعد,مسدود قبل,مسدود بعد,توضیحات,تاریخ\n";
        
        foreach ($transactions as $transaction) {
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $transaction->id,
                $transaction->type,
                $transaction->amount,
                $transaction->balance_before,
                $transaction->balance_after,
                $transaction->frozen_before,
                $transaction->frozen_after,
                str_replace([',', "\n"], [' ', ' '], $transaction->description ?? ''),
                $transaction->created_at->format('Y-m-d H:i:s')
            );
        }
        
        return $csv;
    }
}
