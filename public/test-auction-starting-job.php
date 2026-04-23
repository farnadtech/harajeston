<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "اجرای Job برای فعال‌سازی آگهی‌ها...\n\n";

// Run the job
$job = new \App\Jobs\ProcessAuctionStarting();
$job->handle();

echo "\n✓ Job اجرا شد!\n\n";

// Check listing 39 again
$listing = \App\Models\Listing::find(39);
if ($listing) {
    echo "آگهی #39:\n";
    echo "وضعیت: {$listing->status}\n";
}
