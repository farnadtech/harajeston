<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

// Clear view cache
Artisan::call('view:clear');
echo "View cache cleared!\n";

// Clear config cache
Artisan::call('config:clear');
echo "Config cache cleared!\n";

// Clear route cache
Artisan::call('route:clear');
echo "Route cache cleared!\n";

echo "\nAll caches cleared successfully!\n";
echo "Please refresh your browser.\n";
