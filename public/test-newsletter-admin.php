<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

try {
    $count = App\Models\NewsletterSubscriber::count();
    $active = App\Models\NewsletterSubscriber::active()->count();
    echo "Total: $count, Active: $active\n";
    
    // Test controller
    $ctrl = new App\Http\Controllers\Admin\NewsletterController();
    $req = Illuminate\Http\Request::create('/admin/newsletter', 'GET');
    $response = $ctrl->index($req);
    echo "Controller OK\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
