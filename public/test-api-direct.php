<?php
// تست مستقیم API بدون routing

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

header('Content-Type: application/json; charset=utf-8');

// دریافت category ID از query string
$categoryId = $_GET['category_id'] ?? 58;

echo "=== تست API ویژگی‌های دسته $categoryId ===\n\n";

try {
    // اتصال به دیتابیس
    $category = \App\Models\Category::find($categoryId);
    
    if (!$category) {
        echo json_encode([
            'error' => 'دسته‌بندی پیدا نشد',
            'category_id' => $categoryId
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    echo "✅ دسته پیدا شد: {$category->name}\n\n";
    
    // دریافت ویژگی‌ها
    $attributes = $category->attributes()
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->get();
    
    echo "📊 تعداد ویژگی‌ها: " . $attributes->count() . "\n\n";
    
    // اگر ویژگی نداره، از والد بگیر
    if ($attributes->isEmpty() && $category->parent) {
        echo "⚠️ این دسته ویژگی نداره، از والد می‌گیریم...\n";
        $attributes = $category->parent->attributes()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        echo "📊 تعداد ویژگی‌های والد: " . $attributes->count() . "\n\n";
    }
    
    $result = [
        'success' => true,
        'category' => [
            'id' => $category->id,
            'name' => $category->name,
            'parent_id' => $category->parent_id
        ],
        'attributes' => $attributes->map(function ($attr) {
            return [
                'id' => $attr->id,
                'name' => $attr->name,
                'type' => $attr->type,
                'options' => $attr->options,
                'is_required' => $attr->is_required,
            ];
        })
    ];
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (\Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
