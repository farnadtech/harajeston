<?php

use App\Models\User;
use App\Models\Wallet;
use App\Models\Listing;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use App\Exceptions\Wallet\InsufficientBalanceException;
use App\Exceptions\Wallet\WalletNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->walletService = new WalletService();
});

describe('WalletService - freezeDeposit', function () {
    
    test('Property 4: Wallet Balance Invariant During Freeze - total balance remains constant', function () {
        // Property: balance + frozen before = balance + frozen after
        $user = User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'role' => 'buyer',
        ]);
        
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'balance' => 10000,
            'frozen' => 0,
        ]);
        
        $seller = User::create([
            'name' => 'Seller',
            'username' => 'seller',
            'email' => 'seller@example.com',
            'password' => 'password',
            'role' => 'seller',
        ]);
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'title' => 'Test Auction',
            'description' => 'Test',
            'type' => 'auction',
            'base_price' => 10000,
            'required_deposit' => 1000,
            'status' => 'pending',
        ]);
        
        $totalBefore = $wallet->balance + $wallet->frozen;
        
        $this->walletService->freezeDeposit($user, 1000, $listing);
        
        $wallet->refresh();
        $totalAfter = $wallet->balance + $wallet->frozen;
        
        expect($totalAfter)->toBe($totalBefore);
        expect((float)$wallet->balance)->toBe(9000.0);
        expect((float)$wallet->frozen)->toBe(1000.0);
    });
    
    test('freezeDeposit throws exception when insufficient balance', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 500,
            'frozen' => 0,
        ]);
        
        $listing = Listing::factory()->create(['type' => 'auction']);
        
        expect(fn() => $this->walletService->freezeDeposit($user, 1000, $listing))
            ->toThrow(InsufficientBalanceException::class);
    });
    
    test('freezeDeposit creates transaction record', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 10000,
            'frozen' => 0,
        ]);
        
        $listing = Listing::factory()->create(['type' => 'auction']);
        
        $this->walletService->freezeDeposit($user, 1000, $listing);
        
        $transaction = WalletTransaction::where('wallet_id', $wallet->id)->first();
        
        expect($transaction)->not->toBeNull();
        expect($transaction->type)->toBe('freeze_deposit');
        expect((float)$transaction->amount)->toBe(1000.0);
        expect((float)$transaction->balance_before)->toBe(10000.0);
        expect((float)$transaction->balance_after)->toBe(9000.0);
    });
    
    test('Property 29: Transaction Logging Completeness - all operations logged', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 10000,
            'frozen' => 0,
        ]);
        
        $listing = Listing::factory()->create(['type' => 'auction']);
        
        $this->walletService->freezeDeposit($user, 1000, $listing);
        
        $transactionCount = WalletTransaction::where('wallet_id', $wallet->id)->count();
        
        expect($transactionCount)->toBe(1);
    });
});

describe('WalletService - releaseDeposit', function () {
    
    test('Property 5: Freeze-Release Round Trip - returns to original state', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 10000,
            'frozen' => 0,
        ]);
        
        $listing = Listing::factory()->create(['type' => 'auction']);
        
        $originalBalance = $wallet->balance;
        $originalFrozen = $wallet->frozen;
        
        // Freeze
        $this->walletService->freezeDeposit($user, 1000, $listing);
        
        // Release
        $this->walletService->releaseDeposit($user, 1000, $listing);
        
        $wallet->refresh();
        
        expect((float)$wallet->balance)->toBe((float)$originalBalance);
        expect((float)$wallet->frozen)->toBe((float)$originalFrozen);
    });
    
    test('releaseDeposit creates transaction record', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 9000,
            'frozen' => 1000,
        ]);
        
        $listing = Listing::factory()->create(['type' => 'auction']);
        
        $this->walletService->releaseDeposit($user, 1000, $listing);
        
        $transaction = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('type', 'release_deposit')
            ->first();
        
        expect($transaction)->not->toBeNull();
        expect((float)$transaction->amount)->toBe(1000.0);
    });
});

describe('WalletService - deductFrozenAmount', function () {
    
    test('deductFrozenAmount reduces frozen balance', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 9000,
            'frozen' => 1000,
        ]);
        
        $listing = Listing::factory()->create(['type' => 'auction']);
        
        $this->walletService->deductFrozenAmount($user, 1000, 'پرداخت برنده مزایده', $listing);
        
        $wallet->refresh();
        
        expect((float)$wallet->frozen)->toBe(0.0);
        expect((float)$wallet->balance)->toBe(9000.0);
    });
});

describe('WalletService - addFunds', function () {
    
    test('addFunds increases available balance', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 5000,
            'frozen' => 0,
        ]);
        
        $this->walletService->addFunds($user, 3000, 'شارژ کیف پول');
        
        $wallet->refresh();
        
        expect((float)$wallet->balance)->toBe(8000.0);
    });
});

describe('WalletService - transfer', function () {
    
    test('transfer moves funds between users correctly', function () {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        
        $senderWallet = Wallet::factory()->create([
            'user_id' => $sender->id,
            'balance' => 10000,
            'frozen' => 0,
        ]);
        
        $receiverWallet = Wallet::factory()->create([
            'user_id' => $receiver->id,
            'balance' => 5000,
            'frozen' => 0,
        ]);
        
        $listing = Listing::factory()->create(['type' => 'auction']);
        
        $this->walletService->transfer($sender, $receiver, 2000, 'پرداخت مزایده', $listing);
        
        $senderWallet->refresh();
        $receiverWallet->refresh();
        
        expect((float)$senderWallet->balance)->toBe(8000.0);
        expect((float)$receiverWallet->balance)->toBe(7000.0);
    });
    
    test('transfer throws exception when sender has insufficient balance', function () {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        
        Wallet::factory()->create([
            'user_id' => $sender->id,
            'balance' => 500,
            'frozen' => 0,
        ]);
        
        Wallet::factory()->create([
            'user_id' => $receiver->id,
            'balance' => 5000,
            'frozen' => 0,
        ]);
        
        expect(fn() => $this->walletService->transfer($sender, $receiver, 2000, 'پرداخت'))
            ->toThrow(InsufficientBalanceException::class);
    });
    
    test('transfer creates two transaction records', function () {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        
        $senderWallet = Wallet::factory()->create([
            'user_id' => $sender->id,
            'balance' => 10000,
            'frozen' => 0,
        ]);
        
        $receiverWallet = Wallet::factory()->create([
            'user_id' => $receiver->id,
            'balance' => 5000,
            'frozen' => 0,
        ]);
        
        $this->walletService->transfer($sender, $receiver, 2000, 'پرداخت');
        
        $senderTransactions = WalletTransaction::where('wallet_id', $senderWallet->id)->count();
        $receiverTransactions = WalletTransaction::where('wallet_id', $receiverWallet->id)->count();
        
        expect($senderTransactions)->toBe(1);
        expect($receiverTransactions)->toBe(1);
    });
});

describe('WalletService - Negative Balance Prevention', function () {
    
    test('Property 6: Negative Balance Prevention - balance never goes negative', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 1000,
            'frozen' => 0,
        ]);
        
        try {
            $this->walletService->deduct($user, 2000, 'خرید محصول');
        } catch (InsufficientBalanceException $e) {
            // Expected exception
        }
        
        $wallet->refresh();
        
        expect((float)$wallet->balance)->toBeGreaterThanOrEqual(0);
    });
    
    test('deduct throws exception when insufficient balance', function () {
        $user = User::factory()->create();
        Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 500,
            'frozen' => 0,
        ]);
        
        expect(fn() => $this->walletService->deduct($user, 1000, 'خرید'))
            ->toThrow(InsufficientBalanceException::class);
    });
});

describe('WalletService - refund', function () {
    
    test('refund increases balance correctly', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 5000,
            'frozen' => 0,
        ]);
        
        $this->walletService->refund($user, 2000, 'لغو سفارش');
        
        $wallet->refresh();
        
        expect((float)$wallet->balance)->toBe(7000.0);
    });
    
    test('refund creates transaction record', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 5000,
            'frozen' => 0,
        ]);
        
        $this->walletService->refund($user, 2000, 'لغو سفارش');
        
        $transaction = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('type', 'refund')
            ->first();
        
        expect($transaction)->not->toBeNull();
        expect((float)$transaction->amount)->toBe(2000.0);
    });
});

describe('WalletService - Transaction History', function () {
    
    test('Property 42: Transaction History Ordering - transactions ordered by created_at DESC', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 10000,
            'frozen' => 0,
        ]);
        
        // Create multiple transactions with different timestamps
        $this->walletService->addFunds($user, 1000, 'شارژ اول');
        sleep(1);
        $this->walletService->addFunds($user, 2000, 'شارژ دوم');
        sleep(1);
        $this->walletService->addFunds($user, 3000, 'شارژ سوم');
        
        // Get transaction history
        $history = $this->walletService->getTransactionHistory($user, ['per_page' => 10]);
        
        // Verify ordering: most recent first
        $transactions = $history->items();
        
        expect(count($transactions))->toBeGreaterThanOrEqual(3);
        
        // Check that each transaction is newer than or equal to the next
        for ($i = 0; $i < count($transactions) - 1; $i++) {
            $current = $transactions[$i]->created_at;
            $next = $transactions[$i + 1]->created_at;
            
            expect($current->greaterThanOrEqualTo($next))->toBeTrue();
        }
    });
    
    test('getTransactionHistory returns paginated results', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 10000,
            'frozen' => 0,
        ]);
        
        // Create 5 transactions
        for ($i = 0; $i < 5; $i++) {
            $this->walletService->addFunds($user, 1000, "شارژ {$i}");
        }
        
        // Get with pagination
        $history = $this->walletService->getTransactionHistory($user, ['per_page' => 3]);
        
        expect($history->total())->toBe(5);
        expect(count($history->items()))->toBe(3);
    });
    
    test('getTransactionHistory filters by transaction type', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 10000,
            'frozen' => 0,
        ]);
        
        $listing = Listing::factory()->create(['type' => 'auction']);
        
        // Create different types of transactions
        $this->walletService->addFunds($user, 1000, 'شارژ');
        $this->walletService->freezeDeposit($user, 500, $listing);
        $this->walletService->addFunds($user, 2000, 'شارژ دوم');
        
        // Filter by deposit type
        $history = $this->walletService->getTransactionHistory($user, [
            'type' => 'deposit',
            'per_page' => 50
        ]);
        
        $transactions = $history->items();
        
        foreach ($transactions as $transaction) {
            expect($transaction->type)->toBe('deposit');
        }
        
        expect(count($transactions))->toBe(2);
    });
    
    test('getTransactionHistory filters by date range', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 10000,
            'frozen' => 0,
        ]);
        
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');
        
        // Create transaction today
        $this->walletService->addFunds($user, 1000, 'شارژ امروز');
        
        // Get transactions from today
        $history = $this->walletService->getTransactionHistory($user, [
            'from' => $today,
            'to' => $today,
            'per_page' => 50
        ]);
        
        expect($history->total())->toBeGreaterThanOrEqual(1);
        
        // Get transactions from yesterday (should be 0)
        $historyYesterday = $this->walletService->getTransactionHistory($user, [
            'from' => $yesterday,
            'to' => $yesterday,
            'per_page' => 50
        ]);
        
        expect($historyYesterday->total())->toBe(0);
    });
});

describe('WalletService - CSV Export', function () {
    
    test('exportTransactionsToCsv generates valid CSV', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 10000,
            'frozen' => 0,
        ]);
        
        // Create transactions
        $this->walletService->addFunds($user, 1000, 'شارژ اول');
        $this->walletService->addFunds($user, 2000, 'شارژ دوم');
        
        // Export to CSV
        $csv = $this->walletService->exportTransactionsToCsv($user);
        
        // Verify CSV structure
        expect($csv)->toContain('شناسه,نوع تراکنش,مبلغ');
        expect($csv)->toContain('deposit');
        expect($csv)->toContain('1000');
        expect($csv)->toContain('2000');
    });
    
    test('exportTransactionsToCsv applies filters', function () {
        $user = User::factory()->create();
        $wallet = Wallet::factory()->create([
            'user_id' => $user->id,
            'balance' => 10000,
            'frozen' => 0,
        ]);
        
        $listing = Listing::factory()->create(['type' => 'auction']);
        
        // Create different types
        $this->walletService->addFunds($user, 1000, 'شارژ');
        $this->walletService->freezeDeposit($user, 500, $listing);
        
        // Export only deposits
        $csv = $this->walletService->exportTransactionsToCsv($user, ['type' => 'deposit']);
        
        // Should contain deposit but not freeze_deposit
        expect($csv)->toContain('deposit');
        expect($csv)->not->toContain('freeze_deposit');
    });
});
