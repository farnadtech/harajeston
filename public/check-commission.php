<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$listing = \App\Models\Listing::find(19);
echo "Listing ID: " . $listing->id . "\n";
echo "Category ID: " . $listing->category_id . "\n";

$category = \App\Models\Category::find($listing->category_id);
echo "Category: " . $category->name . "\n";
echo "Parent ID: " . $category->parent_id . "\n";

// Check commission settings
$commissionType = \App\Models\SiteSetting::get('commission_type', 'percentage');
echo "\nCommission Type: " . $commissionType . "\n";

if ($commissionType === 'category') {
    $categoryCommission = \App\Models\CategoryCommission::where('category_id', $listing->category_id)->first();
    if ($categoryCommission) {
        echo "Category Commission Type: " . $categoryCommission->type . "\n";
        echo "Category Commission Percentage: " . $categoryCommission->percentage . "\n";
    } else {
        echo "No category commission found for category " . $listing->category_id . "\n";
        
        // Check parent
        if ($category->parent_id) {
            $parentCommission = \App\Models\CategoryCommission::where('category_id', $category->parent_id)->first();
            if ($parentCommission) {
                echo "Parent Category Commission Type: " . $parentCommission->type . "\n";
                echo "Parent Category Commission Percentage: " . $parentCommission->percentage . "\n";
            }
        }
    }
} else {
    $percentage = \App\Models\SiteSetting::get('commission_percentage', 5);
    echo "Commission Percentage: " . $percentage . "%\n";
}

// Calculate commission
$commissionService = app(\App\Services\CommissionService::class);
$commission = $commissionService->calculateCommission(40000, $listing->category_id);
echo "\nCalculated Commission for 40000: " . $commission . "\n";
echo "Expected (5%): " . (40000 * 0.05) . "\n";
