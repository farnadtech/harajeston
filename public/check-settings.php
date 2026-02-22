<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Admin Settings:\n";
echo "default_show_before_start = " . (\App\Models\SiteSetting::get('default_show_before_start', false) ? 'true' : 'false') . "\n\n";

echo "Listing 'کمیسیون 2':\n";
$listing = \App\Models\Listing::where('slug', 'kmysyon-2-699aa862423f1')->first();
if ($listing) {
    echo "show_before_start = " . ($listing->show_before_start ? 'true' : 'false') . "\n";
    echo "status = {$listing->status}\n";
}
