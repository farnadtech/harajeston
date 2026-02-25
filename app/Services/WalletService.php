<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Listing;
use App\Models\WalletTransaction;
use App\Exceptions\Wallet\InsufficientBalanceException;
use App\Exceptions\Wallet\WalletNotFoundException;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Add funds to user wallet
     */
    public function addFunds(User $user, float $amount, string $description = 'افزودن موجودی'): bool
    {
        return DB::transaction(function () use ($user, $amount, $description) {
            $wallet = Wallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();
            
            if (!$wallet) {
                throw new WalletNotFoundException($user->id);
            }
            
            $beforeBalance = $wallet->balance;
            
            $wallet->balance += $amount;
            $wallet->save();
            
            // Record transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => 'deposit',
                'amount' => $amount,
                'final_amount' => $amount,
                'balance_before' => $beforeBalance,
                'balance_after' => $wallet->balance,
                'frozen_before' => $wallet->frozen,
                'frozen_after' => $wallet->frozen,
                'description' => $description,
            ]);
            
            return true;
        });
    }

    /**
     * Calculate total amount with tax for wallet charge
     * 
     * @param float $amount Base amount to charge
     * @return array ['base_amount' => float, 'tax' => float, 'total' => float, 'tax_percentage' => float]
     */
    public function calculateChargeWithTax(float $amount): array
    {
        $taxPercentage = \App\Models\SiteSetting::get('wallet_charge_tax', 0);
        $tax = ($amount * $taxPercentage) / 100;
        $total = $amount + $tax;
        
        return [
            'base_amount' => $amount,
            'tax' => $tax,
            'total' => $total,
            'tax_percentage' => $taxPercentage,
        ];
    }

    /**
     * Deduct amount from user wallet
     */
    public function deduct(User $user, float $amount, string $description = 'کسر از حساب', ?Listing $listing = null): bool
    {
        return DB::transaction(function () use ($user, $amount, $description, $listing) {
            $wallet = Wallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();
            
            if (!$wallet) {
                throw new WalletNotFoundException($user->id);
            }
            
            if ($wallet->balance < $amount) {
                throw new InsufficientBalanceException($user->id, $amount, $wallet->balance);
            }
            
            $beforeBalance = $wallet->balance;
            
            $wallet->balance -= $amount;
            $wallet->save();
            
            // Record transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => 'withdrawal',
                'amount' => $amount,
                'final_amount' => $amount,
                'balance_before' => $beforeBalance,
                'balance_after' => $wallet->balance,
                'frozen_before' => $wallet->frozen,
                'frozen_after' => $wallet->frozen,
                'reference_type' => $listing ? Listing::class : null,
                'reference_id' => $listing?->id,
                'description' => $description,
            ]);
            
            return true;
        });
    }

    /**
     * Freeze deposit amount in user wallet
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
                throw new InsufficientBalanceException($user->id, $amount, $wallet->balance);
            }
            
            $beforeBalance = $wallet->balance;
            $beforeFrozen = $wallet->frozen;
            
            $wallet->balance -= $amount;
            $wallet->frozen += $amount;
            $wallet->save();
            
            // Record transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'user_id' => $user->id,
                'type' => 'freeze_deposit',
                'amount' => $amount,
                'final_amount' => $amount,
                'balance_before' => $beforeBalance,
                'balance_after' => $wallet->balance,
                'frozen_before' => $beforeFrozen,
                'frozen_after' => $wallet->frozen,
                'reference_type' => Listing::class,
                'reference_id' => $listing->id,
                'description' => sprintf('مسدود سازی سپرده مزایده: %s', $listing->title),
            ]);
            
            return true;
        });
    }

    /**
     * Release frozen deposit back to available balance
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
                'user_id' => $user->id,
                'type' => 'release_deposit',
                'amount' => $amount,
                'final_amount' => $amount,
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
     * Deduct amount from frozen balance
     */
    public function deductFrozenAmount(User $user, float $amount, string $description, ?Listing $listing = null): bool
    {
        return DB::transaction(function () use ($user, $amount, $description, $listing) {
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
                'user_id' => $user->id,
                'type' => 'deduct_frozen',
                'amount' => $amount,
                'final_amount' => $amount,
                'balance_before' => $beforeBalance,
                'balance_after' => $wallet->balance,
                'frozen_before' => $beforeFrozen,
                'frozen_after' => $wallet->frozen,
                'reference_type' => $listing ? Listing::class : null,
                'reference_id' => $listing?->id,
                'description' => $description,
            ]);
            
            return true;
        });
    }
}
