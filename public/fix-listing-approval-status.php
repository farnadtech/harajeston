<?php
/**
 * Fix listing approval status
 * 
 * این اسکریپت آگهی‌هایی که بدون approved_at ساخته شده‌اند ولی
 * نیازی به تایید ادمین ندارند را درست می‌کند.
 * 
 * فقط یک بار اجرا کنید.
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Listing;
use App\Models\SiteSetting;

$requiresApproval = SiteSetting::get('require_listing_approval', false);

echo "<h2>Fix Listing Approval Status</h2>";
echo "<p>require_listing_approval = " . ($requiresApproval ? 'true' : 'false') . "</p>";

if ($requiresApproval) {
    echo "<p style='color:orange'>تایید اگهی فعال است. فقط آگهی‌هایی که توسط ادمین ساخته شده‌اند fix می‌شوند.</p>";
    
    // آگهی‌هایی که توسط ادمین ساخته شده‌اند (seller_id != seller role) و approved_at ندارند
    // این‌ها باید approved_at داشته باشند
    $fixed = 0;
    // در این حالت نیازی به fix نیست چون approval فعاله
    echo "<p>نیازی به fix نیست.</p>";
} else {
    echo "<p style='color:green'>تایید اگهی غیرفعال است. همه آگهی‌های pending که approved_at ندارند fix می‌شوند.</p>";
    
    // همه آگهی‌های pending که approved_at ندارند باید auto-approve بشن
    $listings = Listing::where('status', 'pending')
        ->whereNull('approved_at')
        ->get();
    
    echo "<p>تعداد آگهی‌های نیاز به fix: " . $listings->count() . "</p>";
    
    $fixed = 0;
    foreach ($listings as $listing) {
        $listing->update([
            'approved_at' => $listing->created_at, // از زمان ایجاد تایید شده
        ]);
        $fixed++;
        echo "<p>✅ آگهی #{$listing->id} - {$listing->title} - fix شد</p>";
    }
    
    echo "<hr><p style='color:green;font-weight:bold'>✅ {$fixed} آگهی fix شد.</p>";
}

echo "<p><a href='/admin/listings'>برگشت به پنل ادمین</a></p>";
