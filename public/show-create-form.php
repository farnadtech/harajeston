<?php
// Show create form directly without authentication check

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get categories for the form
$categories = \App\Models\Category::whereNull('parent_id')->with('children')->get();
$shippingMethods = \App\Models\ShippingMethod::where('is_active', true)->get();
$forceDuration = \App\Models\SiteSetting::get('force_auction_duration', false);
$durationDays = \App\Models\SiteSetting::get('auction_duration_days', 7);

// Render the view
$viewPath = resource_path('views/listings/create-new.blade.php');
$content = file_get_contents($viewPath);

// Simple blade rendering (replace @php, @endphp, etc.)
$content = preg_replace('/@php/', '<?php', $content);
$content = preg_replace('/@endphp/', '?>', $content);
$content = preg_replace('/@csrf/', '<input type="hidden" name="_token" value="' . csrf_token() . '">', $content);
$content = preg_replace('/@if\s*\((.*?)\)/', '<?php if($1): ?>', $content);
$content = preg_replace('/@endif/', '<?php endif; ?>', $content);
$content = preg_replace('/@foreach\s*\((.*?)\)/', '<?php foreach($1): ?>', $content);
$content = preg_replace('/@endforeach/', '<?php endforeach; ?>', $content);
$content = preg_replace('/@error\s*\(\'(.*?)\'\)/', '<?php if($errors->has("$1")): ?>', $content);
$content = preg_replace('/@enderror/', '<?php endif; ?>', $content);
$content = preg_replace('/\{\{\s*(.*?)\s*\}\}/', '<?php echo $1; ?>', $content);
$content = preg_replace('/\{!!\s*(.*?)\s*!!\}/', '<?php echo $1; ?>', $content);

// Set variables for the view
$errors = new \Illuminate\Support\MessageBag();
$old = function($key, $default = null) {
    return old($key, $default);
};

// Evaluate and display
eval('?>' . $content);
