# Shipping Cost Null Value Fix

## Problem
When editing a listing and selecting a shipping method without entering a custom cost, the form was sending `null` for `shipping_costs[method_id]`, which caused a database error:
```
SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'custom_cost_adjustment' cannot be null
```

## Root Causes
1. The form input was sending `null` when empty
2. The view was trying to access wrong pivot column (`pivot->cost` instead of `pivot->custom_cost_adjustment`)
3. The service was trying to use base cost as fallback, but that approach was flawed

## Solution

### 1. Fixed ListingService (app/Services/ListingService.php)
Changed the shipping method update logic to use `0` instead of `null` when no custom cost is provided:

```php
// Update shipping methods
if (isset($data['shipping_methods']) && is_array($data['shipping_methods'])) {
    $listing->shippingMethods()->detach();
    
    foreach ($data['shipping_methods'] as $methodId) {
        $customCost = $data['shipping_costs'][$methodId] ?? null;
        
        // If no custom cost provided, use 0 (will use base cost in display)
        if ($customCost === null || $customCost === '') {
            $customCost = 0;
        }
        
        $listing->shippingMethods()->attach($methodId, [
            'custom_cost_adjustment' => $customCost
        ]);
    }
}
```

### 2. Fixed Edit View (resources/views/listings/edit.blade.php)
- Changed `pivot->cost` to `pivot->custom_cost_adjustment` (correct column name)
- Updated input placeholder to indicate it's optional
- Changed value display to show empty string when cost is 0

```php
@php
    $isSelected = $listing->shippingMethods->contains($method->id);
    $customCost = $isSelected ? $listing->shippingMethods->find($method->id)->pivot->custom_cost_adjustment : '';
@endphp
```

## How It Works Now

1. User selects a shipping method checkbox
2. User can optionally enter a custom cost
3. If no custom cost is entered:
   - Form sends `null` or empty string
   - Service converts it to `0`
   - Database stores `0` in `custom_cost_adjustment`
4. When displaying:
   - Final cost = base_cost + custom_cost_adjustment
   - If adjustment is 0, it shows base cost
   - If adjustment is set, it shows base + adjustment

## Database Schema
The `listing_shipping` table has:
```php
$table->decimal('custom_cost_adjustment', 10, 2)->default(0.00);
```

The column has a default of 0.00, but when explicitly passing `null` in attach(), it tries to insert null and fails. By converting null to 0 in the service, we avoid this issue.

## Testing
Created test script: `public/test-shipping-fix.php`

Results:
- Null value → 0 ✓
- Empty string → 0 ✓
- Valid value → preserved ✓
- Listing 19 shipping methods display correctly ✓

## Files Modified
1. `app/Services/ListingService.php` - Fixed null handling in updateListing()
2. `resources/views/listings/edit.blade.php` - Fixed pivot column name and display
3. `public/test-shipping-fix.php` - Created test script

## Status
✅ FIXED - Users can now edit listings and leave shipping costs empty without errors
