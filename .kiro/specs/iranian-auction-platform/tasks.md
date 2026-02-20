# Implementation Plan: Iranian Multi-Vendor Marketplace

## Overview

This implementation plan breaks down the Iranian Multi-Vendor Marketplace into incremental, testable steps. The platform combines auction functionality with direct sales, storefronts, and shipping management. Each task builds on previous work, with property-based tests integrated throughout to catch errors early. The plan follows a bottom-up approach: core services → models → business logic → UI → integration.

**Key Expansion Areas:**
1. **Storefront (Vitrin) System**: Seller profile pages with customization
2. **Listing Type Management**: Support for auction, direct_sale, and hybrid listings
3. **Shopping Cart & Orders**: E-commerce functionality for direct sales
4. **Shipping Management**: Admin-defined shipping methods with seller selection
5. **Inventory Management**: Stock tracking for direct sale items

## Tasks

- [x] 1. Project setup and infrastructure
  - Initialize Laravel 10 project with required dependencies
  - Install Livewire 3, Tailwind CSS 3, Alpine.js
  - Install Pest PHP with property testing plugin
  - Configure RTL support and Vazirmatn font
  - Set up Jalali date library (morilog/jalali)
  - Configure database connection and migrations structure
  - _Requirements: 11.1, 11.2, 11.3, 11.4_

- [x] 2. Database schema and migrations (Updated for Marketplace)
  - [x] 2.1 Create users table migration with role column
    - Add role enum (buyer, seller, admin)
    - Add username column (unique, for storefront URL)
    - Add proper indexes
    - _Requirements: 1.1, 1.3, 1.4, 21.1_
  
  - [x] 2.2 Create wallets table migration
    - Add balance and frozen columns with decimal precision
    - Add check constraints for non-negative values
    - Add foreign key to users with cascade delete
    - _Requirements: 2.1, 2.6_
  
  - [x] 2.3 Create stores table migration (NEW)
    - Add user_id foreign key
    - Add store_name, slug (unique), description
    - Add banner_image, logo_image paths
    - Add is_active boolean
    - Add timestamps
    - _Requirements: 21.1, 21.2, 21.3, 28.1-28.9_
  
  - [x] 2.4 Modify listings table migration (UPDATED - replaces auctions)
    - Add type enum (auction, direct_sale, hybrid)
    - Add seller_id foreign key
    - Add title, description, category
    - **Auction fields**: base_price, required_deposit, current_highest_bid, highest_bidder_id, current_winner_id, start_time, end_time, finalization_deadline
    - **Direct sale fields**: price, stock, low_stock_threshold
    - Add status enum (pending, active, ended, completed, failed, out_of_stock)
    - Add indexes on type, status, seller_id, end_time
    - _Requirements: 3.1-3.6, 22.1-22.8_
  
  - [x] 2.5 Create auction_participations table migration
    - Add unique constraint on (listing_id, user_id)
    - Add deposit_status enum
    - _Requirements: 4.3, 4.4_
  
  - [x] 2.6 Create bids table migration
    - Change auction_id to listing_id
    - Add indexes on listing_id, amount
    - Add composite index on (listing_id, amount DESC)
    - _Requirements: 5.4_
  
  - [x] 2.7 Create wallet_transactions table migration
    - Add type enum with all transaction types (including 'purchase', 'refund')
    - Add before/after balance tracking columns
    - Add reference polymorphic columns
    - _Requirements: 2.5, 7.6, 9.4, 14.6, 23.9_
  
  - [x] 2.8 Create listing_images table migration (renamed from auction_images)
    - listing_id foreign key
    - file_path, file_name, display_order
    - _Requirements: 3.6, 18.1-18.7_
  
  - [x] 2.9 Create shipping_methods table migration (NEW)
    - Add name, description, base_cost
    - Add is_active boolean
    - Add created_by (admin user_id)
    - _Requirements: 24.1, 24.2, 24.8_
  
  - [x] 2.10 Create listing_shipping table migration (NEW - pivot)
    - Add listing_id, shipping_method_id
    - Add custom_cost_adjustment (nullable decimal)
    - Add unique constraint on (listing_id, shipping_method_id)
    - _Requirements: 24.3, 24.4_
  
  - [x] 2.11 Create carts table migration (NEW)
    - Add user_id foreign key
    - Add session_id for guest carts
    - Add timestamps
    - _Requirements: 23.1, 23.2_
  
  - [x] 2.12 Create cart_items table migration (NEW)
    - Add cart_id, listing_id foreign keys
    - Add quantity, price_snapshot
    - _Requirements: 23.2, 23.3, 23.6_
  
  - [x] 2.13 Create orders table migration (NEW)
    - Add order_number (unique)
    - Add buyer_id, seller_id foreign keys
    - Add status enum (pending, processing, shipped, delivered, cancelled)
    - Add subtotal, shipping_cost, total
    - Add shipping_method_id, shipping_address, tracking_number
    - Add timestamps
    - _Requirements: 23.8, 25.1-25.9_
  
  - [x] 2.14 Create order_items table migration (NEW)
    - Add order_id, listing_id foreign keys
    - Add quantity, price_snapshot, subtotal
    - _Requirements: 23.8, 25.6_
  
  - [x] 2.15 Create notifications table migration
    - Standard Laravel notifications table
    - _Requirements: 10.1-10.7, 23.11_


- [x] 3. Eloquent models and relationships (Updated for Marketplace)
  - [x] 3.1 Create User model with relationships
    - Add wallet, store, listings, bids, participations, orders relationships
    - Add role accessor and scope methods
    - Add username for storefront URL
    - _Requirements: 1.1, 1.3, 1.4, 1.6, 21.1_
  
  - [x] 3.2 Create Wallet model with relationships
    - Add user relationship
    - Add transactions relationship
    - Add computed attributes (availableBalance, totalBalance)
    - _Requirements: 2.1_
  
  - [x] 3.3 Create Store model (NEW)
    - Add user (seller) relationship
    - Add listings relationship
    - Add accessors for banner/logo URLs
    - Add slug generation method
    - _Requirements: 21.1-21.9, 28.1-28.9_
  
  - [x] 3.4 Create Listing model (UPDATED - replaces Auction)
    - Add seller, highestBidder, currentWinner relationships
    - Add bids, participations, images, shippingMethods relationships
    - Add cartItems, orderItems relationships
    - Add helper methods (isAuction, isDirectSale, isHybrid, isActive, hasEnded, inStock)
    - Add casts for decimal and datetime fields
    - Add type-specific scopes (auctions, directSales, hybrid)
    - _Requirements: 3.1, 3.2, 22.1-22.8, 26.1-26.7_
  
  - [x] 3.5 Create Bid, AuctionParticipation, WalletTransaction, ListingImage models
    - Update foreign keys to use listing_id instead of auction_id
    - Define fillable fields and casts
    - Add relationships to parent models
    - _Requirements: 4.3, 5.4, 2.5, 3.6_
  
  - [x] 3.6 Create ShippingMethod model (NEW)
    - Add listings relationship (many-to-many through pivot)
    - Add scope for active methods
    - _Requirements: 24.1, 24.2_
  
  - [x] 3.7 Create Cart and CartItem models (NEW)
    - Cart: user, items relationships
    - CartItem: cart, listing relationships
    - Add methods for calculating totals
    - _Requirements: 23.1-23.7_
  
  - [x] 3.8 Create Order and OrderItem models (NEW)
    - Order: buyer, seller, items, shippingMethod relationships
    - OrderItem: order, listing relationships
    - Add order number generation
    - Add status transition methods
    - _Requirements: 23.8, 25.1-25.9_

- [x] 4. Custom exception classes (Updated for Marketplace)
  - Create exception hierarchy for marketplace system
  - **Wallet exceptions**: InsufficientBalanceException, WalletNotFoundException
  - **Auction exceptions**: DepositNotPaidException, InvalidBidAmountException, AuctionNotActiveException, AlreadyParticipatingException
  - **Direct sale exceptions**: OutOfStockException, InvalidQuantityException
  - **Cart exceptions**: CartEmptyException, CartItemNotFoundException
  - **Order exceptions**: OrderNotFoundException, InvalidOrderStatusException
  - **Shipping exceptions**: ShippingMethodNotFoundException, InvalidShippingMethodException
  - **Payment exceptions**: PaymentFailedException
  - **Image exceptions**: InvalidImageFormatException, ImageSizeTooLargeException
  - Add Farsi error messages to each exception
  - _Requirements: 2.6, 4.1, 4.4, 5.1, 5.2, 18.1, 22.6, 22.7, 23.4, 23.7, 24.6_

- [x] 5. Implement WalletService with transaction safety
  - [x] 5.1 Implement freezeDeposit method
    - Use DB transaction with lockForUpdate
    - Validate sufficient balance
    - Move funds from balance to frozen
    - Create transaction record
    - _Requirements: 2.3, 4.1, 4.2_
  
  - [x] 5.2 Write property test for freezeDeposit
    - **Property 4: Wallet Balance Invariant During Freeze**
    - **Validates: Requirements 2.3**
  
  - [x] 5.3 Implement releaseDeposit method
    - Use DB transaction with lockForUpdate
    - Move funds from frozen to balance
    - Create transaction record
    - _Requirements: 2.4, 7.4_
  
  - [x] 5.4 Write property test for freeze-release round trip
    - **Property 5: Freeze-Release Round Trip**
    - **Validates: Requirements 2.4**
  
  - [x] 5.5 Implement deductFrozenAmount method
    - Use DB transaction with lockForUpdate
    - Remove funds from frozen
    - Create transaction record
    - _Requirements: 2.5, 8.2_
  
  - [x] 5.6 Implement addFunds and transfer methods
    - addFunds increases available balance
    - transfer moves funds between users
    - Both create transaction records
    - _Requirements: 2.2, 8.3_
  
  - [x] 5.7 Write property test for negative balance prevention
    - **Property 6: Negative Balance Prevention**
    - **Validates: Requirements 2.6**
  
  - [x] 5.8 Write property test for transaction logging
    - **Property 29: Transaction Logging Completeness**
    - **Validates: Requirements 2.5, 7.6, 9.4, 14.6**

- [x] 6. Checkpoint - Ensure wallet tests pass
  - Run all wallet-related tests
  - Verify transaction safety and logging
  - Ask user if questions arise


- [x] 7. Implement AuctionService
  - [x] 7.1 Implement createAuction method
    - Calculate required deposit (10% of base price)
    - Validate time constraints
    - Create auction with 'pending' status
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_
  
  - [x] 7.2 Write property tests for auction creation
    - **Property 7: Deposit Calculation Invariant**
    - **Property 8: Auction Time Validation**
    - **Property 9: New Auction Status Invariant**
    - **Validates: Requirements 3.2, 3.3, 3.4, 3.5**
  
  - [x] 7.3 Implement startAuction method
    - Change status from 'pending' to 'active'
    - Send notification to seller
    - _Requirements: 10.1_
  
  - [x] 7.4 Implement endAuction method
    - Use DB transaction with lockForUpdate
    - Identify top 3 bidders by amount
    - Keep top 3 deposits frozen
    - Release other deposits via WalletService
    - Set rank 1 as current winner
    - Set finalization deadline (48 hours)
    - Change status to 'ended'
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 8.1_
  
  - [x] 7.5 Write property tests for auction ending
    - **Property 17: Auction Ending Status Transition**
    - **Property 18: Top 3 Bidder Identification**
    - **Property 19: Top 3 Deposits Remain Frozen**
    - **Property 20: Winner Finalization Deadline**
    - **Validates: Requirements 7.1, 7.2, 7.3, 7.4, 8.1**
  
  - [x] 7.6 Implement completeWinnerPayment method
    - Validate winner has sufficient balance
    - Calculate remaining amount (bid - deposit)
    - Deduct remaining from available balance
    - Deduct deposit from frozen balance
    - Transfer full amount to seller
    - Release deposits for rank 2 and rank 3
    - Set auction status to 'completed'
    - _Requirements: 8.2, 8.3, 8.4, 8.5, 9.2, 9.3_
  
  - [x] 7.7 Write property tests for winner payment
    - **Property 21: Winner Payment Calculation**
    - **Property 22: Payment Transfers to Seller**
    - **Property 23: Winner Payment Releases Other Deposits**
    - **Property 24: Payment Completes Auction**
    - **Validates: Requirements 8.2, 8.3, 8.4, 8.5**
  
  - [x] 7.8 Implement handleFinalizationTimeout method (cascade logic)
    - Forfeit current winner's deposit
    - Transfer forfeited deposit to seller
    - Find next ranked bidder
    - Set next bidder as current winner with new deadline
    - If no more bidders, set status to 'failed' and release all deposits
    - _Requirements: 8.6, 8.7, 8.8, 8.9_
  
  - [x] 7.9 Write property tests for cascade logic
    - **Property 25: Cascade Logic on Timeout**
    - **Property 26: Cascade Termination**
    - **Validates: Requirements 8.6, 8.7, 8.8, 8.9**

- [x] 8. Implement DepositService
  - [x] 8.1 Implement participateInAuction method
    - Check for existing participation
    - Call WalletService.freezeDeposit
    - Create AuctionParticipation record
    - _Requirements: 4.1, 4.2, 4.3, 4.4_
  
  - [x] 8.2 Write property tests for participation
    - **Property 11: Participation Requires Sufficient Balance**
    - **Property 12: Participation Creates Record**
    - **Property 13: Participation Idempotence**
    - **Validates: Requirements 4.1, 4.3, 4.4**

- [x] 9. Implement BidService
  - [x] 9.1 Implement placeBid method
    - Use DB transaction with lockForUpdate on auction
    - Validate auction is active
    - Validate user has paid deposit
    - Validate bid amount > current highest and >= base price
    - Create Bid record
    - Update auction current_highest_bid and highest_bidder_id
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_
  
  - [x] 9.2 Write property tests for bidding
    - **Property 14: Bid Requires Participation**
    - **Property 15: Bid Amount Validation**
    - **Property 16: Bid Updates Auction State**
    - **Validates: Requirements 5.1, 5.2, 5.3, 5.4, 5.5**
  
  - [x] 9.3 Implement getCurrentRankings method
    - Query bids ordered by amount DESC
    - Group by user (highest bid per user)
    - Return collection with rank information
    - _Requirements: 7.2_
  
  - [x] 9.4 Write unit tests for edge cases
    - Test simultaneous bids (race conditions)
    - Test bidding on ended auction
    - Test bidding without participation
    - _Requirements: 5.1, 5.2_

- [x] 10. Checkpoint - Ensure core services pass all tests
  - Run all service tests (wallet, auction, deposit, bid)
  - Verify property tests pass with 100+ iterations
  - Verify transaction safety and race condition handling
  - Ask user if questions arise

- [x] 10A. Implement StoreService (NEW - Storefront Management)
  - [x] 10A.1 Implement createStore method
    - Auto-create store when user registers as seller
    - Generate unique slug from username
    - Initialize with default values
    - _Requirements: 1.6, 21.1_
  
  - [x] 10A.2 Implement updateStoreProfile method
    - Validate and update store_name, description
    - Handle banner and logo image uploads
    - Validate image dimensions and sizes
    - _Requirements: 21.5, 21.6, 21.7, 28.1-28.9_
  
  - [x] 10A.3 Implement getStoreBySlug method
    - Retrieve store with active listings
    - Include seller statistics
    - _Requirements: 21.2, 21.9_
  
  - [x] 10A.4 Write unit tests for store operations
    - Test store creation on seller registration
    - Test slug uniqueness
    - Test image upload validation
    - _Requirements: 21.1, 21.5, 28.4, 28.5_

- [x] 10B. Implement ListingService (NEW - Unified Listing Management)
  - [x] 10B.1 Implement createListing method
    - Accept type parameter (auction, direct_sale, hybrid)
    - Validate type-specific fields
    - For auction: calculate 10% deposit, validate times
    - For direct_sale: validate price and stock
    - For hybrid: validate both sets of fields
    - _Requirements: 22.1-22.5, 3.1-3.5_
  
  - [x] 10B.2 Write property tests for listing creation
    - **Property 56: Listing Type Validation**
    - **Property 57: Auction Deposit Calculation (10%)**
    - **Property 58: Stock Validation for Direct Sales**
    - **Validates: Requirements 22.1-22.5**
  
  - [x] 10B.3 Implement updateStock method
    - Validate stock quantity
    - Log stock changes
    - Update listing status if out of stock
    - _Requirements: 27.1-27.8_
  
  - [x] 10B.4 Implement decrementStock method
    - Use DB transaction with lockForUpdate
    - Prevent negative stock
    - Mark as out_of_stock when stock reaches zero
    - _Requirements: 22.6, 27.4_
  
  - [x] 10B.5 Write property test for stock management
    - **Property 59: Stock Never Goes Negative**
    - **Property 60: Stock Decrement Atomicity**
    - **Validates: Requirements 27.4, 27.8**

- [x] 10C. Implement CartService (NEW - Shopping Cart)
  - [x] 10C.1 Implement addToCart method
    - Get or create cart for user/session
    - Validate listing is direct_sale or hybrid
    - Validate stock availability
    - Add or update cart item
    - _Requirements: 23.1, 23.2, 23.4_
  
  - [x] 10C.2 Implement updateCartItem method
    - Validate new quantity against stock
    - Update cart item quantity
    - _Requirements: 23.6_
  
  - [x] 10C.3 Implement removeFromCart method
    - Remove cart item
    - _Requirements: 23.6_
  
  - [x] 10C.4 Implement getCartWithTotals method
    - Calculate subtotal per seller
    - Calculate shipping costs
    - Calculate grand total
    - _Requirements: 23.5, 24.6_
  
  - [x] 10C.5 Implement clearCart method
    - Remove all items from cart
    - _Requirements: 23.8_
  
  - [x] 10C.6 Write property tests for cart operations
    - **Property 61: Cart Quantity Validation**
    - **Property 62: Cart Total Calculation**
    - **Validates: Requirements 23.4, 23.5**

- [x] 10D. Implement OrderService (NEW - Order Management)
  - [x] 10D.1 Implement createOrderFromCart method
    - Use DB transaction
    - Validate stock for all items
    - Generate unique order number
    - Create order and order items
    - Decrement stock for all items
    - Process payment via WalletService
    - Clear cart
    - Send notifications
    - _Requirements: 23.7, 23.8, 23.9, 23.10, 23.11_
  
  - [x] 10D.2 Implement updateOrderStatus method
    - Validate status transitions
    - Update order status
    - Send notification to buyer
    - _Requirements: 25.4, 25.5_
  
  - [x] 10D.3 Implement cancelOrder method
    - Validate cancellation eligibility (within 1 hour, status pending)
    - Refund buyer wallet
    - Restore product stock
    - Update order status to cancelled
    - _Requirements: 25.7, 25.8_
  
  - [x] 10D.4 Implement getOrdersByUser method
    - Retrieve orders for buyer or seller
    - Support filtering and pagination
    - _Requirements: 25.1, 25.6_
  
  - [x] 10D.5 Write property tests for order operations
    - **Property 63: Order Number Uniqueness**
    - **Property 64: Order Payment Atomicity**
    - **Property 65: Order Cancellation Refund**
    - **Validates: Requirements 23.8, 23.9, 25.8**
  
  - [x] 10D.6 Write integration test for complete purchase flow
    - Test: add to cart → checkout → payment → order creation
    - Verify stock decrement
    - Verify wallet transactions
    - _Requirements: 23.1-23.11_

- [x] 10E. Implement ShippingService (NEW - Shipping Management)
  - [x] 10E.1 Implement createShippingMethod method (admin)
    - Validate method name and cost
    - Create shipping method
    - _Requirements: 24.1_
  
  - [x] 10E.2 Implement attachShippingToListing method
    - Attach shipping methods to listing
    - Support custom cost adjustments
    - _Requirements: 24.3, 24.4_
  
  - [x] 10E.3 Implement calculateShippingCost method
    - Get base cost from shipping method
    - Apply listing-specific adjustments
    - _Requirements: 24.6_
  
  - [x] 10E.4 Write unit tests for shipping operations
    - Test shipping method CRUD
    - Test cost calculation with adjustments
    - _Requirements: 24.1-24.6_

- [x] 11. Authentication and authorization (Updated for Marketplace)
  - [x] 11.1 Set up Laravel Breeze or custom auth
    - Configure authentication routes
    - Create registration with email verification
    - Add role assignment during registration
    - Add username field for sellers (storefront URL)
    - Auto-create store for seller registrations
    - _Requirements: 1.1, 1.2, 1.6, 21.1_
  
  - [x] 11.2 Write property tests for authentication
    - **Property 1: User Registration Creates Wallet and Store**
    - **Property 2: Password Complexity Enforcement**
    - **Validates: Requirements 1.1, 1.5, 1.6, 2.1, 21.1**
  
  - [x] 11.3 Create authorization policies
    - ListingPolicy (create, update, delete, cancel) - replaces AuctionPolicy
    - BidPolicy (place bid)
    - StorePolicy (update storefront)
    - OrderPolicy (view, cancel)
    - AdminPolicy (admin actions)
    - _Requirements: 1.3, 1.4, 21.5-21.7, 25.7_
  
  - [x] 11.4 Write property test for role-based access
    - **Property 3: Role-Based Access Control**
    - **Validates: Requirements 1.3, 1.4**
  
  - [x] 11.5 Create middleware for role checking
    - EnsureSeller middleware
    - EnsureAdmin middleware
    - _Requirements: 1.3, 1.4_

- [x] 12. Form request validation classes (Updated for Marketplace)
  - **Listing requests**:
    - CreateListingRequest (with type-specific validation)
    - UpdateListingRequest
  - **Auction requests**:
    - PlaceBidRequest with dynamic validation
    - ParticipateAuctionRequest
  - **Store requests**:
    - UpdateStoreRequest
    - UploadStoreBannerRequest
    - UploadStoreLogoRequest
  - **Cart/Order requests**:
    - AddToCartRequest
    - CheckoutRequest
    - UpdateOrderStatusRequest
  - **Shipping requests**:
    - CreateShippingMethodRequest (admin)
  - **Image requests**:
    - UploadImageRequest with file validation
  - Add Farsi error messages to all requests
  - _Requirements: 3.1, 5.2, 5.3, 18.1, 18.2, 21.5-21.7, 22.1-22.5, 23.2, 24.1, 25.4, 28.2-28.7_

- [x] 13. Notification system (Updated for Marketplace)
  - [x] 13.1 Create notification classes
    - **Auction notifications**:
      - AuctionStartedNotification
      - OutbidNotification
      - AuctionEndedNotification
      - WinnerSelectedNotification
      - DeadlineReminderNotification
      - DepositReleasedNotification
      - DepositForfeitedNotification
    - **Order notifications** (NEW):
      - OrderPlacedNotification (to buyer and seller)
      - OrderStatusUpdatedNotification
      - OrderCancelledNotification
      - OrderShippedNotification
    - **Store notifications** (NEW):
      - LowStockAlertNotification
      - OutOfStockNotification
    - _Requirements: 10.1-10.7, 23.11, 25.5, 27.7_
  
  - [x] 13.2 Integrate notifications into services
    - Add notification dispatch to AuctionService methods
    - Add notification dispatch to WalletService methods
    - Add notification dispatch to OrderService methods
    - Add notification dispatch to ListingService methods (stock alerts)
    - _Requirements: 10.1-10.7, 23.11, 25.5, 27.7_
  
  - [x] 13.3 Write property test for notification creation
    - **Property 30: Notification Creation on Events**
    - **Property 66: Order Notification Delivery** (NEW)
    - **Validates: Requirements 10.1-10.7, 23.11, 25.5**

- [x] 14. Scheduled jobs for automation
  - [x] 14.1 Create ProcessAuctionStarting job
    - Query auctions with status='pending' and start_time <= now
    - Call AuctionService.startAuction for each
    - _Requirements: 20.1_
  
  - [x] 14.2 Create ProcessAuctionEnding job
    - Query auctions with status='active' and end_time <= now
    - Call AuctionService.endAuction for each
    - Implement retry logic with exponential backoff
    - _Requirements: 20.2, 20.7_
  
  - [x] 14.3 Create ProcessFinalizationTimeout job
    - Query auctions with finalization_deadline <= now and status='ended'
    - Call AuctionService.handleFinalizationTimeout for each
    - _Requirements: 20.3_
  
  - [x] 14.4 Register jobs in Kernel schedule
    - ProcessAuctionStarting every minute
    - ProcessAuctionEnding every minute
    - ProcessFinalizationTimeout every hour
    - _Requirements: 20.1, 20.2, 20.3_
  
  - [x] 14.5 Write property tests for job execution
    - **Property 54: Job Execution Logging**
    - **Property 55: Job Retry Logic**
    - **Validates: Requirements 20.6, 20.7**

- [x] 15. Image upload and management
  - [x] 15.1 Create ImageService
    - Implement upload method with validation
    - Implement optimization (thumbnail generation)
    - Implement unique filename generation
    - Implement delete method with time constraint check
    - _Requirements: 18.1, 18.2, 18.3, 18.4, 18.6_
  
  - [x] 15.2 Write property tests for image handling
    - **Property 46: Image Format Validation**
    - **Property 47: Image Size Validation**
    - **Property 48: Image Optimization**
    - **Property 49: Image Filename Uniqueness**
    - **Property 50: Image Deletion Time Constraint**
    - **Validates: Requirements 18.1, 18.2, 18.3, 18.4, 18.6**
  
  - [x] 15.3 Implement image reordering functionality
    - Create method to update display_order
    - _Requirements: 18.5_


- [x] 16. Localization and formatting utilities
  - [x] 16.1 Create JalaliDateService
    - Implement conversion methods using morilog/jalali
    - Create Blade directive @jalali for easy use in views
    - _Requirements: 11.4_
  
  - [x] 16.2 Write property test for Jalali formatting
    - **Property 31: Jalali Date Formatting**
    - **Validates: Requirements 11.4**
  
  - [x] 16.3 Create PersianNumberService
    - Implement number to Persian digit conversion
    - Implement currency formatting with Rial
    - Create Blade directives @persian and @currency
    - _Requirements: 11.5, 11.7_
  
  - [x] 16.4 Write property test for Persian number formatting
    - **Property 32: Persian Number Formatting**
    - **Validates: Requirements 11.5, 11.7**
  
  - [x] 16.5 Set up Farsi language files
    - Create fa.json with all UI strings
    - Configure app locale to 'fa'
    - _Requirements: 11.1_

- [x] 17. Controllers (Updated for Marketplace - thin layer delegating to services)
  - [x] 17.1 Create ListingController (UPDATED - replaces AuctionController)
    - index: list all listings with filters (type, status, seller)
    - show: display single listing (auction or direct sale UI based on type)
    - create/store: create new listing (auction, direct_sale, or hybrid)
    - update: update listing details
    - participate: call DepositService (for auctions)
    - _Requirements: 3.1, 4.1, 19.1, 19.2, 19.3, 19.4, 22.1-22.8, 26.1-26.7_
  
  - [x] 17.2 Create BidController
    - store: call BidService.placeBid
    - _Requirements: 5.1_
  
  - [x] 17.3 Create PaymentController
    - complete: call AuctionService.completeWinnerPayment (for auction winners)
    - _Requirements: 8.2, 9.2_
  
  - [x] 17.4 Create WalletController
    - show: display wallet and transaction history
    - addFunds: call WalletService.addFunds
    - transactions: list with filters
    - export: generate CSV
    - _Requirements: 2.2, 17.1, 17.2, 17.3, 17.4_
  
  - [x] 17.5 Create StoreController (NEW - Storefront)
    - show: display public storefront at /store/{username}
    - edit: show storefront customization form (seller only)
    - update: call StoreService.updateStoreProfile
    - uploadBanner: handle banner image upload
    - uploadLogo: handle logo image upload
    - _Requirements: 21.2, 21.3, 21.4, 21.5, 21.6, 21.7, 28.1-28.9_
  
  - [x] 17.6 Create CartController (NEW - Shopping Cart)
    - index: display cart with items and totals
    - add: call CartService.addToCart
    - update: call CartService.updateCartItem
    - remove: call CartService.removeFromCart
    - _Requirements: 23.1, 23.2, 23.5, 23.6_
  
  - [x] 17.7 Create CheckoutController (NEW - Order Checkout)
    - show: display checkout page with cart summary and shipping options
    - process: call OrderService.createOrderFromCart
    - _Requirements: 23.7, 23.8, 24.5, 24.6_
  
  - [x] 17.8 Create OrderController (NEW - Order Management)
    - index: list orders (buyer or seller view)
    - show: display order details
    - updateStatus: call OrderService.updateOrderStatus (seller only)
    - cancel: call OrderService.cancelOrder (buyer only, within 1 hour)
    - _Requirements: 25.1, 25.3, 25.4, 25.6, 25.7_
  
  - [x] 17.9 Create DashboardController (UPDATED)
    - sellerDashboard: show seller statistics (auctions + direct sales + orders)
    - buyerDashboard: show buyer statistics (bids + purchases + orders)
    - _Requirements: 17.5, 17.6, 25.1_
  
  - [x] 17.10 Create Admin\ListingController (UPDATED - replaces Admin\AuctionController)
    - index: list all listings with admin view
    - show: detailed listing view
    - cancel: call AuctionService/ListingService with admin override
    - releaseDeposit: manual deposit release
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5_
  
  - [x] 17.11 Create Admin\ShippingMethodController (NEW)
    - index: list all shipping methods
    - create/store: call ShippingService.createShippingMethod
    - edit/update: update shipping method
    - destroy: deactivate shipping method
    - _Requirements: 24.1, 24.2_
  
  - [x] 17.12 Create Admin\OrderController (NEW)
    - index: list all orders with filters
    - show: detailed order view
    - _Requirements: 25.1, 25.6_
  
  - [x] 17.13 Write unit tests for controllers
    - Test request validation
    - Test authorization checks
    - Test service method calls
    - Test new marketplace controllers (Store, Cart, Checkout, Order)
    - _Requirements: 1.3, 1.4, 3.1, 5.1, 21.2, 23.2, 23.8, 25.4_

- [x] 18. Checkpoint - Ensure backend logic complete
  - Run all backend tests (services, controllers, jobs)
  - Verify all property tests pass
  - Verify authorization and validation work correctly
  - Ask user if questions arise


- [x] 19. Livewire components for real-time features (Updated for Marketplace)
  - [x] 19.1 Create AuctionBidding component
    - Display current highest bid
    - Display current rankings
    - Bid input form
    - placeBid method calling BidService
    - Listen for BidPlaced event to refresh
    - _Requirements: 5.1, 5.5, 6.2, 6.3_
  
  - [x] 19.2 Write Livewire component tests for bidding
    - Test bid placement updates component state
    - Test real-time refresh on broadcast
    - _Requirements: 5.5, 6.2_
  
  - [x] 19.3 Create AuctionCountdown component
    - Calculate and display remaining time
    - Update every second using wire:poll
    - Display "پایان یافته" when ended
    - _Requirements: 6.1, 6.4, 6.5_
  
  - [x] 19.4 Write Livewire component tests for countdown
    - Test time calculation
    - Test ended state display
    - _Requirements: 6.1, 6.4_
  
  - [x] 19.5 Create AuctionParticipation component
    - Display deposit requirement
    - Participate button calling DepositService
    - Show participation status
    - _Requirements: 4.1, 4.2_
  
  - [x] 19.6 Create WalletBalance component
    - Display available and frozen balance
    - Real-time updates on wallet changes
    - _Requirements: 2.1, 2.3, 2.4_
  
  - [x] 19.7 Create DirectSalePurchase component (NEW)
    - Display price and stock availability
    - Quantity selector
    - "Add to Cart" button calling CartService
    - "Buy Now" button (add to cart + redirect to checkout)
    - Real-time stock updates
    - _Requirements: 23.1, 23.2, 23.3, 23.4, 26.2_
  
  - [x] 19.8 Create CartSummary component (NEW)
    - Display cart item count
    - Display cart total
    - Real-time updates when items added/removed
    - Mini cart dropdown
    - _Requirements: 23.5, 23.6_
  
  - [x] 19.9 Create StoreListings component (NEW)
    - Display seller's listings on storefront
    - Filter by type (auctions, direct sales, all)
    - Pagination
    - _Requirements: 21.4, 29.2_
  
  - [x] 19.10 Write Livewire component tests for marketplace features
    - Test DirectSalePurchase component
    - Test CartSummary updates
    - Test StoreListings filtering
    - _Requirements: 23.2, 23.5, 21.4_

- [x] 20. Blade views with RTL and Tailwind styling (Updated for Marketplace)
  - [x] 20.1 Create layout template
    - RTL direction (dir="rtl")
    - Vazirmatn font loading
    - Tailwind CSS with RTL plugin
    - Alpine.js integration
    - Navigation with role-based menu items (including Store link for sellers)
    - Cart icon with item count
    - _Requirements: 11.1, 11.2, 11.3, 12.1, 12.2, 23.5_
  
  - [x] 20.2 Create listing index page (UPDATED - replaces auction listing)
    - Grid of listing cards (auctions + direct sales)
    - Type badges (مزایده / فروش مستقیم / ترکیبی)
    - Search and filter form (by type, status, price, seller)
    - Pagination
    - Beautiful hover effects
    - _Requirements: 12.3, 12.4, 12.5, 19.1, 19.6, 22.1, 26.4, 26.5, 26.6, 29.1-29.8_
  
  - [x] 20.3 Create listing detail page (UPDATED - dynamic based on type)
    - Image gallery with primary thumbnail
    - Listing information in beautiful cards
    - **For auctions**: Countdown timer, Participation section, Bidding section, Bid history
    - **For direct sales**: Price, Stock status, Quantity selector, Add to Cart, Buy Now
    - **For hybrid**: Both auction and direct sale interfaces
    - Seller info with link to storefront
    - Shipping methods and costs
    - _Requirements: 6.1, 12.3, 12.4, 18.7, 22.1-22.8, 23.1-23.3, 24.5, 26.1-26.7_
  
  - [x] 20.4 Create listing creation form (UPDATED)
    - Multi-step form with Alpine.js
    - Type selection (auction, direct_sale, hybrid)
    - Type-specific fields (conditional display)
    - Image upload with preview
    - Jalali date picker (for auctions)
    - Stock input (for direct sales)
    - Shipping method selection
    - Auto-calculated deposit display (for auctions)
    - _Requirements: 3.1, 3.2, 11.4, 18.1, 22.1-22.5, 24.3_
  
  - [x] 20.5 Create storefront page (NEW)
    - Seller banner and logo
    - Seller description
    - Seller statistics (total sales, member since, rating)
    - Grid of seller's active listings
    - Filter by type (auctions, direct sales, all)
    - Beautiful gradient design
    - _Requirements: 21.2, 21.3, 21.4, 21.9, 28.1-28.9_
  
  - [x] 20.6 Create storefront customization page (NEW - seller only)
    - Banner upload with preview
    - Logo upload with preview
    - Description editor (rich text)
    - Preview button
    - _Requirements: 21.5, 21.6, 21.7, 28.1-28.9_
  
  - [x] 20.7 Create cart page (NEW)
    - Cart items list with thumbnails
    - Quantity update controls
    - Remove item buttons
    - Subtotal per seller
    - Shipping cost calculation
    - Grand total
    - Proceed to checkout button
    - _Requirements: 23.5, 23.6, 24.6_
  
  - [x] 20.8 Create checkout page (NEW)
    - Cart summary (read-only)
    - Shipping method selection per seller
    - Shipping address form
    - Payment summary (subtotal + shipping + total)
    - Wallet balance display
    - Place order button
    - _Requirements: 23.7, 23.8, 24.5, 24.6, 30.2_
  
  - [x] 20.9 Create order list page (NEW)
    - Orders table with filters (status, date range)
    - Different views for buyers and sellers
    - Order number, date, status, total
    - Click to view details
    - _Requirements: 25.1, 25.3, 25.6_
  
  - [x] 20.10 Create order detail page (NEW)
    - Order information card
    - Order items list
    - Shipping information
    - Status timeline
    - Tracking number (if shipped)
    - Cancel button (buyer, within 1 hour)
    - Update status button (seller)
    - _Requirements: 25.4, 25.5, 25.6, 25.7, 24.10_
  
  - [x] 20.11 Create wallet page
    - Balance display with Persian numbers
    - Add funds form
    - Transaction history table
    - Filter and export functionality
    - _Requirements: 11.5, 17.1, 17.2, 17.3, 17.4_
  
  - [x] 20.12 Create dashboard pages (UPDATED)
    - Seller dashboard: statistics cards (auctions + direct sales + orders), recent orders, low stock alerts
    - Buyer dashboard: active bids, recent purchases, order status
    - Beautiful gradient cards and charts
    - _Requirements: 12.5, 12.6, 17.5, 17.6, 25.1, 27.7_
  
  - [x] 20.13 Create admin pages (UPDATED)
    - Admin dashboard with system statistics (listings, orders, users)
    - Listing management table
    - Order management table
    - Shipping methods CRUD
    - Audit log viewer
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5, 13.6, 24.1, 24.2, 25.1_
  
  - [x] 20.14 Create authentication pages
    - Login page with RTL styling
    - Registration page with role selection and username field
    - Password reset pages
    - _Requirements: 1.1, 1.2, 1.6, 11.1, 11.2, 21.1_
    - Search and filter form
    - Pagination
    - Beautiful hover effects
    - _Requirements: 12.3, 12.4, 12.5, 19.1, 19.6_
  
  - [x] 20.3 Create auction detail page
    - Image gallery with primary thumbnail
    - Auction information in beautiful cards
    - Countdown timer (Livewire component)
    - Participation section (Livewire component)
    - Bidding section (Livewire component)
    - Bid history with rankings
    - _Requirements: 6.1, 12.3, 12.4, 18.7_
  
  - [x] 20.4 Create auction creation form
    - Multi-step form with Alpine.js
    - Image upload with preview
    - Jalali date picker
    - Auto-calculated deposit display
    - _Requirements: 3.1, 3.2, 11.4, 18.1_
  
  - [x] 20.5 Create wallet page
    - Balance display with Persian numbers
    - Add funds form
    - Transaction history table
    - Filter and export functionality
    - _Requirements: 11.5, 17.1, 17.2, 17.3, 17.4_
  
  - [x] 20.6 Create dashboard pages
    - Seller dashboard with statistics cards
    - Buyer dashboard with active bids
    - Beautiful gradient cards and charts
    - _Requirements: 12.5, 12.6, 17.5, 17.6_
  
  - [x] 20.7 Create admin pages
    - Admin dashboard with system statistics
    - Auction management table
    - Auction detail with admin actions
    - Audit log viewer
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5, 13.6_
  
  - [x] 20.8 Create authentication pages
    - Login page with RTL styling
    - Registration page with role selection
    - Password reset pages
    - _Requirements: 1.1, 1.2, 11.1, 11.2_

- [x] 21. Search and filtering implementation
  - [x] 21.1 Implement search functionality
    - Full-text search in title and description
    - Use database indexes for performance
    - _Requirements: 19.1_
  
  - [x] 21.2 Write property test for search
    - **Property 51: Search Functionality**
    - **Validates: Requirements 19.1**
  
  - [x] 21.3 Implement filtering functionality
    - Filter by status (active, ended, upcoming)
    - Filter by price range
    - Filter by category (if categories added)
    - _Requirements: 19.2, 19.3, 19.4_
  
  - [x] 21.4 Write property test for filtering
    - **Property 52: Auction Filtering**
    - **Validates: Requirements 19.2, 19.3, 19.4**
  
  - [x] 21.5 Implement pagination
    - 20 items per page
    - Preserve filters in pagination links
    - _Requirements: 19.6_
  
  - [x] 21.6 Write property test for pagination
    - **Property 53: Pagination Consistency**
    - **Validates: Requirements 19.6**


- [x] 22. Security implementation
  - [x] 22.1 Implement password hashing
    - Configure bcrypt cost factor to 10
    - Verify in User model
    - _Requirements: 14.1_
  
  - [x] 22.2 Write property test for password security
    - **Property 36: Password Hashing Security**
    - **Validates: Requirements 14.1**
  
  - [x] 22.3 Implement input sanitization
    - Create middleware for XSS prevention
    - Use Laravel's built-in SQL injection protection
    - Validate all user inputs
    - _Requirements: 14.2, 14.3_
  
  - [x] 22.4 Write property test for input sanitization
    - **Property 37: Input Sanitization**
    - **Validates: Requirements 14.2, 14.3**
  
  - [x] 22.5 Implement CSRF protection
    - Verify CSRF middleware is active
    - Add @csrf to all forms
    - _Requirements: 14.4_
  
  - [x] 22.6 Write property test for CSRF validation
    - **Property 38: CSRF Token Validation**
    - **Validates: Requirements 14.4**
  
  - [x] 22.7 Implement rate limiting
    - Add rate limiter for bid placement
    - Configure limits in RouteServiceProvider
    - _Requirements: 14.8_
  
  - [x] 22.8 Write property test for rate limiting
    - **Property 39: Bid Rate Limiting**
    - **Validates: Requirements 14.8**
  
  - [x] 22.9 Configure HTTPS enforcement
    - Add middleware for production
    - Update .env.example
    - _Requirements: 14.7_

- [x] 23. API layer with Laravel Sanctum
  - [x] 23.1 Install and configure Laravel Sanctum
    - Publish configuration
    - Add Sanctum middleware to api routes
    - _Requirements: 15.1, 15.2_
  
  - [x] 23.2 Create API authentication endpoints
    - POST /api/register
    - POST /api/login (returns token)
    - POST /api/logout
    - _Requirements: 15.2, 15.3_
  
  - [x] 23.3 Write property test for API authentication
    - **Property 40: API Authentication**
    - **Validates: Requirements 15.2, 15.4**
  
  - [x] 23.4 Create API resource classes
    - AuctionResource
    - BidResource
    - WalletResource
    - UserResource
    - _Requirements: 15.5_
  
  - [x] 23.5 Create API controllers
    - Api\AuctionController (index, show, store, participate)
    - Api\BidController (store)
    - Api\WalletController (show, transactions)
    - All return JSON with proper status codes
    - _Requirements: 15.1, 15.5, 15.6_
  
  - [x] 23.6 Write property test for API response format
    - **Property 41: API Response Format Consistency**
    - **Validates: Requirements 15.5, 15.6**
  
  - [x] 23.7 Implement API rate limiting
    - Configure per-user rate limits
    - _Requirements: 15.7_

- [x] 24. Admin features and audit logging
  - [x] 24.1 Implement admin statistics
    - Calculate active auctions count
    - Calculate total users
    - Calculate transaction volume
    - _Requirements: 13.1_
  
  - [x] 24.2 Write property test for admin statistics
    - **Property 33: Admin Statistics Accuracy**
    - **Validates: Requirements 13.1**
  
  - [x] 24.3 Implement admin auction cancellation
    - Add cancel method to AuctionService
    - Release all deposits
    - Update auction status
    - _Requirements: 13.4_
  
  - [x] 24.4 Write property test for admin cancellation
    - **Property 34: Admin Cancellation Releases Deposits**
    - **Validates: Requirements 13.4**
  
  - [x] 24.5 Implement audit logging
    - Create AdminActionLog model
    - Log all admin actions with context
    - Create admin log viewer page
    - _Requirements: 13.6_
  
  - [x] 24.6 Write property test for audit logging
    - **Property 35: Admin Action Audit Logging**
    - **Validates: Requirements 13.6**

- [x] 25. Transaction history and reporting
  - [x] 25.1 Implement transaction history query
    - Order by created_at DESC
    - Include all transaction details
    - _Requirements: 17.1, 17.2_
  
  - [x] 25.2 Write property test for transaction ordering
    - **Property 42: Transaction History Ordering**
    - **Validates: Requirements 17.1**
  
  - [x] 25.3 Implement transaction filtering
    - Filter by date range
    - Filter by transaction type
    - _Requirements: 17.3_
  
  - [x] 25.4 Write property test for transaction filtering
    - **Property 43: Transaction History Filtering**
    - **Validates: Requirements 17.3**
  
  - [x] 25.5 Implement CSV export
    - Generate CSV with all transaction fields
    - Include proper headers
    - _Requirements: 17.4_
  
  - [x] 25.6 Write property test for CSV export
    - **Property 44: CSV Export Completeness**
    - **Validates: Requirements 17.4**
  
  - [x] 25.7 Implement dashboard statistics
    - Seller: total sales, active auctions, completed auctions
    - Buyer: active bids, won auctions, frozen balance
    - _Requirements: 17.5, 17.6_
  
  - [x] 25.8 Write property test for dashboard statistics
    - **Property 45: Dashboard Statistics Accuracy**
    - **Validates: Requirements 17.5, 17.6**

- [x] 26. Checkpoint - Ensure all features complete
  - Run full test suite (unit + property tests)
  - Verify all 55 properties pass with 100+ iterations
  - Test complete workflows end-to-end
  - Verify UI renders correctly in RTL
  - Ask user if questions arise


- [x] 27. Performance optimization
  - [x] 27.1 Add database indexes
    - Verify all foreign keys have indexes
    - Add composite indexes for common queries
    - Add index on (auction_id, amount DESC) for bids
    - _Requirements: 16.3_
  
  - [x] 27.2 Implement query optimization
    - Use eager loading to prevent N+1 queries
    - Add select() to limit columns where appropriate
    - _Requirements: 16.4_
  
  - [x] 27.3 Configure caching
    - Cache auction listings
    - Cache user statistics
    - Set appropriate TTL values
    - _Requirements: 16.6_
  
  - [x] 27.4 Optimize Livewire polling
    - Set polling interval to 3 seconds max
    - Use wire:poll.visible for efficiency
    - _Requirements: 16.5_

- [x] 28. Integration testing
  - [x] 28.1 Write integration test for complete auction workflow
    - Test: create auction → participate → bid → end → payment
    - Verify all state transitions
    - Verify all financial operations
    - _Requirements: 3.1, 4.1, 5.1, 7.1, 8.2_
  
  - [x] 28.2 Write integration test for cascade logic
    - Test: auction ends → rank 1 timeout → rank 2 selected → rank 2 timeout → rank 3 selected
    - Verify deposits forfeited correctly
    - Verify seller receives forfeit compensation
    - _Requirements: 8.6, 8.7, 8.9_
  
  - [x] 28.3 Write integration test for concurrent bidding
    - Simulate multiple users bidding simultaneously
    - Verify no race conditions
    - Verify all bids processed correctly
    - _Requirements: 5.7, 14.5_
  
  - [x] 28.4 Write integration test for wallet operations
    - Test: add funds → freeze → release → deduct
    - Verify transaction history complete
    - Verify balances always correct
    - _Requirements: 2.2, 2.3, 2.4, 2.5_

- [x] 29. Final polish and documentation
  - [x] 29.1 Create seeder for demo data
    - Seed users with different roles
    - Seed auctions in various states
    - Seed bids and participations
    - _Requirements: N/A_
  
  - [x] 29.2 Create README.md
    - Installation instructions
    - Configuration steps
    - Running scheduled jobs
    - Running tests
    - _Requirements: N/A_
  
  - [x] 29.3 Create .env.example
    - All required environment variables
    - Comments explaining each variable
    - _Requirements: N/A_
  
  - [x] 29.4 Add code comments
    - Document complex business logic
    - Add PHPDoc blocks to all public methods
    - _Requirements: N/A_
  
  - [x] 29.5 Create deployment guide
    - Server requirements
    - Database setup
    - Cron job configuration for scheduled tasks
    - HTTPS configuration
    - _Requirements: 14.7, 20.1, 20.2, 20.3_

- [x] 30. Final checkpoint - Production readiness
  - Run full test suite one final time
  - Verify all 55 property tests pass
  - Verify code coverage meets 80% target
  - Test on mobile devices for responsive design
  - Verify RTL rendering on all pages
  - Verify Jalali dates display correctly
  - Verify Persian numbers display correctly
  - Test scheduled jobs manually
  - Review security checklist
  - Ask user for final approval

## Notes

- Tasks marked with `*` are optional test-related sub-tasks and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation at key milestones
- Property tests validate universal correctness properties with 100+ iterations
- Unit tests validate specific examples and edge cases
- Integration tests validate end-to-end workflows
- The implementation follows a bottom-up approach: services → controllers → UI
- All financial operations use database transactions with row locking for safety
- All UI text is in Farsi with RTL layout
- All dates use Jalali calendar
- All numbers use Persian digits where appropriate
