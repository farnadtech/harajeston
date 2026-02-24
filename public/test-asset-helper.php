<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$testPath = 'storage/listings/19/699c1625b5f7c_1771836965.png';

echo "Test Path: {$testPath}\n\n";
echo "asset(): " . asset($testPath) . "\n";
echo "url(): " . url($testPath) . "\n";
echo "config('app.url'): " . config('app.url') . "\n";
echo "Storage::url(): " . \Storage::url('listings/19/699c1625b5f7c_1771836965.png') . "\n";
