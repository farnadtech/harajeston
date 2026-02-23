# ساختار دیتابیس

## جداول اصلی

### 1. users
```sql
- id (bigint, PK)
- name (string)
- email (string, unique)
- password (string)
- role (enum: admin, seller, buyer)
- phone (string, nullable)
- national_id (string, nullable)
- avatar (string, nullable)
- is_verified (boolean, default: false)
- seller_status (enum: pending, approved, rejected, nullable)
- seller_request_date (timestamp, nullable)
- seller_rejection_reason (text, nullable)
- store_name (string, nullable)
- store_slug (string, nullable, unique)
- store_description (text, nullable)
- store_logo (string, nullable)
- store_banner (string, nullable)
- timestamps
```

### 2. listings
```sql
- id (bigint, PK)
- seller_id (bigint, FK -> users)
- category_id (bigint, FK -> categories)
- title (string)
- slug (string, unique)
- description (text)
- condition (enum: new, like_new, used)
- starting_price (decimal)
- current_price (decimal)
- buy_now_price (decimal, nullable)
- deposit_amount (decimal, default: 0)
- bid_increment (decimal, default: 10000)
- starts_at (timestamp)
- ends_at (timestamp)
- status (enum: pending, active, ended, sold, cancelled, suspended)
- auto_extend (boolean, default: false)
- show_before_start (boolean, default: true)
- winner_id (bigint, FK -> users, nullable)
- view_count (integer, default: 0)
- timestamps
- soft_deletes
```

### 3. categories
```sql
- id (bigint, PK)
- parent_id (bigint, FK -> categories, nullable)
- name (string)
- slug (string, unique)
- description (text, nullable)
- icon (string, nullable)
- image (string, nullable)
- is_active (boolean, default: true)
- sort_order (integer, default: 0)
- timestamps
```

### 4. category_attributes
```sql
- id (bigint, PK)
- category_id (bigint, FK -> categories)
- name (string)
- type (enum: text, number, select)
- options (json, nullable)
- is_required (boolean, default: false)
- is_filterable (boolean, default: true)
- sort_order (integer, default: 0)
- is_active (boolean, default: true)
- timestamps
```

### 5. listing_attributes
```sql
- id (bigint, PK)
- listing_id (bigint, FK -> listings)
- attribute_id (bigint, FK -> category_attributes)
- value (text)
- timestamps
```



### 6. bids
```sql
- id (bigint, PK)
- listing_id (bigint, FK -> listings)
- user_id (bigint, FK -> users)
- amount (decimal)
- is_auto (boolean, default: false)
- max_amount (decimal, nullable)
- status (enum: active, outbid, won, lost, refunded)
- timestamps
```

### 7. listing_images
```sql
- id (bigint, PK)
- listing_id (bigint, FK -> listings)
- image_path (string)
- is_primary (boolean, default: false)
- sort_order (integer, default: 0)
- timestamps
```

### 8. wallets
```sql
- id (bigint, PK)
- user_id (bigint, FK -> users, unique)
- balance (decimal, default: 0)
- timestamps
```

### 9. wallet_transactions
```sql
- id (bigint, PK)
- wallet_id (bigint, FK -> wallets)
- type (enum: deposit, withdrawal, bid_deposit, bid_refund, purchase, sale, commission)
- amount (decimal)
- balance_after (decimal)
- description (text, nullable)
- reference_type (string, nullable)
- reference_id (bigint, nullable)
- timestamps
```

### 10. orders
```sql
- id (bigint, PK)
- buyer_id (bigint, FK -> users)
- seller_id (bigint, FK -> users)
- listing_id (bigint, FK -> listings)
- order_number (string, unique)
- total_amount (decimal)
- shipping_method_id (bigint, FK -> shipping_methods)
- shipping_cost (decimal)
- commission_amount (decimal)
- commission_rate (decimal)
- status (enum: pending, paid, processing, shipped, delivered, cancelled, refunded)
- payment_method (string)
- shipping_address (json)
- tracking_number (string, nullable)
- notes (text, nullable)
- timestamps
```

### 11. shipping_methods
```sql
- id (bigint, PK)
- name (string)
- description (text, nullable)
- base_cost (decimal)
- estimated_days (integer, nullable)
- is_active (boolean, default: true)
- timestamps
```

### 12. listing_shipping_methods
```sql
- id (bigint, PK)
- listing_id (bigint, FK -> listings)
- shipping_method_id (bigint, FK -> shipping_methods)
- custom_cost (decimal, nullable)
- timestamps
```



### 13. listing_comments
```sql
- id (bigint, PK)
- listing_id (bigint, FK -> listings)
- user_id (bigint, FK -> users)
- parent_id (bigint, FK -> listing_comments, nullable)
- comment (text)
- is_approved (boolean, default: false)
- timestamps
- soft_deletes
```

### 14. seller_reviews
```sql
- id (bigint, PK)
- seller_id (bigint, FK -> users)
- buyer_id (bigint, FK -> users)
- order_id (bigint, FK -> orders)
- rating (integer, 1-5)
- comment (text, nullable)
- timestamps
```

### 15. site_settings
```sql
- id (bigint, PK)
- key (string, unique)
- value (text, nullable)
- type (enum: string, integer, boolean, json)
- timestamps
```

### 16. notifications
```sql
- id (bigint, PK)
- user_id (bigint, FK -> users)
- type (string)
- title (string)
- message (text)
- data (json, nullable)
- read_at (timestamp, nullable)
- timestamps
```

### 17. auction_participants
```sql
- id (bigint, PK)
- listing_id (bigint, FK -> listings)
- user_id (bigint, FK -> users)
- deposit_paid (boolean, default: false)
- deposit_amount (decimal)
- deposit_refunded (boolean, default: false)
- timestamps
```

## روابط (Relationships)

### User Relations
- hasMany: listings (as seller)
- hasMany: bids
- hasMany: orders (as buyer)
- hasMany: orders (as seller)
- hasOne: wallet
- hasMany: notifications
- hasMany: listing_comments
- hasMany: seller_reviews (as seller)
- hasMany: seller_reviews (as buyer)

### Listing Relations
- belongsTo: user (seller)
- belongsTo: category
- hasMany: bids
- hasMany: listing_images
- hasMany: listing_attributes
- hasMany: listing_comments
- belongsToMany: shipping_methods
- hasMany: auction_participants
- hasOne: order

### Category Relations
- belongsTo: parent (self)
- hasMany: children (self)
- hasMany: listings
- hasMany: category_attributes

