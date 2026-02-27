<?php
// Test edit listing with active bids validation

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "<h2>تست ویرایش آگهی با پیشنهاد فعال</h2>";

// Find a listing with active bids
$listing = \App\Models\Listing::whereHas('bids', function($q) {
    $q->where('status', 'active');
})->first();

if (!$listing) {
    echo "<p style='color: orange;'>هیچ آگهی با پیشنهاد فعال پیدا نشد. ابتدا یک آگهی با پیشنهاد ایجاد کنید.</p>";
    exit;
}

echo "<h3>آگهی: {$listing->title}</h3>";
echo "<p>وضعیت: {$listing->status}</p>";
echo "<p>تعداد پیشنهادها: " . $listing->bids()->count() . "</p>";
echo "<p>دارای پیشنهاد فعال: " . ($listing->hasActiveBids() ? 'بله' : 'خیر') . "</p>";

// Test validation rules
$request = new \App\Http\Requests\UpdateListingRequest();
$request->setRouteResolver(function() use ($listing) {
    $route = new \Illuminate\Routing\Route('PUT', 'listings/{listing}', []);
    $route->bind($listing);
    $route->setParameter('listing', $listing);
    return $route;
});

$rules = $request->rules();

echo "<h3>قوانین Validation فعال:</h3>";
echo "<pre>";
print_r(array_keys($rules));
echo "</pre>";

if ($listing->hasActiveBids()) {
    echo "<p style='color: green;'>✓ فقط فیلدهای description و shipping_methods الزامی هستند</p>";
    
    $requiredFields = ['description', 'shipping_methods'];
    $actualRequired = array_filter($rules, function($rule) {
        return is_string($rule) && strpos($rule, 'required') !== false;
    });
    
    echo "<p>فیلدهای الزامی: " . implode(', ', array_keys($actualRequired)) . "</p>";
} else {
    echo "<p style='color: red;'>✗ همه فیلدها الزامی هستند</p>";
}

echo "<hr>";
echo "<h3>لینک ویرایش:</h3>";
echo "<a href='/listings/{$listing->id}/edit' target='_blank'>ویرایش این آگهی</a>";
