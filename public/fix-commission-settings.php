<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Current commission settings:\n";
$commissionType = \App\Models\SiteSetting::get('commission_type', 'percentage');
$commissionPercentage = \App\Models\SiteSetting::get('commission_percentage', 5);
echo "  Type: " . $commissionType . "\n";
echo "  Percentage: " . $commissionPercentage . "%\n";

echo "\nChanging to percentage-based commission...\n";

\App\Models\SiteSetting::set('commission_type', 'percentage');
\App\Models\SiteSetting::set('commission_percentage', 5);

echo "New settings:\n";
echo "  Type: " . \App\Models\SiteSetting::get('commission_type') . "\n";
echo "  Percentage: " . \App\Models\SiteSetting::get('commission_percentage') . "%\n";

// Test calculation
$commissionService = app(\App\Services\CommissionService::class);
$commission = $commissionService->calculateCommission(40000, 34);
echo "\nCalculated Commission for 40000: " . $commission . "\n";
echo "Expected (5%): " . (40000 * 0.05) . "\n";
