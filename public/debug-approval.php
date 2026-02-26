<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "=== Debug Approval Issue ===\n\n";

$seller = \App\Models\User::where('role', 'seller')->first();
$admin = \App\Models\User::where('role', 'admin')->first();

$listing = \App\Models\Listing::create([
    'seller_id' => $seller->id,
    'title' => 'Debug Test',
    'slug' => 'debug-test-' . uniqid(),
    'description' => 'Test',
    'category_id' => 1,
    'condition' => 'new',
    'starting_price' => 100000,
    'current_price' => 100000,
    'starts_at' => now()->addMinutes(5),
    'ends_at' => now()->addDays(7),
    'status' => 'pending',
    'tags' => ['test'],
]);

echo "Created listing ID: {$listing->id}\n";
echo "Before update:\n";
echo "  - approved_at: " . ($listing->approved_at ?? 'NULL') . "\n";
echo "  - approved_by: " . ($listing->approved_by ?? 'NULL') . "\n\n";

// Try to update
$result = $listing->update([
    'approved_at' => now(),
    'approved_by' => $admin->id,
]);

echo "Update result: " . ($result ? 'true' : 'false') . "\n\n";

$listing->refresh();

echo "After update:\n";
echo "  - approved_at: " . ($listing->approved_at ?? 'NULL') . "\n";
echo "  - approved_by: " . ($listing->approved_by ?? 'NULL') . "\n\n";

// Check if columns exist
echo "Checking if columns exist in database:\n";
$columns = DB::select("SHOW COLUMNS FROM listings LIKE 'approved_%'");
foreach ($columns as $column) {
    echo "  - {$column->Field}: {$column->Type}\n";
}

$listing->delete();
echo "\nCleaned up test listing\n";
