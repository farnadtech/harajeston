<?php
/**
 * Test Notification Routes
 * 
 * این فایل برای تست روت‌های نوتیفیکیشن است
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "<!DOCTYPE html>";
echo "<html dir='rtl' lang='fa'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>تست روت‌های نوتیفیکیشن</title>";
echo "<script src='https://cdn.tailwindcss.com'></script>";
echo "</head>";
echo "<body class='bg-gray-50 p-8'>";

echo "<div class='max-w-4xl mx-auto'>";
echo "<h1 class='text-3xl font-bold text-gray-900 mb-6'>تست روت‌های نوتیفیکیشن</h1>";

try {
    // بررسی روت‌ها
    $routes = [
        'user.notifications.index' => 'GET /notifications',
        'user.notifications.recent' => 'GET /notifications/recent',
        'user.notifications.read' => 'GET /notifications/{id}/read',
        'user.notifications.mark-all-read' => 'POST /notifications/mark-all-read',
        'admin.notifications.index' => 'GET /admin/notifications',
        'admin.notifications.recent' => 'GET /admin/notifications/recent',
        'admin.notifications.read' => 'GET /admin/notifications/{id}/read',
        'admin.notifications.mark-all-read' => 'POST /admin/notifications/mark-all-read',
    ];
    
    echo "<div class='bg-white rounded-lg shadow p-6 mb-6'>";
    echo "<h2 class='text-xl font-bold mb-4'>روت‌های تعریف شده</h2>";
    echo "<div class='space-y-2'>";
    
    foreach ($routes as $name => $path) {
        try {
            $url = route($name, ['id' => 'test-id']);
            $status = "<span class='text-green-600'>✓</span>";
        } catch (Exception $e) {
            $url = "خطا: " . $e->getMessage();
            $status = "<span class='text-red-600'>✗</span>";
        }
        
        echo "<div class='flex items-center gap-3 p-3 bg-gray-50 rounded'>";
        echo "<div class='w-6'>{$status}</div>";
        echo "<div class='flex-1'>";
        echo "<div class='font-bold text-sm'>{$name}</div>";
        echo "<div class='text-xs text-gray-600'>{$path}</div>";
        echo "<div class='text-xs text-blue-600 mt-1'>{$url}</div>";
        echo "</div>";
        echo "</div>";
    }
    
    echo "</div>";
    echo "</div>";
    
    // تست با نوتیفیکیشن واقعی
    $notification = \App\Models\Notification::first();
    
    if ($notification) {
        echo "<div class='bg-white rounded-lg shadow p-6 mb-6'>";
        echo "<h2 class='text-xl font-bold mb-4'>تست با نوتیفیکیشن واقعی</h2>";
        
        echo "<div class='mb-4'>";
        echo "<p><strong>ID:</strong> {$notification->id}</p>";
        echo "<p><strong>عنوان:</strong> {$notification->title}</p>";
        echo "<p><strong>وضعیت:</strong> " . ($notification->is_read ? 'خوانده شده' : 'خوانده نشده') . "</p>";
        echo "</div>";
        
        $userReadUrl = route('user.notifications.read', $notification->id);
        $adminReadUrl = route('admin.notifications.read', $notification->id);
        
        echo "<div class='space-y-2'>";
        echo "<div class='p-3 bg-blue-50 rounded'>";
        echo "<p class='text-sm font-bold mb-1'>User Read URL:</p>";
        echo "<a href='{$userReadUrl}' class='text-xs text-blue-600 hover:underline'>{$userReadUrl}</a>";
        echo "</div>";
        
        echo "<div class='p-3 bg-purple-50 rounded'>";
        echo "<p class='text-sm font-bold mb-1'>Admin Read URL:</p>";
        echo "<a href='{$adminReadUrl}' class='text-xs text-purple-600 hover:underline'>{$adminReadUrl}</a>";
        echo "</div>";
        echo "</div>";
        
        echo "</div>";
    } else {
        echo "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4'>";
        echo "هیچ نوتیفیکیشنی در دیتابیس یافت نشد";
        echo "</div>";
    }
    
    // راهنما
    echo "<div class='bg-green-50 border border-green-200 rounded-lg p-6'>";
    echo "<h2 class='text-xl font-bold mb-4'>✓ مشکل برطرف شد</h2>";
    echo "<p class='text-gray-700 mb-4'>روت‌های نوتیفیکیشن از POST به GET تغییر کردند و حالا با لینک‌های معمولی کار می‌کنند.</p>";
    echo "<div class='space-y-2 text-sm text-gray-600'>";
    echo "<p><strong>قبل:</strong> POST /notifications/{id}/read (نیاز به فرم)</p>";
    echo "<p><strong>بعد:</strong> GET /notifications/{id}/read (کار با لینک معمولی)</p>";
    echo "</div>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>";
    echo "<strong>خطا:</strong> " . $e->getMessage();
    echo "<pre class='mt-2 text-xs'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "</div>";
echo "</body>";
echo "</html>";
