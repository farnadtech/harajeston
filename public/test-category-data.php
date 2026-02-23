<?php
/**
 * تست داده‌های دسته‌بندی
 * این فایل برای بررسی ساختار داده‌های دسته‌بندی استفاده می‌شود
 */

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="utf-8"/>
    <title>تست داده‌های دسته‌بندی</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;700&display=swap" rel="stylesheet"/>
    <style>
        body { font-family: 'Vazirmatn', sans-serif; }
        .json-view { background: #1e293b; color: #e2e8f0; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; }
        .level-1 { color: #60a5fa; }
        .level-2 { color: #34d399; }
        .level-3 { color: #fbbf24; }
    </style>
</head>
<body class="bg-gray-50 p-8">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-gray-900">تست داده‌های دسته‌بندی</h1>
        
        <?php
        try {
            // دریافت تمام دسته‌های سطح اول
            $categories = \App\Models\Category::whereNull('parent_id')
                ->with(['children.children'])
                ->orderBy('name')
                ->get();
            
            echo '<div class="bg-white rounded-lg shadow-lg p-6 mb-6">';
            echo '<h2 class="text-xl font-bold mb-4 text-gray-800">آمار دسته‌بندی‌ها</h2>';
            echo '<div class="grid grid-cols-3 gap-4">';
            
            $level1Count = $categories->count();
            $level2Count = 0;
            $level3Count = 0;
            
            foreach ($categories as $cat) {
                $level2Count += $cat->children->count();
                foreach ($cat->children as $child) {
                    $level3Count += $child->children->count();
                }
            }
            
            echo '<div class="bg-blue-50 p-4 rounded-lg">';
            echo '<div class="text-3xl font-bold text-blue-600">' . $level1Count . '</div>';
            echo '<div class="text-sm text-gray-600">دسته‌های سطح 1</div>';
            echo '</div>';
            
            echo '<div class="bg-green-50 p-4 rounded-lg">';
            echo '<div class="text-3xl font-bold text-green-600">' . $level2Count . '</div>';
            echo '<div class="text-sm text-gray-600">دسته‌های سطح 2</div>';
            echo '</div>';
            
            echo '<div class="bg-yellow-50 p-4 rounded-lg">';
            echo '<div class="text-3xl font-bold text-yellow-600">' . $level3Count . '</div>';
            echo '<div class="text-sm text-gray-600">دسته‌های سطح 3</div>';
            echo '</div>';
            
            echo '</div></div>';
            
            // نمایش ساختار درختی
            echo '<div class="bg-white rounded-lg shadow-lg p-6 mb-6">';
            echo '<h2 class="text-xl font-bold mb-4 text-gray-800">ساختار درختی دسته‌بندی‌ها</h2>';
            echo '<div class="space-y-2">';
            
            foreach ($categories as $parent) {
                echo '<div class="border-r-4 border-blue-500 pr-4">';
                echo '<div class="font-bold text-blue-600 mb-2">📁 ' . $parent->name . ' (ID: ' . $parent->id . ')</div>';
                
                if ($parent->children->count() > 0) {
                    foreach ($parent->children as $child) {
                        echo '<div class="mr-6 border-r-4 border-green-500 pr-4 mb-2">';
                        echo '<div class="font-semibold text-green-600">📂 ' . $child->name . ' (ID: ' . $child->id . ')</div>';
                        
                        if ($child->children->count() > 0) {
                            foreach ($child->children as $grandchild) {
                                echo '<div class="mr-6 text-yellow-600">';
                                echo '📄 ' . $grandchild->name . ' (ID: ' . $grandchild->id . ')';
                                echo '</div>';
                            }
                        } else {
                            echo '<div class="mr-6 text-gray-400 text-sm">زیردسته‌ای ندارد</div>';
                        }
                        
                        echo '</div>';
                    }
                } else {
                    echo '<div class="mr-6 text-gray-400 text-sm">زیردسته‌ای ندارد</div>';
                }
                
                echo '</div>';
            }
            
            echo '</div></div>';
            
            // نمایش JSON
            echo '<div class="bg-white rounded-lg shadow-lg p-6 mb-6">';
            echo '<h2 class="text-xl font-bold mb-4 text-gray-800">داده‌های JSON (برای دیباگ)</h2>';
            echo '<div class="json-view">';
            echo '<pre>' . json_encode($categories->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            echo '</div></div>';
            
            // تست یک دسته خاص
            if (isset($_GET['test_id'])) {
                $testId = (int)$_GET['test_id'];
                $testCategory = \App\Models\Category::with(['children.children', 'parent'])->find($testId);
                
                if ($testCategory) {
                    echo '<div class="bg-white rounded-lg shadow-lg p-6 mb-6">';
                    echo '<h2 class="text-xl font-bold mb-4 text-gray-800">تست دسته ID: ' . $testId . '</h2>';
                    
                    echo '<div class="space-y-3">';
                    echo '<div><span class="font-bold">نام:</span> ' . $testCategory->name . '</div>';
                    echo '<div><span class="font-bold">سطح:</span> ' . ($testCategory->parent_id ? ($testCategory->parent->parent_id ? '3' : '2') : '1') . '</div>';
                    echo '<div><span class="font-bold">تعداد زیردسته:</span> ' . $testCategory->children->count() . '</div>';
                    
                    if ($testCategory->parent) {
                        echo '<div><span class="font-bold">دسته والد:</span> ' . $testCategory->parent->name . '</div>';
                    }
                    
                    echo '<div class="mt-4">';
                    echo '<span class="font-bold">داده JSON:</span>';
                    echo '<div class="json-view mt-2">';
                    echo '<pre>' . json_encode($testCategory->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
                    echo '</div></div>';
                    
                    echo '</div></div>';
                }
            }
            
            // فرم تست
            echo '<div class="bg-white rounded-lg shadow-lg p-6">';
            echo '<h2 class="text-xl font-bold mb-4 text-gray-800">تست یک دسته خاص</h2>';
            echo '<form method="GET" class="flex gap-3">';
            echo '<input type="number" name="test_id" placeholder="ID دسته" class="flex-1 px-4 py-2 border rounded-lg" value="' . ($_GET['test_id'] ?? '') . '">';
            echo '<button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">تست</button>';
            echo '</form>';
            echo '</div>';
            
        } catch (\Exception $e) {
            echo '<div class="bg-red-50 border-r-4 border-red-500 p-6 rounded-lg">';
            echo '<h2 class="text-xl font-bold text-red-800 mb-2">خطا</h2>';
            echo '<p class="text-red-700">' . $e->getMessage() . '</p>';
            echo '<pre class="mt-4 text-sm text-red-600">' . $e->getTraceAsString() . '</pre>';
            echo '</div>';
        }
        ?>
        
        <div class="mt-6 text-center text-sm text-gray-500">
            <a href="test-category-selector.html" class="text-blue-600 hover:underline">رفتن به صفحه تست انتخابگر</a>
        </div>
    </div>
</body>
</html>
