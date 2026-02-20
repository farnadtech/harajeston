# Design Document: Iranian Multi-Vendor Marketplace

## Overview

The Iranian Multi-Vendor Marketplace is built on Laravel 10 with a modern frontend stack (Blade + Livewire 3 + Tailwind CSS 3 + Alpine.js) to deliver a comprehensive e-commerce and auction platform. The system combines sophisticated auction functionality with direct sales, seller storefronts (ویترین), shopping cart, order management, and shipping integration. The architecture emphasizes transaction safety, race condition prevention, inventory management, and a seamless multi-vendor experience.

### Core Design Principles

1. **Transaction Safety First**: All financial operations wrapped in database transactions with row-level locking
2. **Service-Oriented Architecture**: Business logic encapsulated in dedicated service classes
3. **Multi-Vendor Support**: Each seller has an independent storefront with customization options
4. **Unified Listing System**: Single model supporting auction, direct_sale, and hybrid types
5. **Real-Time User Experience**: Livewire components for live updates without full page reloads
6. **Inventory Management**: Atomic stock operations with race condition prevention
7. **API-Ready Design**: Clean separation enabling future API layer with Laravel Sanctum
8. **RTL-First UI**: Complete right-to-left design with Farsi localization and Jalali calendar integration

### Technology Stack Justification

- **Laravel 10**: Stable LTS with improved performance, modern syntax, and robust ecosystem
- **Livewire 3**: Reactive components for real-time updates without complex JavaScript
- **Tailwind CSS 3**: Utility-first CSS for rapid, consistent UI development with RTL support
- **Alpine.js**: Lightweight JavaScript for interactive UI elements (modals, dropdowns, cart)
- **MySQL**: Reliable ACID-compliant database with excellent Laravel integration
- **Laravel Sanctum**: Token-based API authentication for future mobile app

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Presentation Layer                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │ Blade Views  │  │   Livewire   │  │  Alpine.js   │      │
│  │   (RTL UI)   │  │  Components  │  │ (Cart/Modal) │      │
│  │  Storefront  │  │ Auction/Shop │  │              │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
┌─────────────────────────────────────────────────────────────┐
│                    Application Layer                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │ Controllers  │  │   Services   │  │    Jobs      │      │
│  │ Store/Cart/  │  │ Auction/Shop │  │ (Scheduled)  │      │
│  │ Order/Listing│  │ Order/Stock  │  │              │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
┌─────────────────────────────────────────────────────────────┐
│                      Domain Layer                            │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Models     │  │  Eloquent    │  │  Events      │      │
│  │ Listing/Store│  │ Relationships│  │ Order/Stock  │      │
│  │ Cart/Order   │  │              │  │              │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            │
┌─────────────────────────────────────────────────────────────┐
│                    Infrastructure Layer                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │    MySQL     │  │    Cache     │  │   Storage    │      │
│  │   Database   │  │    (Redis)   │  │ (Images/Logo)│      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
```

### Request Flow Examples

**Auction Bid Placement Flow:**
```
User clicks "Place Bid" 
  → Livewire Component (AuctionBidding)
    → BidService::placeBid()
      → DB Transaction Start
        → Lock Listing Row
        → Validate Deposit Paid
        → Validate Bid Amount
        → Create Bid Record
        → Update Listing Current Highest
      → DB Transaction Commit
    → Broadcast BidPlaced Event
  → Livewire Updates All Connected Clients
```

**Direct Sale Purchase Flow:**
```
User clicks "Add to Cart"
  → Livewire Component (DirectSalePurchase)
    → CartService::addToCart()
      → Validate Stock Availability
      → Get or Create Cart
      → Add/Update Cart Item
    → Update Cart Count in Header
  → User proceeds to Checkout
    → OrderService::createOrderFromCart()
      → DB Transaction Start
        → Lock All Listing Rows
        → Validate Stock for All Items
        → Decrement Stock Atomically
        → Create Order & Order Items
        → Process Payment (WalletService)
        → Clear Cart
      → DB Transaction Commit
    → Send Order Notifications
```

**Auction Ending Flow:**
```
Scheduled Job (CheckAuctionEndings)
  → AuctionService::endAuction()
    → DB Transaction Start
      → Lock Listing Row
      → Identify Top 3 Bidders
      → Keep Top 3 Deposits Frozen
      → Release Other Deposits (WalletService)
      → Update Listing Status to 'ended'
    → DB Transaction Commit
  → Dispatch Notifications
```

**Storefront View Flow:**
```
User visits /store/{username}
  → StoreController::show()
    → StoreService::getStoreBySlug()
      → Retrieve Store with Listings
      → Calculate Seller Statistics
    → Return Storefront View
  → Display Banner, Logo, Description
  → Display Active Listings (Auctions + Direct Sales)
```

## Components and Interfaces

### Core Service Classes

#### WalletService

Handles all wallet operations with transaction safety and race condition prevention.

```php
class WalletService
{
    /**
     * Freeze deposit amount for auction participation
     * 
     * @param User $user
     * @param float $amount
     * @param Auction $auction
     * @return bool
     * @throws InsufficientBalanceException
     */
    public function freezeDeposit(User $user, float $amount, Auction $auction): bool
    {
        return DB::transaction(function () use ($user, $amount, $auction) {
            $wallet = Wallet::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();
            
            if ($wallet->balance < $amount) {
                throw new InsufficientBalanceException();
            }
            
            $wallet->balance -= $amount;
            $wallet->frozen += $amount;
            $wallet->save();
            
            // Record transaction
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'freeze_deposit',
                'amount' => $amount,
                'reference_type' => 'auction',
                'reference_id' => $auction->id,
            ]);
            
            return true;
        });
    }
    
    /**
     * Release frozen deposit back to available balance
     */
    public function releaseDeposit(User $user, float $amount, Auction $auction): bool;
    
    /**
     * Deduct frozen deposit (forfeit or apply to purchase)
     */
    public function deductFrozenAmount(User $user, float $amount, string $reason): bool;
    
    /**
     * Add funds to wallet
     */
    public function addFunds(User $user, float $amount, string $source): bool;
    
    /**
     * Transfer funds between users
     */
    public function transfer(User $from, User $to, float $amount, string $reason): bool;
}
```

#### AuctionService

Manages auction lifecycle and business logic.

```php
class AuctionService
{
    /**
     * Create new auction with auto-calculated deposit
     */
    public function createAuction(User $seller, array $data): Auction
    {
        $deposit = $data['base_price'] * 0.10;
        
        return Auction::create([
            'seller_id' => $seller->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'base_price' => $data['base_price'],
            'required_deposit' => $deposit,
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'status' => 'pending',
        ]);
    }
    
    /**
     * Process auction ending - identify top 3 and release others
     */
    public function endAuction(Auction $auction): void
    {
        DB::transaction(function () use ($auction) {
            $auction = Auction::where('id', $auction->id)
                ->lockForUpdate()
                ->first();
            
            // Get all participants ordered by bid amount
            $participants = $auction->bids()
                ->with('user')
                ->orderBy('amount', 'desc')
                ->get()
                ->unique('user_id');
            
            $top3 = $participants->take(3);
            $others = $participants->skip(3);
            
            // Release deposits for non-top-3
            foreach ($others as $bid) {
                $this->walletService->releaseDeposit(
                    $bid->user,
                    $auction->required_deposit,
                    $auction
                );
            }
            
            // Update auction status
            $auction->status = 'ended';
            $auction->save();
            
            // Set rank 1 as current winner
            if ($top3->isNotEmpty()) {
                $auction->current_winner_id = $top3->first()->user_id;
                $auction->finalization_deadline = now()->addHours(48);
                $auction->save();
            }
        });
    }
    
    /**
     * Process winner payment completion
     */
    public function completeWinnerPayment(Auction $auction, User $winner): void;
    
    /**
     * Handle finalization timeout - cascade to next bidder
     */
    public function handleFinalizationTimeout(Auction $auction): void;
    
    /**
     * Start auction when start_time is reached
     */
    public function startAuction(Auction $auction): void;
}
```

#### BidService

Handles bid placement with validation and race condition prevention.

```php
class BidService
{
    /**
     * Place a bid on an auction
     * 
     * @throws DepositNotPaidException
     * @throws InvalidBidAmountException
     * @throws AuctionNotActiveException
     */
    public function placeBid(User $user, Auction $auction, float $amount): Bid
    {
        return DB::transaction(function () use ($user, $auction, $amount) {
            // Lock auction row to prevent race conditions
            $auction = Auction::where('id', $auction->id)
                ->lockForUpdate()
                ->first();
            
            // Validate auction is active
            if ($auction->status !== 'active') {
                throw new AuctionNotActiveException();
            }
            
            // Validate deposit paid
            $participation = AuctionParticipation::where('auction_id', $auction->id)
                ->where('user_id', $user->id)
                ->first();
            
            if (!$participation) {
                throw new DepositNotPaidException();
            }
            
            // Validate bid amount
            if ($amount <= $auction->current_highest_bid) {
                throw new InvalidBidAmountException('Bid must be higher than current highest');
            }
            
            if ($amount < $auction->base_price) {
                throw new InvalidBidAmountException('Bid must be at least base price');
            }
            
            // Create bid
            $bid = Bid::create([
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'amount' => $amount,
            ]);
            
            // Update auction
            $auction->current_highest_bid = $amount;
            $auction->highest_bidder_id = $user->id;
            $auction->save();
            
            return $bid;
        });
    }
    
    /**
     * Get current ranking for an auction
     */
    public function getCurrentRankings(Auction $auction): Collection;
}
```

#### DepositService

Specialized service for deposit management and participation.

```php
class DepositService
{
    /**
     * Process auction participation by freezing deposit
     */
    public function participateInAuction(User $user, Listing $listing): AuctionParticipation
    {
        return DB::transaction(function () use ($user, $listing) {
            // Check if already participating
            $existing = AuctionParticipation::where('listing_id', $listing->id)
                ->where('user_id', $user->id)
                ->first();
            
            if ($existing) {
                throw new AlreadyParticipatingException();
            }
            
            // Freeze deposit
            $this->walletService->freezeDeposit(
                $user,
                $listing->required_deposit,
                $listing
            );
            
            // Record participation
            return AuctionParticipation::create([
                'listing_id' => $listing->id,
                'user_id' => $user->id,
                'deposit_amount' => $listing->required_deposit,
            ]);
        });
    }
}
```

#### StoreService (NEW)

Manages seller storefronts (ویترین).

```php
class StoreService
{
    /**
     * Create store for new seller
     */
    public function createStore(User $seller, string $username): Store
    {
        $slug = Str::slug($username);
        
        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while (Store::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }
        
        return Store::create([
            'user_id' => $seller->id,
            'store_name' => $seller->name,
            'slug' => $slug,
            'is_active' => true,
        ]);
    }
    
    /**
     * Update store profile
     */
    public function updateStoreProfile(Store $store, array $data): Store
    {
        // Validate and handle banner upload
        if (isset($data['banner'])) {
            $this->validateImage($data['banner'], 2048, 1920, 400); // 2MB, 1920x400
            $bannerPath = $data['banner']->store('stores/banners', 'public');
            $store->banner_image = $bannerPath;
        }
        
        // Validate and handle logo upload
        if (isset($data['logo'])) {
            $this->validateImage($data['logo'], 1024, 300, 300); // 1MB, 300x300
            $logoPath = $data['logo']->store('stores/logos', 'public');
            $store->logo_image = $logoPath;
        }
        
        $store->update([
            'store_name' => $data['store_name'] ?? $store->store_name,
            'description' => $data['description'] ?? $store->description,
        ]);
        
        return $store;
    }
    
    /**
     * Get store by slug with listings
     */
    public function getStoreBySlug(string $slug): ?Store
    {
        return Store::where('slug', $slug)
            ->where('is_active', true)
            ->with(['user', 'listings' => function ($query) {
                $query->where('status', 'active')
                    ->orWhere('status', 'pending')
                    ->orderBy('created_at', 'desc');
            }])
            ->first();
    }
}
```

#### ListingService (NEW)

Unified service for managing all listing types (auction, direct_sale, hybrid).

```php
class ListingService
{
    /**
     * Create listing with type-specific validation
     */
    public function createListing(User $seller, array $data): Listing
    {
        $type = $data['type']; // auction, direct_sale, hybrid
        
        // Type-specific validation and processing
        if ($type === 'auction' || $type === 'hybrid') {
            $data['required_deposit'] = $data['base_price'] * 0.10;
            $this->validateAuctionTimes($data['start_time'], $data['end_time']);
        }
        
        if ($type === 'direct_sale' || $type === 'hybrid') {
            if (!isset($data['price']) || !isset($data['stock'])) {
                throw new InvalidListingDataException('Price and stock required for direct sale');
            }
        }
        
        if ($type === 'hybrid') {
            if ($data['price'] <= $data['base_price']) {
                throw new InvalidListingDataException('Direct sale price must be higher than auction base price');
            }
        }
        
        return Listing::create([
            'seller_id' => $seller->id,
            'type' => $type,
            'title' => $data['title'],
            'description' => $data['description'],
            'category' => $data['category'] ?? null,
            // Auction fields
            'base_price' => $data['base_price'] ?? null,
            'required_deposit' => $data['required_deposit'] ?? null,
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            // Direct sale fields
            'price' => $data['price'] ?? null,
            'stock' => $data['stock'] ?? null,
            'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
            'status' => $type === 'auction' ? 'pending' : 'active',
        ]);
    }
    
    /**
     * Update stock with atomic operation
     */
    public function updateStock(Listing $listing, int $newStock, string $reason): void
    {
        DB::transaction(function () use ($listing, $newStock, $reason) {
            $listing = Listing::where('id', $listing->id)
                ->lockForUpdate()
                ->first();
            
            $oldStock = $listing->stock;
            $listing->stock = $newStock;
            
            if ($newStock === 0) {
                $listing->status = 'out_of_stock';
            } elseif ($listing->status === 'out_of_stock' && $newStock > 0) {
                $listing->status = 'active';
            }
            
            $listing->save();
            
            // Log stock change
            StockLog::create([
                'listing_id' => $listing->id,
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'reason' => $reason,
            ]);
            
            // Send low stock alert
            if ($newStock > 0 && $newStock <= $listing->low_stock_threshold) {
                $listing->seller->notify(new LowStockAlertNotification($listing));
            }
        });
    }
    
    /**
     * Decrement stock atomically
     */
    public function decrementStock(Listing $listing, int $quantity): void
    {
        DB::transaction(function () use ($listing, $quantity) {
            $listing = Listing::where('id', $listing->id)
                ->lockForUpdate()
                ->first();
            
            if ($listing->stock < $quantity) {
                throw new OutOfStockException();
            }
            
            $listing->stock -= $quantity;
            
            if ($listing->stock === 0) {
                $listing->status = 'out_of_stock';
            }
            
            $listing->save();
        });
    }
}
```

#### CartService (NEW)

Manages shopping cart operations.

```php
class CartService
{
    /**
     * Add item to cart
     */
    public function addToCart(User $user, Listing $listing, int $quantity): CartItem
    {
        // Validate listing type
        if (!in_array($listing->type, ['direct_sale', 'hybrid'])) {
            throw new InvalidListingTypeException('Only direct sale items can be added to cart');
        }
        
        // Validate stock
        if ($listing->stock < $quantity) {
            throw new OutOfStockException();
        }
        
        return DB::transaction(function () use ($user, $listing, $quantity) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('listing_id', $listing->id)
                ->first();
            
            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $quantity;
                if ($listing->stock < $newQuantity) {
                    throw new OutOfStockException();
                }
                $cartItem->quantity = $newQuantity;
                $cartItem->save();
            } else {
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'listing_id' => $listing->id,
                    'quantity' => $quantity,
                    'price_snapshot' => $listing->price,
                ]);
            }
            
            return $cartItem;
        });
    }
    
    /**
     * Get cart with calculated totals
     */
    public function getCartWithTotals(User $user): array
    {
        $cart = Cart::where('user_id', $user->id)
            ->with(['items.listing.seller', 'items.listing.shippingMethods'])
            ->first();
        
        if (!$cart) {
            return ['items' => [], 'subtotal' => 0, 'shipping' => 0, 'total' => 0];
        }
        
        $subtotal = 0;
        $shipping = 0;
        $itemsBySeller = [];
        
        foreach ($cart->items as $item) {
            $itemTotal = $item->price_snapshot * $item->quantity;
            $subtotal += $itemTotal;
            
            $sellerId = $item->listing->seller_id;
            if (!isset($itemsBySeller[$sellerId])) {
                $itemsBySeller[$sellerId] = [];
            }
            $itemsBySeller[$sellerId][] = $item;
        }
        
        // Calculate shipping per seller
        foreach ($itemsBySeller as $sellerId => $items) {
            // Get cheapest shipping method for this seller's items
            $shippingCost = $this->calculateShippingForSeller($items);
            $shipping += $shippingCost;
        }
        
        return [
            'items' => $cart->items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $subtotal + $shipping,
        ];
    }
    
    /**
     * Update cart item quantity
     */
    public function updateCartItem(CartItem $cartItem, int $quantity): void;
    
    /**
     * Remove item from cart
     */
    public function removeFromCart(CartItem $cartItem): void;
    
    /**
     * Clear cart
     */
    public function clearCart(Cart $cart): void;
}
```

#### OrderService (NEW)

Manages order creation and lifecycle.

```php
class OrderService
{
    /**
     * Create order from cart
     */
    public function createOrderFromCart(User $buyer, array $shippingData): Collection
    {
        return DB::transaction(function () use ($buyer, $shippingData) {
            $cart = Cart::where('user_id', $buyer->id)
                ->with('items.listing')
                ->lockForUpdate()
                ->first();
            
            if (!$cart || $cart->items->isEmpty()) {
                throw new CartEmptyException();
            }
            
            // Group items by seller
            $itemsBySeller = $cart->items->groupBy('listing.seller_id');
            $orders = collect();
            
            foreach ($itemsBySeller as $sellerId => $items) {
                // Validate stock for all items
                foreach ($items as $item) {
                    $listing = Listing::where('id', $item->listing_id)
                        ->lockForUpdate()
                        ->first();
                    
                    if ($listing->stock < $item->quantity) {
                        throw new OutOfStockException("Item {$listing->title} is out of stock");
                    }
                }
                
                // Calculate totals
                $subtotal = $items->sum(fn($item) => $item->price_snapshot * $item->quantity);
                $shippingCost = $this->calculateShipping($items, $shippingData[$sellerId]);
                $total = $subtotal + $shippingCost;
                
                // Verify buyer has sufficient balance
                if ($buyer->wallet->balance < $total) {
                    throw new InsufficientBalanceException();
                }
                
                // Create order
                $order = Order::create([
                    'order_number' => $this->generateOrderNumber(),
                    'buyer_id' => $buyer->id,
                    'seller_id' => $sellerId,
                    'status' => 'pending',
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'total' => $total,
                    'shipping_method_id' => $shippingData[$sellerId]['method_id'],
                    'shipping_address' => $shippingData[$sellerId]['address'],
                ]);
                
                // Create order items and decrement stock
                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'listing_id' => $item->listing_id,
                        'quantity' => $item->quantity,
                        'price_snapshot' => $item->price_snapshot,
                        'subtotal' => $item->price_snapshot * $item->quantity,
                    ]);
                    
                    // Decrement stock
                    app(ListingService::class)->decrementStock(
                        $item->listing,
                        $item->quantity
                    );
                }
                
                // Process payment
                app(WalletService::class)->transfer(
                    $buyer,
                    User::find($sellerId),
                    $total,
                    "Order #{$order->order_number}"
                );
                
                // Send notifications
                $buyer->notify(new OrderPlacedNotification($order));
                User::find($sellerId)->notify(new OrderPlacedNotification($order));
                
                $orders->push($order);
            }
            
            // Clear cart
            app(CartService::class)->clearCart($cart);
            
            return $orders;
        });
    }
    
    /**
     * Update order status
     */
    public function updateOrderStatus(Order $order, string $newStatus): void;
    
    /**
     * Cancel order with refund
     */
    public function cancelOrder(Order $order): void;
    
    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}
```

#### ShippingService (NEW)

Manages shipping methods and cost calculation.

```php
class ShippingService
{
    /**
     * Create shipping method (admin)
     */
    public function createShippingMethod(array $data): ShippingMethod
    {
        return ShippingMethod::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'base_cost' => $data['base_cost'],
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);
    }
    
    /**
     * Attach shipping methods to listing
     */
    public function attachShippingToListing(Listing $listing, array $methodIds, array $adjustments = []): void
    {
        $syncData = [];
        foreach ($methodIds as $methodId) {
            $syncData[$methodId] = [
                'custom_cost_adjustment' => $adjustments[$methodId] ?? 0,
            ];
        }
        
        $listing->shippingMethods()->sync($syncData);
    }
    
    /**
     * Calculate shipping cost
     */
    public function calculateShippingCost(Listing $listing, int $shippingMethodId): float
    {
        $pivot = $listing->shippingMethods()
            ->where('shipping_method_id', $shippingMethodId)
            ->first();
        
        if (!$pivot) {
            throw new ShippingMethodNotFoundException();
        }
        
        $baseCost = $pivot->base_cost;
        $adjustment = $pivot->pivot->custom_cost_adjustment ?? 0;
        
        return $baseCost + $adjustment;
    }
}
```

### Livewire Components

#### AuctionBidding Component

Real-time bidding interface with live updates.

```php
class AuctionBidding extends Component
{
    public Auction $auction;
    public $bidAmount;
    public $currentHighestBid;
    public $rankings;
    
    protected $listeners = ['bidPlaced' => 'refreshBids'];
    
    public function mount(Auction $auction)
    {
        $this->auction = $auction;
        $this->currentHighestBid = $auction->current_highest_bid;
        $this->loadRankings();
    }
    
    public function placeBid()
    {
        $this->validate([
            'bidAmount' => 'required|numeric|min:' . ($this->currentHighestBid + 1),
        ]);
        
        try {
            $bid = app(BidService::class)->placeBid(
                auth()->user(),
                $this->auction,
                $this->bidAmount
            );
            
            // Broadcast to all connected clients
            broadcast(new BidPlaced($bid))->toOthers();
            
            $this->refreshBids();
            $this->bidAmount = '';
            
            session()->flash('success', 'پیشنهاد شما ثبت شد');
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }
    
    public function refreshBids()
    {
        $this->auction->refresh();
        $this->currentHighestBid = $this->auction->current_highest_bid;
        $this->loadRankings();
    }
    
    private function loadRankings()
    {
        $this->rankings = app(BidService::class)
            ->getCurrentRankings($this->auction);
    }
    
    public function render()
    {
        return view('livewire.auction-bidding');
    }
}
```

#### AuctionCountdown Component

Live countdown timer for auction ending.

```php
class AuctionCountdown extends Component
{
    public Auction $auction;
    public $remainingTime;
    public $isEnded = false;
    
    public function mount(Auction $auction)
    {
        $this->auction = $auction;
        $this->calculateRemainingTime();
    }
    
    public function calculateRemainingTime()
    {
        if ($this->auction->status === 'ended') {
            $this->isEnded = true;
            return;
        }
        
        $now = now();
        $endTime = $this->auction->end_time;
        
        if ($now->greaterThanOrEqualTo($endTime)) {
            $this->isEnded = true;
            $this->remainingTime = 'پایان یافته';
        } else {
            $diff = $now->diff($endTime);
            $this->remainingTime = sprintf(
                '%d روز %d:%02d:%02d',
                $diff->days,
                $diff->h,
                $diff->i,
                $diff->s
            );
        }
    }
    
    public function render()
    {
        return view('livewire.auction-countdown');
    }
}
```

### Controllers

Controllers remain thin, delegating to services.

```php
class AuctionController extends Controller
{
    public function __construct(
        private AuctionService $auctionService,
        private DepositService $depositService
    ) {}
    
    public function store(StoreAuctionRequest $request)
    {
        $auction = $this->auctionService->createAuction(
            auth()->user(),
            $request->validated()
        );
        
        return redirect()
            ->route('auctions.show', $auction)
            ->with('success', 'آگهی شما ایجاد شد');
    }
    
    public function participate(Auction $auction)
    {
        try {
            $this->depositService->participateInAuction(
                auth()->user(),
                $auction
            );
            
            return back()->with('success', 'شما در مزایده شرکت کردید');
        } catch (InsufficientBalanceException $e) {
            return back()->with('error', 'موجودی کافی نیست');
        }
    }
}
```


## Data Models

### Database Schema

#### users table
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL, -- NEW: for storefront URL
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('buyer', 'seller', 'admin') DEFAULT 'buyer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role)
);
```

#### wallets table
```sql
CREATE TABLE wallets (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED UNIQUE NOT NULL,
    balance DECIMAL(15, 2) DEFAULT 0.00,
    frozen DECIMAL(15, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    CONSTRAINT chk_balance_positive CHECK (balance >= 0),
    CONSTRAINT chk_frozen_positive CHECK (frozen >= 0)
);
```

#### stores table (NEW)
```sql
CREATE TABLE stores (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED UNIQUE NOT NULL,
    store_name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    banner_image VARCHAR(500) NULL,
    logo_image VARCHAR(500) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active)
);
```

#### listings table (UPDATED - replaces auctions)
```sql
CREATE TABLE listings (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    seller_id BIGINT UNSIGNED NOT NULL,
    type ENUM('auction', 'direct_sale', 'hybrid') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100) NULL,
    
    -- Auction-specific fields
    base_price DECIMAL(15, 2) NULL,
    required_deposit DECIMAL(15, 2) NULL,
    current_highest_bid DECIMAL(15, 2) DEFAULT 0.00,
    highest_bidder_id BIGINT UNSIGNED NULL,
    current_winner_id BIGINT UNSIGNED NULL,
    start_time TIMESTAMP NULL,
    end_time TIMESTAMP NULL,
    finalization_deadline TIMESTAMP NULL,
    
    -- Direct sale specific fields
    price DECIMAL(15, 2) NULL,
    stock INT DEFAULT 0,
    low_stock_threshold INT DEFAULT 5,
    
    status ENUM('pending', 'active', 'ended', 'completed', 'failed', 'out_of_stock') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (highest_bidder_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (current_winner_id) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_end_time (end_time),
    INDEX idx_start_time (start_time),
    INDEX idx_seller_id (seller_id),
    INDEX idx_finalization_deadline (finalization_deadline),
    INDEX idx_category (category)
);
```

#### auction_participations table
```sql
CREATE TABLE auction_participations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    listing_id BIGINT UNSIGNED NOT NULL, -- UPDATED: was auction_id
    user_id BIGINT UNSIGNED NOT NULL,
    deposit_amount DECIMAL(15, 2) NOT NULL,
    deposit_status ENUM('frozen', 'released', 'forfeited', 'applied') DEFAULT 'frozen',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_participation (listing_id, user_id),
    INDEX idx_listing_user (listing_id, user_id),
    INDEX idx_deposit_status (deposit_status)
);
```

#### bids table
```sql
CREATE TABLE bids (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    listing_id BIGINT UNSIGNED NOT NULL, -- UPDATED: was auction_id
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_listing_id (listing_id),
    INDEX idx_user_id (user_id),
    INDEX idx_amount (amount),
    INDEX idx_listing_amount (listing_id, amount DESC)
);
```

#### wallet_transactions table
```sql
CREATE TABLE wallet_transactions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    wallet_id BIGINT UNSIGNED NOT NULL,
    type ENUM('deposit', 'withdrawal', 'freeze_deposit', 'release_deposit', 
              'deduct_frozen', 'transfer_in', 'transfer_out', 'forfeit',
              'purchase', 'refund') NOT NULL, -- UPDATED: added purchase, refund
    amount DECIMAL(15, 2) NOT NULL,
    balance_before DECIMAL(15, 2) NOT NULL,
    balance_after DECIMAL(15, 2) NOT NULL,
    frozen_before DECIMAL(15, 2) NOT NULL,
    frozen_after DECIMAL(15, 2) NOT NULL,
    reference_type VARCHAR(50) NULL,
    reference_id BIGINT UNSIGNED NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
    INDEX idx_wallet_id (wallet_id),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at),
    INDEX idx_reference (reference_type, reference_id)
);
```

#### listing_images table (UPDATED - was auction_images)
```sql
CREATE TABLE listing_images (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    listing_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    INDEX idx_listing_id (listing_id),
    INDEX idx_display_order (listing_id, display_order)
);
```

#### shipping_methods table (NEW)
```sql
CREATE TABLE shipping_methods (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    base_cost DECIMAL(10, 2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_is_active (is_active)
);
```

#### listing_shipping table (NEW - pivot)
```sql
CREATE TABLE listing_shipping (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    listing_id BIGINT UNSIGNED NOT NULL,
    shipping_method_id BIGINT UNSIGNED NOT NULL,
    custom_cost_adjustment DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    FOREIGN KEY (shipping_method_id) REFERENCES shipping_methods(id) ON DELETE CASCADE,
    UNIQUE KEY unique_listing_shipping (listing_id, shipping_method_id)
);
```

#### carts table (NEW)
```sql
CREATE TABLE carts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED UNIQUE NOT NULL,
    session_id VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_session_id (session_id)
);
```

#### cart_items table (NEW)
```sql
CREATE TABLE cart_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    cart_id BIGINT UNSIGNED NOT NULL,
    listing_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price_snapshot DECIMAL(15, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    INDEX idx_cart_id (cart_id),
    INDEX idx_listing_id (listing_id)
);
```

#### orders table (NEW)
```sql
CREATE TABLE orders (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    buyer_id BIGINT UNSIGNED NOT NULL,
    seller_id BIGINT UNSIGNED NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    subtotal DECIMAL(15, 2) NOT NULL,
    shipping_cost DECIMAL(10, 2) NOT NULL,
    total DECIMAL(15, 2) NOT NULL,
    shipping_method_id BIGINT UNSIGNED NULL,
    shipping_address TEXT NOT NULL,
    tracking_number VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (shipping_method_id) REFERENCES shipping_methods(id) ON DELETE SET NULL,
    INDEX idx_order_number (order_number),
    INDEX idx_buyer_id (buyer_id),
    INDEX idx_seller_id (seller_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);
```

#### order_items table (NEW)
```sql
CREATE TABLE order_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    listing_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price_snapshot DECIMAL(15, 2) NOT NULL,
    subtotal DECIMAL(15, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id),
    INDEX idx_listing_id (listing_id)
);
```

#### notifications table
```sql
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    data TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notifiable (notifiable_type, notifiable_id),
    INDEX idx_read_at (read_at)
);
```

#### auction_images table
```sql
CREATE TABLE auction_images (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    auction_id BIGINT UNSIGNED NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
    INDEX idx_auction_id (auction_id),
    INDEX idx_display_order (auction_id, display_order)
);
```

#### notifications table
```sql
CREATE TABLE notifications (
    id CHAR(36) PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    notifiable_type VARCHAR(255) NOT NULL,
    notifiable_id BIGINT UNSIGNED NOT NULL,
    data TEXT NOT NULL,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notifiable (notifiable_type, notifiable_id),
    INDEX idx_read_at (read_at)
);
```

### Eloquent Models and Relationships

#### User Model
```php
class User extends Authenticatable
{
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }
    
    public function auctions(): HasMany
    {
        return $this->hasMany(Auction::class, 'seller_id');
    }
    
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }
    
    public function participations(): HasMany
    {
        return $this->hasMany(AuctionParticipation::class);
    }
    
    public function wonAuctions(): HasMany
    {
        return $this->hasMany(Auction::class, 'current_winner_id')
            ->where('status', 'completed');
    }
}
```

#### Wallet Model
```php
class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'frozen'];
    
    protected $casts = [
        'balance' => 'decimal:2',
        'frozen' => 'decimal:2',
    ];
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }
    
    public function getAvailableBalanceAttribute(): float
    {
        return $this->balance;
    }
    
    public function getTotalBalanceAttribute(): float
    {
        return $this->balance + $this->frozen;
    }
}
```

#### Auction Model
```php
class Auction extends Model
{
    protected $fillable = [
        'seller_id', 'title', 'description', 'base_price',
        'required_deposit', 'current_highest_bid', 'highest_bidder_id',
        'current_winner_id', 'status', 'start_time', 'end_time',
        'finalization_deadline'
    ];
    
    protected $casts = [
        'base_price' => 'decimal:2',
        'required_deposit' => 'decimal:2',
        'current_highest_bid' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'finalization_deadline' => 'datetime',
    ];
    
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
    
    public function highestBidder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'highest_bidder_id');
    }
    
    public function currentWinner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_winner_id');
    }
    
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }
    
    public function participations(): HasMany
    {
        return $this->hasMany(AuctionParticipation::class);
    }
    
    public function images(): HasMany
    {
        return $this->hasMany(AuctionImage::class)->orderBy('display_order');
    }
    
    public function isActive(): bool
    {
        return $this->status === 'active' 
            && now()->between($this->start_time, $this->end_time);
    }
    
    public function hasEnded(): bool
    {
        return now()->greaterThanOrEqualTo($this->end_time);
    }
}
```

#### Bid Model
```php
class Bid extends Model
{
    protected $fillable = ['auction_id', 'user_id', 'amount'];
    
    protected $casts = [
        'amount' => 'decimal:2',
    ];
    
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

#### AuctionParticipation Model
```php
class AuctionParticipation extends Model
{
    protected $fillable = [
        'auction_id', 'user_id', 'deposit_amount', 'deposit_status'
    ];
    
    protected $casts = [
        'deposit_amount' => 'decimal:2',
    ];
    
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

#### WalletTransaction Model
```php
class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id', 'type', 'amount', 'balance_before', 'balance_after',
        'frozen_before', 'frozen_after', 'reference_type', 'reference_id',
        'description'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'frozen_before' => 'decimal:2',
        'frozen_after' => 'decimal:2',
    ];
    
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
```


## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: User Registration Creates Wallet

*For any* valid user registration data, creating a user account should automatically initialize a wallet with zero balance and zero frozen amount.

**Validates: Requirements 1.1, 2.1**

### Property 2: Password Complexity Enforcement

*For any* password string, the system should accept it only if it contains at least 8 characters with mixed case letters and numbers.

**Validates: Requirements 1.5**

### Property 3: Role-Based Access Control

*For any* user and protected resource, access should be granted only if the user's role matches the required role for that resource.

**Validates: Requirements 1.3, 1.4**

### Property 4: Wallet Balance Invariant During Freeze

*For any* wallet and freeze operation, the total balance (available + frozen) should remain constant before and after the freeze operation.

**Validates: Requirements 2.3**

### Property 5: Freeze-Release Round Trip

*For any* wallet with frozen funds, freezing an amount then immediately releasing the same amount should restore the wallet to its original state.

**Validates: Requirements 2.4**

### Property 6: Negative Balance Prevention

*For any* wallet operation that would result in negative available balance, the system should reject the operation and leave the wallet unchanged.

**Validates: Requirements 2.6**

### Property 7: Deposit Calculation Invariant

*For any* auction with a base price, the required deposit should always equal exactly 10% of the base price.

**Validates: Requirements 3.2**

### Property 8: Auction Time Validation

*For any* auction creation request, the system should reject it if the end time is not after the start time, or if the start time is less than 1 hour in the future.

**Validates: Requirements 3.3, 3.4**

### Property 9: New Auction Status Invariant

*For any* newly created auction, the initial status should be 'pending' regardless of the auction's start time.

**Validates: Requirements 3.5**

### Property 10: Image Upload Limits

*For any* auction, the system should accept up to 5 image uploads and reject any attempt to upload a 6th image.

**Validates: Requirements 3.6**

### Property 11: Participation Requires Sufficient Balance

*For any* auction participation attempt, the system should reject it if the user's available balance is less than the required deposit.

**Validates: Requirements 4.1**

### Property 12: Participation Creates Record

*For any* successful deposit freeze for auction participation, a participation record should be created with the correct auction ID, user ID, and deposit amount.

**Validates: Requirements 4.3**

### Property 13: Participation Idempotence

*For any* user and auction, attempting to participate multiple times should succeed only once, with subsequent attempts being rejected.

**Validates: Requirements 4.4**

### Property 14: Bid Requires Participation

*For any* bid placement attempt, the system should reject it if the user has not paid the required deposit for that auction.

**Validates: Requirements 5.1**

### Property 15: Bid Amount Validation

*For any* bid placement attempt, the system should reject it if the bid amount is not both higher than the current highest bid and at least equal to the base price.

**Validates: Requirements 5.2, 5.3**

### Property 16: Bid Updates Auction State

*For any* successful bid placement, the auction's current highest bid should be updated to match the bid amount, and the highest bidder should be set to the bidding user.

**Validates: Requirements 5.4, 5.5**

### Property 17: Auction Ending Status Transition

*For any* auction that has reached its end time, processing the auction ending should change its status to 'ended'.

**Validates: Requirements 7.1**

### Property 18: Top 3 Bidder Identification

*For any* ended auction with N bidders where N ≥ 3, the system should correctly identify the 3 bidders with the highest bid amounts as the top 3.

**Validates: Requirements 7.2**

### Property 19: Top 3 Deposits Remain Frozen

*For any* ended auction, the deposits of the top 3 bidders should remain in frozen status while deposits of all other bidders should be released.

**Validates: Requirements 7.3, 7.4**

### Property 20: Winner Finalization Deadline

*For any* ended auction with at least one bidder, the system should set the rank 1 bidder as current winner and establish a finalization deadline exactly 48 hours from the auction end time.

**Validates: Requirements 8.1**

### Property 21: Winner Payment Calculation

*For any* winning bidder completing payment, the amount deducted from available balance should equal (winning bid amount - frozen deposit amount).

**Validates: Requirements 8.2**

### Property 22: Payment Transfers to Seller

*For any* completed winner payment, the seller's wallet balance should increase by exactly the winning bid amount.

**Validates: Requirements 8.3**

### Property 23: Winner Payment Releases Other Deposits

*For any* rank 1 winner completing payment, the frozen deposits of rank 2 and rank 3 bidders should be released back to their available balances.

**Validates: Requirements 8.4**

### Property 24: Payment Completes Auction

*For any* winning bidder completing payment, the auction status should transition to 'completed' and the current winner should be recorded as the final winner.

**Validates: Requirements 8.5**

### Property 25: Cascade Logic on Timeout

*For any* finalization deadline that expires without payment, the system should forfeit the current winner's deposit, transfer it to the seller, and offer the auction to the next-ranked bidder (if available).

**Validates: Requirements 8.6, 8.7, 8.9**

### Property 26: Cascade Termination

*For any* auction where all top 3 bidders fail to complete payment within their respective deadlines, the auction status should be set to 'failed' and all remaining frozen deposits should be released.

**Validates: Requirements 8.8**

### Property 27: Payment Balance Validation

*For any* payment initiation, the system should reject it if the buyer's available balance is insufficient to cover the remaining amount (bid amount minus frozen deposit).

**Validates: Requirements 9.2**

### Property 28: Payment Deduction Correctness

*For any* completed payment, the buyer's available balance should decrease by the remaining amount and the frozen balance should decrease by the deposit amount.

**Validates: Requirements 9.3**

### Property 29: Transaction Logging Completeness

*For any* financial operation (deposit, freeze, release, deduct, transfer), a transaction record should be created with type, amount, before/after balances, and reference information.

**Validates: Requirements 2.5, 7.6, 9.4, 14.6**

### Property 30: Notification Creation on Events

*For any* significant auction event (start, end, outbid, winner selection, deadline reminder, deposit release/forfeit), a notification should be created for the affected user(s).

**Validates: Requirements 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7**

### Property 31: Jalali Date Formatting

*For any* date value displayed to users, the formatting function should produce a string in Jalali (Shamsi) calendar format.

**Validates: Requirements 11.4**

### Property 32: Persian Number Formatting

*For any* numeric amount displayed to users, the formatting function should produce a string with Persian digit characters (۰-۹) and appropriate Rial formatting.

**Validates: Requirements 11.5, 11.7**

### Property 33: Admin Statistics Accuracy

*For any* admin dashboard view, the displayed statistics (active auction count, total users, transaction volume) should match the actual counts from the database.

**Validates: Requirements 13.1**

### Property 34: Admin Cancellation Releases Deposits

*For any* admin-initiated auction cancellation, all frozen deposits for that auction should be released back to participants' available balances.

**Validates: Requirements 13.4**

### Property 35: Admin Action Audit Logging

*For any* admin action (cancellation, manual deposit release), an audit log entry should be created with the action type, timestamp, admin identifier, and affected resources.

**Validates: Requirements 13.6**

### Property 36: Password Hashing Security

*For any* user password, the stored value should be a bcrypt hash with cost factor of at least 10, and should never match the plaintext password.

**Validates: Requirements 14.1**

### Property 37: Input Sanitization

*For any* user input containing SQL injection or XSS attack patterns, the system should sanitize or reject the input before processing.

**Validates: Requirements 14.2, 14.3**

### Property 38: CSRF Token Validation

*For any* state-changing request (POST, PUT, DELETE), the system should reject it if a valid CSRF token is not included.

**Validates: Requirements 14.4**

### Property 39: Bid Rate Limiting

*For any* user attempting to place more than N bids within M seconds, the system should reject subsequent bids until the rate limit window resets.

**Validates: Requirements 14.8**

### Property 40: API Authentication

*For any* API request without a valid Sanctum token, the system should return a 401 Unauthorized response.

**Validates: Requirements 15.2, 15.4**

### Property 41: API Response Format Consistency

*For any* API endpoint response, the structure should be valid JSON with consistent field naming and appropriate HTTP status codes.

**Validates: Requirements 15.5, 15.6**

### Property 42: Transaction History Ordering

*For any* user's transaction history query, the results should be ordered chronologically with the most recent transactions first.

**Validates: Requirements 17.1**

### Property 43: Transaction History Filtering

*For any* transaction history query with date range and type filters, only transactions matching all specified criteria should be returned.

**Validates: Requirements 17.3**

### Property 44: CSV Export Completeness

*For any* transaction history CSV export, every transaction in the user's history should appear as a row with all required fields.

**Validates: Requirements 17.4**

### Property 45: Dashboard Statistics Accuracy

*For any* user dashboard (seller or buyer), the displayed statistics should accurately reflect the user's auctions, bids, and wallet state.

**Validates: Requirements 17.5, 17.6**

### Property 46: Image Format Validation

*For any* image upload attempt, the system should accept only files with MIME types of image/jpeg, image/png, or image/webp.

**Validates: Requirements 18.1**

### Property 47: Image Size Validation

*For any* image upload attempt, the system should reject files larger than 5MB.

**Validates: Requirements 18.2**

### Property 48: Image Optimization

*For any* successfully uploaded image, the system should generate both thumbnail and full-size optimized versions.

**Validates: Requirements 18.3**

### Property 49: Image Filename Uniqueness

*For any* two images uploaded to the system, they should be stored with different filenames to prevent conflicts.

**Validates: Requirements 18.4**

### Property 50: Image Deletion Time Constraint

*For any* image deletion attempt, the system should allow it only if the auction status is 'pending' (before start time).

**Validates: Requirements 18.6**

### Property 51: Search Functionality

*For any* search query string, the results should include all auctions where the query appears in either the title or description (case-insensitive).

**Validates: Requirements 19.1**

### Property 52: Auction Filtering

*For any* combination of filters (status, price range, category), the results should include only auctions matching all specified criteria.

**Validates: Requirements 19.2, 19.3, 19.4**

### Property 53: Pagination Consistency

*For any* paginated result set, each page should contain exactly 20 items (except the last page which may contain fewer).

**Validates: Requirements 19.6**

### Property 54: Job Execution Logging

*For any* scheduled job execution (auction start, end, finalization timeout), a log entry should be created with timestamp, job type, and execution result.

**Validates: Requirements 20.6**

### Property 55: Job Retry Logic

*For any* scheduled job that fails, the system should retry it up to 3 times with exponentially increasing delays between attempts.

**Validates: Requirements 20.7**


## Error Handling

### Exception Hierarchy

```php
// Base exception for all auction-related errors
abstract class AuctionException extends Exception {}

// Wallet-related exceptions
class InsufficientBalanceException extends AuctionException {}
class WalletNotFoundException extends AuctionException {}
class NegativeBalanceException extends AuctionException {}

// Auction-related exceptions
class AuctionNotFoundException extends AuctionException {}
class AuctionNotActiveException extends AuctionException {}
class AuctionAlreadyEndedException extends AuctionException {}
class InvalidAuctionTimeException extends AuctionException {}

// Bid-related exceptions
class DepositNotPaidException extends AuctionException {}
class InvalidBidAmountException extends AuctionException {}
class BidTooLowException extends AuctionException {}

// Participation-related exceptions
class AlreadyParticipatingException extends AuctionException {}
class ParticipationNotFoundException extends AuctionException {}

// Payment-related exceptions
class PaymentFailedException extends AuctionException {}
class FinalizationDeadlineExpiredException extends AuctionException {}

// Image-related exceptions
class InvalidImageFormatException extends AuctionException {}
class ImageSizeTooLargeException extends AuctionException {}
class ImageUploadLimitExceededException extends AuctionException {}

// Authorization exceptions
class UnauthorizedActionException extends AuctionException {}
class InsufficientPermissionsException extends AuctionException {}
```

### Error Handling Strategies

#### Database Transaction Failures

All financial operations are wrapped in database transactions. On failure:

```php
try {
    DB::transaction(function () {
        // Financial operations
    });
} catch (QueryException $e) {
    Log::error('Transaction failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    throw new PaymentFailedException(
        'عملیات مالی با خطا مواجه شد. لطفا دوباره تلاش کنید.'
    );
}
```

#### Concurrent Bid Handling

When multiple users bid simultaneously, row locking prevents race conditions:

```php
try {
    $auction = Auction::where('id', $auctionId)
        ->lockForUpdate()
        ->first();
    
    // Process bid
} catch (QueryException $e) {
    if ($e->getCode() === '40001') { // Deadlock
        // Retry logic with exponential backoff
        return $this->retryBid($user, $auction, $amount, $attempt + 1);
    }
    throw $e;
}
```

#### Validation Errors

Laravel Form Requests handle validation with automatic error responses:

```php
class PlaceBidRequest extends FormRequest
{
    public function rules()
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:' . $this->auction->base_price,
                'gt:' . $this->auction->current_highest_bid,
            ],
        ];
    }
    
    public function messages()
    {
        return [
            'amount.required' => 'مبلغ پیشنهاد الزامی است',
            'amount.min' => 'پیشنهاد باید حداقل برابر قیمت پایه باشد',
            'amount.gt' => 'پیشنهاد باید بیشتر از بالاترین پیشنهاد فعلی باشد',
        ];
    }
}
```

#### File Upload Errors

Image upload failures are handled gracefully:

```php
try {
    $path = $request->file('image')->store('auctions', 'public');
} catch (FileException $e) {
    Log::error('Image upload failed', [
        'auction_id' => $auction->id,
        'error' => $e->getMessage()
    ]);
    
    return back()->with('error', 'آپلود تصویر با خطا مواجه شد');
}
```

#### Scheduled Job Failures

Jobs implement retry logic with exponential backoff:

```php
class ProcessAuctionEnding implements ShouldQueue
{
    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min
    
    public function handle()
    {
        try {
            $this->auctionService->endAuction($this->auction);
        } catch (Exception $e) {
            Log::error('Auction ending failed', [
                'auction_id' => $this->auction->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);
            
            if ($this->attempts() >= $this->tries) {
                // Notify admin of critical failure
                Notification::send(
                    User::admins()->get(),
                    new AuctionEndingFailedNotification($this->auction)
                );
            }
            
            throw $e; // Re-throw to trigger retry
        }
    }
}
```

#### User-Facing Error Messages

All error messages are in Farsi and user-friendly:

```php
class ErrorMessageService
{
    public static function translate(Exception $e): string
    {
        return match (get_class($e)) {
            InsufficientBalanceException::class => 
                'موجودی کیف پول شما کافی نیست',
            DepositNotPaidException::class => 
                'برای ثبت پیشنهاد ابتدا باید سپرده را پرداخت کنید',
            InvalidBidAmountException::class => 
                'مبلغ پیشنهاد معتبر نیست',
            AlreadyParticipatingException::class => 
                'شما قبلا در این مزایده شرکت کرده‌اید',
            AuctionNotActiveException::class => 
                'این مزایده فعال نیست',
            default => 'خطایی رخ داده است. لطفا دوباره تلاش کنید'
        };
    }
}
```

### Logging Strategy

#### Financial Operations Logging

All financial operations are logged with full context:

```php
Log::channel('financial')->info('Deposit frozen', [
    'user_id' => $user->id,
    'auction_id' => $auction->id,
    'amount' => $amount,
    'balance_before' => $wallet->balance,
    'balance_after' => $wallet->balance - $amount,
    'frozen_before' => $wallet->frozen,
    'frozen_after' => $wallet->frozen + $amount,
    'timestamp' => now()->toIso8601String()
]);
```

#### Security Event Logging

Security-relevant events are logged separately:

```php
Log::channel('security')->warning('Failed login attempt', [
    'email' => $request->email,
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'timestamp' => now()->toIso8601String()
]);
```

#### Admin Action Logging

All admin actions are logged for audit trail:

```php
Log::channel('admin')->info('Auction cancelled by admin', [
    'admin_id' => auth()->id(),
    'admin_email' => auth()->user()->email,
    'auction_id' => $auction->id,
    'reason' => $request->reason,
    'timestamp' => now()->toIso8601String()
]);
```

## Testing Strategy

### Dual Testing Approach

The Iranian Auction Platform requires both unit testing and property-based testing for comprehensive coverage:

- **Unit Tests**: Verify specific examples, edge cases, and error conditions
- **Property Tests**: Verify universal properties across all inputs

Both approaches are complementary and necessary. Unit tests catch concrete bugs in specific scenarios, while property tests verify general correctness across a wide range of inputs.

### Property-Based Testing Configuration

**Library Selection**: We will use **Pest PHP** with the **Pest Property Testing Plugin** for Laravel.

**Configuration**:
- Minimum 100 iterations per property test (due to randomization)
- Each property test references its design document property
- Tag format: `Feature: iranian-auction-platform, Property {number}: {property_text}`

**Example Property Test**:

```php
use function Pest\property;

test('Property 4: Wallet Balance Invariant During Freeze')
    ->property(
        'user' => fn() => User::factory()->create(),
        'amount' => fn() => fake()->randomFloat(2, 100, 10000)
    )
    ->runs(100)
    ->expect(function ($user, $amount) {
        // Arrange: Setup wallet with sufficient balance
        $wallet = $user->wallet;
        $wallet->balance = $amount * 2;
        $wallet->save();
        
        $totalBefore = $wallet->balance + $wallet->frozen;
        
        // Act: Freeze deposit
        $auction = Auction::factory()->create();
        app(WalletService::class)->freezeDeposit($user, $amount, $auction);
        
        // Assert: Total balance unchanged
        $wallet->refresh();
        $totalAfter = $wallet->balance + $wallet->frozen;
        
        return abs($totalBefore - $totalAfter) < 0.01; // Float comparison
    })
    ->toBeTrue()
    ->tags(['Feature: iranian-auction-platform', 'Property 4']);
```

### Unit Testing Strategy

Unit tests focus on:

1. **Specific Examples**: Concrete scenarios that demonstrate correct behavior
2. **Edge Cases**: Boundary conditions and special cases
3. **Error Conditions**: Invalid inputs and error handling
4. **Integration Points**: Component interactions

**Example Unit Test**:

```php
test('user cannot participate in auction without sufficient balance')
    ->expect(function () {
        $user = User::factory()->create();
        $user->wallet->balance = 50;
        $user->wallet->save();
        
        $auction = Auction::factory()->create([
            'required_deposit' => 100
        ]);
        
        app(DepositService::class)->participateInAuction($user, $auction);
    })
    ->throws(InsufficientBalanceException::class);

test('auction deposit is exactly 10% of base price')
    ->expect(function () {
        $auction = Auction::factory()->create([
            'base_price' => 1000
        ]);
        
        return $auction->required_deposit;
    })
    ->toBe(100.00);

test('top 3 bidders are correctly identified')
    ->expect(function () {
        $auction = Auction::factory()->create();
        
        // Create 5 bidders with different amounts
        $bidders = collect([500, 300, 400, 200, 100]);
        $bidders->each(function ($amount) use ($auction) {
            $user = User::factory()->create();
            Bid::factory()->create([
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'amount' => $amount
            ]);
        });
        
        $top3 = app(BidService::class)->getCurrentRankings($auction)->take(3);
        
        return $top3->pluck('amount')->toArray();
    })
    ->toBe([500.00, 400.00, 300.00]);
```

### Test Coverage Requirements

**Minimum Coverage Targets**:
- Service Classes: 90% code coverage
- Models: 80% code coverage
- Controllers: 70% code coverage
- Overall: 80% code coverage

**Critical Path Testing**:
The following workflows must have 100% coverage:
1. Wallet freeze/release/deduct operations
2. Bid placement with race condition handling
3. Auction ending and top 3 selection
4. Cascade winner selection logic
5. Payment processing and fund transfers

### Integration Testing

Integration tests verify component interactions:

```php
test('complete auction workflow from creation to winner payment')
    ->expect(function () {
        // Create seller and auction
        $seller = User::factory()->create();
        $auction = app(AuctionService::class)->createAuction($seller, [
            'title' => 'Test Auction',
            'description' => 'Test Description',
            'base_price' => 1000,
            'start_time' => now()->addHour(),
            'end_time' => now()->addHours(2),
        ]);
        
        // Create buyers and participate
        $buyer1 = User::factory()->create();
        $buyer1->wallet->balance = 5000;
        $buyer1->wallet->save();
        
        app(DepositService::class)->participateInAuction($buyer1, $auction);
        
        // Start auction
        $auction->status = 'active';
        $auction->save();
        
        // Place bid
        app(BidService::class)->placeBid($buyer1, $auction, 1500);
        
        // End auction
        app(AuctionService::class)->endAuction($auction);
        
        // Complete payment
        app(AuctionService::class)->completeWinnerPayment($auction, $buyer1);
        
        // Verify final state
        $auction->refresh();
        $seller->wallet->refresh();
        $buyer1->wallet->refresh();
        
        return [
            'auction_completed' => $auction->status === 'completed',
            'seller_received_payment' => $seller->wallet->balance == 1500,
            'buyer_paid_correct_amount' => $buyer1->wallet->balance == 3500, // 5000 - 1500
        ];
    })
    ->toMatchArray([
        'auction_completed' => true,
        'seller_received_payment' => true,
        'buyer_paid_correct_amount' => true,
    ]);
```

### Livewire Component Testing

Livewire components are tested using Livewire's testing utilities:

```php
test('auction bidding component updates in real-time')
    ->expect(function () {
        $user = User::factory()->create();
        $auction = Auction::factory()->create([
            'status' => 'active',
            'current_highest_bid' => 1000
        ]);
        
        Livewire::actingAs($user)
            ->test(AuctionBidding::class, ['auction' => $auction])
            ->set('bidAmount', 1500)
            ->call('placeBid')
            ->assertHasNoErrors()
            ->assertSet('currentHighestBid', 1500);
    });

test('auction countdown component displays remaining time')
    ->expect(function () {
        $auction = Auction::factory()->create([
            'end_time' => now()->addHours(2)
        ]);
        
        Livewire::test(AuctionCountdown::class, ['auction' => $auction])
            ->assertSee('ساعت') // Should show hours in Farsi
            ->assertDontSee('پایان یافته'); // Should not show "ended"
    });
```

### Performance Testing

While not part of property-based testing, performance benchmarks should be established:

```php
test('bid placement completes within 500ms under normal load')
    ->expect(function () {
        $user = User::factory()->create();
        $auction = Auction::factory()->create(['status' => 'active']);
        
        $start = microtime(true);
        app(BidService::class)->placeBid($user, $auction, 1000);
        $duration = (microtime(true) - $start) * 1000;
        
        return $duration;
    })
    ->toBeLessThan(500);
```

### Test Data Factories

Laravel factories generate realistic test data:

```php
class AuctionFactory extends Factory
{
    public function definition()
    {
        return [
            'seller_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'base_price' => fake()->randomFloat(2, 100, 100000),
            'required_deposit' => fn($attributes) => $attributes['base_price'] * 0.10,
            'current_highest_bid' => 0,
            'status' => 'pending',
            'start_time' => now()->addHour(),
            'end_time' => now()->addHours(25),
        ];
    }
    
    public function active()
    {
        return $this->state(fn($attributes) => [
            'status' => 'active',
            'start_time' => now()->subHour(),
            'end_time' => now()->addHour(),
        ]);
    }
    
    public function ended()
    {
        return $this->state(fn($attributes) => [
            'status' => 'ended',
            'start_time' => now()->subHours(25),
            'end_time' => now()->subHour(),
        ]);
    }
}
```

### Continuous Integration

Tests run automatically on every commit:

```yaml
# .github/workflows/tests.yml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          extensions: mbstring, pdo_mysql
          
      - name: Install Dependencies
        run: composer install
        
      - name: Run Tests
        run: php artisan test --coverage --min=80
        
      - name: Run Property Tests
        run: php artisan test --group=property --min-runs=100
```

### Test Organization

Tests are organized by feature and type:

```
tests/
├── Feature/
│   ├── Auction/
│   │   ├── AuctionCreationTest.php
│   │   ├── AuctionEndingTest.php
│   │   └── CascadeLogicTest.php
│   ├── Bidding/
│   │   ├── BidPlacementTest.php
│   │   └── BidValidationTest.php
│   ├── Wallet/
│   │   ├── DepositFreezeTest.php
│   │   ├── DepositReleaseTest.php
│   │   └── PaymentTest.php
│   └── Livewire/
│       ├── AuctionBiddingTest.php
│       └── AuctionCountdownTest.php
├── Unit/
│   ├── Services/
│   │   ├── AuctionServiceTest.php
│   │   ├── BidServiceTest.php
│   │   ├── WalletServiceTest.php
│   │   └── DepositServiceTest.php
│   └── Models/
│       ├── AuctionTest.php
│       ├── BidTest.php
│       └── WalletTest.php
└── Property/
    ├── WalletPropertiesTest.php
    ├── AuctionPropertiesTest.php
    ├── BiddingPropertiesTest.php
    └── CascadePropertiesTest.php
```

This comprehensive testing strategy ensures the Iranian Auction Platform is reliable, secure, and correct across all scenarios.
