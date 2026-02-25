<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $updated = 0;
    
    // گرفتن تمام نوتیفیکیشن‌ها با لینک listing
    $notifications = DB::table('notifications')
        ->where(function($q) {
            $q->where('link', 'like', '%/listings/%')
              ->orWhere('link', 'like', '%/admin/listings/%');
        })
        ->get();
    
    foreach ($notifications as $notification) {
        // استخراج ID از لینک
        if (preg_match('/listings\/(\d+)/', $notification->link, $matches)) {
            $listingId = $matches[1];
            
            // پیدا کردن slug
            $listing = DB::table('listings')->where('id', $listingId)->first();
            
            if ($listing && $listing->slug) {
                // گرفتن نقش کاربر
                $user = DB::table('users')->where('id', $notification->user_id)->first();
                
                // ساخت لینک جدید
                if ($user && $user->role === 'admin') {
                    $newLink = url("/admin/listings/{$listing->slug}");
                } else {
                    $newLink = url("/listings/{$listing->slug}");
                }
                
                DB::table('notifications')
                    ->where('id', $notification->id)
                    ->update(['link' => $newLink]);
                
                $updated++;
            }
        }
    }
    
    echo "✓ تعداد {$updated} نوتیفیکیشن به slug تبدیل شد";
} catch (Exception $e) {
    echo "خطا: " . $e->getMessage();
}
