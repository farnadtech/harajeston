<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\Notification;

try {
    $notifications = Notification::whereNotNull('link')
        ->where('link', 'like', '%admin/listings/%')
        ->get();

    $updated = 0;

    foreach ($notifications as $notification) {
        $user = $notification->user;
        
        if (!$user) continue;
        
        // اگر کاربر ادمین نیست، لینک رو تغییر بده
        if ($user->role !== 'admin') {
            $oldLink = $notification->link;
            
            // استخراج listing ID از لینک
            if (preg_match('/listings\/(\d+)/', $oldLink, $matches)) {
                $listingId = $matches[1];
                
                try {
                    $newLink = url("/listings/{$listingId}");
                    
                    $notification->link = $newLink;
                    $notification->save();
                    
                    $updated++;
                } catch (Exception $e) {
                    // Skip this notification
                }
            }
        }
    }

    echo "✓ تعداد {$updated} نوتیفیکیشن به‌روز شد";
} catch (Exception $e) {
    echo "خطا: " . $e->getMessage();
}
