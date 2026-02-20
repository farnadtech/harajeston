# Requirements Document: Iranian Multi-Vendor Marketplace

## Introduction

The Iranian Multi-Vendor Marketplace is a professional, secure online platform optimized for the Iranian web market. It combines a sophisticated auction system with direct sales capabilities, allowing sellers to create storefronts (ویترین) and offer products through multiple sales channels. The platform implements a deposit-based bidding system for auctions, integrated shopping cart for direct sales, comprehensive shipping management, and full RTL/Farsi support.

## Glossary

- **Marketplace_System**: The complete multi-vendor e-commerce and auction platform
- **Seller/Vendor**: A user who creates a storefront and lists items for auction or direct sale
- **Buyer/Customer**: A user who participates in auctions or purchases products directly
- **Admin**: System administrator with oversight and management capabilities
- **Storefront (Vitrin)**: A seller's public profile page displaying their products and auctions
- **Listing**: A generic term for any item (auction or direct sale product)
- **Listing_Type**: Enum defining the sales method (auction, direct_sale, hybrid)
- **Deposit**: A frozen amount (10% of base price) required to participate in an auction
- **Base_Price**: The starting minimum price for an auction item
- **Frozen_Balance**: Wallet funds that are blocked but not deducted
- **Top_3_Bidders**: The three highest bidders when an auction ends
- **Cascade_Logic**: The sequential offer process from Rank 1 to Rank 3 if higher-ranked bidders fail to complete purchase
- **Wallet**: User's account balance system with available and frozen funds
- **Finalization_Window**: 48-hour period for auction winner to complete payment
- **Direct_Sale**: Standard e-commerce purchase with immediate checkout
- **Hybrid_Listing**: A listing that supports both auction and direct purchase options
- **Cart**: Shopping cart for direct sale items
- **Order**: A completed purchase transaction for direct sale items
- **Shipping_Method**: Delivery service option (e.g., Pishtaz, Tipax, Peyk)
- **Stock/Inventory**: Available quantity for direct sale items

## Requirements

### Requirement 1: User Authentication and Authorization

**User Story:** As a user, I want to register and authenticate securely, so that I can participate in the marketplace with proper access controls.

#### Acceptance Criteria

1. WHEN a new user registers, THE Marketplace_System SHALL create a user account with email verification
2. WHEN a user logs in with valid credentials, THE Marketplace_System SHALL authenticate the user and create a session
3. WHEN a user attempts to access seller features, THE Marketplace_System SHALL verify the user has seller role permissions
4. WHEN a user attempts to access admin features, THE Marketplace_System SHALL verify the user has admin role permissions
5. THE Marketplace_System SHALL enforce password complexity requirements of minimum 8 characters with mixed case and numbers
6. WHEN a user registers as a seller, THE Marketplace_System SHALL automatically create a storefront profile for that user

### Requirement 2: Wallet Management

**User Story:** As a buyer, I want to manage my wallet with available and frozen balances, so that I can participate in auctions and make purchases.

#### Acceptance Criteria

1. WHEN a user account is created, THE Marketplace_System SHALL initialize a wallet with zero balance and zero frozen amount
2. WHEN a user deposits funds, THE Marketplace_System SHALL increase the available balance within 2 seconds
3. WHEN a deposit is required for auction participation, THE Marketplace_System SHALL move funds from available balance to frozen balance without deduction
4. WHEN frozen funds are released, THE Marketplace_System SHALL move funds from frozen balance back to available balance within 2 seconds
5. WHEN frozen funds are deducted, THE Marketplace_System SHALL remove funds from frozen balance and update transaction history
6. THE Marketplace_System SHALL prevent any wallet operation that would result in negative available balance
7. THE Marketplace_System SHALL use database row locking to prevent race conditions during concurrent wallet operations

### Requirement 3: Auction Creation

**User Story:** As a seller, I want to create auctions for high-value items, so that I can sell items through a competitive bidding process.

#### Acceptance Criteria

1. WHEN a seller creates an auction, THE Marketplace_System SHALL require item title, description, base price, start time, and end time
2. WHEN a seller specifies a base price, THE Marketplace_System SHALL automatically calculate the required deposit as 10% of the base price
3. WHEN a seller sets auction times, THE Marketplace_System SHALL validate that end time is after start time
4. WHEN a seller sets auction times, THE Marketplace_System SHALL validate that start time is at least 1 hour in the future
5. WHEN an auction is created, THE Marketplace_System SHALL store the auction with status 'pending' until start time
6. THE Marketplace_System SHALL support uploading multiple images for each auction item with maximum 5 images per auction

### Requirement 4: Auction Participation and Deposit Management

**User Story:** As a buyer, I want to pay a deposit to participate in auctions, so that I can place bids on items I'm interested in.

#### Acceptance Criteria

1. WHEN a buyer attempts to participate in an auction, THE Marketplace_System SHALL verify the buyer has sufficient available balance for the deposit
2. WHEN a buyer pays the deposit, THE Marketplace_System SHALL freeze the deposit amount in the buyer's wallet
3. WHEN a deposit is frozen, THE Marketplace_System SHALL record the participation with timestamp and buyer information
4. WHEN a buyer has already paid deposit for an auction, THE Marketplace_System SHALL prevent duplicate deposit payments
5. IF a buyer attempts to participate without sufficient balance, THEN THE Marketplace_System SHALL reject the participation and display required amount

### Requirement 5: Bidding Process

**User Story:** As a participating buyer, I want to place bids on auction items, so that I can compete to win the auction.

#### Acceptance Criteria

1. WHEN a buyer places a bid, THE Marketplace_System SHALL verify the buyer has paid the required deposit
2. WHEN a buyer places a bid, THE Marketplace_System SHALL validate the bid amount is higher than the current highest bid
3. WHEN a buyer places a bid, THE Marketplace_System SHALL validate the bid amount is at least the base price
4. WHEN a valid bid is placed, THE Marketplace_System SHALL record the bid with timestamp and buyer information within 1 second
5. WHEN a valid bid is placed, THE Marketplace_System SHALL update the auction's current highest bid and highest bidder
6. WHEN a bid is placed, THE Marketplace_System SHALL broadcast the update to all active viewers within 2 seconds
7. THE Marketplace_System SHALL use database locking to prevent race conditions when processing simultaneous bids

### Requirement 6: Real-Time Auction Updates

**User Story:** As a buyer viewing an auction, I want to see live updates of bids and rankings, so that I can make informed bidding decisions.

#### Acceptance Criteria

1. WHEN a user views an active auction, THE Marketplace_System SHALL display a live countdown timer showing remaining time
2. WHEN a new bid is placed, THE Marketplace_System SHALL update the displayed current highest bid for all viewers within 2 seconds
3. WHEN a new bid is placed, THE Marketplace_System SHALL update the bidder rankings for all viewers within 2 seconds
4. WHEN the auction time expires, THE Marketplace_System SHALL display auction ended status to all viewers immediately
5. THE Marketplace_System SHALL update countdown timers every second without full page reload

### Requirement 7: Auction Ending and Top 3 Selection

**User Story:** As the system, I want to automatically process auction endings, so that winners are identified and non-winning deposits are released.

#### Acceptance Criteria

1. WHEN an auction's end time is reached, THE Marketplace_System SHALL change the auction status to 'ended'
2. WHEN an auction ends, THE Marketplace_System SHALL identify the top 3 highest bidders based on bid amounts
3. WHEN an auction ends with top 3 bidders identified, THE Marketplace_System SHALL keep their deposits frozen
4. WHEN an auction ends, THE Marketplace_System SHALL release frozen deposits for all bidders outside the top 3 within 5 minutes
5. WHEN an auction ends with fewer than 3 bidders, THE Marketplace_System SHALL keep all participating bidders' deposits frozen
6. WHEN deposits are released, THE Marketplace_System SHALL record the release transaction in the wallet history

### Requirement 8: Winner Finalization with Cascade Logic

**User Story:** As the system, I want to implement cascade winner selection, so that the auction completes even if the highest bidder fails to pay.

#### Acceptance Criteria

1. WHEN an auction ends, THE Marketplace_System SHALL notify the rank 1 bidder and set a 48-hour finalization deadline
2. WHEN the rank 1 bidder completes payment within 48 hours, THE Marketplace_System SHALL deduct the frozen deposit from the final payment amount
3. WHEN the rank 1 bidder completes payment, THE Marketplace_System SHALL transfer the full payment to the seller's wallet
4. WHEN the rank 1 bidder completes payment, THE Marketplace_System SHALL release frozen deposits for rank 2 and rank 3 bidders
5. WHEN the rank 1 bidder completes payment, THE Marketplace_System SHALL mark the auction as 'completed' with rank 1 as winner
6. IF the rank 1 bidder fails to pay within 48 hours, THEN THE Marketplace_System SHALL forfeit their deposit and offer the item to rank 2 bidder
7. IF the rank 2 bidder fails to pay within 48 hours, THEN THE Marketplace_System SHALL forfeit their deposit and offer the item to rank 3 bidder
8. IF all top 3 bidders fail to pay, THEN THE Marketplace_System SHALL mark the auction as 'failed' and release all remaining frozen deposits
9. WHEN a bidder's deposit is forfeited, THE Marketplace_System SHALL transfer the forfeited amount to the seller as compensation

### Requirement 9: Payment Processing

**User Story:** As a winning buyer, I want to complete my payment for won auctions, so that I can receive the purchased item.

#### Acceptance Criteria

1. WHEN a buyer is selected as current winner, THE Marketplace_System SHALL calculate the remaining balance as final bid minus frozen deposit
2. WHEN a buyer initiates payment, THE Marketplace_System SHALL verify the buyer has sufficient available balance for the remaining amount
3. WHEN a buyer completes payment, THE Marketplace_System SHALL deduct the remaining balance from available funds and the deposit from frozen funds
4. WHEN payment is completed, THE Marketplace_System SHALL create a transaction record with all payment details
5. WHEN payment is completed, THE Marketplace_System SHALL update the auction status to 'completed'
6. THE Marketplace_System SHALL process all payment operations within a database transaction to ensure atomicity

### Requirement 10: Notification System

**User Story:** As a user, I want to receive notifications about auction events, so that I stay informed about my auctions and bids.

#### Acceptance Criteria

1. WHEN an auction starts, THE Marketplace_System SHALL notify the seller
2. WHEN a user is outbid, THE Marketplace_System SHALL notify that user within 1 minute
3. WHEN an auction ends, THE Marketplace_System SHALL notify all top 3 bidders of their ranking
4. WHEN a buyer becomes the current winner, THE Marketplace_System SHALL notify that buyer with payment instructions
5. WHEN a finalization deadline approaches (6 hours remaining), THE Marketplace_System SHALL send a reminder notification
6. WHEN a deposit is released, THE Marketplace_System SHALL notify the affected buyer
7. WHEN a deposit is forfeited, THE Marketplace_System SHALL notify the affected buyer with explanation

### Requirement 11: User Interface and Localization

**User Story:** As an Iranian user, I want a fully localized RTL interface with Farsi language and Jalali calendar, so that I can use the platform naturally.

#### Acceptance Criteria

1. THE Marketplace_System SHALL display all text content in Farsi language
2. THE Marketplace_System SHALL render all layouts in right-to-left (RTL) direction
3. THE Marketplace_System SHALL use Vazirmatn font family for all text elements
4. WHEN displaying dates, THE Marketplace_System SHALL format them in Jalali (Shamsi) calendar
5. WHEN displaying currency, THE Marketplace_System SHALL format amounts in Rial with Persian number formatting
6. THE Marketplace_System SHALL display all form inputs with RTL text alignment
7. THE Marketplace_System SHALL use Persian digits (۰-۹) for number display where culturally appropriate

### Requirement 12: Responsive Design and Visual Quality

**User Story:** As a user on any device, I want a beautiful and responsive interface, so that I can use the platform comfortably on desktop and mobile.

#### Acceptance Criteria

1. THE Marketplace_System SHALL render properly on screen widths from 320px to 2560px
2. WHEN viewed on mobile devices, THE Marketplace_System SHALL adapt navigation to a mobile-friendly menu
3. WHEN viewed on mobile devices, THE Marketplace_System SHALL stack auction cards vertically
4. THE Marketplace_System SHALL use Tailwind CSS utility classes for consistent styling
5. THE Marketplace_System SHALL implement smooth hover effects on interactive elements
6. THE Marketplace_System SHALL use gradient backgrounds and modern card designs for premium appearance
7. THE Marketplace_System SHALL load and display images with lazy loading for performance

### Requirement 13: Admin Dashboard and Management

**User Story:** As an admin, I want to monitor and manage all auctions and users, so that I can ensure platform integrity and resolve issues.

#### Acceptance Criteria

1. WHEN an admin accesses the dashboard, THE Marketplace_System SHALL display statistics for active auctions, total users, and transaction volume
2. WHEN an admin views auction list, THE Marketplace_System SHALL show all auctions with status, bidder count, and current highest bid
3. WHEN an admin selects an auction, THE Marketplace_System SHALL display full auction details including all bids and participants
4. WHERE admin privileges are granted, THE Marketplace_System SHALL allow manual auction cancellation with automatic deposit release
5. WHERE admin privileges are granted, THE Marketplace_System SHALL allow manual deposit release for dispute resolution
6. WHEN an admin performs any action, THE Marketplace_System SHALL log the action with timestamp and admin identifier

### Requirement 14: Security and Data Integrity

**User Story:** As a platform stakeholder, I want robust security measures, so that user data and financial transactions are protected.

#### Acceptance Criteria

1. THE Marketplace_System SHALL hash all passwords using bcrypt with minimum cost factor of 10
2. THE Marketplace_System SHALL validate and sanitize all user inputs to prevent SQL injection
3. THE Marketplace_System SHALL validate and sanitize all user inputs to prevent XSS attacks
4. THE Marketplace_System SHALL implement CSRF protection on all state-changing requests
5. THE Marketplace_System SHALL use database transactions with row locking for all financial operations
6. THE Marketplace_System SHALL log all financial transactions with immutable audit trail
7. THE Marketplace_System SHALL enforce HTTPS for all connections in production environment
8. THE Marketplace_System SHALL implement rate limiting on bid placement to prevent abuse

### Requirement 15: API Readiness for Mobile Application

**User Story:** As a mobile app developer, I want RESTful APIs with authentication, so that I can build an Android application for the platform.

#### Acceptance Criteria

1. THE Marketplace_System SHALL provide RESTful API endpoints for all core functionality
2. THE Marketplace_System SHALL implement Laravel Sanctum token-based authentication for API requests
3. WHEN an API client authenticates, THE Marketplace_System SHALL issue a secure access token
4. WHEN an API request includes a valid token, THE Marketplace_System SHALL authorize the request
5. THE Marketplace_System SHALL return API responses in JSON format with consistent structure
6. THE Marketplace_System SHALL include appropriate HTTP status codes in all API responses
7. THE Marketplace_System SHALL implement API rate limiting to prevent abuse

### Requirement 16: Performance and Scalability

**User Story:** As a platform operator, I want the system to handle high traffic efficiently, so that users have a smooth experience during popular auctions.

#### Acceptance Criteria

1. WHEN processing a bid, THE Marketplace_System SHALL complete the operation within 500 milliseconds under normal load
2. WHEN 100 concurrent users view an auction, THE Marketplace_System SHALL maintain page load times under 2 seconds
3. THE Marketplace_System SHALL use database indexes on frequently queried columns including auction status, end time, and user ID
4. THE Marketplace_System SHALL implement query optimization to minimize N+1 query problems
5. WHERE real-time updates are required, THE Marketplace_System SHALL use Livewire polling with maximum 3-second intervals
6. THE Marketplace_System SHALL cache static content and frequently accessed data with appropriate TTL values

### Requirement 17: Transaction History and Reporting

**User Story:** As a user, I want to view my complete transaction history, so that I can track my financial activities on the platform.

#### Acceptance Criteria

1. WHEN a user accesses transaction history, THE Marketplace_System SHALL display all wallet transactions in chronological order
2. WHEN displaying transactions, THE Marketplace_System SHALL show transaction type, amount, date, and related auction reference
3. WHEN a user filters transactions, THE Marketplace_System SHALL support filtering by date range and transaction type
4. THE Marketplace_System SHALL allow users to export their transaction history in CSV format
5. WHEN a seller views their dashboard, THE Marketplace_System SHALL display total sales, active auctions, and completed auctions
6. WHEN a buyer views their dashboard, THE Marketplace_System SHALL display active bids, won auctions, and frozen balance

### Requirement 18: Image Management

**User Story:** As a seller, I want to upload and manage images for my auction items, so that buyers can see detailed photos of the items.

#### Acceptance Criteria

1. WHEN a seller uploads an image, THE Marketplace_System SHALL validate the file is a supported image format (JPEG, PNG, WebP)
2. WHEN a seller uploads an image, THE Marketplace_System SHALL validate the file size is under 5MB
3. WHEN an image is uploaded, THE Marketplace_System SHALL generate optimized versions for thumbnail and full-size display
4. WHEN an image is uploaded, THE Marketplace_System SHALL store the image with a unique filename to prevent conflicts
5. THE Marketplace_System SHALL allow sellers to reorder images with drag-and-drop functionality
6. THE Marketplace_System SHALL allow sellers to delete images from their auctions before auction start time
7. WHEN displaying auction images, THE Marketplace_System SHALL show the first image as the primary thumbnail

### Requirement 19: Search and Filtering

**User Story:** As a buyer, I want to search and filter auctions, so that I can find items I'm interested in bidding on.

#### Acceptance Criteria

1. WHEN a user searches for auctions, THE Marketplace_System SHALL search in item titles and descriptions
2. WHEN a user applies filters, THE Marketplace_System SHALL support filtering by auction status (active, ended, upcoming)
3. WHEN a user applies filters, THE Marketplace_System SHALL support filtering by price range
4. WHEN a user applies filters, THE Marketplace_System SHALL support filtering by category
5. WHEN search results are displayed, THE Marketplace_System SHALL show results within 1 second
6. THE Marketplace_System SHALL display search results with pagination showing 20 items per page
7. WHEN no results match the search criteria, THE Marketplace_System SHALL display a helpful message with suggestions

### Requirement 20: Automated Background Jobs

**User Story:** As the system, I want to process time-based events automatically, so that auctions progress without manual intervention.

#### Acceptance Criteria

1. THE Marketplace_System SHALL run a scheduled job every minute to check for auctions reaching start time
2. THE Marketplace_System SHALL run a scheduled job every minute to check for auctions reaching end time
3. THE Marketplace_System SHALL run a scheduled job every hour to check for expired finalization windows
4. WHEN a scheduled job processes an auction ending, THE Marketplace_System SHALL execute all ending logic within 30 seconds
5. WHEN a scheduled job processes a finalization timeout, THE Marketplace_System SHALL execute cascade logic within 30 seconds
6. THE Marketplace_System SHALL log all scheduled job executions with timestamp and results
7. IF a scheduled job fails, THEN THE Marketplace_System SHALL retry the job up to 3 times with exponential backoff

### Requirement 21: Seller Storefront (Vitrin)

**User Story:** As a seller, I want a public storefront page to showcase all my products and auctions, so that buyers can discover my offerings in one place.

#### Acceptance Criteria

1. WHEN a seller account is created, THE Marketplace_System SHALL automatically create a storefront with a unique username-based URL
2. WHEN a buyer visits /store/{username}, THE Marketplace_System SHALL display the seller's public storefront page
3. WHEN displaying a storefront, THE Marketplace_System SHALL show the seller's banner image, logo, and description
4. WHEN displaying a storefront, THE Marketplace_System SHALL list all active auctions and direct sale products from that seller
5. THE Marketplace_System SHALL allow sellers to customize their storefront banner (max 2MB, 1920x400px recommended)
6. THE Marketplace_System SHALL allow sellers to customize their storefront logo (max 1MB, 300x300px recommended)
7. THE Marketplace_System SHALL allow sellers to edit their storefront description (max 1000 characters)
8. WHEN a storefront has no active listings, THE Marketplace_System SHALL display a friendly message
9. THE Marketplace_System SHALL display seller statistics on the storefront (total sales, member since date, rating if implemented)

### Requirement 22: Listing Type Management

**User Story:** As a seller, I want to create listings with different sales methods (auction, direct sale, or hybrid), so that I can offer products through the most appropriate channel.

#### Acceptance Criteria

1. WHEN a seller creates a listing, THE Marketplace_System SHALL require selection of listing type (auction, direct_sale, or hybrid)
2. WHEN listing type is 'auction', THE Marketplace_System SHALL require base price, start time, end time, and calculate 10% deposit
3. WHEN listing type is 'direct_sale', THE Marketplace_System SHALL require fixed price and stock quantity
4. WHEN listing type is 'hybrid', THE Marketplace_System SHALL require both auction parameters and direct sale price
5. THE Marketplace_System SHALL validate that hybrid listing direct sale price is higher than auction base price
6. WHEN a direct sale item is purchased, THE Marketplace_System SHALL decrement the stock quantity
7. WHEN stock reaches zero for a direct sale item, THE Marketplace_System SHALL mark the listing as 'out_of_stock'
8. THE Marketplace_System SHALL prevent auction creation if listing type is 'direct_sale'

### Requirement 23: Direct Sale and Shopping Cart

**User Story:** As a buyer, I want to add direct sale items to a cart and checkout, so that I can purchase multiple items in a single transaction.

#### Acceptance Criteria

1. WHEN a buyer views a direct_sale listing, THE Marketplace_System SHALL display "Add to Cart" and "Buy Now" buttons
2. WHEN a buyer clicks "Add to Cart", THE Marketplace_System SHALL add the item to the session cart with selected quantity
3. WHEN a buyer clicks "Buy Now", THE Marketplace_System SHALL redirect to checkout with only that item
4. THE Marketplace_System SHALL validate that requested quantity does not exceed available stock
5. WHEN a buyer views their cart, THE Marketplace_System SHALL display all cart items with subtotal, shipping, and total
6. THE Marketplace_System SHALL allow buyers to update quantities or remove items from cart
7. WHEN a buyer proceeds to checkout, THE Marketplace_System SHALL validate stock availability for all cart items
8. WHEN checkout is completed, THE Marketplace_System SHALL create an Order record with all items and payment details
9. THE Marketplace_System SHALL deduct the total amount from buyer's wallet balance
10. THE Marketplace_System SHALL transfer payment to seller's wallet (minus platform commission if applicable)
11. THE Marketplace_System SHALL send order confirmation notification to buyer and seller

### Requirement 24: Shipping Management

**User Story:** As an admin, I want to define shipping methods and costs, so that sellers can offer delivery options to buyers.

#### Acceptance Criteria

1. THE Marketplace_System SHALL allow admins to create shipping methods with name, description, and base cost
2. THE Marketplace_System SHALL allow admins to set shipping methods as active or inactive
3. WHEN a seller creates a listing, THE Marketplace_System SHALL allow selection of applicable shipping methods
4. THE Marketplace_System SHALL allow sellers to add custom shipping cost adjustments per listing
5. WHEN a buyer views a listing, THE Marketplace_System SHALL display available shipping methods with costs
6. WHEN a buyer adds an item to cart, THE Marketplace_System SHALL calculate shipping cost based on selected method
7. WHEN checkout includes items from multiple sellers, THE Marketplace_System SHALL calculate shipping per seller
8. THE Marketplace_System SHALL support common Iranian shipping methods (Pishtaz, Tipax, Peyk, Chapar, etc.)
9. WHEN an order is placed, THE Marketplace_System SHALL record the selected shipping method and cost
10. THE Marketplace_System SHALL allow sellers to update shipping tracking information for orders

### Requirement 25: Order Management

**User Story:** As a buyer, I want to view my order history and track order status, so that I can monitor my purchases.

#### Acceptance Criteria

1. WHEN a buyer completes a purchase, THE Marketplace_System SHALL create an Order with status 'pending'
2. THE Marketplace_System SHALL assign a unique order number to each order
3. WHEN a seller views their orders, THE Marketplace_System SHALL display all orders with status, buyer info, and items
4. THE Marketplace_System SHALL allow sellers to update order status (pending, processing, shipped, delivered, cancelled)
5. WHEN order status changes, THE Marketplace_System SHALL notify the buyer
6. WHEN a buyer views order details, THE Marketplace_System SHALL display items, quantities, prices, shipping info, and status
7. THE Marketplace_System SHALL allow buyers to cancel orders within 1 hour of placement if status is 'pending'
8. WHEN an order is cancelled, THE Marketplace_System SHALL refund the buyer's wallet and restore product stock
9. THE Marketplace_System SHALL maintain order history for at least 2 years

### Requirement 26: Unified Listing Display

**User Story:** As a buyer, I want to see appropriate UI elements based on listing type, so that I can interact with auctions and direct sales correctly.

#### Acceptance Criteria

1. WHEN a buyer views an auction listing, THE Marketplace_System SHALL display bidding interface with deposit requirement
2. WHEN a buyer views a direct sale listing, THE Marketplace_System SHALL display purchase interface with quantity selector
3. WHEN a buyer views a hybrid listing, THE Marketplace_System SHALL display both bidding and purchase options
4. THE Marketplace_System SHALL clearly indicate the listing type with visual badges or labels
5. WHEN displaying search results, THE Marketplace_System SHALL show appropriate price information based on type
6. THE Marketplace_System SHALL allow filtering by listing type (show only auctions, only direct sales, or both)
7. WHEN a listing transitions from auction to completed, THE Marketplace_System SHALL hide bidding interface

### Requirement 27: Inventory Management

**User Story:** As a seller, I want to manage product inventory, so that I can track stock levels and prevent overselling.

#### Acceptance Criteria

1. WHEN a seller creates a direct sale listing, THE Marketplace_System SHALL require initial stock quantity
2. THE Marketplace_System SHALL allow sellers to update stock quantity from their dashboard
3. WHEN stock is updated, THE Marketplace_System SHALL log the change with timestamp and reason
4. WHEN a purchase is completed, THE Marketplace_System SHALL automatically decrement stock
5. WHEN stock reaches zero, THE Marketplace_System SHALL mark the listing as 'out_of_stock' and hide purchase buttons
6. THE Marketplace_System SHALL allow sellers to set low stock alerts (e.g., notify when stock < 5)
7. WHEN stock is low, THE Marketplace_System SHALL notify the seller
8. THE Marketplace_System SHALL prevent negative stock values

### Requirement 28: Storefront Customization

**User Story:** As a seller, I want to customize my storefront appearance, so that I can build my brand identity.

#### Acceptance Criteria

1. THE Marketplace_System SHALL provide a storefront settings page for sellers
2. THE Marketplace_System SHALL allow sellers to upload a banner image with preview before saving
3. THE Marketplace_System SHALL allow sellers to upload a logo image with preview before saving
4. THE Marketplace_System SHALL validate banner dimensions and file size (max 2MB)
5. THE Marketplace_System SHALL validate logo dimensions and file size (max 1MB)
6. THE Marketplace_System SHALL allow sellers to write a storefront description with rich text formatting
7. THE Marketplace_System SHALL allow sellers to set a custom storefront slug (username-based, unique)
8. THE Marketplace_System SHALL display a preview of the storefront before publishing changes
9. THE Marketplace_System SHALL optimize uploaded images for web display

### Requirement 29: Multi-Vendor Search and Discovery

**User Story:** As a buyer, I want to search across all sellers and filter by various criteria, so that I can find products from any vendor.

#### Acceptance Criteria

1. WHEN a buyer searches, THE Marketplace_System SHALL search across all active listings from all sellers
2. THE Marketplace_System SHALL allow filtering by listing type (auction, direct_sale, hybrid)
3. THE Marketplace_System SHALL allow filtering by seller/storefront
4. THE Marketplace_System SHALL allow filtering by price range (for both auction base price and direct sale price)
5. THE Marketplace_System SHALL allow sorting by price (low to high, high to low)
6. THE Marketplace_System SHALL allow sorting by date (newest first, ending soon for auctions)
7. WHEN displaying search results, THE Marketplace_System SHALL show seller name and storefront link
8. THE Marketplace_System SHALL highlight featured or promoted listings if implemented

### Requirement 30: Payment Integration for Mixed Transactions

**User Story:** As a buyer, I want to complete payments for both auction wins and direct purchases using my wallet, so that I have a unified payment experience.

#### Acceptance Criteria

1. WHEN a buyer wins an auction, THE Marketplace_System SHALL calculate total as (winning bid - frozen deposit) + shipping cost
2. WHEN a buyer purchases direct sale items, THE Marketplace_System SHALL calculate total as (item price × quantity) + shipping cost
3. THE Marketplace_System SHALL verify sufficient wallet balance before completing any transaction
4. WHEN payment is completed, THE Marketplace_System SHALL create appropriate transaction records
5. THE Marketplace_System SHALL transfer funds to seller's wallet after deducting platform fees (if applicable)
6. THE Marketplace_System SHALL support split payments for cart items from multiple sellers
7. WHEN a transaction fails, THE Marketplace_System SHALL rollback all changes and notify the buyer

