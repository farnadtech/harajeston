<?php
// Clear Laravel view cache
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Clearing view cache...\n";
Artisan::call('view:clear');
echo Artisan::output();

echo "\nClearing config cache...\n";
Artisan::call('config:clear');
echo Artisan::output();

echo "\nClearing route cache...\n";
Artisan::call('route:clear');
echo Artisan::output();

echo "\nAll caches cleared!\n";
echo "\nNow try accessing your order page again.\n";
