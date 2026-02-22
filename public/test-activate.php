<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate POST request to activate
$request = Illuminate\Http\Request::create(
    '/admin/listings/17/activate',
    'POST',
    ['_token' => csrf_token()],
    [],
    [],
    ['HTTP_ACCEPT' => 'application/json']
);

// Set authenticated user (admin)
$admin = \App\Models\User::where('role', 'admin')->first();
if ($admin) {
    auth()->login($admin);
}

try {
    $response = $kernel->handle($request);
    
    echo "<h1>Test Activate Listing 17</h1>";
    echo "<p><strong>Status Code:</strong> " . $response->getStatusCode() . "</p>";
    echo "<p><strong>Content:</strong></p>";
    echo "<pre>" . htmlspecialchars($response->getContent()) . "</pre>";
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
